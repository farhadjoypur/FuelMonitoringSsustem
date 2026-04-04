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
        $path = resource_path('data/location.json');

        if (!file_exists($path)) {
            dd("Location file not found at: " . $path);
        }

        $locations = json_decode(file_get_contents($path), true);
        // ── Filter Dropdown Data (সবসময় load হবে) ────────────
        $divisions = FillingStation::whereNotNull('division')
            ->distinct()->pluck('division')->sort()->values();

        $districts = FillingStation::whereNotNull('district')
            ->distinct()->pluck('district')->sort()->values();

        $upazilas = FillingStation::whereNotNull('upazila')
            ->distinct()->pluck('upazila')->sort()->values();

        $depots = Depot::orderBy('depot_name')->get(['id', 'depot_name']);

        $stations = FillingStation::orderBy('station_name')
            ->get(['id', 'station_name']);

        $companies = Company::orderBy('name')->get(['id', 'name']);

        // ── Filter applied কিনা check ─────────────────────────
        // যেকোনো একটি filter দিলেই data load হবে
        $filtered = $request->hasAny([
            'from_date',
            'to_date',
            'division',
            'district',
            'upazila',
            'company_id',
            'depot_id',
            'station_name',
            'fuel_type',
            'stock_status',
            'assign_status',
            'officer_id',
            'min_diff',
        ]);

        // ── Filter না করলে সব empty return করো ───────────────
        if (! $filtered) {
            return view('backend.admin.pages.reports.index', [
                // dropdowns
                'divisions'      => $divisions,
                'districts'      => $districts,
                'upazilas'       => $upazilas,
                'depots'         => $depots,
                'stations'       => $stations,
                'companies'      => $companies,
                // empty collections
                'stockReports'   => collect(),
                'salesReports'   => collect(),
                'officerReports' => collect(),
                'diffReports'    => collect(),
                // flag
                'filtered'       => false,
                'locations'      => $locations,
            ]);
        }

        // ═══════════════════════════════════════════════════════
        //  BASE QUERY — shared filters (date, location, station)
        //  সব tab এই base query use করে
        // ═══════════════════════════════════════════════════════
        $baseQuery = function () use ($request) {
            return Fuelreport::query()

                // তারিখ range
                ->when(
                    $request->filled('from_date'),
                    fn($q) => $q->whereDate('report_date', '>=', $request->from_date)
                )
                ->when(
                    $request->filled('to_date'),
                    fn($q) => $q->whereDate('report_date', '<=', $request->to_date)
                )

                // location filters
                // ->when(
                //     $request->filled('division'),
                //     fn($q) => $q->where('division', $request->division)
                // )
                ->when(
                    $request->filled('district'),
                    fn($q) => $q->where('district', $request->district)
                )
                ->when(
                    $request->filled('upazila'),
                    fn($q) => $q->where('thana_upazila', $request->upazila)
                )

                // station filter
                ->when(
                    $request->filled('station_name'),
                    fn($q) => $q->where('station_name', $request->station_name)
                );
        };

        // ═══════════════════════════════════════════════════════
        //  TAB 1 — STOCK REPORT
        //
        //  Calculation:
        //    Opening  = prev_stock (আগের দিনের closing)
        //    Received = supply আসা fuel
        //    Sold     = বিক্রয়
        //    Closing  = prev_stock + received - sales
        //    Status   : closing >= 2000 → Available
        //               1–1999         → Low Stock
        //               <= 0           → Zero Stock
        // ═══════════════════════════════════════════════════════
        $stockQuery = $baseQuery()

            // Depot filter — depot_name দিয়ে match
            ->when($request->filled('depot_id'), function ($q) use ($request) {
                $depotName = Depot::find($request->depot_id)?->depot_name;
                if ($depotName) {
                    $q->where('depot_name', $depotName);
                }
            })

            // Company filter — FillingStation থেকে station names নিয়ে filter
            ->when($request->filled('company_id'), function ($q) use ($request) {
                $stationNames = FillingStation::where('company_id', $request->company_id)
                    ->pluck('station_name');
                $q->whereIn('station_name', $stationNames);
            })

            // Stock Status filter
            ->when($request->filled('stock_status'), function ($q) use ($request) {
                $status = $request->stock_status;

                if ($status === 'available') {
                    // মোট closing >= 2000 L
                    $q->whereRaw(
                        '(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) >= 2000'
                    );
                } elseif ($status === 'low') {
                    // মোট closing 1 থেকে 1999 এর মধ্যে
                    $q->whereRaw(
                        '(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) BETWEEN 1 AND 1999'
                    );
                } elseif ($status === 'zero') {
                    // মোট closing 0 বা তার কম
                    $q->whereRaw(
                        '(diesel_closing_stock + petrol_closing_stock + octane_closing_stock) <= 0'
                    );
                }
            })

            // Company name পাওয়ার জন্য relation load
            ->with(['fillingStation.company', 'tagOfficer'])

            ->orderByDesc('report_date');

        $stockReports = $stockQuery->get();

        // ═══════════════════════════════════════════════════════
        //  TAB 2 — SALES REPORT
        //
        //  Calculation:
        //    Total Sold = diesel_sales + petrol_sales + octane_sales
        //    fuel_type filter দিলে শুধু সেই fuel এর sales > 0 দেখাবে
        // ═══════════════════════════════════════════════════════
        $salesReports = $baseQuery()

            // Depot filter
            ->when($request->filled('depot_id'), function ($q) use ($request) {
                $depotName = Depot::find($request->depot_id)?->depot_name;
                if ($depotName) {
                    $q->where('depot_name', $depotName);
                }
            })

            // Fuel type filter — সেই fuel এর sales > 0 হলে দেখাবে
            ->when($request->filled('fuel_type'), function ($q) use ($request) {
                $type = $request->fuel_type; // diesel | petrol | octane
                $q->where("{$type}_sales", '>', 0);
            })

            ->orderByDesc('report_date')
            ->get();

        // ═══════════════════════════════════════════════════════
        //  TAB 3 — TAG OFFICER REPORT
        //
        //  AssignTagOfficer টেবিল থেকে আসে
        //  officer → User model
        //  fillingStation → FillingStation model
        // ═══════════════════════════════════════════════════════
        $officerQuery = AssignTagOfficer::with([
            'officer',
            'fillingStation:id,station_name,district,division',
        ]);

        // District filter — fillingStation এর district দিয়ে
        if ($request->filled('district')) {
            $officerQuery->whereHas(
                'fillingStation',
                fn($q) => $q->where('district', $request->district)
            );
        }

        // Division filter — fillingStation এর division দিয়ে
        if ($request->filled('division')) {
            $officerQuery->whereHas(
                'fillingStation',
                fn($q) => $q->where('division', $request->division)
            );
        }

        // Assignment status filter (active / inactive)
        if ($request->filled('assign_status')) {
            $officerQuery->where('status', $request->assign_status);
        }

        $officerReports = $officerQuery->latest()->get();

        // ═══════════════════════════════════════════════════════
        //  TAB 4 — DIFFERENCE (%) REPORT
        //
        //  Calculation:
        //    difference     = received - (closing - prev_stock)
        //                   = received - sales  (ideally = 0)
        //
        //    difference %   = difference / received × 100
        //
        //    difference > 0 → বেশি fuel এসেছে / কম বিক্রি এন্ট্রি
        //    difference < 0 → কম fuel এসেছে / বেশি বিক্রি এন্ট্রি
        //    difference = 0 → সব ঠিক আছে
        //
        //    High Diff      = total |diff| > 50 L (alert threshold)
        // ═══════════════════════════════════════════════════════
        $minDiff = (float) $request->get('min_diff', 0);

        $diffReports = $baseQuery()

            // Min difference filter
            ->when($minDiff > 0, function ($q) use ($minDiff) {
                $q->whereRaw(
                    'ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) >= ?',
                    [$minDiff]
                );
            })

            ->orderByDesc('report_date')
            ->get();

        // ═══════════════════════════════════════════════════════
        //  RETURN VIEW
        // ═══════════════════════════════════════════════════════
        return view('backend.admin.pages.reports.index', compact(
            // filter dropdowns
            'divisions',
            'districts',
            'upazilas',
            'depots',
            'stations',
            'companies',

            // tab data
            'stockReports',
            'salesReports',
            'officerReports',
            'diffReports',
            'locations'
        ) + ['filtered' => true]);
    }
}
