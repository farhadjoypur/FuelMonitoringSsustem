<?php

namespace App\Http\Controllers\Backend\TagOfficer;

use App\Http\Controllers\Controller;
use App\Models\AssignTagOfficer;
use App\Models\Fuelreport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $officer   = Auth::user();
        $officerId = $officer->id;
        $today     = Carbon::today();

        // =============================================
        // লগইন করা Officer এর Active Station Assignment
        // =============================================
        $assignment = AssignTagOfficer::with('fillingStation')
            ->where('officer_id', $officerId)
            ->where('status', 'active')
            ->latest()
            ->first();

        $stationId   = $assignment?->fillingStation?->id          ?? null;
        $stationName = $assignment?->fillingStation?->station_name ?? null;
        $stationInfo = $assignment?->fillingStation                ?? null;

        // =============================================
        // BASE QUERY — tag_officer_id + station_id দুটো দিয়ে
        // =============================================
        //
        // যদি station assign না থাকে → empty result দেবে
        //
        $baseQuery = function () use ($officerId, $stationId) {
            $q = Fuelreport::where('tag_officer_id', $officerId);

            if ($stationId) {
                $q->where('station_id', $stationId);
            }

            return $q;
        };

        // =============================================
        // TODAY'S REPORT
        // =============================================
        $todayReport = $stationId
            ? $baseQuery()->whereDate('report_date', $today)->first()
            : null;

        // ─── TOP 5 STAT CARDS ─────────────────────────────────
        $totalStockToday = $todayReport
            ? ($todayReport->petrol_closing_stock
                + $todayReport->diesel_closing_stock
                + $todayReport->octane_closing_stock)
            : 0;

        $totalReceivedToday = $todayReport
            ? ($todayReport->petrol_received
                + $todayReport->diesel_received
                + $todayReport->octane_received)
            : 0;

        $totalSoldToday = $todayReport
            ? ($todayReport->petrol_sales
                + $todayReport->diesel_sales
                + $todayReport->octane_sales)
            : 0;

        $totalDiffToday = $todayReport
            ? ($todayReport->petrol_difference
                + $todayReport->diesel_difference
                + $todayReport->octane_difference)
            : 0;

        $totalDiffPct = $totalReceivedToday > 0
            ? round(abs($totalDiffToday) / $totalReceivedToday * 100, 1)
            : 0;

        // গত মাসের daily average এর সাথে আজকের তুলনা
        $lastMonthReceived = $stationId
            ? $baseQuery()
                ->whereMonth('report_date', Carbon::now()->subMonth()->month)
                ->whereYear('report_date', Carbon::now()->subMonth()->year)
                ->sum(DB::raw('petrol_received + diesel_received + octane_received'))
            : 0;

        $daysInLastMonth   = Carbon::now()->subMonth()->daysInMonth;
        $dailyAvgLastMonth = $lastMonthReceived > 0 ? $lastMonthReceived / $daysInLastMonth : 0;

        $receivedChangePct = $dailyAvgLastMonth > 0
            ? round((($totalReceivedToday - $dailyAvgLastMonth) / $dailyAvgLastMonth) * 100, 1)
            : 0;

        // ─── TODAY'S STOCK per fuel ───────────────────────────
        $todayPetrolStock = $todayReport?->petrol_closing_stock ?? 0;
        $todayDieselStock = $todayReport?->diesel_closing_stock ?? 0;
        $todayOctaneStock = $todayReport?->octane_closing_stock ?? 0;

        // ─── TODAY'S SOLD per fuel ────────────────────────────
        $todayPetrolSold = $todayReport?->petrol_sales ?? 0;
        $todayDieselSold = $todayReport?->diesel_sales ?? 0;
        $todayOctaneSold = $todayReport?->octane_sales ?? 0;

        // =============================================
        // LAST 7 DAYS CHART — Received vs Sold
        // =============================================
        $last7Days = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            $report = $stationId
                ? $baseQuery()->whereDate('report_date', $date)->first()
                : null;

            $last7Days->push([
                'label'    => $date->format('d M'),
                'received' => $report
                    ? ($report->petrol_received + $report->diesel_received + $report->octane_received)
                    : 0,
                'sold'     => $report
                    ? ($report->petrol_sales + $report->diesel_sales + $report->octane_sales)
                    : 0,
            ]);
        }

        // =============================================
        // WEEKLY SUMMARY (last 7 days)
        // =============================================
        $weekStart = Carbon::today()->subDays(6)->startOfDay();

        $weeklyReceived = $stationId
            ? $baseQuery()->where('report_date', '>=', $weekStart)
                ->sum(DB::raw('petrol_received + diesel_received + octane_received'))
            : 0;

        $weeklySold = $stationId
            ? $baseQuery()->where('report_date', '>=', $weekStart)
                ->sum(DB::raw('petrol_sales + diesel_sales + octane_sales'))
            : 0;

        $weeklyDifference = $weeklyReceived - $weeklySold;

        // =============================================
        // RECENT ALERTS
        // =============================================
        $recentAlerts = collect();

        if ($stationId) {

            // ① Stock difference alert
            $baseQuery()
                ->whereRaw('ABS(petrol_difference) + ABS(diesel_difference) + ABS(octane_difference) > 0')
                ->latest('report_date')
                ->take(2)
                ->get()
                ->each(function ($r) use (&$recentAlerts) {
                    $recentAlerts->push([
                        'color'   => 'red',
                        'message' => 'Stock difference detected (' . $r->report_date->format('d M') . ')',
                    ]);
                });

            // ② আজকের report না থাকলে pending
            if (! $todayReport) {
                $recentAlerts->push([
                    'color'   => 'yellow',
                    'message' => 'Pending inspection report for today',
                ]);
            }

            // ③ সর্বশেষ dispatch received (আজ বাদে)
            $lastReceived = $baseQuery()
                ->whereDate('report_date', '!=', $today)
                ->latest('report_date')
                ->first();

            if ($lastReceived &&
                ($lastReceived->petrol_received
                    + $lastReceived->diesel_received
                    + $lastReceived->octane_received) > 0
            ) {
                $recentAlerts->push([
                    'color'   => 'blue',
                    'message' => 'New dispatch received (' . $lastReceived->report_date->format('d M') . ')',
                ]);
            }
        }

        if ($recentAlerts->isEmpty()) {
            $recentAlerts->push(['color' => 'blue', 'message' => 'No recent alerts']);
        }

        // =============================================
        // VIEW
        // =============================================
        return view('backend.tag-officer.pages.dashboard.index', compact(
            'officer',
            'stationInfo',
            'stationName',
            'stationId',

            // stat cards
            'totalStockToday',
            'totalReceivedToday',
            'totalSoldToday',
            'totalDiffToday',
            'totalDiffPct',
            'receivedChangePct',

            // today fuel breakdown
            'todayPetrolStock',
            'todayDieselStock',
            'todayOctaneStock',
            'todayPetrolSold',
            'todayDieselSold',
            'todayOctaneSold',

            // chart
            'last7Days',

            // weekly & alerts
            'weeklyReceived',
            'weeklySold',
            'weeklyDifference',
            'recentAlerts'
        ));
    }
}