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
    //  HELPER — Today only check
    // ═══════════════════════════════════════════════════════
    private function authorizeTodayOnly(Fuelreport $fuelReport): void
    {
        $reportDate = Carbon::parse($fuelReport->report_date)->toDateString();
        $today      = Carbon::today()->toDateString();

        if ($reportDate !== $today) {
            if (request()->expectsJson() || request()->ajax()) {
                abort(response()->json(['success' => false, 'message' => 'PREVIOUS_REPORT'], 403));
            }
            abort(403, 'PREVIOUS_REPORT');
        }
    }

    // ═══════════════════════════════════════════════════════
    //  HELPER — Officer assignment (optional station filter)
    // ═══════════════════════════════════════════════════════
    private function getOfficerAssignment(?int $stationId = null): array
    {
        $officer = Auth::user();

        $allAssignments = AssignTagOfficer::with('fillingStation')
            ->where('officer_id', $officer->id)
            ->where('status', 'active')
            ->get();

        // Requested station খোঁজো, না পেলে প্রথমটা নাও
        $assignment = null;
        if ($stationId) {
            $assignment = $allAssignments->first(
                fn($a) => $a->fillingStation && (int) $a->fillingStation->id === $stationId
            );
        }
        if (! $assignment) {
            $assignment = $allAssignments->first();
        }

        // Station list (id => name)
        $stationList = $allAssignments
            ->filter(fn($a) => $a->fillingStation !== null)
            ->mapWithKeys(fn($a) => [
                (int) $a->fillingStation->id => $a->fillingStation->station_name,
            ]);

        return [
            'officer'     => $officer,
            'officerId'   => $officer->id,
            'stationId'   => $assignment?->fillingStation?->id           ?? null,
            'stationName' => $assignment?->fillingStation?->station_name  ?? null,
            'stationInfo' => $assignment?->fillingStation                 ?? null,
            'stationList' => $stationList,
        ];
    }

    // ═══════════════════════════════════════════════════════
    //  HELPER — Request থেকে station_id নিরাপদে বের করা
    // ═══════════════════════════════════════════════════════
    private function resolveStationId(Request $request): ?int
    {
        return $request->filled('station_id') ? (int) $request->input('station_id') : null;
    }

    // ═══════════════════════════════════════════════════════
    //  INDEX
    // ═══════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $ctx = $this->getOfficerAssignment($this->resolveStationId($request));

        if (! $ctx['stationId']) {
            return view('backend.tag-officer.pages.fuel-reports.index', [
                'reports'         => new LengthAwarePaginator([], 0, 15),
                'stationName'     => null,
                'stationInfo'     => null,
                'stationList'     => $ctx['stationList'],
                'selectedStation' => null,
                'previousStocks'  => ['octane' => 0, 'petrol' => 0, 'diesel' => 0, 'others' => 0],
                'defaultDate'     => Carbon::today()->format('Y-m-d'),
            ]);
        }

        $query = Fuelreport::where('tag_officer_id', $ctx['officerId'])
            ->where('station_id', $ctx['stationId'])
            ->orderBy('report_date', 'desc');

        if ($request->filled('from_date')) {
            $query->whereDate('report_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('report_date', '<=', $request->to_date);
        }

        $reports        = $query->paginate(15)->withQueryString();
        $previousStocks = Fuelreport::getPreviousStocks($ctx['officerId'], $ctx['stationId']);

        return view('backend.tag-officer.pages.fuel-reports.index', [
            'reports'         => $reports,
            'stationName'     => $ctx['stationName'],
            'stationInfo'     => $ctx['stationInfo'],
            'stationList'     => $ctx['stationList'],
            'selectedStation' => $ctx['stationId'],
            'previousStocks'  => $previousStocks,
            'defaultDate'     => Carbon::today()->format('Y-m-d'),
        ]);
    }

    // ═══════════════════════════════════════════════════════
    //  CREATE
    // ═══════════════════════════════════════════════════════
    public function create(Request $request)
    {
        $ctx = $this->getOfficerAssignment($this->resolveStationId($request));

        if (! $ctx['stationId']) {
            return redirect()->route('fuel-reports.index')
                ->with('error', 'You do not have any active station assignment.');
        }

        $previousStocks = Fuelreport::getPreviousStocks($ctx['officerId'], $ctx['stationId']);

        $reports = Fuelreport::where('tag_officer_id', $ctx['officerId'])
            ->where('station_id', $ctx['stationId'])
            ->orderBy('report_date', 'desc')
            ->paginate(15);

        return view('backend.tag-officer.pages.fuel-reports.create', [
            'previousStocks'  => $previousStocks,
            'stationName'     => $ctx['stationName'],
            'stationInfo'     => $ctx['stationInfo'],
            'stationList'     => $ctx['stationList'],
            'selectedStation' => $ctx['stationId'],
            'defaultDate'     => Carbon::today()->format('Y-m-d'),
            'reports'         => $reports,
        ]);
    }

    // ═══════════════════════════════════════════════════════
    //  STORE
    // ═══════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $ctx = $this->getOfficerAssignment($this->resolveStationId($request));

        if (! $ctx['stationId']) {
            return redirect()->route('fuel-reports.index')
                ->with('error', 'You do not have any active station assignment.');
        }

        $request->validate([
            'report_date'       => 'required|date',
            'octane_prev_stock' => 'required|numeric|min:0',
            'octane_supply'     => 'required|numeric|min:0',
            'octane_received'   => 'required|numeric|min:0',
            'octane_sales'      => 'required|numeric|min:0',
            'petrol_prev_stock' => 'required|numeric|min:0',
            'petrol_supply'     => 'required|numeric|min:0',
            'petrol_received'   => 'required|numeric|min:0',
            'petrol_sales'      => 'required|numeric|min:0',
            'diesel_prev_stock' => 'required|numeric|min:0',
            'diesel_supply'     => 'required|numeric|min:0',
            'diesel_received'   => 'required|numeric|min:0',
            'diesel_sales'      => 'required|numeric|min:0',
            'others_prev_stock' => 'required|numeric|min:0',
            'others_supply'     => 'required|numeric|min:0',
            'others_received'   => 'required|numeric|min:0',
            'others_sales'      => 'required|numeric|min:0',
            'comment'           => 'nullable|string|max:500',
        ]);

        $exists = Fuelreport::where('tag_officer_id', $ctx['officerId'])
            ->where('station_id', $ctx['stationId'])
            ->where('report_date', $request->report_date)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->with('error', 'A report for "' . $ctx['stationName'] . '" on this date already exists.');
        }

        $octaneDiff    = $request->octane_supply - $request->octane_received;
        $octaneClosing = $request->octane_prev_stock + $request->octane_received - $request->octane_sales;
        $petrolDiff    = $request->petrol_supply - $request->petrol_received;
        $petrolClosing = $request->petrol_prev_stock + $request->petrol_received - $request->petrol_sales;
        $dieselDiff    = $request->diesel_supply - $request->diesel_received;
        $dieselClosing = $request->diesel_prev_stock + $request->diesel_received - $request->diesel_sales;
        $othersDiff    = $request->others_supply - $request->others_received;
        $othersClosing = $request->others_prev_stock + $request->others_received - $request->others_sales;

        Fuelreport::create([
            'tag_officer_id'       => $ctx['officerId'],
            'station_id'           => $ctx['stationId'],
            'station_name'         => $ctx['stationName'],
            'division'             => $ctx['stationInfo']?->division ?? '',
            'thana_upazila'        => $ctx['stationInfo']?->upazila  ?? '',
            'district'             => $ctx['stationInfo']?->district ?? '',
            'report_date'          => $request->report_date,
            'comment'              => $request->comment,

            'octane_prev_stock'    => $request->octane_prev_stock,
            'octane_supply'        => $request->octane_supply,
            'octane_received'      => $request->octane_received,
            'octane_difference'    => $octaneDiff,
            'octane_sales'         => $request->octane_sales,
            'octane_closing_stock' => $octaneClosing,
            'octane_status'        => $this->getFuelStatus($octaneClosing, $octaneDiff, $request->octane_supply),

            'petrol_prev_stock'    => $request->petrol_prev_stock,
            'petrol_supply'        => $request->petrol_supply,
            'petrol_received'      => $request->petrol_received,
            'petrol_difference'    => $petrolDiff,
            'petrol_sales'         => $request->petrol_sales,
            'petrol_closing_stock' => $petrolClosing,
            'petrol_status'        => $this->getFuelStatus($petrolClosing, $petrolDiff, $request->petrol_supply),

            'diesel_prev_stock'    => $request->diesel_prev_stock,
            'diesel_supply'        => $request->diesel_supply,
            'diesel_received'      => $request->diesel_received,
            'diesel_difference'    => $dieselDiff,
            'diesel_sales'         => $request->diesel_sales,
            'diesel_closing_stock' => $dieselClosing,
            'diesel_status'        => $this->getFuelStatus($dieselClosing, $dieselDiff, $request->diesel_supply),

            'others_prev_stock'    => $request->others_prev_stock,
            'others_supply'        => $request->others_supply,
            'others_received'      => $request->others_received,
            'others_difference'    => $othersDiff,
            'others_sales'         => $request->others_sales,
            'others_closing_stock' => $othersClosing,
            'others_status'        => $this->getFuelStatus($othersClosing, $othersDiff, $request->others_supply),
        ]);

        return redirect()->route('fuel-reports.index', ['station_id' => $ctx['stationId']])
            ->with('success', 'Report saved successfully!');
    }

    // ═══════════════════════════════════════════════════════
    //  SHOW
    // ═══════════════════════════════════════════════════════
    public function show(Fuelreport $fuelReport)
    {
        $this->authorizeReport($fuelReport);
        return view('backend.tag-officer.pages.fuel-reports.show', compact('fuelReport'));
    }

    // ═══════════════════════════════════════════════════════
    //  EDIT
    // ═══════════════════════════════════════════════════════
    public function edit(Fuelreport $fuelReport)
    {
        $this->authorizeReport($fuelReport);
        $this->authorizeTodayOnly($fuelReport);
        return view('backend.tag-officer.pages.fuel-reports.edit', compact('fuelReport'));
    }

    // ═══════════════════════════════════════════════════════
    //  UPDATE
    // ═══════════════════════════════════════════════════════
    public function update(Request $request, Fuelreport $fuelReport)
    {
        $this->authorizeReport($fuelReport);
        $this->authorizeTodayOnly($fuelReport);

        // Update এ report এর নিজের station_id ব্যবহার করো
        $ctx = $this->getOfficerAssignment((int) $fuelReport->station_id);

        $request->validate([
            'report_date'       => 'required|date',
            'octane_prev_stock' => 'required|numeric|min:0',
            'octane_supply'     => 'required|numeric|min:0',
            'octane_received'   => 'required|numeric|min:0',
            'octane_sales'      => 'required|numeric|min:0',
            'petrol_prev_stock' => 'required|numeric|min:0',
            'petrol_supply'     => 'required|numeric|min:0',
            'petrol_received'   => 'required|numeric|min:0',
            'petrol_sales'      => 'required|numeric|min:0',
            'diesel_prev_stock' => 'required|numeric|min:0',
            'diesel_supply'     => 'required|numeric|min:0',
            'diesel_received'   => 'required|numeric|min:0',
            'diesel_sales'      => 'required|numeric|min:0',
            'others_prev_stock' => 'required|numeric|min:0',
            'others_supply'     => 'required|numeric|min:0',
            'others_received'   => 'required|numeric|min:0',
            'others_sales'      => 'required|numeric|min:0',
            'comment'           => 'nullable|string|max:500',
        ]);

        $exists = Fuelreport::where('tag_officer_id', $ctx['officerId'])
            ->where('station_id', $ctx['stationId'])
            ->where('report_date', $request->report_date)
            ->where('id', '!=', $fuelReport->id)
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->with('error', 'Another report for this station on this date already exists.');
        }

        $octaneDiff    = $request->octane_supply - $request->octane_received;
        $octaneClosing = $request->octane_prev_stock + $request->octane_received - $request->octane_sales;
        $petrolDiff    = $request->petrol_supply - $request->petrol_received;
        $petrolClosing = $request->petrol_prev_stock + $request->petrol_received - $request->petrol_sales;
        $dieselDiff    = $request->diesel_supply - $request->diesel_received;
        $dieselClosing = $request->diesel_prev_stock + $request->diesel_received - $request->diesel_sales;
        $othersDiff    = $request->others_supply - $request->others_received;
        $othersClosing = $request->others_prev_stock + $request->others_received - $request->others_sales;

        $fuelReport->update([
            'report_date'          => $request->report_date,
            'comment'              => $request->comment,

            'octane_prev_stock'    => $request->octane_prev_stock,
            'octane_supply'        => $request->octane_supply,
            'octane_received'      => $request->octane_received,
            'octane_difference'    => $octaneDiff,
            'octane_sales'         => $request->octane_sales,
            'octane_closing_stock' => $octaneClosing,
            'octane_status'        => $this->getFuelStatus($octaneClosing, $octaneDiff, $request->octane_supply),

            'petrol_prev_stock'    => $request->petrol_prev_stock,
            'petrol_supply'        => $request->petrol_supply,
            'petrol_received'      => $request->petrol_received,
            'petrol_difference'    => $petrolDiff,
            'petrol_sales'         => $request->petrol_sales,
            'petrol_closing_stock' => $petrolClosing,
            'petrol_status'        => $this->getFuelStatus($petrolClosing, $petrolDiff, $request->petrol_supply),

            'diesel_prev_stock'    => $request->diesel_prev_stock,
            'diesel_supply'        => $request->diesel_supply,
            'diesel_received'      => $request->diesel_received,
            'diesel_difference'    => $dieselDiff,
            'diesel_sales'         => $request->diesel_sales,
            'diesel_closing_stock' => $dieselClosing,
            'diesel_status'        => $this->getFuelStatus($dieselClosing, $dieselDiff, $request->diesel_supply),

            'others_prev_stock'    => $request->others_prev_stock,
            'others_supply'        => $request->others_supply,
            'others_received'      => $request->others_received,
            'others_difference'    => $othersDiff,
            'others_sales'         => $request->others_sales,
            'others_closing_stock' => $othersClosing,
            'others_status'        => $this->getFuelStatus($othersClosing, $othersDiff, $request->others_supply),
        ]);

        return redirect()->route('fuel-reports.index', ['station_id' => $fuelReport->station_id])
            ->with('success', 'Report updated successfully!');
    }

    // ═══════════════════════════════════════════════════════
    //  DESTROY
    // ═══════════════════════════════════════════════════════
    public function destroy(Fuelreport $fuelReport)
    {
        $this->authorizeReport($fuelReport);
        $this->authorizeTodayOnly($fuelReport);
        $stationId = $fuelReport->station_id;
        $fuelReport->delete();

        return redirect()->route('fuel-reports.index', ['station_id' => $stationId])
            ->with('success', 'The report has been deleted.');
    }

    // ═══════════════════════════════════════════════════════
    //  AJAX — Station change এ data fetch
    // ═══════════════════════════════════════════════════════
    public function getStationData(Request $request)
    {
        $ctx = $this->getOfficerAssignment($this->resolveStationId($request));

        if (! $ctx['stationId']) {
            return response()->json(['success' => false, 'message' => 'No station found.'], 422);
        }

        $previousStocks = Fuelreport::getPreviousStocks($ctx['officerId'], $ctx['stationId']);
        $reports = Fuelreport::where('tag_officer_id', $ctx['officerId'])
            ->where('station_id', $ctx['stationId'])
            ->orderBy('report_date', 'desc')
            ->paginate(15);

        $today = Carbon::today()->toDateString();

        $reportData = $reports->map(fn($r) => [
            'id'            => $r->id,
            'report_date'   => $r->report_date,
            'station_name'  => $r->station_name,
            'thana_upazila' => $r->thana_upazila,
            'district'      => $r->district,
            'comment'       => $r->comment,
            'is_today'      => Carbon::parse($r->report_date)->toDateString() === $today,

            'octane_prev_stock'    => (float) $r->octane_prev_stock,
            'octane_supply'        => (float) $r->octane_supply,
            'octane_received'      => (float) $r->octane_received,
            'octane_difference'    => (float) $r->octane_difference,
            'octane_sales'         => (float) $r->octane_sales,
            'octane_closing_stock' => (float) $r->octane_closing_stock,
            'octane_status'        => $r->octane_status,

            'petrol_prev_stock'    => (float) $r->petrol_prev_stock,
            'petrol_supply'        => (float) $r->petrol_supply,
            'petrol_received'      => (float) $r->petrol_received,
            'petrol_difference'    => (float) $r->petrol_difference,
            'petrol_sales'         => (float) $r->petrol_sales,
            'petrol_closing_stock' => (float) $r->petrol_closing_stock,
            'petrol_status'        => $r->petrol_status,

            'diesel_prev_stock'    => (float) $r->diesel_prev_stock,
            'diesel_supply'        => (float) $r->diesel_supply,
            'diesel_received'      => (float) $r->diesel_received,
            'diesel_difference'    => (float) $r->diesel_difference,
            'diesel_sales'         => (float) $r->diesel_sales,
            'diesel_closing_stock' => (float) $r->diesel_closing_stock,
            'diesel_status'        => $r->diesel_status,

            'others_prev_stock'    => (float) $r->others_prev_stock,
            'others_supply'        => (float) $r->others_supply,
            'others_received'      => (float) $r->others_received,
            'others_difference'    => (float) $r->others_difference,
            'others_sales'         => (float) $r->others_sales,
            'others_closing_stock' => (float) $r->others_closing_stock,
            'others_status'        => $r->others_status,
        ]);

        return response()->json([
            'success'         => true,
            'stationId'       => $ctx['stationId'],
            'stationName'     => $ctx['stationName'],
            'division'        => $ctx['stationInfo']?->division ?? '',
            'district'        => $ctx['stationInfo']?->district ?? '',
            'upazila'         => $ctx['stationInfo']?->upazila  ?? '',
            'previousStocks'  => $previousStocks,
            'reports'         => $reportData,
            'hasMorePages'    => $reports->hasMorePages(),
            'total'           => $reports->total(),
        ]);
    }

    // ═══════════════════════════════════════════════════════
    //  EXPORT
    // ═══════════════════════════════════════════════════════
    public function exportPdf()
    {
        return back()->with('error', 'PDF export coming soon.');
    }

    public function exportExcel()
    {
        return back()->with('error', 'Excel export coming soon.');
    }

    // ═══════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═══════════════════════════════════════════════════════
    private function authorizeReport(Fuelreport $fuelReport): void
    {
        if ($fuelReport->tag_officer_id !== Auth::id()) {
            abort(403, 'You are not authorized to view this report.');
        }
    }

    private function getFuelStatus($closing, $difference, $supply): string
    {
        if ($closing <= 0)   return 'Zero Stock';
        if ($closing < 1000) return 'Low Stock';
        return 'Normal';
    }
}
