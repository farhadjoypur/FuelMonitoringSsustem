<?php

namespace App\Http\Controllers\Backend\UNO;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Depot;
use App\Models\FillingStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FillingStationController extends Controller
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

    public function index()
    {
        $path = resource_path('data/location.json');

        if (! file_exists($path)) {
            dd('Location file not found at: '.$path);
        }

        $locations = json_decode(file_get_contents($path), true);
        $stations = FillingStation::with('company')->latest()->paginate(10);

        $totalStations = FillingStation::count();
        $activeStations = FillingStation::where('status', 'active')->count();
        $inactiveStations = FillingStation::where('status', 'inactive')->count();
        $govtStations = FillingStation::where('type', 'government')->count();
        $privateStations = FillingStation::where('type', 'private')->count();

        $divisions = FillingStation::whereNotNull('division')->distinct()->pluck('division');
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $allStationNames = FillingStation::orderBy('station_name')->get(['id', 'station_name']);
        $depots = Depot::orderBy('depot_name')->get(['id', 'depot_name']);

        return view('backend.uno.pages.fillingStation.index', compact(
            'stations',
            'totalStations', 'activeStations', 'inactiveStations',
            'govtStations', 'privateStations',
            'divisions', 'companies',
            'locations',
            'allStationNames',
            'depots'
        ));
    }

    public function create()
    {
        $companies = Company::all();

        return view('backend.uno.pages.fillingStation.create', compact('companies'));
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

        return view('backend.uno.pages.fillingStation.edit', compact('station', 'companies'));
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
