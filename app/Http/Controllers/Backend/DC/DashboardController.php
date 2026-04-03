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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dc        = auth()->user();  // logged-in DC
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
    
        // =============================================
        // DC-এর আন্ডারে যে Station IDs আছে সেগুলো বের করো
        // AssignTagOfficer টেবিলে dc_id কলাম আছে ধরে নিচ্ছি
        // যদি না থাকে, নিচের বিকল্প দেখো
        // =============================================
        $stationIds = AssignTagOfficer::where('dc_id', $dc->id)
                        ->pluck('filling_station_id')
                        ->unique()
                        ->toArray();
    
        // বিকল্প: যদি dc_id না থেকে district দিয়ে link থাকে তাহলে:
        // $stationIds = FillingStation::where('district', $dc->district)->pluck('id')->toArray();
    
        // =============================================
        // DC-এর আন্ডারে যে Station Names আছে (Fuelreport filter-এর জন্য)
        // =============================================
        $stationNames = FillingStation::whereIn('id', $stationIds)
                        ->pluck('station_name')
                        ->toArray();
    
        // =============================================
        // TOP STAT CARDS
        // =============================================
    
        // আজকের রিপোর্ট — শুধু DC-এর stations থেকে
        $todayReports = Fuelreport::whereDate('report_date', $today)
                        ->whereIn('station_name', $stationNames)
                        ->get();
    
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
    
        // গত মাস vs এই মাসের received — DC-এর stations
        $lastMonthReceived = Fuelreport::whereIn('station_name', $stationNames)
            ->whereMonth('report_date', Carbon::now()->subMonth()->month)
            ->sum(DB::raw('petrol_received + diesel_received + octane_received'));
    
        $thisMonthReceived = Fuelreport::whereIn('station_name', $stationNames)
            ->whereMonth('report_date', Carbon::now()->month)
            ->sum(DB::raw('petrol_received + diesel_received + octane_received'));
    
        $receivedChangePct = $lastMonthReceived > 0
            ? round((($thisMonthReceived - $lastMonthReceived) / $lastMonthReceived) * 100, 1)
            : 0;
    
        // =============================================
        // TODAY'S STOCK & SOLD (per fuel type) — DC scope
        // =============================================
        $todayPetrolStock = $todayReports->sum('petrol_closing_stock');
        $todayDieselStock = $todayReports->sum('diesel_closing_stock');
        $todayOctaneStock = $todayReports->sum('octane_closing_stock');
    
        $todayPetrolSold  = $todayReports->sum('petrol_sales');
        $todayDieselSold  = $todayReports->sum('diesel_sales');
        $todayOctaneSold  = $todayReports->sum('octane_sales');
    
        // =============================================
        // SUMMARY CARDS — শুধু DC-এর scope
        // =============================================
        $totalDepots   = Depot::whereHas('fillingStations', fn($q) =>
                            $q->whereIn('id', $stationIds)
                         )->count();
    
        $totalStations = FillingStation::whereIn('id', $stationIds)->count();
    
        // DC-এর আন্ডারে Tag Officers (unique)
       // ✅ সঠিক:
        $totalOfficers = AssignTagOfficer::where('dc_id', $dc->id)
        ->distinct('officer_id')
        ->count('officer_id');

        $newOfficers = AssignTagOfficer::where('dc_id', $dc->id)
        ->where('created_at', '>=', $thisMonth)
        ->distinct('officer_id')
        ->count('officer_id');
    
        $activeAssignments = AssignTagOfficer::where('dc_id', $dc->id)
                                ->where('status', 'active')
                                ->count();
    
        // এই মাসের নতুন additions — DC scope
        $newDepots   = Depot::whereHas('fillingStations', fn($q) =>
                            $q->whereIn('id', $stationIds)
                       )->where('created_at', '>=', $thisMonth)->count();
    
        $newStations = FillingStation::whereIn('id', $stationIds)
                        ->where('created_at', '>=', $thisMonth)->count();
    
        $newOfficers = AssignTagOfficer::where('dc_id', $dc->id)
                        ->where('created_at', '>=', $thisMonth)
                        ->distinct('officer_id')->count('officer_id');
    
        $assignChange = AssignTagOfficer::where('dc_id', $dc->id)
                            ->where('created_at', '>=', $thisMonth)->count()
                      - AssignTagOfficer::where('dc_id', $dc->id)
                            ->where('status', 'inactive')
                            ->where('updated_at', '>=', $thisMonth)->count();
    
        // =============================================
        // FUEL TYPE DISTRIBUTION PIE — DC scope
        // =============================================
        $latestReports = Fuelreport::select(
                'station_name',
                DB::raw('MAX(report_date) as max_date')
            )
            ->whereIn('station_name', $stationNames)
            ->groupBy('station_name');
    
        $fuelDistribution = Fuelreport::joinSub($latestReports, 'latest', function ($join) {
            $join->on('fuelreports.station_name', '=', 'latest.station_name')
                 ->on('fuelreports.report_date', '=', 'latest.max_date');
        })
        ->whereIn('fuelreports.station_name', $stationNames)
        ->selectRaw('
            SUM(petrol_closing_stock) as total_petrol,
            SUM(diesel_closing_stock) as total_diesel,
            SUM(octane_closing_stock) as total_octane
        ')
        ->first();
    
        $totalPetrol = $fuelDistribution->total_petrol ?? 0;
        $totalDiesel = $fuelDistribution->total_diesel ?? 0;
        $totalOctane = $fuelDistribution->total_octane ?? 0;
        $totalOthers = 0;
    
        // =============================================
        // COMPANY TYPE DISTRIBUTION — DC scope
        // =============================================
        $companyDistribution = FillingStation::select('company_id', DB::raw('count(*) as total'))
            ->with('company:id,name')
            ->whereIn('id', $stationIds)
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
        // DIVISION-WISE DISTRIBUTION BAR — DC scope
        // =============================================
        $divisionDistribution = FillingStation::select('division', DB::raw('count(*) as total'))
            ->whereIn('id', $stationIds)
            ->whereNotNull('division')
            ->groupBy('division')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'division' => $item->division,
                'total'    => $item->total,
            ]);
    
        // =============================================
        // FUEL SALES TREND (last 12 months) — DC scope
        // =============================================
        $salesTrend = Fuelreport::select(
                DB::raw("DATE_FORMAT(report_date, '%b') as month_label"),
                DB::raw("DATE_FORMAT(report_date, '%Y-%m') as month_key"),
                DB::raw('SUM(petrol_sales + diesel_sales + octane_sales) as total_sales')
            )
            ->whereIn('station_name', $stationNames)
            ->where('report_date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key')
            ->get();
    
        // =============================================
        // RECENT DEPOT ENTRIES — DC scope
        // =============================================
        $recentDepots = Depot::whereHas('fillingStations', fn($q) =>
                            $q->whereIn('id', $stationIds)
                        )
            ->latest()->take(5)->get()
            ->map(function ($depot) use ($stationIds) {
                $stationCount = FillingStation::where('linked_depot', $depot->id)
                                ->whereIn('id', $stationIds)->count();
                $utilization  = $depot->capacity > 0
                    ? min(100, round(($stationCount * 5000 / $depot->capacity) * 100))
                    : 0;
    
                return [
                    'name'        => $depot->depot_name,
                    'district'    => $depot->district,
                    'capacity'    => number_format($depot->capacity) . ' L',
                    'utilization' => $utilization,
                    'status'      => $depot->status,
                ];
            });
    
        // =============================================
        // RECENT ACTIVITIES — DC scope
        // =============================================
        $recentActivities = collect();
    
        // নতুন depot (DC-এর stations এর সাথে linked)
        Depot::whereHas('fillingStations', fn($q) => $q->whereIn('id', $stationIds))
            ->latest()->take(3)->get()
            ->each(function ($d) use (&$recentActivities) {
                $recentActivities->push([
                    'type'  => 'depot',
                    'title' => 'New Depot Added',
                    'sub'   => $d->district . ' — ' . $d->depot_name,
                    'time'  => $d->created_at,
                    'color' => 'green',
                    'icon'  => 'fa-circle-info',
                ]);
            });
    
        // নতুন assignment — DC scope
        AssignTagOfficer::with(['fillingStation:id,station_name,district'])
            ->where('dc_id', $dc->id)
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
    
        // Stock alert — DC scope
        Fuelreport::whereIn('station_name', $stationNames)
            ->whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) > 50')
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
    
        // Operational loss — DC scope
        Fuelreport::whereIn('station_name', $stationNames)
            ->where(function ($q) {
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
    
        $recentActivities = $recentActivities
            ->sortByDesc('time')
            ->take(5)
            ->values();
    
        return view('backend.dc.pages.dashboard.index', compact(
            'totalStockToday', 'totalReceivedToday', 'totalSoldToday',
            'totalDiffToday',  'totalDiffPct', 'receivedChangePct',
            'todayPetrolStock', 'todayDieselStock', 'todayOctaneStock',
            'todayPetrolSold',  'todayDieselSold',  'todayOctaneSold',
            'totalDepots', 'totalStations', 'totalOfficers', 'activeAssignments',
            'newDepots', 'newStations', 'newOfficers', 'assignChange',
            'totalPetrol', 'totalDiesel', 'totalOctane', 'totalOthers',
            'companyDistribution', 'divisionDistribution', 'salesTrend',
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
