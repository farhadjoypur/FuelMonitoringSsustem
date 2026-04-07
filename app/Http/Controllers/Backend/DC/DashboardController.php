<?php

namespace App\Http\Controllers\Backend\Dc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignTagOfficer;
use App\Models\Depot;
use App\Models\FillingStation;
use App\Models\Fuelreport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dc = Auth::user();
        $dcId = $dc->id;
        
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // =============================================
        // DC এর assigned divisions/districts থেকে data filter
        // (assuming DC profile has division/district fields)
        // =============================================
        $dcDivision = $dc->profile?->division;
        $dcDistrict = $dc->profile?->district;

        // =============================================
        // DC এর jurisdiction এর সব stations
        // =============================================
        $stationsQuery = FillingStation::query();
        
        if ($dcDivision) {
            $stationsQuery->where('division', $dcDivision);
        }
        if ($dcDistrict) {
            $stationsQuery->where('district', $dcDistrict);
        }
        
        $jurisdictionStations = $stationsQuery->pluck('station_name');

        // =============================================
        // TODAY'S REPORTS (DC এর jurisdiction থেকে)
        // =============================================
        $todayReports = Fuelreport::whereDate('report_date', $today)
            ->whereIn('station_name', $jurisdictionStations)
            ->get();

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
        // TODAY'S DIFFERENCE (supply - received)
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
        // TODAY'S DIFFERENCE PERCENTAGE (%)
        // =============================================
        $todayPetrolDiffPct = $todayPetrolReceived > 0
            ? round(($todayPetrolDiff / $todayPetrolReceived) * 100, 1) : 0;
        $todayDieselDiffPct = $todayDieselReceived > 0
            ? round(($todayDieselDiff / $todayDieselReceived) * 100, 1) : 0;
        $todayOctaneDiffPct = $todayOctaneReceived > 0
            ? round(($todayOctaneDiff / $todayOctaneReceived) * 100, 1) : 0;

        // =============================================
        // SUMMARY CARDS (DC jurisdiction)
        // =============================================
        $totalStationsQuery = FillingStation::query();
        if ($dcDivision) {
            $totalStationsQuery->where('division', $dcDivision);
        }
        if ($dcDistrict) {
            $totalStationsQuery->where('district', $dcDistrict);
        }
        $totalStations = $totalStationsQuery->count();

        // Total Tag Officers under this DC
        $totalOfficers = AssignTagOfficer::whereHas('fillingStation', function($q) use ($dcDivision, $dcDistrict) {
            if ($dcDivision) {
                $q->where('division', $dcDivision);
            }
            if ($dcDistrict) {
                $q->where('district', $dcDistrict);
            }
        })
        ->where('status', 'active')
        ->distinct('officer_id')
        ->count('officer_id');

        // =============================================
        // RECENT ACTIVITIES (DC jurisdiction)
        // =============================================
        $recentActivities = collect();

        /* Zero Stock Alert */
        Fuelreport::where(function ($q) {
            $q->where('petrol_closing_stock', 0)
                ->orWhere('diesel_closing_stock', 0)
                ->orWhere('octane_closing_stock', 0);
        })
            ->whereIn('station_name', $jurisdictionStations)
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

        /* Low Stock Alert */
        Fuelreport::where(function ($q) {
            $q->where('petrol_closing_stock', '<', 100)
                ->orWhere('diesel_closing_stock', '<', 100)
                ->orWhere('octane_closing_stock', '<', 100);
        })
            ->whereIn('station_name', $jurisdictionStations)
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

        /* Difference (L) Alert */
        Fuelreport::whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) > 50')
            ->whereIn('station_name', $jurisdictionStations)
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

        /* Difference (%) Alert */
        Fuelreport::whereRaw('
            (ABS(petrol_difference) / NULLIF(petrol_closing_stock,1)) * 100 > 10
            OR (ABS(diesel_difference) / NULLIF(diesel_closing_stock,1)) * 100 > 10
            OR (ABS(octane_difference) / NULLIF(octane_closing_stock,1)) * 100 > 10
        ')
            ->whereIn('station_name', $jurisdictionStations)
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

        /* Final Sort */
        $recentActivities = $recentActivities
            ->sortByDesc('time')
            ->take(4)
            ->values();

        return view('backend.dc.pages.dashboard.index', compact(
            // today stock
            'todayPetrolStock',
            'todayDieselStock',
            'todayOctaneStock',

            // today received
            'todayPetrolReceived',
            'todayDieselReceived',
            'todayOctaneReceived',

            // today difference L
            'todayPetrolDiff',
            'todayDieselDiff',
            'todayOctaneDiff',

            // today difference %
            'todayPetrolDiffPct',
            'todayDieselDiffPct',
            'todayOctaneDiffPct',

            // today sold
            'todayPetrolSold',
            'todayDieselSold',
            'todayOctaneSold',

            // summary
            'totalStations',
            'totalOfficers',

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