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
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

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
            ->orderBy('report_date', 'desc')
            ->orderBy('station_id')
            ->get();

        $officerMap       = $this->loadOfficerMap();
        $formattedReports = $rawReports->map(
            fn($report) => $this->formatSingleReport($report, $officerMap)
        );

        $perPage          = 10;
        $currentPage      = (int) $request->get('page', 1);
        $total            = $formattedReports->count();
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

        if ($request->filled('from_date') && !$request->filled('to_date')) {
            $from = \Carbon\Carbon::parse($request->from_date)->startOfDay();
            $to   = \Carbon\Carbon::parse($request->from_date)->endOfDay();
            $query->whereBetween('report_date', [$from, $to]);
        } elseif ($request->filled('from_date') && $request->filled('to_date')) {
            $from = \Carbon\Carbon::parse($request->from_date)->startOfDay();
            $to   = \Carbon\Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('report_date', [$from, $to]);
        }

        if ($request->filled('division'))
            $query->whereHas('fillingStation', fn($q) => $q->where('division', $request->division));
        if ($request->filled('district'))
            $query->where('district', $request->district);
        if ($request->filled('thana_upazila'))
            $query->where('thana_upazila', $request->thana_upazila);
        if ($request->filled('company_id'))
            $query->whereHas('fillingStation', fn($q) => $q->where('company_id', $request->company_id));
        if ($request->filled('depot_id'))
            $query->whereHas('fillingStation.depot', fn($q) => $q->where('id', $request->depot_id));
        if ($request->filled('station_id'))
            $query->where('station_id', $request->station_id);
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
        if ($request->filled('stock_status')) {
            $status = $request->stock_status;
            $query->where(function ($q) use ($status) {
                $fuelKeys    = ['diesel', 'petrol', 'octane', 'others'];
                $closingCols = array_map(fn($f) => "COALESCE({$f}_closing_stock, 0)", $fuelKeys);
                $sumExpr     = implode(' + ', $closingCols);
                match ($status) {
                    'available' => $q->whereRaw("({$sumExpr}) >= 1000"),
                    'low'       => $q->whereRaw("({$sumExpr}) > 0 AND ({$sumExpr}) < 1000"),
                    'zero'      => $q->whereRaw("({$sumExpr}) <= 0"),
                    default     => null,
                };
            });
        }

        return $query;
    }

    // ─────────────────────────────────────────────────────────────
    // OFFICER MAP
    // ─────────────────────────────────────────────────────────────

    private function loadOfficerMap(): Collection
    {
        return AssignTagOfficer::with(['officer.profile'])
            ->where('status', 'active')
            ->get()
            ->keyBy('filling_station_id')
            ->map(function ($assignment) {
                return $assignment->officer?->profile?->name
                    ?? $assignment->officer?->name
                    ?? '—';
            });
    }

    // ─────────────────────────────────────────────────────────────
    // FORMAT SINGLE REPORT (no groupBy)
    // ─────────────────────────────────────────────────────────────

    private function formatSingleReport($report, Collection $officerMap): array
    {
        $fuelKeys   = ['diesel', 'petrol', 'octane', 'others'];
        $tagOfficer = $officerMap->get($report->station_id, '—');

        $fuelStatuses = [];
        foreach ($fuelKeys as $fuel) {
            $fuelStatuses[$fuel] = $this->resolveFuelStatus(
                (float) ($report->{"{$fuel}_closing_stock"} ?? 0),
                (float) ($report->{"{$fuel}_supply"} ?? 0) - (float) ($report->{"{$fuel}_received"} ?? 0)
            );
        }

        $totalClosing  = array_sum(array_map(fn($f) => (float) ($report->{"{$f}_closing_stock"} ?? 0), $fuelKeys));
        $overallStatus = match (true) {
            $totalClosing <= 0   => ['label' => 'Zero Stock', 'css' => 'status-zero'],
            $totalClosing < 1000 => ['label' => 'Low Stock',  'css' => 'status-low'],
            default              => ['label' => 'Available',  'css' => 'status-available'],
        };

        return [
            'id'               => $report->id,
            'report_date_from' => $report->report_date?->format('Y-m-d') ?? '',
            'report_date_to'   => $report->report_date?->format('Y-m-d') ?? '',
            'station_name'     => $report->station_name ?? $report->fillingStation?->station_name ?? '—',
            'district'         => $report->district ?? '',
            'division'         => $report->fillingStation?->division ?? '',
            'thana_upazila'    => $report->thana_upazila ?? '',
            'company_name'     => $report->fillingStation?->company?->code ?? '—',
            'depot_name'       => $report->depot_name ?? $report->fillingStation?->depot?->depot_name ?? '',
            'tag_officer'      => $tagOfficer,
            'comment'          => $report->comment ?? '',
            'fuel_statuses'    => $fuelStatuses,
            'overall_status'   => $overallStatus,

            'diesel_prev_stock'    => (float) ($report->diesel_prev_stock ?? 0),
            'diesel_supply'        => (float) ($report->diesel_supply ?? 0),
            'diesel_received'      => (float) ($report->diesel_received ?? 0),
            'diesel_difference'    => (float) ($report->diesel_supply ?? 0) - (float) ($report->diesel_received ?? 0),
            'diesel_sales'         => (float) ($report->diesel_sales ?? 0),
            'diesel_closing_stock' => (float) ($report->diesel_closing_stock ?? 0),

            'petrol_prev_stock'    => (float) ($report->petrol_prev_stock ?? 0),
            'petrol_supply'        => (float) ($report->petrol_supply ?? 0),
            'petrol_received'      => (float) ($report->petrol_received ?? 0),
            'petrol_difference'    => (float) ($report->petrol_supply ?? 0) - (float) ($report->petrol_received ?? 0),
            'petrol_sales'         => (float) ($report->petrol_sales ?? 0),
            'petrol_closing_stock' => (float) ($report->petrol_closing_stock ?? 0),

            'octane_prev_stock'    => (float) ($report->octane_prev_stock ?? 0),
            'octane_supply'        => (float) ($report->octane_supply ?? 0),
            'octane_received'      => (float) ($report->octane_received ?? 0),
            'octane_difference'    => (float) ($report->octane_supply ?? 0) - (float) ($report->octane_received ?? 0),
            'octane_sales'         => (float) ($report->octane_sales ?? 0),
            'octane_closing_stock' => (float) ($report->octane_closing_stock ?? 0),

            'others_prev_stock'    => (float) ($report->others_prev_stock ?? 0),
            'others_supply'        => (float) ($report->others_supply ?? 0),
            'others_received'      => (float) ($report->others_received ?? 0),
            'others_difference'    => (float) ($report->others_supply ?? 0) - (float) ($report->others_received ?? 0),
            'others_sales'         => (float) ($report->others_sales ?? 0),
            'others_closing_stock' => (float) ($report->others_closing_stock ?? 0),
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // FORMATTING HELPERS
    // ─────────────────────────────────────────────────────────────

    private function resolveFuelStatus(float $closingStock, float $difference): array
    {
        if ($closingStock <= 0)   return ['label' => 'Zero',      'css' => 'status-zero'];
        if ($closingStock < 1000) return ['label' => 'Low',       'css' => 'status-low'];
        return ['label' => 'Available', 'css' => 'status-available'];
    }

    private function buildTotalRow(Collection $allFormattedReports): array
    {
        $fuelKeys = ['diesel', 'petrol', 'octane', 'others'];
        $totals   = [];

        foreach ($fuelKeys as $fuel) {
            // supply, received, sales, difference — সব row এর sum (ঠিক আছে)
            $totals["{$fuel}_prev_stock"] = $allFormattedReports->sum("{$fuel}_prev_stock");
            $totals["{$fuel}_supply"]     = $allFormattedReports->sum("{$fuel}_supply");
            $totals["{$fuel}_received"]   = $allFormattedReports->sum("{$fuel}_received");
            $totals["{$fuel}_difference"] = $allFormattedReports->sum("{$fuel}_difference");
            $totals["{$fuel}_sales"]      = $allFormattedReports->sum("{$fuel}_sales");

            // closing_stock — প্রতিটা station এর সর্বশেষ row শুধু
            $totals["{$fuel}_closing_stock"] = $allFormattedReports
                ->groupBy('station_name')
                ->map(
                    fn($stationRows) => $stationRows
                        ->sortByDesc('report_date_from')
                        ->first()["{$fuel}_closing_stock"] ?? 0
                )
                ->sum();
        }

        return $totals;
    }

    // ─────────────────────────────────────────────────────────────
    // MPDF HELPER
    // ─────────────────────────────────────────────────────────────

    private function makeMpdf(string $format = 'A4-L'): Mpdf
    {
        $tempDir = '/tmp/mpdf';
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }

        $defaultConfig   = (new ConfigVariables())->getDefaults();
        $defaultFontData = (new FontVariables())->getDefaults()['fontdata'];

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => $format,
            'margin_top'    => 10,
            'margin_bottom' => 10,
            'margin_left'   => 10,
            'margin_right'  => 10,
            'tempDir'       => $tempDir,
            'fontDir'       => array_merge($defaultConfig['fontDir'], [
                base_path('public/fonts'),
            ]),
            'fontdata'      => array_merge($defaultFontData, [
                'solaimanlipi' => ['R' => 'SolaimanLipi.ttf'],
            ]),
            'default_font'  => 'solaimanlipi',
        ]);

        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont   = true;

        return $mpdf;
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

    // ─────────────────────────────────────────────────────────────
    // DIFFERENCE REPORT — daily, no groupBy
    // ─────────────────────────────────────────────────────────────

    public function differenceReport(Request $request)
    {
        $validated = $request->validate([
            'from_date'     => 'nullable|date',
            'to_date'       => 'nullable|date|after_or_equal:from_date',
            'division'      => 'nullable|string|max:100',
            'district'      => 'nullable|string|max:100',
            'thana_upazila' => 'nullable|string|max:100',
            'company_id'    => 'nullable|integer|exists:companies,id',
            'station_id'    => 'nullable|integer|exists:filling_stations,id',
            'tag_officer'   => 'nullable|string|max:100',
            'diff_status'   => 'nullable|in:high,low,normal',
            'min_diff_l'    => 'nullable|numeric|min:0',
            'min_diff_pct'  => 'nullable|numeric|min:0',
            'page'          => 'nullable|integer|min:1',
        ]);

        $query = Fuelreport::query()
            ->with(['fillingStation.company']);

        if (!empty($validated['from_date']) && empty($validated['to_date']))
            $query->whereDate('report_date', $validated['from_date']);
        elseif (!empty($validated['from_date']) && !empty($validated['to_date']))
            $query->whereBetween('report_date', [$validated['from_date'], $validated['to_date']]);

        if (!empty($validated['division']))
            $query->whereHas('fillingStation', fn($q) => $q->where('division', $validated['division']));
        if (!empty($validated['district']))
            $query->where('district', $validated['district']);
        if (!empty($validated['thana_upazila']))
            $query->where('thana_upazila', $validated['thana_upazila']);
        if (!empty($validated['company_id']))
            $query->whereHas('fillingStation', fn($q) => $q->where('company_id', $validated['company_id']));
        if (!empty($validated['station_id']))
            $query->where('station_id', $validated['station_id']);
        if (!empty($validated['tag_officer'])) {
            $officerName = $validated['tag_officer'];
            $query->whereHas(
                'fillingStation.assignedOfficer.officer.profile',
                fn($q) => $q->where('name', 'like', "%{$officerName}%")
            );
        }

        $fuelTypes   = ['octane', 'petrol', 'diesel', 'others'];
        $perPage     = 10;
        $currentPage = (int) ($validated['page'] ?? 1);

        $officerMap = AssignTagOfficer::with(['officer.profile'])
            ->where('status', 'active')->get()->keyBy('filling_station_id');

        $allRawReports = $query->orderBy('report_date', 'desc')->orderBy('station_id')->get();

        // Build per-row (daily) difference data — NO groupBy
        $rows = $allRawReports->map(function ($report) use ($fuelTypes, $officerMap) {
            $stationId          = $report->station_id;
            $assignment         = $officerMap->get($stationId);
            $officerProfile     = $assignment?->officer?->profile;
            $tagOfficerName     = $officerProfile?->name ?? $assignment?->officer?->name ?? '—';
            $officerDesignation = $officerProfile?->designation ?? '—';
            $officerPhone       = $officerProfile?->phone ?? $assignment?->officer?->phone ?? '—';

            $fuelBreakdown = [];
            foreach ($fuelTypes as $fuel) {
                $totalSupply       = (float) ($report->{"{$fuel}_supply"} ?? 0);
                $totalReceived     = (float) ($report->{"{$fuel}_received"} ?? 0);
                $differenceL       = $totalSupply - $totalReceived;
                $differencePercent = $totalSupply > 0 ? round(($differenceL / $totalSupply) * 100, 2) : 0;
                $diffStatus        = match (true) {
                    abs($differencePercent) >= 5 => 'High',
                    abs($differencePercent) >= 1 => 'Low',
                    default                      => 'Normal',
                };
                $fuelBreakdown[] = [
                    'fuelType'          => ucfirst($fuel),
                    'differenceL'       => number_format($differenceL, 0),
                    'differencePercent' => $differencePercent,
                    'diffStatus'        => $diffStatus,
                ];
            }

            return [
                'reportId'           => $report->id,
                'stationId'          => $stationId,
                'reportDate'         => $report->report_date,
                'stationName'        => $report->station_name ?? $report->fillingStation?->station_name ?? '—',
                'companyName'        => $report->fillingStation?->company?->code ?? '—',
                'tagOfficerName'     => $tagOfficerName,
                'officerDesignation' => $officerDesignation,
                'officerPhone'       => $officerPhone,
                'district'           => $report->district ?? '—',
                'thanaUpazila'       => $report->thana_upazila ?? '—',
                'dateFormatted'      => \Carbon\Carbon::parse($report->report_date)->format('d M Y'),
                'dayName'            => \Carbon\Carbon::parse($report->report_date)->format('l'),
                'fuelBreakdown'      => $fuelBreakdown,
            ];
        });

        if (!empty($validated['min_diff_l'])) {
            $minL = (float) $validated['min_diff_l'];
            $rows = $rows->filter(
                fn($row) => collect($row['fuelBreakdown'])->contains(
                    fn($f) => abs((float) str_replace(',', '', $f['differenceL'])) >= $minL
                )
            )->values();
        }

        if (!empty($validated['min_diff_pct'])) {
            $minPct = (float) $validated['min_diff_pct'];
            $rows   = $rows->filter(
                fn($row) => collect($row['fuelBreakdown'])->contains(
                    fn($f) => abs($f['differencePercent']) >= $minPct
                )
            )->values();
        }

        if (!empty($validated['diff_status'])) {
            $targetStatus = ucfirst($validated['diff_status']);
            $rows         = $rows->filter(
                fn($row) => collect($row['fuelBreakdown'])->contains(fn($f) => $f['diffStatus'] === $targetStatus)
            )->values();
        }

        $totalRecords  = $rows->count();
        $totalPages    = (int) ceil($totalRecords / $perPage) ?: 1;
        $paginatedRows = $rows->forPage($currentPage, $perPage)->values();

        return response()->json([
            'success'     => true,
            'rows'        => $paginatedRows,
            'total'       => $totalRecords,
            'currentPage' => $currentPage,
            'lastPage'    => $totalPages,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // MISSING REPORT
    // ─────────────────────────────────────────────────────────────

    public function missingReport(Request $request)
    {
        $perPage     = 10;
        $currentPage = (int) $request->get('page', 1);

        $assignmentsQuery = AssignTagOfficer::with([
            'officer.profile',
            'fillingStation.company',
            'fillingStation.depot'
        ])->where('status', 'active');

        if ($request->filled('division'))
            $assignmentsQuery->whereHas('fillingStation', fn($q) => $q->where('division', $request->division));
        if ($request->filled('district'))
            $assignmentsQuery->whereHas('fillingStation', fn($q) => $q->where('district', $request->district));
        if ($request->filled('thana_upazila'))
            $assignmentsQuery->whereHas('fillingStation', fn($q) => $q->where('upazila', $request->thana_upazila));
        if ($request->filled('company_id'))
            $assignmentsQuery->whereHas('fillingStation', fn($q) => $q->where('company_id', $request->company_id));
        if ($request->filled('depot_id'))
            $assignmentsQuery->whereHas('fillingStation', fn($q) => $q->where('depot_id', $request->depot_id));
        if ($request->filled('station_id'))
            $assignmentsQuery->where('filling_station_id', $request->station_id);

        $allAssignments = $assignmentsQuery->get();

        if ($request->filled('from_date') && !$request->filled('to_date')) {
            $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
            $toDate   = \Carbon\Carbon::parse($request->from_date)->endOfDay();
        } elseif ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = \Carbon\Carbon::parse($request->from_date)->startOfDay();
            $toDate   = \Carbon\Carbon::parse($request->to_date)->endOfDay();
        } else {
            $fromDate = now()->startOfDay();
            $toDate   = now()->endOfDay();
        }

        $reportedStationIds = Fuelreport::whereBetween('report_date', [$fromDate, $toDate])
            ->whereNotNull('station_id')
            ->pluck('station_id')
            ->unique()
            ->toArray();

        $missingRows = $allAssignments
            ->filter(fn($assignment) => !in_array($assignment->filling_station_id, $reportedStationIds))
            ->map(function ($assignment) use ($fromDate) {
                $officer = $assignment->officer;
                $profile = $officer?->profile;
                $station = $assignment->fillingStation;
                return [
                    'id'           => $assignment->id,
                    'missingDate'  => $fromDate->format('Y-m-d'),
                    'officerName'  => $profile?->name ?? $officer?->name ?? '—',
                    'officerPhone' => $profile?->phone ?? $officer?->phone ?? '—',
                    'division'     => $station?->division ?? '—',
                    'district'     => $station?->district ?? '—',
                    'thanaUpazila' => $station?->upazila ?? '—',
                    'stationName'  => $station?->station_name ?? '—',
                    'companyName'  => $station?->company?->code ?? '—',
                    'depotName'    => $station?->depot?->depot_name ?? '—',
                    'status'       => 'Pending',
                ];
            })->values();

        $total      = $missingRows->count();
        $totalPages = (int) ceil($total / $perPage) ?: 1;
        $rows       = $missingRows->forPage($currentPage, $perPage)->values();

        return response()->json([
            'success'     => true,
            'rows'        => $rows,
            'total'       => $total,
            'currentPage' => $currentPage,
            'lastPage'    => $totalPages,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // SUBMITTED REPORT — daily, no groupBy
    // ─────────────────────────────────────────────────────────────

    public function submittedReport(Request $request)
    {
        $perPage     = 10;
        $currentPage = (int) $request->get('page', 1);

        $query = Fuelreport::query()->with([
            'fillingStation.company',
            'fillingStation.depot',
        ]);

        if ($request->filled('from_date') && !$request->filled('to_date'))
            $query->whereDate('report_date', $request->from_date);
        elseif ($request->filled('from_date') && $request->filled('to_date'))
            $query->whereBetween('report_date', [$request->from_date, $request->to_date]);

        if ($request->filled('division'))
            $query->whereHas('fillingStation', fn($q) => $q->where('division', $request->division));
        if ($request->filled('district'))
            $query->where('district', $request->district);
        if ($request->filled('thana_upazila'))
            $query->where('thana_upazila', $request->thana_upazila);
        if ($request->filled('company_id'))
            $query->whereHas('fillingStation', fn($q) => $q->where('company_id', $request->company_id));
        if ($request->filled('depot_id'))
            $query->whereHas('fillingStation', fn($q) => $q->where('depot_id', $request->depot_id));
        if ($request->filled('station_id'))
            $query->where('station_id', $request->station_id);

        $fuelTypes  = ['octane', 'petrol', 'diesel', 'others'];
        $allReports = $query->orderBy('report_date', 'desc')->get();

        $officerMap = AssignTagOfficer::with(['officer.profile'])
            ->where('status', 'active')->get()->keyBy('filling_station_id');

        $rows = $allReports->map(function ($report) use ($fuelTypes, $officerMap) {
            $assignment     = $officerMap->get($report->station_id);
            $officerProfile = $assignment?->officer?->profile;

            $fuelBreakdown = collect($fuelTypes)->map(fn($fuel) => [
                'fuelType'     => ucfirst($fuel),
                'closingStock' => number_format((float) ($report->{"{$fuel}_closing_stock"} ?? 0), 0),
            ])->toArray();

            return [
                'id'                  => $report->id,
                'submitDateFormatted' => \Carbon\Carbon::parse($report->report_date)->format('d M Y'),
                'submitDayName'       => \Carbon\Carbon::parse($report->report_date)->format('l'),
                'officerName'         => $officerProfile?->name ?? $assignment?->officer?->name ?? '—',
                'officerPhone'        => $officerProfile?->phone ?? $assignment?->officer?->phone ?? '—',
                'division'            => $report->fillingStation?->division ?? '—',
                'district'            => $report->district ?? '—',
                'thanaUpazila'        => $report->thana_upazila ?? '—',
                'stationName'         => $report->station_name ?? $report->fillingStation?->station_name ?? '—',
                'companyName'         => $report->fillingStation?->company?->code ?? '—',
                'depotName'           => $report->depot_name ?? $report->fillingStation?->depot?->depot_name ?? '',
                'fuelBreakdown'       => $fuelBreakdown,
                'status'              => 'Submitted',
            ];
        });

        $total      = $rows->count();
        $totalPages = (int) ceil($total / $perPage) ?: 1;
        $paged      = $rows->forPage($currentPage, $perPage)->values();

        return response()->json([
            'success'     => true,
            'rows'        => $paged,
            'total'       => $total,
            'currentPage' => $currentPage,
            'lastPage'    => $totalPages,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // EXPORT PDF — STOCK & SALES
    // ─────────────────────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $filters = $request->only([
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
        ]);

        $rawReports = $this->buildFilteredQuery($request)
            ->orderBy('report_date', 'desc')
            ->orderBy('station_id')
            ->get();

        $officerMap       = $this->loadOfficerMap();
        $formattedReports = $rawReports->map(fn($r) => $this->formatSingleReport($r, $officerMap));
        $totalRow         = $this->buildTotalRow($formattedReports);

        $html = view('backend.admin.pages.reports.pdf_template', [
            'reports'  => $formattedReports,
            'totalRow' => $totalRow,
            'filters'  => $filters,
        ])->render();

        $mpdf = $this->makeMpdf('A4-L');
        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'S')),
            'stock-report-' . now()->format('Y-m-d') . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    // ─────────────────────────────────────────────────────────────
    // EXPORT PDF — DIFFERENCE
    // ─────────────────────────────────────────────────────────────

    public function exportDifferencePdf(Request $request)
    {
        $filters = $request->only([
            'from_date',
            'to_date',
            'division',
            'district',
            'thana_upazila',
            'company_id',
            'station_id',
            'tag_officer',
            'diff_status',
            'min_diff_l',
            'min_diff_pct',
        ]);

        $fuelTypes = ['octane', 'petrol', 'diesel', 'others'];

        $query = Fuelreport::query()->with(['fillingStation.company']);

        if (!empty($filters['from_date']) && empty($filters['to_date']))
            $query->whereDate('report_date', $filters['from_date']);
        elseif (!empty($filters['from_date']) && !empty($filters['to_date']))
            $query->whereBetween('report_date', [$filters['from_date'], $filters['to_date']]);

        if (!empty($filters['division']))
            $query->whereHas('fillingStation', fn($q) => $q->where('division', $filters['division']));
        if (!empty($filters['district']))
            $query->where('district', $filters['district']);
        if (!empty($filters['thana_upazila']))
            $query->where('thana_upazila', $filters['thana_upazila']);
        if (!empty($filters['company_id']))
            $query->whereHas('fillingStation', fn($q) => $q->where('company_id', $filters['company_id']));
        if (!empty($filters['station_id']))
            $query->where('station_id', $filters['station_id']);

        $officerMap    = AssignTagOfficer::with(['officer.profile'])
            ->where('status', 'active')->get()->keyBy('filling_station_id');
        $allRawReports = $query->orderBy('report_date', 'desc')->orderBy('station_id')->get();

        $rows = $allRawReports->map(function ($report) use ($fuelTypes, $officerMap) {
            $stationId          = $report->station_id;
            $assignment         = $officerMap->get($stationId);
            $officerProfile     = $assignment?->officer?->profile;
            $tagOfficerName     = $officerProfile?->name ?? $assignment?->officer?->name ?? '—';
            $officerDesignation = $officerProfile?->designation ?? '—';
            $officerPhone       = $officerProfile?->phone ?? $assignment?->officer?->phone ?? '—';

            $fuelBreakdown = [];
            foreach ($fuelTypes as $fuel) {
                $totalSupply       = (float) ($report->{"{$fuel}_supply"} ?? 0);
                $totalReceived     = (float) ($report->{"{$fuel}_received"} ?? 0);
                $differenceL       = $totalSupply - $totalReceived;
                $differencePercent = $totalSupply > 0 ? round(($differenceL / $totalSupply) * 100, 2) : 0;
                $diffStatus        = match (true) {
                    abs($differencePercent) >= 5 => 'High',
                    abs($differencePercent) >= 1 => 'Low',
                    default                      => 'Normal',
                };
                $fuelBreakdown[] = [
                    'fuelType'          => ucfirst($fuel),
                    'differenceL'       => number_format($differenceL, 0),
                    'differencePercent' => $differencePercent,
                    'diffStatus'        => $diffStatus,
                ];
            }

            return [
                'stationName'        => $report->station_name ?? $report->fillingStation?->station_name ?? '—',
                'companyName'        => $report->fillingStation?->company?->code ?? '—',
                'tagOfficerName'     => $tagOfficerName,
                'officerDesignation' => $officerDesignation,
                'officerPhone'       => $officerPhone,
                'district'           => $report->district ?? '—',
                'thanaUpazila'       => $report->thana_upazila ?? '—',
                'dateFormatted'      => \Carbon\Carbon::parse($report->report_date)->format('d M Y'),
                'fuelBreakdown'      => $fuelBreakdown,
            ];
        });

        if (!empty($filters['min_diff_l'])) {
            $minL = (float) $filters['min_diff_l'];
            $rows = $rows->filter(
                fn($row) => collect($row['fuelBreakdown'])->contains(
                    fn($f) => abs((float) str_replace(',', '', $f['differenceL'])) >= $minL
                )
            )->values();
        }
        if (!empty($filters['min_diff_pct'])) {
            $minPct = (float) $filters['min_diff_pct'];
            $rows   = $rows->filter(
                fn($row) => collect($row['fuelBreakdown'])->contains(
                    fn($f) => abs($f['differencePercent']) >= $minPct
                )
            )->values();
        }
        if (!empty($filters['diff_status'])) {
            $target = ucfirst($filters['diff_status']);
            $rows   = $rows->filter(
                fn($row) => collect($row['fuelBreakdown'])->contains(fn($f) => $f['diffStatus'] === $target)
            )->values();
        }

        $html = view('backend.admin.pages.reports.pdf_difference', [
            'rows'    => $rows,
            'filters' => $filters,
        ])->render();

        $mpdf = $this->makeMpdf('A4-L');
        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'S')),
            'difference-report-' . now()->format('Y-m-d') . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    // ─────────────────────────────────────────────────────────────
    // EXPORT PDF — MISSING
    // ─────────────────────────────────────────────────────────────

    public function exportMissingPdf(Request $request)
    {
        $filters = $request->only([
            'from_date',
            'to_date',
            'division',
            'district',
            'thana_upazila',
            'company_id',
            'depot_id',
            'station_id',
        ]);

        $assignmentsQuery = AssignTagOfficer::with([
            'officer.profile',
            'fillingStation.company',
            'fillingStation.depot',
        ])->where('status', 'active');

        if (!empty($filters['division']))
            $assignmentsQuery->whereHas('fillingStation', fn($q) => $q->where('division', $filters['division']));
        if (!empty($filters['district']))
            $assignmentsQuery->whereHas('fillingStation', fn($q) => $q->where('district', $filters['district']));
        if (!empty($filters['thana_upazila']))
            $assignmentsQuery->whereHas('fillingStation', fn($q) => $q->where('upazila', $filters['thana_upazila']));
        if (!empty($filters['company_id']))
            $assignmentsQuery->whereHas('fillingStation', fn($q) => $q->where('company_id', $filters['company_id']));
        if (!empty($filters['depot_id']))
            $assignmentsQuery->whereHas('fillingStation', fn($q) => $q->where('depot_id', $filters['depot_id']));
        if (!empty($filters['station_id']))
            $assignmentsQuery->where('filling_station_id', $filters['station_id']);

        $allAssignments = $assignmentsQuery->get();

        if (!empty($filters['from_date']) && empty($filters['to_date'])) {
            $fromDate = \Carbon\Carbon::parse($filters['from_date'])->startOfDay();
            $toDate   = \Carbon\Carbon::parse($filters['from_date'])->endOfDay();
        } elseif (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $fromDate = \Carbon\Carbon::parse($filters['from_date'])->startOfDay();
            $toDate   = \Carbon\Carbon::parse($filters['to_date'])->endOfDay();
        } else {
            $fromDate = now()->startOfDay();
            $toDate   = now()->endOfDay();
        }

        $reportedStationIds = Fuelreport::whereBetween('report_date', [$fromDate, $toDate])
            ->whereNotNull('station_id')
            ->pluck('station_id')->unique()->toArray();

        $rows = $allAssignments
            ->filter(fn($a) => !in_array($a->filling_station_id, $reportedStationIds))
            ->map(function ($assignment) use ($fromDate) {
                $officer = $assignment->officer;
                $profile = $officer?->profile;
                $station = $assignment->fillingStation;
                return [
                    'missingDate'  => $fromDate->format('d M Y'),
                    'officerName'  => $profile?->name ?? $officer?->name ?? '—',
                    'officerPhone' => $profile?->phone ?? $officer?->phone ?? '—',
                    'division'     => $station?->division ?? '—',
                    'district'     => $station?->district ?? '—',
                    'thanaUpazila' => $station?->upazila ?? '—',
                    'stationName'  => $station?->station_name ?? '—',
                    'companyName'  => $station?->company?->code ?? '—',
                    'depotName'    => $station?->depot?->depot_name ?? '—',
                ];
            })->values();

        $html = view('backend.admin.pages.reports.pdf_missing', [
            'rows'     => $rows,
            'filters'  => $filters,
            'fromDate' => $fromDate->format('d M Y'),
            'toDate'   => $toDate->format('d M Y'),
        ])->render();

        $mpdf = $this->makeMpdf('A4-L');
        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'S')),
            'missing-report-' . now()->format('Y-m-d') . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    // ─────────────────────────────────────────────────────────────
    // EXPORT PDF — SUBMITTED
    // ─────────────────────────────────────────────────────────────

    public function exportSubmittedPdf(Request $request)
    {
        $filters = $request->only([
            'from_date',
            'to_date',
            'division',
            'district',
            'thana_upazila',
            'company_id',
            'depot_id',
            'station_id',
        ]);

        $fuelTypes = ['octane', 'petrol', 'diesel', 'others'];

        $query = Fuelreport::query()->with([
            'fillingStation.company',
            'fillingStation.depot',
        ]);

        if (!empty($filters['from_date']) && empty($filters['to_date']))
            $query->whereDate('report_date', $filters['from_date']);
        elseif (!empty($filters['from_date']) && !empty($filters['to_date']))
            $query->whereBetween('report_date', [$filters['from_date'], $filters['to_date']]);

        if (!empty($filters['division']))
            $query->whereHas('fillingStation', fn($q) => $q->where('division', $filters['division']));
        if (!empty($filters['district']))
            $query->where('district', $filters['district']);
        if (!empty($filters['thana_upazila']))
            $query->where('thana_upazila', $filters['thana_upazila']);
        if (!empty($filters['company_id']))
            $query->whereHas('fillingStation', fn($q) => $q->where('company_id', $filters['company_id']));
        if (!empty($filters['depot_id']))
            $query->whereHas('fillingStation', fn($q) => $q->where('depot_id', $filters['depot_id']));
        if (!empty($filters['station_id']))
            $query->where('station_id', $filters['station_id']);

        $allReports = $query->orderBy('report_date', 'desc')->get();

        $officerMap = AssignTagOfficer::with(['officer.profile'])
            ->where('status', 'active')->get()->keyBy('filling_station_id');

        $rows = $allReports->map(function ($report) use ($fuelTypes, $officerMap) {
            $assignment     = $officerMap->get($report->station_id);
            $officerProfile = $assignment?->officer?->profile;

            $fuelBreakdown = collect($fuelTypes)->map(fn($fuel) => [
                'fuelType'     => ucfirst($fuel),
                'closingStock' => number_format((float) ($report->{"{$fuel}_closing_stock"} ?? 0), 0),
            ])->toArray();

            return [
                'submitDate'    => \Carbon\Carbon::parse($report->report_date)->format('d M Y'),
                'officerName'   => $officerProfile?->name ?? $assignment?->officer?->name ?? '—',
                'officerPhone'  => $officerProfile?->phone ?? $assignment?->officer?->phone ?? '—',
                'division'      => $report->fillingStation?->division ?? '—',
                'district'      => $report->district ?? '—',
                'thanaUpazila'  => $report->thana_upazila ?? '—',
                'stationName'   => $report->station_name ?? $report->fillingStation?->station_name ?? '—',
                'companyName'   => $report->fillingStation?->company?->code ?? '—',
                'depotName'     => $report->depot_name ?? $report->fillingStation?->depot?->depot_name ?? '—',
                'fuelBreakdown' => $fuelBreakdown,
            ];
        });

        $html = view('backend.admin.pages.reports.pdf_submitted', [
            'rows'    => $rows,
            'filters' => $filters,
        ])->render();

        $mpdf = $this->makeMpdf('A4-L');
        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print($mpdf->Output('', 'S')),
            'submitted-report-' . now()->format('Y-m-d') . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
