<?php

namespace App\Http\Controllers\Backend\TagOfficer;

use App\Http\Controllers\Controller;
use App\Models\AssignTagOfficer;
use App\Models\Fuelreport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class FuelReportController extends Controller
{
    // ═══════════════════════════════════════════════════════
    //  HELPER — লগইন করা Officer এর active assignment বের করা
    // ═══════════════════════════════════════════════════════
    private function getOfficerAssignment()
    {
        $officer = Auth::user();

        $assignment = AssignTagOfficer::with('fillingStation')
            ->where('officer_id', $officer->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        return [
            'officer'     => $officer,
            'officerId'   => $officer->id,
            'stationId'   => $assignment?->fillingStation?->id          ?? null,
            'stationName' => $assignment?->fillingStation?->station_name ?? null,
            'stationInfo' => $assignment?->fillingStation                ?? null,
        ];
    }

    // ═══════════════════════════════════════════════════════
    //  INDEX — সব রিপোর্টের লিস্ট (officer + station base)
    // ═══════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $ctx = $this->getOfficerAssignment();
        $stationList = AssignTagOfficer::with('fillingStation')
            ->where('officer_id', $ctx['officerId'])
            ->where('status', 'active')
            ->get()
            ->pluck('fillingStation.station_name', 'fillingStation.id');
        // Station assign না থাকলে empty paginator
        if (! $ctx['stationId']) {
            $emptyReports = new LengthAwarePaginator([], 0, 15);
            return view('backend.tag-officer.pages.fuel-reports.index', [
                'reports'     => $emptyReports,
                'stationName' => null,
                'stationInfo' => null,
            ]);
        }

        $query = Fuelreport::where('tag_officer_id', $ctx['officerId'])
                           ->where('station_id', $ctx['stationId'])
                           ->orderBy('report_date', 'desc');

        // ফিল্টার: তারিখ range
        if ($request->filled('from_date')) {
            $query->whereDate('report_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('report_date', '<=', $request->to_date);
        }

        $reports = $query->paginate(15)->withQueryString();

        return view('backend.tag-officer.pages.fuel-reports.index', [
            'reports'     => $reports,
            'stationName' => $ctx['stationName'],
            'stationInfo' => $ctx['stationInfo'],
                'stationList' => $stationList,
        ]);
    }

    // ═══════════════════════════════════════════════════════
    //  CREATE — নতুন রিপোর্ট ফর্ম
    //  Previous Stock auto-fill হবে officer + station base
    // ═══════════════════════════════════════════════════════
    public function create()
    {
        $ctx = $this->getOfficerAssignment();
        $stationList = AssignTagOfficer::with('fillingStation')
            ->where('officer_id', $ctx['officerId'])
            ->where('status', 'active')
            ->get()
            ->pluck('fillingStation.station_name', 'fillingStation.id');
        // Station assign না থাকলে ফর্ম দেখানো যাবে না
        if (! $ctx['stationId']) {
            return redirect()
                ->route('fuel-reports.index')
                ->with('error', 'You do not have any active station assignment.');
        }

        // Officer + Station দিয়ে আগের closing stock বের করা
        $previousStocks = Fuelreport::getPreviousStocks(
            $ctx['officerId'],
            $ctx['stationId']
        );

        // আজকের তারিখ default
        $defaultDate = Carbon::today()->format('Y-m-d');

        return view('backend.tag-officer.pages.fuel-reports.create', [
            'previousStocks' => $previousStocks,
            'stationName'    => $ctx['stationName'],
            'stationInfo'    => $ctx['stationInfo'],
            'defaultDate'    => $defaultDate,
            'stationList'    => $stationList,
        ]);
    }

    // ═══════════════════════════════════════════════════════
    //  STORE — নতুন রিপোর্ট সেভ
    // ═══════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $ctx = $this->getOfficerAssignment();

        if (! $ctx['stationId']) {
            return redirect()
                ->route('tag-officer.fuel-reports.index')
                ->with('error', 'You do not have any active station assignment.');
        }

        $request->validate([
            'report_date'       => 'required|date',

            'petrol_prev_stock' => 'required|numeric|min:0',
            'petrol_supply'     => 'required|numeric|min:0',
            'petrol_received'   => 'required|numeric|min:0',
            'petrol_sales'      => 'required|numeric|min:0',

            'diesel_prev_stock' => 'required|numeric|min:0',
            'diesel_supply'     => 'required|numeric|min:0',
            'diesel_received'   => 'required|numeric|min:0',
            'diesel_sales'      => 'required|numeric|min:0',

            'octane_prev_stock' => 'required|numeric|min:0',
            'octane_supply'     => 'required|numeric|min:0',
            'octane_received'   => 'required|numeric|min:0',
            'octane_sales'      => 'required|numeric|min:0',
        ]);

        // Duplicate check — একই officer, station, date এ দুটো report নয়
        $exists = Fuelreport::where('tag_officer_id', $ctx['officerId'])
            ->where('station_id', $ctx['stationId'])
            ->where('report_date', $request->report_date)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'A report for the station "' . $ctx['stationName'] . '" on this date already exists. Please edit it.');
        }
    
        // Auto Calculate — difference & closing stock
        $petrolDiff    = $request->petrol_supply  - $request->petrol_received;
        $petrolClosing = $request->petrol_prev_stock + $request->petrol_received - $request->petrol_sales;

        $dieselDiff    = $request->diesel_supply  - $request->diesel_received;
        $dieselClosing = $request->diesel_prev_stock + $request->diesel_received - $request->diesel_sales;

        $octaneDiff    = $request->octane_supply  - $request->octane_received;
        $octaneClosing = $request->octane_prev_stock + $request->octane_received - $request->octane_sales;

        Fuelreport::create([
            // ── FK দুটো ──────────────────────────────
            'tag_officer_id' => $ctx['officerId'],
            'station_id'     => $ctx['stationId'],

            // ── Station info (display purpose) ───────
            'station_name'  => $ctx['stationName'],
            'thana_upazila' => $ctx['stationInfo']?->upazila  ?? '',
            'district'      => $ctx['stationInfo']?->district ?? '',
            'report_date'   => $request->report_date,

            // ── Petrol ───────────────────────────────
            'petrol_prev_stock'    => $request->petrol_prev_stock,
            'petrol_supply'        => $request->petrol_supply,
            'petrol_received'      => $request->petrol_received,
            'petrol_difference'    => $petrolDiff,
            'petrol_sales'         => $request->petrol_sales,
            'petrol_closing_stock' => $petrolClosing,

            // ── Diesel ───────────────────────────────
            'diesel_prev_stock'    => $request->diesel_prev_stock,
            'diesel_supply'        => $request->diesel_supply,
            'diesel_received'      => $request->diesel_received,
            'diesel_difference'    => $dieselDiff,
            'diesel_sales'         => $request->diesel_sales,
            'diesel_closing_stock' => $dieselClosing,

            // ── Octane ───────────────────────────────
            'octane_prev_stock'    => $request->octane_prev_stock,
            'octane_supply'        => $request->octane_supply,
            'octane_received'      => $request->octane_received,
            'octane_difference'    => $octaneDiff,
            'octane_sales'         => $request->octane_sales,
            'octane_closing_stock' => $octaneClosing,
        ]);

        return redirect()
            ->route('fuel-reports.index')
            ->with('success', 'Report saved successfully!');
    }

    // ═══════════════════════════════════════════════════════
    //  SHOW — একটি রিপোর্টের বিস্তারিত
    // ═══════════════════════════════════════════════════════
    public function show(Fuelreport $fuelReport)
    {
        // শুধু নিজের report দেখতে পারবে
        $this->authorizeReport($fuelReport);

        return view('backend.tag-officer.pages.fuel-reports.show', compact('fuelReport'));
    }

    // ═══════════════════════════════════════════════════════
    //  EDIT — রিপোর্ট Edit ফর্ম
    // ═══════════════════════════════════════════════════════
    public function edit(Fuelreport $fuelReport)
    {
        $this->authorizeReport($fuelReport);

        return view('backend.tag-officer.pages.fuel-reports.edit', compact('fuelReport'));
    }

    // ═══════════════════════════════════════════════════════
    //  UPDATE — রিপোর্ট Update
    // ═══════════════════════════════════════════════════════
    public function update(Request $request, Fuelreport $fuelReport)
    {
        $this->authorizeReport($fuelReport);

        $ctx = $this->getOfficerAssignment();

        $request->validate([
            'report_date'       => 'required|date',

            'petrol_prev_stock' => 'required|numeric|min:0',
            'petrol_supply'     => 'required|numeric|min:0',
            'petrol_received'   => 'required|numeric|min:0',
            'petrol_sales'      => 'required|numeric|min:0',

            'diesel_prev_stock' => 'required|numeric|min:0',
            'diesel_supply'     => 'required|numeric|min:0',
            'diesel_received'   => 'required|numeric|min:0',
            'diesel_sales'      => 'required|numeric|min:0',

            'octane_prev_stock' => 'required|numeric|min:0',
            'octane_supply'     => 'required|numeric|min:0',
            'octane_received'   => 'required|numeric|min:0',
            'octane_sales'      => 'required|numeric|min:0',
        ]);

        // Duplicate check (নিজের ID বাদ দিয়ে)
        $exists = Fuelreport::where('tag_officer_id', $ctx['officerId'])
            ->where('station_id', $ctx['stationId'])
            ->where('report_date', $request->report_date)
            ->where('id', '!=', $fuelReport->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Another report for this station on this date already exists.');
        }

        // Auto Calculate
        $petrolDiff    = $request->petrol_supply  - $request->petrol_received;
        $petrolClosing = $request->petrol_prev_stock + $request->petrol_received - $request->petrol_sales;

        $dieselDiff    = $request->diesel_supply  - $request->diesel_received;
        $dieselClosing = $request->diesel_prev_stock + $request->diesel_received - $request->diesel_sales;

        $octaneDiff    = $request->octane_supply  - $request->octane_received;
        $octaneClosing = $request->octane_prev_stock + $request->octane_received - $request->octane_sales;

        $fuelReport->update([
            'report_date'   => $request->report_date,

            'petrol_prev_stock'    => $request->petrol_prev_stock,
            'petrol_supply'        => $request->petrol_supply,
            'petrol_received'      => $request->petrol_received,
            'petrol_difference'    => $petrolDiff,
            'petrol_sales'         => $request->petrol_sales,
            'petrol_closing_stock' => $petrolClosing,

            'diesel_prev_stock'    => $request->diesel_prev_stock,
            'diesel_supply'        => $request->diesel_supply,
            'diesel_received'      => $request->diesel_received,
            'diesel_difference'    => $dieselDiff,
            'diesel_sales'         => $request->diesel_sales,
            'diesel_closing_stock' => $dieselClosing,

            'octane_prev_stock'    => $request->octane_prev_stock,
            'octane_supply'        => $request->octane_supply,
            'octane_received'      => $request->octane_received,
            'octane_difference'    => $octaneDiff,
            'octane_sales'         => $request->octane_sales,
            'octane_closing_stock' => $octaneClosing,
        ]);

        return redirect()
            ->route('fuel-reports.index')
            ->with('success', 'Report updated successfully!');
    }

    // ═══════════════════════════════════════════════════════
    //  DESTROY — রিপোর্ট Delete
    // ═══════════════════════════════════════════════════════
    public function destroy(Fuelreport $fuelReport)
    {
        $this->authorizeReport($fuelReport);

        $fuelReport->delete();

        return redirect()
            ->route('fuel-reports.index')
            ->with('success', 'The report has been deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  AJAX — Previous Stocks (officer + station base)
    // ═══════════════════════════════════════════════════════
    public function getPreviousStocks(Request $request)
    {
        $ctx = $this->getOfficerAssignment();

        if (! $ctx['stationId']) {
            return response()->json([
                'success' => false,
                'message' => 'No active station assignment.',
            ], 422);
        }

        $stocks = Fuelreport::getPreviousStocks(
            $ctx['officerId'],
            $ctx['stationId']
        );

        return response()->json([
            'success' => true,
            'stocks'  => $stocks,
        ]);
    }

    // ═══════════════════════════════════════════════════════
    //  PRIVATE — Officer নিজের report কিনা check
    // ═══════════════════════════════════════════════════════
    private function authorizeReport(Fuelreport $fuelReport): void
    {
        $officerId = Auth::id();

        if ($fuelReport->tag_officer_id !== $officerId) {
            abort(403, 'You are not authorized to view this report.');
        }
    }
}