<?php

namespace App\Http\Controllers\Backend\TagOfficer;

use App\Http\Controllers\Controller;
use App\Models\AssignTagOfficer;
use App\Models\FillingStation;
use App\Models\Fuelreport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $officer   = Auth::user();
        $officerId = $officer->id;
        $today     = Carbon::today();
        $yesterday = Carbon::yesterday();

        // =============================================
        // Officer এর সব Active Assignment থেকে Stations
        // =============================================
        $assignments = AssignTagOfficer::with('fillingStation')
            ->where('officer_id', $officerId)
            ->where('status', 'active')
            ->latest()
            ->get();

        $assignedStations = $assignments
            ->map(fn($a) => $a->fillingStation)
            ->filter()
            ->values();

        // =============================================
        // Selected Station
        // =============================================
        $selectedStationId = $request->input('station_id');
        $stationInfo       = null;
        $stationName       = null;
        $location          = '';

        if ($selectedStationId) {
            $stationInfo = FillingStation::find($selectedStationId);
        } else {
            $stationInfo       = $assignedStations->first();
            $selectedStationId = $stationInfo?->id;
        }

        if ($stationInfo) {
            $stationName = $stationInfo->station_name ?? null;
            $location    = collect([
                $stationInfo->upazila  ?? null,
                $stationInfo->district ?? null,
            ])->filter()->implode(', ');
        }

        // =============================================
        // Default সব variable initialize
        // =============================================
        $lastReport  = null;
        $todayReport = null;

        // =============================================
        // Report Data
        // =============================================
        if ($selectedStationId) {

            // আজকের report
            $todayReport = Fuelreport::where('station_id', $selectedStationId)
                ->whereDate('report_date', today())
                ->first();

            // আজকের report না থাকলে সর্বশেষ report
            if (!$todayReport) {
                $lastReport = Fuelreport::where('station_id', $selectedStationId)
                    ->whereDate('report_date', '<', today())
                    ->orderBy('report_date', 'desc')
                    ->first();
            }
        }

        // ─── TODAY'S STOCK ────────────────────────────────────
        // আজকের report থাকলে → closing_stock
        // না থাকলে → last report এর closing_stock (estimated)
        $todayPetrolStock = (float) ($todayReport?->petrol_closing_stock ?? $lastReport?->petrol_closing_stock ?? 0);
        $todayDieselStock = (float) ($todayReport?->diesel_closing_stock ?? $lastReport?->diesel_closing_stock ?? 0);
        $todayOctaneStock = (float) ($todayReport?->octane_closing_stock ?? $lastReport?->octane_closing_stock ?? 0);
        $todayOthersStock = (float) ($todayReport?->others_closing_stock ?? $lastReport?->others_closing_stock ?? 0);

        // ─── TODAY'S RECEIVED ─────────────────────────────────
        $todayPetrolReceived = (float) ($todayReport?->petrol_received ?? 0);
        $todayDieselReceived = (float) ($todayReport?->diesel_received ?? 0);
        $todayOctaneReceived = (float) ($todayReport?->octane_received ?? 0);
        $todayOthersReceived = (float) ($todayReport?->others_received ?? 0);

        // ─── TODAY'S SALES ────────────────────────────────────
        $todayPetrolSold = (float) ($todayReport?->petrol_sales ?? 0);
        $todayDieselSold = (float) ($todayReport?->diesel_sales ?? 0);
        $todayOctaneSold = (float) ($todayReport?->octane_sales ?? 0);
        $todayOthersSold = (float) ($todayReport?->others_sales ?? 0);

        // ─── TODAY'S DIFFERENCE (L) ───────────────────────────
        $todayPetrolDiff = (float) ($todayReport?->petrol_difference ?? 0);
        $todayDieselDiff = (float) ($todayReport?->diesel_difference ?? 0);
        $todayOctaneDiff = (float) ($todayReport?->octane_difference ?? 0);
        $todayOthersDiff = (float) ($todayReport?->others_difference ?? 0);

        // ─── TODAY'S DIFFERENCE (%) ───────────────────────────
        $todayPetrolDiffPct = $todayPetrolReceived > 0
            ? round(($todayPetrolDiff / $todayPetrolReceived) * 100, 1) : 0;
        $todayDieselDiffPct = $todayDieselReceived > 0
            ? round(($todayDieselDiff / $todayDieselReceived) * 100, 1) : 0;
        $todayOctaneDiffPct = $todayOctaneReceived > 0
            ? round(($todayOctaneDiff / $todayOctaneReceived) * 100, 1) : 0;
        $todayOthersDiffPct = $todayOthersReceived > 0
            ? round(($todayOthersDiff / $todayOthersReceived) * 100, 1) : 0;

        // ─── IS ESTIMATED (আজকের report নেই) ─────────────────
        $isEstimatedStock = !$todayReport && $lastReport;
        $lastReportDate   = $lastReport?->report_date
            ? Carbon::parse($lastReport->report_date)->format('d M Y')
            : null;

        return view('backend.tag-officer.pages.dashboard.index', compact(
            'officer',
            'assignedStations',
            'selectedStationId',
            'stationInfo',
            'stationName',
            'location',
            'today',

            // stock
            'todayPetrolStock',
            'todayDieselStock',
            'todayOctaneStock',
            'todayOthersStock',

            // received
            'todayPetrolReceived',
            'todayDieselReceived',
            'todayOctaneReceived',
            'todayOthersReceived',

            // sales
            'todayPetrolSold',
            'todayDieselSold',
            'todayOctaneSold',
            'todayOthersSold',

            // difference L
            'todayPetrolDiff',
            'todayDieselDiff',
            'todayOctaneDiff',
            'todayOthersDiff',

            // difference %
            'todayPetrolDiffPct',
            'todayDieselDiffPct',
            'todayOctaneDiffPct',
            'todayOthersDiffPct',

            // estimated stock info
            'isEstimatedStock',
            'lastReportDate',
        ));
    }
}
