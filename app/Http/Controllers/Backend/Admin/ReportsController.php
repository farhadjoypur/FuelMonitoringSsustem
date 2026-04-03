<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignTagOfficer;
use App\Models\Company;
use App\Models\Depot;
use App\Models\FillingStation;
use App\Models\Fuelreport;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function salesReport(Request $request)
    {
        $tab = $request->get('tab', 'stock');

        // ── Common Filters (Filter Dropdown গুলো populate করতে) ──────────────
        $divisions = FillingStation::whereNotNull('division')->distinct()->pluck('division')->sort()->values();
        $districts = FillingStation::whereNotNull('district')->distinct()->pluck('district')->sort()->values();
        $depots    = Depot::orderBy('depot_name')->get(['id', 'depot_name']);
        $stations  = FillingStation::orderBy('station_name')->get(['id', 'station_name']);
        $companies = \App\Models\Company::orderBy('name')->get(['id', 'name']);
        // যদি আলাদা Company model না থাকে, FillingStation থেকে নিন:
        // $companies = FillingStation::with('company:id,name')->whereNotNull('company_id')
        //     ->get()->pluck('company')->unique('id')->filter();

        // ── Base Query Builder (shared filters) ──────────────────────────────
        $baseQuery = function () use ($request) {
            return Fuelreport::query()
                ->when($request->from_date, fn($q) => $q->whereDate('report_date', '>=', $request->from_date))
                ->when($request->to_date,   fn($q) => $q->whereDate('report_date', '<=', $request->to_date))
                ->when($request->division,  fn($q) => $q->where('division',  $request->division))
                ->when($request->district,  fn($q) => $q->where('district',  $request->district))
                ->when($request->station_name, fn($q) => $q->where('station_name', $request->station_name));
        };

        // ── TAB 1: STOCK REPORT ──────────────────────────────────────────────
        $stockQuery = $baseQuery()
            ->when($request->depot_id, function ($q) use ($request) {
                // depot_name ব্যবহার করে join (depot_id relation থাকলে সরাসরি use করুন)
                $depotName = Depot::find($request->depot_id)?->depot_name;
                return $q->where('depot_name', $depotName);
            })
            ->when($request->company_id, function ($q) use ($request) {
                // FillingStation থেকে station names নিয়ে filter
                $stationNames = FillingStation::where('company_id', $request->company_id)->pluck('station_name');
                return $q->whereIn('station_name', $stationNames);
            })
            ->when($request->stock_status, function ($q, $status) {
                // stock_status filter logic
                if ($status === 'available') {
                    $q->whereRaw('(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) >= 2000');
                } elseif ($status === 'low') {
                    $q->whereRaw('(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) > 0')
                        ->whereRaw('(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) < 2000');
                } elseif ($status === 'zero') {
                    $q->whereRaw('(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) <= 0');
                }
            })
            ->orderByDesc('report_date');

        // fuel_type filter — শুধু column hide/show এর জন্য PHP তে পাঠাচ্ছি,
        // DB filter দরকার হলে নিচে uncomment করুন:
        // ->when($request->fuel_type === 'diesel', fn($q) => $q->where('diesel_closing_stock', '>', 0))

        $stockReports = $stockQuery->get();

        // ── TAB 2: SALES REPORT ──────────────────────────────────────────────
        $salesReports = $baseQuery()
            ->when($request->depot_id, function ($q) use ($request) {
                $depotName = Depot::find($request->depot_id)?->depot_name;
                return $q->where('depot_name', $depotName);
            })
            ->when($request->fuel_type, function ($q, $type) {
                // শুধু সেই fuel type এর sales > 0 দেখাও
                return $q->where("{$type}_sales", '>', 0);
            })
            ->orderByDesc('report_date')
            ->get();

        // ── TAB 3: TAG OFFICER REPORT ────────────────────────────────────────
        $officerQuery = AssignTagOfficer::with([
            'officer',
            'fillingStation:id,station_name,district,division',
        ]);

        if ($request->district) {
            $officerQuery->whereHas('fillingStation', fn($q) => $q->where('district', $request->district));
        }
        if ($request->division) {
            $officerQuery->whereHas('fillingStation', fn($q) => $q->where('division', $request->division));
        }
        if ($request->assign_status) {
            $officerQuery->where('status', $request->assign_status);
        }

        $officerReports = $officerQuery->latest()->get();

        // ── TAB 4: DIFFERENCE (%) REPORT ─────────────────────────────────────
        $minDiff = $request->get('min_diff', 0);

        $diffReports = $baseQuery()
            ->when(
                $minDiff > 0,
                fn($q) =>
                $q->whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) >= ?', [$minDiff])
            )
            ->orderByDesc('report_date')
            ->get();

        // ── TAB 5: DUE SALES REPORT ──────────────────────────────────────────
        // Fuelreport এ due_sales column থাকলে সরাসরি query করুন।
        // যদি diesel_due_sales, petrol_due_sales, octane_due_sales column থাকে:
        // $dueReports = $baseQuery()
        //     ->where(function ($q) {
        //         $q->where('diesel_due_sales', '>', 0)
        //             ->orWhere('petrol_due_sales', '>', 0)
        //             ->orWhere('octane_due_sales', '>', 0);
        //     })
        //     ->orderByDesc('report_date')
        //     ->get();

        // ── Return View ───────────────────────────────────────────────────────
        return view('backend.admin.pages.reports.index', compact(
            // Filter dropdowns
            'divisions',
            'districts',
            'depots',
            'stations',
            'companies',

            // Tab data
            'stockReports',
            'salesReports',
            'officerReports',
            'diffReports',
            // 'dueReports',
        ));
    }
}
