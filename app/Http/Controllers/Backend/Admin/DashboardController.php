<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignTagOfficer;
use App\Models\Company;
use App\Models\Depot;
use App\Models\FillingStation;
use App\Models\Fuelreport;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $yesterday = Carbon::yesterday();
        // =============================================
        // TODAY'S REPORTS
        // =============================================
        $todayReports = Fuelreport::whereDate('report_date', $today)->get();

        // =============================================
        // TODAY'S STOCK
        // Last Closing Stock − Today's Total Sales
        // =============================================

        $currentStock = DB::table('fuelreports as f')
            ->joinSub(
                DB::table('fuelreports')
                    ->selectRaw('station_id, MAX(report_date) as latest_date')
                    ->groupBy('station_id'),
                'latest',
                function ($join) {
                    $join->on('f.station_id', '=', 'latest.station_id')
                        ->on('f.report_date', '=', 'latest.latest_date');
                }
            )
            ->selectRaw('
        COUNT(DISTINCT f.station_id)             as total_stations,
        COALESCE(SUM(f.octane_closing_stock), 0) as octane,
        COALESCE(SUM(f.petrol_closing_stock), 0) as petrol,
        COALESCE(SUM(f.diesel_closing_stock), 0) as diesel,
        COALESCE(SUM(f.others_closing_stock), 0) as others,
        COALESCE(SUM(
            f.octane_closing_stock +
            f.petrol_closing_stock +
            f.diesel_closing_stock +
            f.others_closing_stock
        ), 0) as grand_total
    ')
            ->first();

        $todayOctaneStock = $currentStock->octane;
        $todayPetrolStock = $currentStock->petrol;
        $todayDieselStock = $currentStock->diesel;
        $todayOthersStock = $currentStock->others;

        // আজকের sales
        $todayOctaneSold = $todayReports->sum('octane_sales');
        $todayPetrolSold = $todayReports->sum('petrol_sales');
        $todayDieselSold = $todayReports->sum('diesel_sales');
        $todayOthersSold = $todayReports->sum('others_sales');

        // যাতে blade এ কিছু বদলাতে না হয়
        $todayOctaneStock = $currentStock->octane;
        $todayPetrolStock = $currentStock->petrol;
        $todayDieselStock = $currentStock->diesel;
        $todayOthersStock = $currentStock->others;

        // =============================================
        // TODAY'S RECEIVED
        // =============================================
        $todayPetrolReceived = $todayReports->sum('petrol_received');
        $todayDieselReceived = $todayReports->sum('diesel_received');
        $todayOctaneReceived = $todayReports->sum('octane_received');
        $todayOthersReceived = $todayReports->sum('others_received');

        // =============================================
        // TODAY'S DIFFERENCE
        // =============================================
        $todayPetrolDiff = $todayReports->sum('petrol_difference');
        $todayDieselDiff = $todayReports->sum('diesel_difference');
        $todayOctaneDiff = $todayReports->sum('octane_difference');
        $todayOthersDiff = $todayReports->sum('others_difference');

        // =============================================
        // TODAY'S DIFFERENCE PERCENTAGE (%)
        // =============================================
        $todayPetrolDiffPct = $todayPetrolReceived > 0
            ? round(($todayPetrolDiff / $todayPetrolReceived) * 100, 1) : 0;
        $todayDieselDiffPct = $todayDieselReceived > 0
            ? round(($todayDieselDiff / $todayDieselReceived) * 100, 1) : 0;
        $todayOctaneDiffPct = $todayOctaneReceived > 0
            ? round(($todayOctaneDiff / $todayOctaneReceived) * 100, 1) : 0;
        $todayOthersDiffPct = $todayOthersReceived > 0
            ? round(($todayOthersDiff / $todayOthersReceived) * 100, 1) : 0;

        // =============================================
        // SUMMARY CARDS
        // =============================================
        $totalDepots   = Depot::count();
        $totalStations = FillingStation::count();
        $totalOfficers = User::where('role', '2')->count();

        $newDepots   = Depot::where('created_at', '>=', $thisMonth)->count();
        $newStations = FillingStation::where('created_at', '>=', $thisMonth)->count();
        $newOfficers = User::where('created_at', '>=', $thisMonth)->count();

        // =============================================
        // DIVISION-WISE FUEL SALES TODAY — Bar Chart
        // fuelreports has a 'district' column but not 'division'
        // If FillingStation has division, join via station_name.
        // Otherwise group by district as fallback.
        // =============================================
        $divisionSalesToday = Fuelreport::whereDate('report_date', $today)
            ->select(
                'district as division',
                DB::raw('COUNT(DISTINCT station_name) as total_stations'),
                DB::raw('SUM(petrol_sales + diesel_sales + octane_sales) as total_fuel_liters')
            )
            ->whereNotNull('district')
            ->groupBy('district')
            ->orderByDesc('total_fuel_liters')
            ->get()
            ->map(fn($item) => [
                'division'          => $item->division,
                'total_stations'    => (int) $item->total_stations,
                'total_fuel_liters' => round($item->total_fuel_liters / 1000, 1), // convert to metric ton / ×1000L
            ]);

        $recentActivities = collect();

        /* =========================
   1. ZERO STOCK ALERT
========================= */
        Fuelreport::where(function ($q) {
            $q->where('petrol_closing_stock', 0)
                ->orWhere('diesel_closing_stock', 0)
                ->orWhere('octane_closing_stock', 0)
                ->orWhere('others_closing_stock', 0);
        })
            ->latest('report_date')
            ->take(3)
            ->get()
            ->each(function ($r) use (&$recentActivities) {
                $recentActivities->push([
                    'title' => 'Zero Stock Alert',
                    'sub'   => $r->district . ' — ' . $r->station_name,
                    'time'  => $r->updated_at,
                    'color' => 'red',
                    'icon'  => 'fa-battery-empty',
                ]);
            });

        /* =========================
   2. LOW STOCK ALERT
   (example: below 100L)
========================= */
        Fuelreport::where(function ($q) {
            $q->where('petrol_closing_stock', '<', 100)
                ->orWhere('diesel_closing_stock', '<', 100)
                ->orWhere('octane_closing_stock', '<', 100)
                ->orWhere('others_closing_stock', '<', 100);
        })
            ->latest('report_date')
            ->take(3)
            ->get()
            ->each(function ($r) use (&$recentActivities) {
                $recentActivities->push([
                    'title' => 'Low Stock Alert',
                    'sub'   => $r->district . ' — ' . $r->station_name,
                    'time'  => $r->updated_at,
                    'color' => 'yellow',
                    'icon'  => 'fa-triangle-exclamation',
                ]);
            });

        /* =========================
   3. DIFFERENCE (L) ALERT
========================= */
        Fuelreport::whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) > 50')
            ->latest('report_date')
            ->take(3)
            ->get()
            ->each(function ($r) use (&$recentActivities) {
                $recentActivities->push([
                    'title' => 'Difference (L) Alert',
                    'sub'   => $r->district . ' — ' . $r->station_name,
                    'time'  => $r->updated_at,
                    'color' => 'orange',
                    'icon'  => 'fa-scale-balanced',
                ]);
            });

        /* =========================
   4. DIFFERENCE (%) ALERT
========================= */
        Fuelreport::whereRaw('
    (ABS(petrol_difference) / NULLIF(petrol_closing_stock,1)) * 100 > 10
    OR (ABS(diesel_difference) / NULLIF(diesel_closing_stock,1)) * 100 > 10
    OR (ABS(octane_difference) / NULLIF(octane_closing_stock,1)) * 100 > 10
    OR (ABS(others_difference) / NULLIF(others_closing_stock,1)) * 100 > 10
')
            ->latest('report_date')
            ->take(3)
            ->get()
            ->each(function ($r) use (&$recentActivities) {
                $recentActivities->push([
                    'title' => 'Difference (%) Alert',
                    'sub'   => $r->district . ' — ' . $r->station_name,
                    'time'  => $r->updated_at,
                    'color' => 'blue',
                    'icon'  => 'fa-percent',
                ]);
            });

        /* =========================
   FINAL SORT (IMPORTANT)
========================= */
        $recentActivities = $recentActivities
            ->sortByDesc('time')
            ->take(4)
            ->values();

        $highDifferenceReports = Fuelreport::whereDate('report_date', $today)
            ->whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) + ABS(others_difference) > 0')
            ->orderByRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) + ABS(others_difference) DESC')
            ->with(['tagOfficer.profile', 'fillingStation.company'])
            ->take(20)
            ->get();

        return view('backend.admin.pages.dashboard.index', compact(
            // today stock
            'todayPetrolStock',
            'todayDieselStock',
            'todayOctaneStock',
            'todayOthersStock',

            // today received
            'todayPetrolReceived',
            'todayDieselReceived',
            'todayOctaneReceived',
            'todayOthersReceived',

            // today difference L
            'todayPetrolDiff',
            'todayDieselDiff',
            'todayOctaneDiff',
            'todayOthersDiff',


            // today difference %
            'todayPetrolDiffPct',
            'todayDieselDiffPct',
            'todayOctaneDiffPct',
            'todayOthersDiffPct',


            // today sold
            'todayPetrolSold',
            'todayDieselSold',
            'todayOctaneSold',
            'todayOthersSold',

            // summary
            'totalDepots',
            'totalStations',
            'totalOfficers',
            'newDepots',
            'newStations',
            'newOfficers',

            // chart
            'divisionSalesToday',

            // activities
            'recentActivities',

            'highDifferenceReports',
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
