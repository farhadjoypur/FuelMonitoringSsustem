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

        // =============================================
        // TODAY'S REPORTS
        // =============================================
        $todayReports = Fuelreport::whereDate('report_date', $today)->get();

        // =============================================
        // TODAY'S CLOSING STOCK (per fuel type)
        // =============================================
        $todayPetrolStock = $todayReports->sum('petrol_closing_stock');
        $todayDieselStock = $todayReports->sum('diesel_closing_stock');
        $todayOctaneStock = $todayReports->sum('octane_closing_stock');

        // =============================================
        // TODAY'S RECEIVED (needed for difference %)
        // =============================================
        $todayPetrolReceived = $todayReports->sum('petrol_received');
        $todayDieselReceived = $todayReports->sum('diesel_received');
        $todayOctaneReceived = $todayReports->sum('octane_received');

        // =============================================
        // TODAY'S DIFFERENCE (supply - received per migration)
        // =============================================
        $todayPetrolDiff = $todayReports->sum('petrol_difference');
        $todayDieselDiff = $todayReports->sum('diesel_difference');
        $todayOctaneDiff = $todayReports->sum('octane_difference');

        // =============================================
        // TODAY'S SALES (per fuel type)
        // =============================================
        $todayPetrolSold = $todayReports->sum('petrol_sales');
        $todayDieselSold = $todayReports->sum('diesel_sales');
        $todayOctaneSold = $todayReports->sum('octane_sales');

        // =============================================
        // SUMMARY CARDS
        // =============================================
        $totalDepots   = Depot::count();
        $totalStations = FillingStation::count();
        $totalOfficers = User::where('role', 'tag_officer')->count();

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

        // =============================================
        // RECENT ACTIVITIES
        // =============================================
        $recentActivities = collect();

        // নতুন depot
        Depot::latest()->take(3)->get()->each(function ($d) use (&$recentActivities) {
            $recentActivities->push([
                'title' => 'New Depot Added',
                'sub'   => $d->district . ' — ' . $d->depot_name,
                'time'  => $d->created_at,
                'color' => 'green',
                'icon'  => 'fa-circle-info',
            ]);
        });

        // নতুন officer assignment
        AssignTagOfficer::with(['fillingStation:id,station_name,district'])
            ->latest()->take(3)->get()
            ->each(function ($a) use (&$recentActivities) {
                $recentActivities->push([
                    'title' => 'Officer Assigned',
                    'sub'   => ($a->fillingStation->district ?? '') . ' — ' . ($a->fillingStation->station_name ?? ''),
                    'time'  => $a->created_at,
                    'color' => 'blue',
                    'icon'  => 'fa-circle-info',
                ]);
            });

        // Stock alert — difference > 50L
        Fuelreport::whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) > 50')
            ->latest('report_date')->take(2)->get()
            ->each(function ($r) use (&$recentActivities) {
                $recentActivities->push([
                    'title' => 'Stock Alert',
                    'sub'   => $r->district . ' — ' . $r->station_name,
                    'time'  => $r->updated_at,
                    'color' => 'yellow',
                    'icon'  => 'fa-circle-exclamation',
                ]);
            });

        // Operational loss — negative closing stock
        Fuelreport::where(function ($q) {
            $q->where('petrol_closing_stock', '<', 0)
                ->orWhere('diesel_closing_stock', '<', 0)
                ->orWhere('octane_closing_stock', '<', 0);
        })
            ->latest('report_date')->take(2)->get()
            ->each(function ($r) use (&$recentActivities) {
                $recentActivities->push([
                    'title' => 'Operational Loss Detected',
                    'sub'   => $r->district . ' — ' . $r->station_name,
                    'time'  => $r->updated_at,
                    'color' => 'red',
                    'icon'  => 'fa-circle-xmark',
                ]);
            });

        $recentActivities = $recentActivities
            ->sortByDesc('time')
            ->take(5)
            ->values();

        return view('backend.admin.pages.dashboard.index', compact(
            // today stock
            'todayPetrolStock',
            'todayDieselStock',
            'todayOctaneStock',

            // today received (for diff % calc in blade)
            'todayPetrolReceived',
            'todayDieselReceived',
            'todayOctaneReceived',

            // today difference
            'todayPetrolDiff',
            'todayDieselDiff',
            'todayOctaneDiff',

            // today sold
            'todayPetrolSold',
            'todayDieselSold',
            'todayOctaneSold',

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
            'recentActivities'
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
