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
        // TOP STAT CARDS
        // =============================================
 
        // আজকের সব রিপোর্ট থেকে মোট stock, received, sold, difference
        $todayReports = Fuelreport::whereDate('report_date', $today)->get();
 
        $totalStockToday = $todayReports->sum(fn($r) =>
            $r->petrol_closing_stock + $r->diesel_closing_stock + $r->octane_closing_stock
        );
        $totalReceivedToday = $todayReports->sum(fn($r) =>
            $r->petrol_received + $r->diesel_received + $r->octane_received
        );
        $totalSoldToday = $todayReports->sum(fn($r) =>
            $r->petrol_sales + $r->diesel_sales + $r->octane_sales
        );
        $totalDiffToday = $todayReports->sum(fn($r) =>
            $r->petrol_difference + $r->diesel_difference + $r->octane_difference
        );
        $totalDiffPct = $totalReceivedToday > 0
            ? round(abs($totalDiffToday) / $totalReceivedToday * 100, 1)
            : 0;
 
        // গত মাসের তুলনায় received % change
        $lastMonthReceived = Fuelreport::whereMonth('report_date', Carbon::now()->subMonth()->month)
            ->sum(DB::raw('petrol_received + diesel_received + octane_received'));
        $thisMonthReceived = Fuelreport::whereMonth('report_date', Carbon::now()->month)
            ->sum(DB::raw('petrol_received + diesel_received + octane_received'));
        $receivedChangePct = $lastMonthReceived > 0
            ? round((($thisMonthReceived - $lastMonthReceived) / $lastMonthReceived) * 100, 1)
            : 0;
 
        // =============================================
        // TODAY'S STOCK (per fuel type)
        // =============================================
        $todayPetrolStock  = $todayReports->sum('petrol_closing_stock');
        $todayDieselStock  = $todayReports->sum('diesel_closing_stock');
        $todayOctaneStock  = $todayReports->sum('octane_closing_stock');
 
        // =============================================
        // TODAY'S SOLD (per fuel type)
        // =============================================
        $todayPetrolSold  = $todayReports->sum('petrol_sales');
        $todayDieselSold  = $todayReports->sum('diesel_sales');
        $todayOctaneSold  = $todayReports->sum('octane_sales');
 
        // =============================================
        // SUMMARY CARDS
        // =============================================
        $totalDepots    = Depot::count();
        $totalStations  = FillingStation::count();
        $totalOfficers = User::where('role', 'tag_officer')->count();
        // যদি Spatie না থাকে:
        // $totalOfficers = User::where('role', 'tag_officer')->count();
 
        $activeAssignments = AssignTagOfficer::where('status', 'active')->count();
 
        // this month new counts
        $newDepots    = Depot::where('created_at', '>=', $thisMonth)->count();
        $newStations  = FillingStation::where('created_at', '>=', $thisMonth)->count();
        $newOfficers  = User::where('created_at', '>=', $thisMonth)->count();
        $assignChange = AssignTagOfficer::where('created_at', '>=', $thisMonth)->count()
                      - AssignTagOfficer::where('status', 'inactive')
                            ->where('updated_at', '>=', $thisMonth)->count();
 
        // =============================================
        // FUEL TYPE DISTRIBUTION PIE (from all reports - latest per station)
        // =============================================
        $latestReports = Fuelreport::select(
                'station_name',
                DB::raw('MAX(report_date) as max_date')
            )
            ->groupBy('station_name');
 
        $fuelDistribution = Fuelreport::joinSub($latestReports, 'latest', function ($join) {
            $join->on('fuelreports.station_name', '=', 'latest.station_name')
                 ->on('fuelreports.report_date', '=', 'latest.max_date');
        })
        ->selectRaw('
            SUM(petrol_closing_stock) as total_petrol,
            SUM(diesel_closing_stock) as total_diesel,
            SUM(octane_closing_stock) as total_octane
        ')
        ->first();
 
        $totalPetrol = $fuelDistribution->total_petrol ?? 0;
        $totalDiesel = $fuelDistribution->total_diesel ?? 0;
        $totalOctane = $fuelDistribution->total_octane ?? 0;
        $totalOthers = 0; // প্রয়োজনে অন্য fuel type থাকলে যোগ করুন
 
        // =============================================
        // COMPANY TYPE DISTRIBUTION PIE
        // =============================================
        $companyDistribution = FillingStation::select('company_id', DB::raw('count(*) as total'))
            ->with('company:id,name')
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'name'  => $item->company->name ?? 'Unknown',
                'total' => $item->total,
            ]);
 
        // =============================================
        // DIVISION-WISE DISTRIBUTION BAR
        // =============================================
        $divisionDistribution = FillingStation::select('division', DB::raw('count(*) as total'))
            ->whereNotNull('division')
            ->groupBy('division')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'division' => $item->division,
                'total'    => $item->total,
            ]);
 
        // =============================================
        // FUEL SALES TREND (last 12 months)
        // =============================================
        $salesTrend = Fuelreport::select(
                DB::raw("DATE_FORMAT(report_date, '%b') as month_label"),
                DB::raw("DATE_FORMAT(report_date, '%Y-%m') as month_key"),
                DB::raw('SUM(petrol_sales + diesel_sales + octane_sales) as total_sales')
            )
            ->where('report_date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key')
            ->get();
 
        // =============================================
        // RECENT DEPOT ENTRIES
        // =============================================
        $recentDepots = Depot::latest()->take(5)->get()->map(function ($depot) {
            // utilization: linked stations এর received vs capacity
            $stationCount = FillingStation::where('linked_depot', $depot->id)->count();
            $utilization  = $depot->capacity > 0
                ? min(100, round(($stationCount * 5000 / $depot->capacity) * 100))
                : 0; // placeholder logic; real data থাকলে replace করুন
 
            return [
                'name'        => $depot->depot_name,
                'district'    => $depot->district,
                'capacity'    => number_format($depot->capacity) . ' L',
                'utilization' => $utilization,
                'status'      => $depot->status,
            ];
        });
 
        // =============================================
        // RECENT ACTIVITIES (mixed from multiple models)
        // =============================================
        $recentActivities = collect();
 
        // নতুন depot add
        Depot::latest()->take(3)->get()->each(function ($d) use (&$recentActivities) {
            $recentActivities->push([
                'type'  => 'depot',
                'title' => 'New Depot Added',
                'sub'   => $d->district . ' — ' . $d->depot_name,
                'time'  => $d->created_at,
                'color' => 'green',
                'icon'  => 'fa-circle-info',
            ]);
        });
 
        // নতুন assignment
        AssignTagOfficer::with(['fillingStation:id,station_name,district'])
            ->latest()->take(3)->get()
            ->each(function ($a) use (&$recentActivities) {
                $recentActivities->push([
                    'type'  => 'assign',
                    'title' => 'Officer Assigned',
                    'sub'   => ($a->fillingStation->district ?? '') . ' — ' . ($a->fillingStation->station_name ?? ''),
                    'time'  => $a->created_at,
                    'color' => 'blue',
                    'icon'  => 'fa-circle-info',
                ]);
            });
 
        // Stock alert — difference > 0 reports
        Fuelreport::whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) > 50')
            ->latest('report_date')->take(2)->get()
            ->each(function ($r) use (&$recentActivities) {
                $recentActivities->push([
                    'type'  => 'alert',
                    'title' => 'Stock Alert',
                    'sub'   => $r->district . ' — ' . $r->station_name,
                    'time'  => $r->updated_at,
                    'color' => 'yellow',
                    'icon'  => 'fa-circle-exclamation',
                ]);
            });
 
        // Operational loss — negative closing
        Fuelreport::where(function ($q) {
                $q->where('petrol_closing_stock', '<', 0)
                  ->orWhere('diesel_closing_stock', '<', 0)
                  ->orWhere('octane_closing_stock', '<', 0);
            })
            ->latest('report_date')->take(2)->get()
            ->each(function ($r) use (&$recentActivities) {
                $recentActivities->push([
                    'type'  => 'loss',
                    'title' => 'Operational Loss Detected',
                    'sub'   => $r->district . ' — ' . $r->station_name,
                    'time'  => $r->updated_at,
                    'color' => 'red',
                    'icon'  => 'fa-circle-xmark',
                ]);
            });
 
        // সময় অনুযায়ী sort করে সর্বশেষ ৫টা দেখাও
        $recentActivities = $recentActivities
            ->sortByDesc('time')
            ->take(5)
            ->values();
 
        return view('backend.admin.pages.dashboard.index', compact(
            // stat cards
            'totalStockToday', 'totalReceivedToday', 'totalSoldToday',
            'totalDiffToday',  'totalDiffPct', 'receivedChangePct',
 
            // fuel type stocks & sold
            'todayPetrolStock', 'todayDieselStock', 'todayOctaneStock',
            'todayPetrolSold',  'todayDieselSold',  'todayOctaneSold',
 
            // summary
            'totalDepots', 'totalStations', 'totalOfficers', 'activeAssignments',
            'newDepots', 'newStations', 'newOfficers', 'assignChange',
 
            // charts
            'totalPetrol', 'totalDiesel', 'totalOctane', 'totalOthers',
            'companyDistribution', 'divisionDistribution', 'salesTrend',
 
            // table & activities
            'recentDepots', 'recentActivities'
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
