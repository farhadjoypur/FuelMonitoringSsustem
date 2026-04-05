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
    public function index(Request $request)
    {
        // ── Location JSON ──────────────────────────────────────────
        $locationPath = resource_path('data/location.json');
        $locations    = file_exists($locationPath)
            ? json_decode(file_get_contents($locationPath), true)
            : ['divisions' => []];

        // ── Stations ───────────────────────────────────────────────
        $stationsList = FillingStation::orderBy('station_name')
            ->get(['id', 'station_name', 'district', 'division', 'company_id'])
            ->map(function ($s) {
                return [
                    'id'         => $s->id,
                    'name'       => $s->station_name,
                    'district'   => $s->district   ?? '',
                    'division'   => $s->division   ?? '',
                    'company_id' => (string) ($s->company_id ?? ''),
                ];
            })
            ->values()
            ->toArray();

        // ── Companies ──────────────────────────────────────────────
        $companiesList = Company::orderBy('name')
            ->get(['id', 'name'])
            ->map(function ($c) {
                return ['id' => $c->id, 'name' => $c->name];
            })
            ->values()
            ->toArray();

        // ── Depots ─────────────────────────────────────────────────
        $depotsList = Depot::orderBy('depot_name')
            ->get(['id', 'depot_name'])
            ->map(function ($d) {
                return ['id' => $d->id, 'depot_name' => $d->depot_name];
            })
            ->values()
            ->toArray();

        // ── Stock Reports ──────────────────────────────────────────
        $stockReports = Fuelreport::with(['fillingStation.company'])
            ->orderByDesc('report_date')
            ->get()
            ->map(function ($r) {
                return [
                    'station_name'         => $r->station_name,
                    'district'             => $r->district      ?? '',
                    'division'             => $r->fillingStation?->division ?? '',
                    'thana_upazila'        => $r->thana_upazila ?? '',
                    'report_date'          => $r->report_date?->format('Y-m-d') ?? '',
                    'company_name'         => $r->fillingStation?->company?->name ?? '—',
                    'company_id'           => (string) ($r->fillingStation?->company_id ?? ''),
                    'depot_name'           => $r->depot_name ?? '',

                    'diesel_prev_stock'    => (float) ($r->diesel_prev_stock    ?? 0),
                    'diesel_received'      => (float) ($r->diesel_received      ?? 0),
                    'diesel_sales'         => (float) ($r->diesel_sales         ?? 0),
                    'diesel_closing_stock' => (float) ($r->diesel_closing_stock ?? 0),
                    'diesel_difference'    => (float) ($r->diesel_difference    ?? 0),

                    'petrol_prev_stock'    => (float) ($r->petrol_prev_stock    ?? 0),
                    'petrol_received'      => (float) ($r->petrol_received      ?? 0),
                    'petrol_sales'         => (float) ($r->petrol_sales         ?? 0),
                    'petrol_closing_stock' => (float) ($r->petrol_closing_stock ?? 0),
                    'petrol_difference'    => (float) ($r->petrol_difference    ?? 0),

                    'octane_prev_stock'    => (float) ($r->octane_prev_stock    ?? 0),
                    'octane_received'      => (float) ($r->octane_received      ?? 0),
                    'octane_sales'         => (float) ($r->octane_sales         ?? 0),
                    'octane_closing_stock' => (float) ($r->octane_closing_stock ?? 0),
                    'octane_difference'    => (float) ($r->octane_difference    ?? 0),
                ];
            })
            ->values()
            ->toArray();

        // ── Officer Reports ────────────────────────────────────────
        $officerReports = AssignTagOfficer::with([
            'officer',
            'fillingStation:id,station_name,district,division',
        ])
            ->latest()
            ->get()
            ->map(function ($a) {
                return [
                    'officer_name'       => $a->officer?->name  ?? '—',
                    'officer_email'      => $a->officer?->email ?? '',
                    'officer_phone'      => $a->officer?->phone ?? '',
                    'station_name'       => $a->fillingStation?->station_name ?? '—',
                    'district'           => $a->fillingStation?->district     ?? '—',
                    'division'           => $a->fillingStation?->division     ?? '—',
                    'filling_station_id' => $a->filling_station_id,
                    'status'             => $a->status,
                    'assigned_at'        => $a->created_at?->format('d M Y') ?? '—',
                ];
            })
            ->values()
            ->toArray();

        return view('backend.admin.pages.reports.index', compact(
            'locations',
            'stationsList',
            'companiesList',
            'depotsList',
            'stockReports',
            'officerReports',
        ));
    }
}
