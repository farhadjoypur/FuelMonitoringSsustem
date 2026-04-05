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

        // =============================================
        // Officer এর সব Active Assignment থেকে Stations
        // =============================================
        $assignments = AssignTagOfficer::with('fillingStation')
            ->where('officer_id', $officerId)
            ->where('status', 'active')
            ->latest()
            ->get();

        // Officer এর assigned সব station list
        $assignedStations = $assignments
            ->map(function ($a) {
                return $a->fillingStation;
            })
            ->filter()
            ->values();

        // =============================================
        // Selected Station (request থেকে অথবা প্রথমটা)
        // =============================================
        $selectedStationId = $request->input('station_id');

        if ($selectedStationId) {
            $stationInfo = FillingStation::find($selectedStationId);
        } else {
            $stationInfo = $assignedStations->first();
            $selectedStationId = $stationInfo?->id;
        }

        $stationName = $stationInfo?->station_name ?? null;

        // Location string: upazila, district
        $location = collect([
            $stationInfo?->upazila   ?? null,
            $stationInfo?->district  ?? null,
        ])->filter()->implode(', ');

        // =============================================
        // Today's Report for selected station
        // =============================================
        $todayReport = null;
        if ($selectedStationId) {
            $todayReport = Fuelreport::where('station_name', $stationName)
                ->whereDate('report_date', $today)
                ->first();
        }

        // ─── TODAY'S STOCK ────────────────────────────────────
        $todayPetrolStock  = (float) ($todayReport?->petrol_closing_stock  ?? 0);
        $todayDieselStock  = (float) ($todayReport?->diesel_closing_stock  ?? 0);
        $todayOctaneStock  = (float) ($todayReport?->octane_closing_stock  ?? 0);

        // ─── TODAY'S RECEIVED ─────────────────────────────────
        $todayPetrolReceived = (float) ($todayReport?->petrol_received ?? 0);
        $todayDieselReceived = (float) ($todayReport?->diesel_received ?? 0);
        $todayOctaneReceived = (float) ($todayReport?->octane_received ?? 0);

        // ─── TODAY'S SALES ────────────────────────────────────
        $todayPetrolSold  = (float) ($todayReport?->petrol_sales ?? 0);
        $todayDieselSold  = (float) ($todayReport?->diesel_sales ?? 0);
        $todayOctaneSold  = (float) ($todayReport?->octane_sales ?? 0);

        // ─── TODAY'S DIFFERENCE (L) ───────────────────────────
        // difference = supply - received (already stored in DB)
        $todayPetrolDiff  = (float) ($todayReport?->petrol_difference ?? 0);
        $todayDieselDiff  = (float) ($todayReport?->diesel_difference ?? 0);
        $todayOctaneDiff  = (float) ($todayReport?->octane_difference ?? 0);

        // ─── TODAY'S DIFFERENCE (%) ───────────────────────────
        // % = (difference / received) * 100
        $todayPetrolDiffPct = $todayPetrolReceived > 0
            ? round(($todayPetrolDiff / $todayPetrolReceived) * 100, 1) : 0;

        $todayDieselDiffPct = $todayDieselReceived > 0
            ? round(($todayDieselDiff / $todayDieselReceived) * 100, 1) : 0;

        $todayOctaneDiffPct = $todayOctaneReceived > 0
            ? round(($todayOctaneDiff / $todayOctaneReceived) * 100, 1) : 0;

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

            // received
            'todayPetrolReceived',
            'todayDieselReceived',
            'todayOctaneReceived',

            // sales
            'todayPetrolSold',
            'todayDieselSold',
            'todayOctaneSold',

            // difference L
            'todayPetrolDiff',
            'todayDieselDiff',
            'todayOctaneDiff',

            // difference %
            'todayPetrolDiffPct',
            'todayDieselDiffPct',
            'todayOctaneDiffPct',
        ));
    }
}
