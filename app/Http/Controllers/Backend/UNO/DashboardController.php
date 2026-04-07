<?php

namespace App\Http\Controllers\Backend\UNO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignTagOfficer;
use App\Models\FillingStation;
use App\Models\Fuelreport;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $uno   = Auth::user();
        $today = Carbon::today();

        // =============================================
        // UNO-এর jurisdiction — profile থেকে upazila/district
        // =============================================
        $unoUpazila = $uno->profile?->upazila;   // adjust field name if different
        $unoDistrict = $uno->profile?->district;  // adjust field name if different

        // =============================================
        // UNO-এর jurisdiction এর সব stations
        // =============================================
        $stationsQuery = FillingStation::query();

        if ($unoUpazila) {
            $stationsQuery->where('upazila', $unoUpazila);
        }
        if ($unoDistrict) {
            $stationsQuery->where('district', $unoDistrict);
        }

        $stationIds   = $stationsQuery->pluck('id')->toArray();
        $stationNames = $stationsQuery->pluck('station_name')->toArray();

        // =============================================
        // TODAY'S REPORTS
        // =============================================
        $todayReports = Fuelreport::whereDate('report_date', $today)
            ->whereIn('station_name', $stationNames)
            ->get();

        // =============================================
        // TODAY'S CLOSING STOCK (per fuel type)
        // =============================================
        $todayPetrolStock = $todayReports->sum('petrol_closing_stock');
        $todayDieselStock = $todayReports->sum('diesel_closing_stock');
        $todayOctaneStock = $todayReports->sum('octane_closing_stock');

        // =============================================
        // TODAY'S RECEIVED
        // =============================================
        $todayPetrolReceived = $todayReports->sum('petrol_received');
        $todayDieselReceived = $todayReports->sum('diesel_received');
        $todayOctaneReceived = $todayReports->sum('octane_received');

        // =============================================
        // TODAY'S DIFFERENCE (L)
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
        // SUMMARY CARDS
        // =============================================
        $totalStations = count($stationIds);

        $totalOfficers = AssignTagOfficer::whereIn('filling_station_id', $stationIds)
            ->where('status', 'active')
            ->distinct('officer_id')
            ->count('officer_id');

        // =============================================
        // RECENT ACTIVITIES
        // =============================================
        $recentActivities = collect();

        /* Zero Stock Alert */
        Fuelreport::where(function ($q) {
            $q->where('petrol_closing_stock', 0)
                ->orWhere('diesel_closing_stock', 0)
                ->orWhere('octane_closing_stock', 0);
        })
            ->whereIn('station_name', $stationNames)
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
            ->whereIn('station_name', $stationNames)
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
        Fuelreport::whereRaw(
            'ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) > 50'
        )
            ->whereIn('station_name', $stationNames)
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
            ->whereIn('station_name', $stationNames)
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

        return view('backend.uno.pages.dashboard.index', compact(
            'todayPetrolStock',
            'todayDieselStock',
            'todayOctaneStock',
            'todayPetrolReceived',
            'todayDieselReceived',
            'todayOctaneReceived',
            'todayPetrolDiff',
            'todayDieselDiff',
            'todayOctaneDiff',
            'todayPetrolDiffPct',
            'todayDieselDiffPct',
            'todayOctaneDiffPct',
            'todayPetrolSold',
            'todayDieselSold',
            'todayOctaneSold',
            'totalStations',
            'totalOfficers',
            'recentActivities'
        ));
    }

    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}