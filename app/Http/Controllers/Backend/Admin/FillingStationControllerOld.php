<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Depot;
use App\Models\FillingStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FillingStationControllerOld extends Controller
{
    private function getLocationData()
    {
        $path = resource_path('data/location.json');

        if (! File::exists($path)) {
            return [];
        }

        $json = File::get($path);

        return json_decode($json, true);
    }

    public function index(Request $request)
    {
        // ── 1. Base query with eager-loaded relations ──────────────────────
        $reportQuery = FillingStation::with('company', 'depot')->latest();

        // ── 2. Apply filters only when parameters are present ─────────────
        if ($request->filled('search')) {
            $keyword = $request->search;
            $reportQuery->where(function ($q) use ($keyword) {
                $q->where('station_name', 'like', "%{$keyword}%")
                    ->orWhere('station_code', 'like', "%{$keyword}%")
                    ->orWhere('owner_name', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('station_name')) {
            $reportQuery->where('station_name', $request->station_name);
        }

        if ($request->filled('division')) {
            $reportQuery->where('division', $request->division);
        }

        if ($request->filled('district')) {
            $reportQuery->where('district', $request->district);
        }

        if ($request->filled('upazila')) {
            $reportQuery->where('upazila', $request->upazila);
        }

        if ($request->filled('company_id')) {
            $reportQuery->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $reportQuery->where('status', $request->status);
        }

        // ── 3. Paginate results (preserve filter params in pagination links) ─
        $filteredReports = $reportQuery->paginate(15)->withQueryString();

        // ── 4. AJAX request → return JSON with rendered table HTML ─────────
        if ($request->ajax()) {
            $tableHtml = view(
                'backend.admin.pages.fillingStation.table',
                compact('filteredReports')
            )->render();

            return response()->json([
                'success' => true,
                'html' => $tableHtml,
                'total' => $filteredReports->total(),
            ]);
        }

        // ── 5. Normal request → return full index view with sidebar data ───
        $path = resource_path('data/location.json');
        $locations = file_exists($path) ? json_decode(file_get_contents($path), true) : ['divisions' => []];

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $allStationNames = FillingStation::orderBy('station_name')->get(['id', 'station_name']);
        $depots = Depot::orderBy('depot_name')->get(['id', 'depot_name']);

        return view('backend.admin.pages.fillingStation.index', compact(
            'filteredReports',
            'locations',
            'companies',
            'allStationNames',
            'depots'
        ));
    }

    public function create()
    {
        $companies = Company::all();

        return view('backend.admin.pages.fillingStation.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required',
            'station_name' => 'required|unique:filling_stations,station_name',
            'station_code' => 'required|unique:filling_stations,station_code',
        ]);

        $data = $request->all();
        $data['fuel_types'] = $request->fuel_types ?? [];

        if ($request->hasFile('license_file')) {
            $data['license_file'] = $request->file('license_file')
                ->store('licenses', 'public');
        }

        FillingStation::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
        ]);
    }

    // ── NEW: return station JSON for edit modal ──
    public function getStation($id)
    {
        $station = FillingStation::findOrFail($id);

        return response()->json($station);
    }

    public function edit($id)
    {
        $station = FillingStation::findOrFail($id);
        $companies = Company::all();
        $depots = Depot::all();

        return view('backend.admin.pages.fillingStation.edit', compact('station', 'companies', 'depots'));
    }

    public function update(Request $request, $id)
    {
        $station = FillingStation::findOrFail($id);

        $request->validate([
            'station_name' => 'required',
            'station_code' => 'required|unique:filling_stations,station_code,'.$id,
        ]);

        $data = $request->all();
        $data['fuel_types'] = $request->fuel_types ?? [];

        if ($request->hasFile('license_file')) {
            $data['license_file'] = $request->file('license_file')
                ->store('licenses', 'public');
        }

        $station->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
        ]);
    }

    public function destroy($id)
    {
        FillingStation::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}