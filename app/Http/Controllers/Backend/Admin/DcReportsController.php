<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignTagOfficer;
use App\Models\Depot;
use App\Models\FillingStation;
use App\Models\Fuelreport;
use Illuminate\Support\Facades\DB;

class DcReportsController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    //  DC SCOPE HELPER — এই DC-এর আন্ডারের officer & station ids
    // ═══════════════════════════════════════════════════════════
    private function getDcScope(): array
    {
        $dc = auth()->user();

        $assignments = AssignTagOfficer::where('dc_id', $dc->id)->get();

        $officerIds = $assignments->pluck('officer_id')->unique()->filter()->values()->toArray();
        $stationIds = $assignments->pluck('filling_station_id')->unique()->filter()->values()->toArray();

        return compact('officerIds', 'stationIds');
    }

    // ═══════════════════════════════════════════════════════════
    //  MAIN INDEX
    // ═══════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        ['officerIds' => $officerIds, 'stationIds' => $stationIds] = $this->getDcScope();

        // ── Filter Dropdowns (শুধু DC-এর stations থেকে) ──────────────────────
        $districts = FillingStation::whereIn('id', $stationIds)
            ->whereNotNull('district')
            ->distinct()->pluck('district')->sort()->values();

        $divisions = FillingStation::whereIn('id', $stationIds)
            ->whereNotNull('division')
            ->distinct()->pluck('division')->sort()->values();

        $depots = Depot::whereIn(
            'id',
            FillingStation::whereIn('id', $stationIds)
                ->whereNotNull('linked_depot')
                ->pluck('linked_depot')
        )->orderBy('depot_name')->get(['id', 'depot_name']);

        $stations = FillingStation::whereIn('id', $stationIds)
            ->orderBy('station_name')->get(['id', 'station_name']);

        $officers = AssignTagOfficer::where('dc_id', auth()->id())
            ->with('officer')
            ->distinct('officer_id')
            ->get()
            ->pluck('officer')
            ->filter()
            ->unique('id')
            ->values();

        // ── Base Fuelreport Query (DC scope: tag_officer_id OR station_id) ───
        $baseQuery = function () use ($request, $officerIds, $stationIds) {
            return Fuelreport::query()
                ->where(function ($q) use ($officerIds, $stationIds) {
                    $q->whereIn('tag_officer_id', $officerIds)
                        ->orWhereIn('station_id', $stationIds);
                })
                ->when($request->from_date,    fn($q) => $q->whereDate('report_date', '>=', $request->from_date))
                ->when($request->to_date,      fn($q) => $q->whereDate('report_date', '<=', $request->to_date))
                ->when($request->district,     fn($q) => $q->where('district', $request->district))
                ->when($request->station_name, fn($q) => $q->where('station_name', $request->station_name))
                ->when($request->officer_id,   fn($q) => $q->where('tag_officer_id', $request->officer_id));
        };

        // ── TAB 1: STOCK REPORT ───────────────────────────────────────────────
        $stockReports = $baseQuery()
            ->when($request->depot_id, function ($q) use ($request) {
                $depotName = Depot::find($request->depot_id)?->depot_name;
                return $q->where('depot_name', $depotName);
            })
            ->when($request->stock_status, function ($q, $status) {
                if ($status === 'available') {
                    $q->whereRaw('(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) >= 2000');
                } elseif ($status === 'low') {
                    $q->whereRaw('(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) > 0')
                        ->whereRaw('(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) < 2000');
                } elseif ($status === 'zero') {
                    $q->whereRaw('(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) <= 0');
                }
            })
            ->orderByDesc('report_date')
            ->get();

        // ── TAB 2: SALES REPORT ───────────────────────────────────────────────
        $salesReports = $baseQuery()
            ->when($request->fuel_type, fn($q, $type) => $q->where("{$type}_sales", '>', 0))
            ->orderByDesc('report_date')
            ->get();

        // ── TAB 3: TAG OFFICER REPORT (DC-এর officers) ───────────────────────
        $officerQuery = AssignTagOfficer::where('dc_id', auth()->id())
            ->with([
                'officer',
                'fillingStation:id,station_name,district,division',
            ]);

        if ($request->district) {
            $officerQuery->whereHas('fillingStation', fn($q) => $q->where('district', $request->district));
        }
        if ($request->assign_status) {
            $officerQuery->where('status', $request->assign_status);
        }
        if ($request->officer_id) {
            $officerQuery->where('officer_id', $request->officer_id);
        }

        $officerReports = $officerQuery->latest()->get();

        // ── TAB 3 EXTRA: প্রতিটি Officer কতটা report submit করেছে ─────────────
        $officerSubmitCounts = Fuelreport::whereIn('tag_officer_id', $officerIds)
            ->when($request->from_date, fn($q) => $q->whereDate('report_date', '>=', $request->from_date))
            ->when($request->to_date,   fn($q) => $q->whereDate('report_date', '<=', $request->to_date))
            ->select('tag_officer_id', DB::raw('COUNT(*) as report_count'))
            ->groupBy('tag_officer_id')
            ->pluck('report_count', 'tag_officer_id');

        // ── TAB 4: DIFFERENCE (%) REPORT ─────────────────────────────────────
        $minDiff     = $request->get('min_diff', 0);
        $diffReports = $baseQuery()
            ->when(
                $minDiff > 0,
                fn($q) => $q->whereRaw(
                    'ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) >= ?',
                    [$minDiff]
                )
            )
            ->orderByDesc('report_date')
            ->get();

        // ── TAB 5: DUE SALES — opening stock এ আগের দিনের closing মিলছে না ──
        // যে reports এ petrol_prev_stock != আগের দিনের petrol_closing_stock সেগুলো
        // For now: যেখানে sales > received (negative closing indicator)
        $dueSalesReports = $baseQuery()
            ->where(function ($q) {
                $q->whereRaw('petrol_sales > petrol_received + petrol_prev_stock')
                    ->orWhereRaw('diesel_sales > diesel_received + diesel_prev_stock')
                    ->orWhereRaw('octane_sales > octane_received + octane_prev_stock');
            })
            ->orderByDesc('report_date')
            ->get();

        return view('backend.dc.pages.reports.index', compact(
            // dropdowns
            'districts',
            'divisions',
            'depots',
            'stations',
            'officers',
            // tab data
            'stockReports',
            'salesReports',
            'officerReports',
            'officerSubmitCounts',
            'diffReports',
            'dueSalesReports',
        ));
    }
}