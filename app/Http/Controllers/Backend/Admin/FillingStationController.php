<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FillingStation;
use App\Models\Company;

class FillingStationController extends Controller
{
    public function index()
    {
        $stations = FillingStation::with('company')->latest()->paginate(10);

        $totalStations    = FillingStation::count();
        $activeStations   = FillingStation::where('status', 'active')->count();
        $inactiveStations = FillingStation::where('status', 'inactive')->count();
        $govtStations     = FillingStation::where('type', 'government')->count();
        $privateStations  = FillingStation::where('type', 'private')->count();

        $divisions = FillingStation::whereNotNull('division')->distinct()->pluck('division');
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('backend.admin.pages.fillingStation.index', compact(
            'stations',
            'totalStations', 'activeStations', 'inactiveStations',
            'govtStations', 'privateStations',
            'divisions', 'companies'
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
            'company_id'   => 'required',
            'station_name' => 'required',
            'station_code' => 'required|unique:filling_stations',
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
            'message' => 'Created successfully'
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
        $station  = FillingStation::findOrFail($id);
        $companies = Company::all();
        return view('backend.admin.pages.fillingStation.edit', compact('station', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $station = FillingStation::findOrFail($id);

        $request->validate([
            'station_name' => 'required',
            'station_code' => 'required|unique:filling_stations,station_code,' . $id,
        ]);

        $data = $request->all();
        $data['fuel_types'] = $request->fuel_types ?? [];

        if ($request->hasFile('license_file')) {
            $data['license_file'] = $request->file('license_file')
                ->store('licenses', 'public');
        }

        $station->update($data);

        return redirect()->route('stations.index')
            ->with('success', 'Station updated successfully');
    }

    public function destroy($id)
    {
        FillingStation::findOrFail($id)->delete();

        return redirect()->route('stations.index')
            ->with('success', 'Deleted successfully');
    }
}