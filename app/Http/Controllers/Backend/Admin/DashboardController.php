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
        // TODAY'S STOCK — সব station এর latest closing stock
        // =============================================
        $currentStock = DB::table('fuelreports as f')
            ->joinSub(
                DB::table('fuelreports')
                    ->selectRaw('station_id, MAX(report_date) as latest_date')
                    ->groupBy('station_id'),
                'latest',
                fn($join) => $join
                    ->on('f.station_id', '=', 'latest.station_id')
                    ->on('f.report_date', '=', 'latest.latest_date')
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

        $todayOctaneStock = (float) $currentStock->octane;
        $todayPetrolStock = (float) $currentStock->petrol;
        $todayDieselStock = (float) $currentStock->diesel;
        $todayOthersStock = (float) $currentStock->others;

        // =============================================
        // TODAY'S SALES
        // =============================================
        $todayOctaneSold = (float) $todayReports->sum('octane_sales');
        $todayPetrolSold = (float) $todayReports->sum('petrol_sales');
        $todayDieselSold = (float) $todayReports->sum('diesel_sales');
        $todayOthersSold = (float) $todayReports->sum('others_sales');

        // =============================================
        // TODAY'S SUPPLY (Depot Release)
        // =============================================
        $todayPetrolSupply = (float) $todayReports->sum('petrol_supply');
        $todayDieselSupply = (float) $todayReports->sum('diesel_supply');
        $todayOctaneSupply = (float) $todayReports->sum('octane_supply');
        $todayOthersSupply = (float) $todayReports->sum('others_supply');

        // =============================================
        // TODAY'S RECEIVED (Filling Station Received)
        // =============================================
        $todayPetrolReceived = (float) $todayReports->sum('petrol_received');
        $todayDieselReceived = (float) $todayReports->sum('diesel_received');
        $todayOctaneReceived = (float) $todayReports->sum('octane_received');
        $todayOthersReceived = (float) $todayReports->sum('others_received');

        // =============================================
        // TODAY'S DIFFERENCE (L) — runtime calculate
        // Formula: Supply − Received
        // =============================================
        $todayPetrolDiff = $todayPetrolSupply - $todayPetrolReceived;
        $todayDieselDiff = $todayDieselSupply - $todayDieselReceived;
        $todayOctaneDiff = $todayOctaneSupply - $todayOctaneReceived;
        $todayOthersDiff = $todayOthersSupply - $todayOthersReceived;

        // =============================================
        // TODAY'S DIFFERENCE (%) — runtime calculate
        // Formula: (Supply − Received) / Supply × 100
        // =============================================
        $todayPetrolDiffPct = $todayPetrolSupply > 0
            ? round(($todayPetrolDiff / $todayPetrolSupply) * 100, 2) : 0;

        $todayDieselDiffPct = $todayDieselSupply > 0
            ? round(($todayDieselDiff / $todayDieselSupply) * 100, 2) : 0;

        $todayOctaneDiffPct = $todayOctaneSupply > 0
            ? round(($todayOctaneDiff / $todayOctaneSupply) * 100, 2) : 0;

        $todayOthersDiffPct = $todayOthersSupply > 0
            ? round(($todayOthersDiff / $todayOthersSupply) * 100, 2) : 0;
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
    (ABS(petrol_difference)  / NULLIF(petrol_supply, 0))  * 100 > 10
    OR (ABS(diesel_difference)  / NULLIF(diesel_supply, 0))  * 100 > 10
    OR (ABS(octane_difference)  / NULLIF(octane_supply, 0))  * 100 > 10
    OR (ABS(others_difference)  / NULLIF(others_supply, 0))  * 100 > 10
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

        $highDifferenceReports = Fuelreport::query()
            ->select('*')
            ->selectRaw("
        (ABS(petrol_difference)
        + ABS(diesel_difference)
        + ABS(octane_difference)
        + ABS(others_difference)) as total_difference
    ")
            ->whereDate('report_date', $today)
            ->whereRaw("
        (ABS(petrol_difference)
        + ABS(diesel_difference)
        + ABS(octane_difference)
        + ABS(others_difference)) > 0
    ")
            ->with(['tagOfficer.profile', 'fillingStation.company'])
            ->orderByDesc('total_difference')
            ->limit(20)
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
        Fuelreport::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Report deleted successfully.');
    }
}
