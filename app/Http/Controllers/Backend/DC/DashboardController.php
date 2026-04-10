<?php

namespace App\Http\Controllers\Backend\DC;

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
    // DC OFFICER এর নিজের DISTRICT বের করা
    // ─────────────────────────────────────────────────────────────

    private function getDcDistrict(): ?string
    {
        return Auth::user()?->profile?->district;
    }

    // ─────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────

    public function index()
    {
        $dcDistrict = $this->getDcDistrict();

        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $yesterday = Carbon::yesterday();

        // =============================================
        // TODAY'S STOCK
        // Last closing stock − আজকের sales পর্যন্ত
        // =============================================

        // সর্বশেষ report date (আজকের আগে)
        $lastReportDate = Fuelreport::where('district', $dcDistrict)
            ->whereDate('report_date', '<', today())
            ->max('report_date');

        $lastAvailableReports = $lastReportDate
            ? Fuelreport::where('district', $dcDistrict)
            ->whereDate('report_date', $lastReportDate)
            ->get()
            : collect();

        // আজকের sales (যেসব station report submit করেছে)
        $todayReports = Fuelreport::whereDate('report_date', $today)
            ->where('district', $dcDistrict)
            ->get();

        // Last closing stock
        $lastOctaneClosing = $lastAvailableReports->sum('octane_closing_stock');
        $lastPetrolClosing = $lastAvailableReports->sum('petrol_closing_stock');
        $lastDieselClosing = $lastAvailableReports->sum('diesel_closing_stock');
        $lastOthersClosing = $lastAvailableReports->sum('others_closing_stock');

        // আজকের sales
        $todayOctaneSold = $todayReports->sum('octane_sales');
        $todayPetrolSold = $todayReports->sum('petrol_sales');
        $todayDieselSold = $todayReports->sum('diesel_sales');
        $todayOthersSold = $todayReports->sum('others_sales');

        // Today Stock = Last Closing − Today Sales
        $todayOctaneStock = $lastOctaneClosing - $todayOctaneSold;
        $todayPetrolStock = $lastPetrolClosing - $todayPetrolSold;
        $todayDieselStock = $lastDieselClosing - $todayDieselSold;
        $todayOthersStock = $lastOthersClosing - $todayOthersSold;

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
        // TODAY'S RECEIVED
        // =============================================
        $todayPetrolReceived = $todayReports->sum('petrol_received');
        $todayDieselReceived = $todayReports->sum('diesel_received');
        $todayOctaneReceived = $todayReports->sum('octane_received');
        // others received added if needed in future
        $todayOthersReceived = $todayReports->sum('others_received');

        // =============================================
        // TODAY'S DIFFERENCE
        // =============================================
        $todayPetrolDiff = $todayReports->sum('petrol_difference');
        $todayDieselDiff = $todayReports->sum('diesel_difference');
        $todayOctaneDiff = $todayReports->sum('octane_difference');
        // others difference added if needed in future
        $todayOthersDiff = $todayReports->sum('others_difference');

        // =============================================
        // TODAY'S SALES (per fuel type)
        // =============================================
        $todayPetrolSold = $todayReports->sum('petrol_sales');
        $todayDieselSold = $todayReports->sum('diesel_sales');
        $todayOctaneSold = $todayReports->sum('octane_sales');
        // others sold added if needed in future
        $todayOthersSold = $todayReports->sum('others_sales');

        // =============================================
        // TODAY'S DIFFERENCE PERCENTAGE (%)
        // =============================================
        $todayPetrolDiffPct = $todayPetrolReceived > 0
            ? round(($todayPetrolDiff / $todayPetrolReceived) * 100, 1) : 0;
        $todayDieselDiffPct = $todayDieselReceived > 0
            ? round(($todayDieselDiff / $todayDieselReceived) * 100, 1) : 0;
        $todayOctaneDiffPct = $todayOctaneReceived > 0
            ? round(($todayOctaneDiff / $todayOctaneReceived) * 100, 1) : 0;
        // others difference percentage added if needed in future
        $todayOthersDiffPct = $todayOthersReceived > 0  
            ? round(($todayOthersDiff / $todayOthersReceived) * 100, 1) : 0;

        // =============================================
        // SUMMARY CARDS — শুধু DC এর district এর data
        // =============================================

        // DC এর district এর filling_stations এর id গুলো
        $districtStationIds = FillingStation::where('district', $dcDistrict)
            ->pluck('id');

        $totalDepots = Depot::whereHas('fillingStations', fn($q) => $q->where('district', $dcDistrict))->count();

        $totalStations = FillingStation::where('district', $dcDistrict)->count();

        // DC এর district এ assigned active tag officers
        $totalOfficers = AssignTagOfficer::where('status', 'active')
            ->whereIn('filling_station_id', $districtStationIds)
            ->distinct('officer_id')
            ->count('officer_id');

        $newDepots = Depot::where('created_at', '>=', $thisMonth)
            ->whereHas('fillingStations', fn($q) => $q->where('district', $dcDistrict))
            ->count();

        $newStations = FillingStation::where('district', $dcDistrict)
            ->where('created_at', '>=', $thisMonth)
            ->count();

        $newOfficers = AssignTagOfficer::where('status', 'active')
            ->whereIn('filling_station_id', $districtStationIds)
            ->where('created_at', '>=', $thisMonth)
            ->distinct('officer_id')
            ->count('officer_id');

        // =============================================
        // THANA/UPAZILA-WISE FUEL SALES TODAY — Bar Chart
        // Admin এ division ছিল, DC এর জন্য thana_upazila দিয়ে group করা হচ্ছে
        // কারণ DC একটাই district দেখে
        // =============================================
        $divisionSalesToday = Fuelreport::whereDate('report_date', $today)
            ->where('district', $dcDistrict)  // ★ district restrict
            ->select(
                'thana_upazila as division',
                DB::raw('COUNT(DISTINCT station_name) as total_stations'),
                DB::raw('SUM(petrol_sales + diesel_sales + octane_sales) as total_fuel_liters')
            )
            ->whereNotNull('thana_upazila')
            ->groupBy('thana_upazila')
            ->orderByDesc('total_fuel_liters')
            ->get()
            ->map(fn($item) => [
                'division'          => $item->division,
                'total_stations'    => (int) $item->total_stations,
                'total_fuel_liters' => round($item->total_fuel_liters / 1000, 1),
            ]);

        // =============================================
        // RECENT ACTIVITIES — শুধু DC এর district
        // =============================================
        $recentActivities = collect();

        /* 1. ZERO STOCK ALERT */
        Fuelreport::where('district', $dcDistrict)  // ★
            ->where(function ($q) {
                $q->where('petrol_closing_stock', 0)
                    ->orWhere('diesel_closing_stock', 0)
                    ->orWhere('octane_closing_stock', 0)
                    ->orWhere('others_closing_stock', 0); // others stock added if needed in future 
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
        Fuelreport::where('district', $dcDistrict)  // ★
            ->where(function ($q) {
                $q->where('petrol_closing_stock', '<', 100)
                    ->orWhere('diesel_closing_stock', '<', 100)
                    ->orWhere('octane_closing_stock', '<', 100)
                    ->orWhere('others_closing_stock', '<', 100); // others stock added if needed in future
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
        Fuelreport::where('district', $dcDistrict)  // ★
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
        Fuelreport::where('district', $dcDistrict)  // ★
            ->whereRaw('
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

        $recentActivities = $recentActivities
            ->sortByDesc('time')
            ->take(4)
            ->values();

        // =============================================
        // HIGH DIFFERENCE REPORTS — শুধু DC এর district
        // =============================================
        $highDifferenceReports = Fuelreport::whereDate('report_date', $today)
            ->where('district', $dcDistrict)  // ★ district restrict
            ->whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) + ABS(others_difference) > 0')
            ->orderByRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) + ABS(others_difference) DESC')
            ->with(['tagOfficer.profile', 'fillingStation.company'])
            ->take(20)
            ->get();

        return view('backend.dc.pages.dashboard.index', compact(
            // today stock
            'todayPetrolStock',
            'todayDieselStock',
            'todayOctaneStock',
            // others stock added if needed in future
            'todayOthersStock',

            // today received
            'todayPetrolReceived',
            'todayDieselReceived',
            'todayOctaneReceived',
            // others received added if needed in future
            'todayOthersReceived',

            // today difference L
            'todayPetrolDiff',
            'todayDieselDiff',
            'todayOctaneDiff',
            // others difference added if needed in future
            'todayOthersDiff',

            // today difference %
            'todayPetrolDiffPct',
            'todayDieselDiffPct',
            'todayOctaneDiffPct',
            // others difference added if needed in future
            'todayOthersDiffPct',

            // today sold
            'todayPetrolSold',
            'todayDieselSold',
            'todayOctaneSold',
            // others sold added if needed in future
            'todayOthersSold',

            // summary
            'totalDepots',
            'totalStations',
            'totalOfficers',
            'newDepots',
            'newStations',
            'newOfficers',

            // chart — thana/upazila grouped (district এর ভেতরে)
            'divisionSalesToday',

            // activities
            'recentActivities',

            'highDifferenceReports',

            // blade এ DC এর district দেখানোর জন্য
            'dcDistrict',
        ));
    }
}
