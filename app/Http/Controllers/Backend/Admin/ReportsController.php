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
    public function index(Request $request)
    {
        $hasAnyFilter = $this->hasAnyFilterApplied($request);

        if (! $hasAnyFilter) {
            if (! $request->ajax()) {
                return view('backend.admin.pages.reports.index', [
                    'reports'   => collect(),
                    'companies' => Company::orderBy('name')->get(['id', 'name']),
                    'depots'    => Depot::orderBy('depot_name')->get(['id', 'depot_name']),
                    'stations'  => FillingStation::orderBy('station_name')->get(['id', 'station_name', 'district']),
                    'divisions' => $this->loadDivisions(),
                ]);
            }

            $tableHtml = view('backend.admin.pages.reports.table', [
                'reports'     => collect(),
                'totalRow'    => null,
                'currentPage' => 1,
                'lastPage'    => 1,
                'total'       => 0,
            ])->render();

            return response()->json(['success' => true, 'html' => $tableHtml, 'total' => 0]);
        }

        $rawReports = $this->buildFilteredQuery($request)
            ->orderBy('station_id')
            ->orderBy('report_date')
            ->get();

        $officerMap = $this->loadOfficerMap();

        $aggregatedReports = $this->aggregateByStation($rawReports, $request);

        $formattedReports = $aggregatedReports->map(
            fn($stationData) => $this->formatAggregatedReport($stationData, $officerMap)
        );

        $perPage      = 10;
        $currentPage  = (int) $request->get('page', 1);
        $total        = $formattedReports->count();
        $paginatedReports = $formattedReports->forPage($currentPage, $perPage);

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

            return response()->json(['success' => true, 'html' => $tableHtml, 'total' => $total]);
        }

        return view('backend.admin.pages.reports.index', [
            'reports'   => collect(),
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'depots'    => Depot::orderBy('depot_name')->get(['id', 'depot_name']),
            'stations'  => FillingStation::orderBy('station_name')->get(['id', 'station_name', 'district']),
            'divisions' => $this->loadDivisions(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // QUERY BUILDING
    // ─────────────────────────────────────────────────────────────

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
            $query->whereHas('fillingStation', fn($q) => $q->where('division', $request->division));
        }
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }
        if ($request->filled('thana_upazila')) {
            $query->where('thana_upazila', $request->thana_upazila);
        }
        if ($request->filled('company_id')) {
            $query->whereHas('fillingStation', fn($q) => $q->where('company_id', $request->company_id));
        }
        if ($request->filled('depot_id')) {
            $query->whereHas('fillingStation.depot', fn($q) => $q->where('id', $request->depot_id));
        }
        if ($request->filled('station_id')) {
            $query->where('station_id', $request->station_id);
        }
        if ($request->filled('fuel_type')) {
            $fuelType = strtolower(trim($request->fuel_type));
            $allowed  = ['octane', 'petrol', 'diesel', 'others'];
            if (in_array($fuelType, $allowed)) {
                $query->where(
                    fn($q) => $q
                        ->where("{$fuelType}_received", '>', 0)
                        ->orWhere("{$fuelType}_sales", '>', 0)
                        ->orWhere("{$fuelType}_closing_stock", '>', 0)
                );
            }
        }

        return $query;
    }

    // ─────────────────────────────────────────────────────────────
    // OFFICER MAP
    // ─────────────────────────────────────────────────────────────

    /**
     * station_id → officer name
     * assign_tag_officers.filling_station_id দিয়ে join করা হয়।
     * profiles table থেকে name নেওয়া হচ্ছে।
     */
    private function loadOfficerMap(): Collection
    {
        return AssignTagOfficer::with(['officer.profile'])
            ->where('status', 'active')
            ->get()
            ->keyBy('filling_station_id')
            ->map(function ($assignment) {
                // profiles.name → users.name → fallback '—'
                return $assignment->officer?->profile?->name
                    ?? $assignment->officer?->name
                    ?? '—';
            });
    }

    // ─────────────────────────────────────────────────────────────
    // AGGREGATION
    // ─────────────────────────────────────────────────────────────

    private function aggregateByStation(Collection $rawReports, Request $request): Collection
    {
        $fuelKeys = ['diesel', 'petrol', 'octane', 'others'];

        $aggregated = $rawReports->groupBy('station_id')->map(function ($stationRows) use ($fuelKeys) {
            $firstRow = $stationRows->first();
            $lastRow  = $stationRows->last();

            $stationData = [
                'id'               => $lastRow->id,
                'station_id'       => $firstRow->station_id,
                'station_name'     => $firstRow->station_name ?? $firstRow->fillingStation?->station_name ?? '—',
                'district'         => $firstRow->district ?? '',
                'division'         => $firstRow->fillingStation?->division ?? '',
                'thana_upazila'    => $firstRow->thana_upazila ?? '',
                'company_name'     => $firstRow->fillingStation?->company?->name ?? '—',
                'depot_name'       => $firstRow->depot_name ?? $firstRow->fillingStation?->depot?->depot_name ?? '',
                'comment'          => $lastRow->comment ?? '',
                'report_date_from' => $firstRow->report_date?->format('Y-m-d') ?? '',
                'report_date_to'   => $lastRow->report_date?->format('Y-m-d') ?? '',
            ];

            foreach ($fuelKeys as $fuel) {
                $stationData["{$fuel}_prev_stock"]    = (float) ($firstRow->{"{$fuel}_prev_stock"} ?? 0);
                $stationData["{$fuel}_supply"]        = (float) $stationRows->sum("{$fuel}_supply");
                $stationData["{$fuel}_received"]      = (float) $stationRows->sum("{$fuel}_received");
                $stationData["{$fuel}_sales"]         = (float) $stationRows->sum("{$fuel}_sales");
                $stationData["{$fuel}_closing_stock"] = (float) ($lastRow->{"{$fuel}_closing_stock"} ?? 0);
                $stationData["{$fuel}_difference"]    = $stationData["{$fuel}_supply"] - $stationData["{$fuel}_received"];
            }

            return $stationData;
        });

        if (request()->filled('stock_status')) {
            $status     = request('stock_status');
            $aggregated = $aggregated->filter(function ($row) use ($status, $fuelKeys) {
                $closingTotal = array_sum(array_map(fn($f) => $row["{$f}_closing_stock"], $fuelKeys));
                $diffTotal    = array_sum(array_map(fn($f) => abs($row["{$f}_difference"]), $fuelKeys));

                return match ($status) {
                    'available' => $closingTotal >= 1000,
                    'low'       => $closingTotal > 0 && $closingTotal < 1000,
                    'zero'      => $closingTotal <= 0,
                    // 'highdiff'  => $diffTotal > 50,
                    default     => true,
                };
            });
        }

        return $aggregated->values();
    }

    // ─────────────────────────────────────────────────────────────
    // FORMATTING
    // ─────────────────────────────────────────────────────────────

    private function formatAggregatedReport(array $stationData, Collection $officerMap): array
    {
        $fuelKeys   = ['diesel', 'petrol', 'octane', 'others'];
        $stationId  = $stationData['station_id'];

        // assign_tag_officers.filling_station_id দিয়ে officer নাও
        $tagOfficer = $officerMap->get($stationId, '—');

        $fuelStatuses = [];
        foreach ($fuelKeys as $fuel) {
            $fuelStatuses[$fuel] = $this->resolveFuelStatus(
                $stationData["{$fuel}_closing_stock"],
                $stationData["{$fuel}_difference"]
            );
        }

        $totalClosing = array_sum(array_map(fn($f) => $stationData["{$f}_closing_stock"], $fuelKeys));
        $totalDiff    = array_sum(array_map(fn($f) => abs($stationData["{$f}_difference"]), $fuelKeys));

        $overallStatus = match (true) {
            // $totalDiff > 50      => ['label' => 'High Difference', 'css' => 'status-highdiff'],
            $totalClosing <= 0   => ['label' => 'Zero Stock',      'css' => 'status-zero'],
            $totalClosing < 1000 => ['label' => 'Low Stock',       'css' => 'status-low'],
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

            'diesel_prev_stock'    => $stationData['diesel_prev_stock'],
            'diesel_supply'        => $stationData['diesel_supply'],
            'diesel_received'      => $stationData['diesel_received'],
            'diesel_difference'    => $stationData['diesel_difference'],
            'diesel_sales'         => $stationData['diesel_sales'],
            'diesel_closing_stock' => $stationData['diesel_closing_stock'],

            'petrol_prev_stock'    => $stationData['petrol_prev_stock'],
            'petrol_supply'        => $stationData['petrol_supply'],
            'petrol_received'      => $stationData['petrol_received'],
            'petrol_difference'    => $stationData['petrol_difference'],
            'petrol_sales'         => $stationData['petrol_sales'],
            'petrol_closing_stock' => $stationData['petrol_closing_stock'],

            'octane_prev_stock'    => $stationData['octane_prev_stock'],
            'octane_supply'        => $stationData['octane_supply'],
            'octane_received'      => $stationData['octane_received'],
            'octane_difference'    => $stationData['octane_difference'],
            'octane_sales'         => $stationData['octane_sales'],
            'octane_closing_stock' => $stationData['octane_closing_stock'],

            'others_prev_stock'    => $stationData['others_prev_stock'],
            'others_supply'        => $stationData['others_supply'],
            'others_received'      => $stationData['others_received'],
            'others_difference'    => $stationData['others_difference'],
            'others_sales'         => $stationData['others_sales'],
            'others_closing_stock' => $stationData['others_closing_stock'],
        ];
    }

    private function resolveFuelStatus(float $closingStock, float $difference): array
    {
        $absDiff = abs($difference);
        if ($closingStock <= 0)   return ['label' => 'Zero',      'css' => 'status-zero'];
        // if ($absDiff > 50)        return ['label' => 'High Diff', 'css' => 'status-highdiff'];
        if ($closingStock < 1000) return ['label' => 'Low',       'css' => 'status-low'];
        return ['label' => 'Available', 'css' => 'status-available'];
    }

    private function buildTotalRow(Collection $allFormattedReports): array
    {
        $totals = [];
        foreach (['diesel', 'petrol', 'octane', 'others'] as $fuel) {
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

    private function hasAnyFilterApplied(Request $request): bool
    {
        foreach (
            [
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
            ] as $key
        ) {
            if ($request->filled($key)) return true;
        }
        return false;
    }

    private function loadDivisions(): array
    {
        $filePath = resource_path('data/location.json');
        if (! file_exists($filePath)) return [];
        return json_decode(file_get_contents($filePath), true)['divisions'] ?? [];
    }

    // ─────────────────────────────────────────────────────────────
    // OTHER ACTIONS
    // ─────────────────────────────────────────────────────────────

    public function sendMessage(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:fuelreports,id',
            'message'   => 'required|string|max:1000',
        ]);

        return response()->json(['success' => true, 'message' => 'Message sent successfully.']);
    }

    public function destroy(int $id)
    {
        Fuelreport::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Report deleted successfully.']);
    }
}
