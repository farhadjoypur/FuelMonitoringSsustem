<?php

namespace App\Http\Controllers\Backend\UNO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignTagOfficer;
use App\Models\Company;
use App\Models\Depot;
use App\Models\FillingStation;
use App\Models\Fuelreport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // UNO OFFICER এর নিজের UPAZILA/DISTRICT বের করা
    // ─────────────────────────────────────────────────────────────

    private function getUnoJurisdiction(): array
    {
        $uno = Auth::user();
        return [
            'upazila' => $uno->profile?->upazila,
            'district' => $uno->profile?->district,
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────

    public function index()
    {
        $jurisdiction = $this->getUnoJurisdiction();
        $unoUpazila = $jurisdiction['upazila'];
        $unoDistrict = $jurisdiction['district'];

        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

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
        // TODAY'S REPORTS — শুধু UNO এর jurisdiction
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
        // TODAY'S DIFFERENCE
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
        // SUMMARY CARDS — শুধু UNO এর jurisdiction এর data
        // =============================================
        $totalDepots = Depot::whereHas('fillingStations', function($q) use ($unoUpazila, $unoDistrict) {
            if ($unoUpazila) {
                $q->where('upazila', $unoUpazila);
            }
            if ($unoDistrict) {
                $q->where('district', $unoDistrict);
            }
        })->count();

        $totalStations = count($stationIds);

        // UNO এর jurisdiction এ assigned active tag officers
        $totalOfficers = AssignTagOfficer::where('status', 'active')
            ->whereIn('filling_station_id', $stationIds)
            ->distinct('officer_id')
            ->count('officer_id');

        $newDepots = Depot::where('created_at', '>=', $thisMonth)
            ->whereHas('fillingStations', function($q) use ($unoUpazila, $unoDistrict) {
                if ($unoUpazila) {
                    $q->where('upazila', $unoUpazila);
                }
                if ($unoDistrict) {
                    $q->where('district', $unoDistrict);
                }
            })
            ->count();

        $newStations = FillingStation::where('created_at', '>=', $thisMonth)
            ->when($unoUpazila, fn($q) => $q->where('upazila', $unoUpazila))
            ->when($unoDistrict, fn($q) => $q->where('district', $unoDistrict))
            ->count();

        $newOfficers = AssignTagOfficer::where('status', 'active')
            ->whereIn('filling_station_id', $stationIds)
            ->where('created_at', '>=', $thisMonth)
            ->distinct('officer_id')
            ->count('officer_id');

        // =============================================
        // RECENT ACTIVITIES — শুধু UNO এর jurisdiction
        // =============================================
        $recentActivities = collect();

        /* 1. ZERO STOCK ALERT */
        Fuelreport::whereIn('station_name', $stationNames)
            ->where(function ($q) {
                $q->where('petrol_closing_stock', 0)
                    ->orWhere('diesel_closing_stock', 0)
                    ->orWhere('octane_closing_stock', 0);
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

        /* 2. LOW STOCK ALERT */
        Fuelreport::whereIn('station_name', $stationNames)
            ->where(function ($q) {
                $q->where('petrol_closing_stock', '<', 100)
                    ->orWhere('diesel_closing_stock', '<', 100)
                    ->orWhere('octane_closing_stock', '<', 100);
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

        /* 3. DIFFERENCE (L) ALERT */
        Fuelreport::whereIn('station_name', $stationNames)
            ->whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) > 50')
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

        /* 4. DIFFERENCE (%) ALERT */
        Fuelreport::whereIn('station_name', $stationNames)
            ->whereRaw('
                (ABS(petrol_difference) / NULLIF(petrol_closing_stock,1)) * 100 > 10
                OR (ABS(diesel_difference) / NULLIF(diesel_closing_stock,1)) * 100 > 10
                OR (ABS(octane_difference) / NULLIF(octane_closing_stock,1)) * 100 > 10
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

        $recentActivities = $recentActivities
            ->sortByDesc('time')
            ->take(4)
            ->values();

        // =============================================
        // HIGH DIFFERENCE REPORTS — শুধু UNO এর jurisdiction
        // =============================================
        $highDifferenceReports = Fuelreport::whereDate('report_date', $today)
            ->whereIn('station_name', $stationNames)
            ->whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) + ABS(others_difference) > 0')
            ->orderByRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) + ABS(others_difference) DESC')
            ->with(['tagOfficer.profile', 'fillingStation.company'])
            ->take(20)
            ->get();

        return view('backend.uno.pages.dashboard.index', compact(
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
            'totalDepots',
            'totalStations',
            'totalOfficers',
            'newDepots',
            'newStations',
            'newOfficers',

            // activities
            'recentActivities',

            'highDifferenceReports',

            // blade এ UNO এর jurisdiction দেখানোর জন্য
            'unoUpazila',
            'unoDistrict',
        ));
    }

    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}