<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignTagOfficer;
use App\Models\Company;
use App\Models\Depot;
use App\Models\FillingStation;
use App\Models\Fuelreport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportsController extends Controller
{
    /**
     * Single entry point:
     *   - Normal page load  → returns index.blade.php
     *   - AJAX filter call  → returns JSON { success, html, total }
     *
     * Date-range aggregation rules:
     *   - supply_from_depot  → SUM across date range
     *   - received_at_station → SUM across date range
     *   - sales              → SUM across date range
     *   - difference         → total_supply - total_received (calculated)
     *   - closing_stock      → value from the LAST date in range only
     *   - prev_stock         → value from the FIRST date in range only
     */
    public function index(Request $request)
    {
        $hasAnyFilter = $this->hasAnyFilterApplied($request);

        // ── Normal page load with no filters → show empty table ──
        if (! $hasAnyFilter) {

            // Not AJAX → return full page with empty reports
            if (! $request->ajax()) {
                return view('backend.admin.pages.reports.index', [
                    'reports'   => collect(),   // empty — user must apply filter
                    'companies' => Company::orderBy('name')->get(['id', 'name']),
                    'depots'    => Depot::orderBy('depot_name')->get(['id', 'depot_name']),
                    'stations'  => FillingStation::orderBy('station_name')->get(['id', 'station_name', 'district']),
                    'divisions' => $this->loadDivisions(),
                ]);
            }

            // AJAX with no filter → return empty table HTML
            $tableHtml = view('backend.admin.pages.reports.table', [
                'reports'     => collect(),
                'totalRow'    => null,
                'currentPage' => 1,
                'lastPage'    => 1,
                'total'       => 0,
            ])->render();

            return response()->json([
                'success' => true,
                'html'    => $tableHtml,
                'total'   => 0,
            ]);
        }

        // ── Build filtered query ──────────────────────────────────
        $baseQuery = $this->buildFilteredQuery($request);

        // ── Get all matching raw reports (for aggregation) ────────
        $rawReports = $baseQuery
            ->orderBy('station_id')
            ->orderBy('report_date')
            ->get();

        // ── Aggregate: group by station, then summarize ───────────
        $aggregatedReports = $this->aggregateByStation($rawReports, $request);

        // ── Tag officer map (station_id → officer name) ───────────
        $officerMap = $this->loadOfficerMap();

        // ── Format for view ───────────────────────────────────────
        $formattedReports = $aggregatedReports->map(
            fn($stationData) => $this->formatAggregatedReport($stationData, $officerMap)
        );

        // ── Manual pagination ─────────────────────────────────────
        $perPage     = 10;
        $currentPage = (int) $request->get('page', 1);
        $total       = $formattedReports->count();
        $paginatedReports = $formattedReports->forPage($currentPage, $perPage);

        // ── AJAX → return JSON ────────────────────────────────────
        if ($request->ajax()) {
            $tableHtml = view('backend.admin.pages.reports.table', [
                'reports'     => $paginatedReports,
                'totalRow'    => $this->buildTotalRow($formattedReports),
                'currentPage' => $currentPage,
                'lastPage'    => (int) ceil($total / $perPage),
                'total'       => $total,
                'filters'     => $request->only([
                    'from_date',
                    'to_date',
                    'division',
                    'district',
                    'thana_upazila',
                    'company_id',
                    'depot_id',
                    'station_id',
                    'fuel_type',
                    'stock_status',
                ]),
            ])->render();

            return response()->json([
                'success' => true,
                'html'    => $tableHtml,
                'total'   => $total,
            ]);
        }

        // ── Normal page load with filters ─────────────────────────
        return view('backend.admin.pages.reports.index', [
            'reports'   => collect(),   // initial load always empty; user presses Apply
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'depots'    => Depot::orderBy('depot_name')->get(['id', 'depot_name']),
            'stations'  => FillingStation::orderBy('station_name')->get(['id', 'station_name', 'district']),
            'divisions' => $this->loadDivisions(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // QUERY BUILDING
    // ─────────────────────────────────────────────────────────────

    /**
     * Build Eloquent query with all active filters applied.
     */
    private function buildFilteredQuery(Request $request)
    {
        $query = Fuelreport::query()
            ->with(['fillingStation.company', 'fillingStation.depot']);

        if ($request->filled('from_date')) {
            $query->whereDate('report_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('report_date', '<=', $request->to_date);
        }

        if ($request->filled('division')) {
            $query->whereHas('fillingStation', function ($q) use ($request) {
                $q->where('division', $request->division);
            });
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('thana_upazila')) {
            $query->where('thana_upazila', $request->thana_upazila);
        }

        if ($request->filled('company_id')) {
            $query->whereHas('fillingStation', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        if ($request->filled('depot_id')) {
            $query->whereHas('fillingStation.depot', function ($q) use ($request) {
                $q->where('id', $request->depot_id);
            });
        }

        if ($request->filled('station_id')) {
            $query->where('station_id', $request->station_id);
        }

        if ($request->filled('fuel_type')) {
            $fuelType = strtolower(trim($request->fuel_type));
            $allowed  = ['octane', 'petrol', 'diesel', 'others'];

            if (in_array($fuelType, $allowed)) {
                $query->where(function ($q) use ($fuelType) {
                    $q->where("{$fuelType}_received", '>', 0)
                        ->orWhere("{$fuelType}_sales", '>', 0)
                        ->orWhere("{$fuelType}_closing_stock", '>', 0);
                });
            }
        }

        // Note: stock_status filter applied AFTER aggregation (see aggregateByStation)

        return $query;
    }

    // ─────────────────────────────────────────────────────────────
    // AGGREGATION
    // ─────────────────────────────────────────────────────────────

    /**
     * Group raw Fuelreport rows by station_id, then:
     *   - SUM supply, received, sales across the date range
     *   - LAST date's closing_stock as the closing stock
     *   - FIRST date's prev_stock as the opening stock
     *   - difference = total_supply - total_received (recalculated)
     *
     * @param  \Illuminate\Support\Collection $rawReports   Already sorted by station + date ASC
     * @param  Request                        $request
     * @return \Illuminate\Support\Collection
     */
    private function aggregateByStation(Collection $rawReports, Request $request): Collection
    {
        $fuelKeys = ['diesel', 'petrol', 'octane', 'others'];

        // Group all rows by station ID
        $groupedByStation = $rawReports->groupBy('station_id');

        $aggregated = $groupedByStation->map(function ($stationRows) use ($fuelKeys) {

            // Rows are sorted ASC by date — first = opening, last = closing
            $firstRow = $stationRows->first();
            $lastRow  = $stationRows->last();

            $stationData = [
                'station_id'    => $firstRow->station_id,
                'station_name'  => $firstRow->station_name         ?? $firstRow->fillingStation?->station_name ?? '—',
                'district'      => $firstRow->district             ?? '',
                'division'      => $firstRow->fillingStation?->division ?? '',
                'thana_upazila' => $firstRow->thana_upazila        ?? '',
                'company_name'  => $firstRow->fillingStation?->company?->name ?? '—',
                'depot_name'    => $firstRow->depot_name           ?? $firstRow->fillingStation?->depot?->depot_name ?? '',
                'comment'       => $lastRow->comment               ?? '',
                'report_date_from' => $firstRow->report_date?->format('Y-m-d') ?? '',
                'report_date_to'   => $lastRow->report_date?->format('Y-m-d')  ?? '',
                // Keep the last report's ID for actions (view/message/delete)
                'id'            => $lastRow->id,
            ];

            // For each fuel type: SUM supply+received+sales, take LAST closing, FIRST prev
            foreach ($fuelKeys as $fuel) {
                $totalSupply   = $stationRows->sum("{$fuel}_supply");
                $totalReceived = $stationRows->sum("{$fuel}_received");
                $totalSales    = $stationRows->sum("{$fuel}_sales");

                // prev_stock = first row's prev_stock (opening stock of the period)
                $openingStock  = (float) ($firstRow->{"{$fuel}_prev_stock"} ?? 0);

                // closing_stock = last row's closing_stock (end of period)
                $closingStock  = (float) ($lastRow->{"{$fuel}_closing_stock"} ?? 0);

                // difference = total_supply - total_received (recalculated, not summed)
                $difference    = $totalSupply - $totalReceived;

                $stationData["{$fuel}_prev_stock"]    = $openingStock;
                $stationData["{$fuel}_supply"]        = (float) $totalSupply;
                $stationData["{$fuel}_received"]      = (float) $totalReceived;
                $stationData["{$fuel}_difference"]    = (float) $difference;
                $stationData["{$fuel}_sales"]         = (float) $totalSales;
                $stationData["{$fuel}_closing_stock"] = $closingStock;
            }

            return $stationData;
        });

        // ── Apply stock_status filter AFTER aggregation ───────────
        if (request()->filled('stock_status')) {
            $status = request('stock_status');

            $aggregated = $aggregated->filter(function ($row) use ($status, $fuelKeys) {
                $closingTotal = 0;
                $diffTotal    = 0;

                foreach ($fuelKeys as $fuel) {
                    $closingTotal += $row["{$fuel}_closing_stock"];
                    $diffTotal    += abs($row["{$fuel}_difference"]);
                }

                return match ($status) {
                    'available' => $closingTotal >= 2000,
                    'low'       => $closingTotal > 0 && $closingTotal < 2000,
                    'zero'      => $closingTotal <= 0,
                    'highdiff'  => $diffTotal > 50,
                    default     => true,
                };
            });
        }

        return $aggregated->values();
    }

    // ─────────────────────────────────────────────────────────────
    // FORMATTING
    // ─────────────────────────────────────────────────────────────

    /**
     * Format an aggregated station-data array into the final shape for the view.
     */
    private function formatAggregatedReport(array $stationData, Collection $officerMap): array
    {
        $stationId  = $stationData['station_id'];
        $tagOfficer = $officerMap->get($stationId, '—');

        $fuelKeys = ['diesel', 'petrol', 'octane', 'others'];

        // Per-fuel statuses
        $fuelStatuses = [];
        foreach ($fuelKeys as $fuel) {
            $fuelStatuses[$fuel] = $this->resolveFuelStatus(
                $stationData["{$fuel}_closing_stock"],
                $stationData["{$fuel}_difference"]
            );
        }

        // Overall status (based on all fuels combined)
        $totalClosing = array_sum(array_map(fn($f) => $stationData["{$f}_closing_stock"], $fuelKeys));
        $totalDiff    = array_sum(array_map(fn($f) => abs($stationData["{$f}_difference"]), $fuelKeys));

        $overallStatus = match (true) {
            $totalDiff > 50      => ['label' => 'High Difference', 'css' => 'status-highdiff'],
            $totalClosing <= 0   => ['label' => 'Zero Stock',      'css' => 'status-zero'],
            $totalClosing < 2000 => ['label' => 'Low Stock',       'css' => 'status-low'],
            default              => ['label' => 'Available',       'css' => 'status-available'],
        };

        return [
            'id'               => $stationData['id'],
            'report_date_from' => $stationData['report_date_from'],
            'report_date_to'   => $stationData['report_date_to'],
            'station_name'     => $stationData['station_name'],
            'district'         => $stationData['district'],
            'division'         => $stationData['division'],
            'thana_upazila'    => $stationData['thana_upazila'],
            'company_name'     => $stationData['company_name'],
            'depot_name'       => $stationData['depot_name'],
            'tag_officer'      => $tagOfficer,
            'comment'          => $stationData['comment'],
            'fuel_statuses'    => $fuelStatuses,
            'overall_status'   => $overallStatus,

            // Diesel
            'diesel_prev_stock'    => $stationData['diesel_prev_stock'],
            'diesel_supply'        => $stationData['diesel_supply'],
            'diesel_received'      => $stationData['diesel_received'],
            'diesel_difference'    => $stationData['diesel_difference'],
            'diesel_sales'         => $stationData['diesel_sales'],
            'diesel_closing_stock' => $stationData['diesel_closing_stock'],

            // Petrol
            'petrol_prev_stock'    => $stationData['petrol_prev_stock'],
            'petrol_supply'        => $stationData['petrol_supply'],
            'petrol_received'      => $stationData['petrol_received'],
            'petrol_difference'    => $stationData['petrol_difference'],
            'petrol_sales'         => $stationData['petrol_sales'],
            'petrol_closing_stock' => $stationData['petrol_closing_stock'],

            // Octane
            'octane_prev_stock'    => $stationData['octane_prev_stock'],
            'octane_supply'        => $stationData['octane_supply'],
            'octane_received'      => $stationData['octane_received'],
            'octane_difference'    => $stationData['octane_difference'],
            'octane_sales'         => $stationData['octane_sales'],
            'octane_closing_stock' => $stationData['octane_closing_stock'],

            // Others
            'others_prev_stock'    => $stationData['others_prev_stock'],
            'others_supply'        => $stationData['others_supply'],
            'others_received'      => $stationData['others_received'],
            'others_difference'    => $stationData['others_difference'],
            'others_sales'         => $stationData['others_sales'],
            'others_closing_stock' => $stationData['others_closing_stock'],
        ];
    }

    /**
     * Resolve status for a single fuel type.
     */
    private function resolveFuelStatus(float $closingStock, float $difference): array
    {
        $absDiff = abs($difference);

        if ($closingStock <= 0) {
            return ['label' => 'Zero',      'css' => 'status-zero'];
        }
        if ($absDiff > 50) {
            return ['label' => 'High Diff', 'css' => 'status-highdiff'];
        }
        if ($closingStock < 2000) {
            return ['label' => 'Low',       'css' => 'status-low'];
        }

        return ['label' => 'Available', 'css' => 'status-available'];
    }

    /**
     * Build the grand total row across ALL paginated reports.
     * (Totals are always from full result, not just current page.)
     */
    private function buildTotalRow(Collection $allFormattedReports): array
    {
        $fuelKeys = ['diesel', 'petrol', 'octane', 'others'];
        $totals   = [];

        foreach ($fuelKeys as $fuel) {
            $totals["{$fuel}_prev_stock"]    = $allFormattedReports->sum("{$fuel}_prev_stock");
            $totals["{$fuel}_supply"]        = $allFormattedReports->sum("{$fuel}_supply");
            $totals["{$fuel}_received"]      = $allFormattedReports->sum("{$fuel}_received");
            $totals["{$fuel}_difference"]    = $allFormattedReports->sum("{$fuel}_difference");
            $totals["{$fuel}_sales"]         = $allFormattedReports->sum("{$fuel}_sales");
            $totals["{$fuel}_closing_stock"] = $allFormattedReports->sum("{$fuel}_closing_stock");
        }

        return $totals;
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────

    /**
     * Returns true if the request has at least one non-empty filter param.
     */
    private function hasAnyFilterApplied(Request $request): bool
    {
        $filterKeys = [
            'from_date',
            'to_date',
            'division',
            'district',
            'thana_upazila',
            'company_id',
            'depot_id',
            'station_id',
            'fuel_type',
            'stock_status',
        ];

        foreach ($filterKeys as $key) {
            if ($request->filled($key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load the active officer map: station_id → officer name.
     * Uses AssignTagOfficer model directly (no relationship on Fuelreport needed).
     */
    private function loadOfficerMap(): Collection
    {
        return AssignTagOfficer::with('officer')
            ->where('status', 'active')
            ->get()
            ->keyBy('station_id')
            ->map(fn($assignment) => $assignment->officer?->name ?? '—');
    }

    /**
     * Load Bangladesh division/district/upazila tree from JSON.
     */
    private function loadDivisions(): array
    {
        $filePath = resource_path('data/location.json');

        if (! file_exists($filePath)) {
            return [];
        }

        $data = json_decode(file_get_contents($filePath), true);

        return $data['divisions'] ?? [];
    }

    // ─────────────────────────────────────────────────────────────
    // OTHER ACTIONS
    // ─────────────────────────────────────────────────────────────

    /**
     * Send a message to the tagged officer of a filling station.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:fuelreports,id',
            'message'   => 'required|string|max:1000',
        ]);



        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
        ]);
    }

    /**
     * Delete a fuel report.
     */
    public function destroy(int $id)
    {
        Fuelreport::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully.',
        ]);
    }
}
