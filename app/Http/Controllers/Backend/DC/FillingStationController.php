<?php

namespace App\Http\Controllers\Backend\DC;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Depot;
use App\Models\FillingStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class FillingStationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

        $userProfile = Auth::user()->profile;
        if (! $userProfile || ! $userProfile->district) {
            return back()->with('error', 'Your profile does not have a district assigned. Please update your profile.');
        }
        $dcDistrict = $userProfile->district;
        $dcDivision = $userProfile->division;

        $baseQuery = FillingStation::with('company')
            ->where('district', $dcDistrict)
            ->where('division', $dcDivision);

        $stations = $baseQuery->latest()->paginate(10)->withQueryString();
        $divisions = FillingStation::whereNotNull('division')->distinct()->pluck('division');
        $companies = Company::orderBy('name')->get(['id', 'name']);

        $allStationNames = FillingStation::where('district', $dcDistrict)
            ->orderBy('station_name')
            ->get(['id', 'station_name']);

        $depots = Depot::orderBy('depot_name')->get(['id', 'depot_name']);

        return view('backend.dc.pages.fillingStation.index', compact(
            'stations',
            'divisions', 'companies',
            'locations',
            'allStationNames',
            'depots'
        ));
    }

    public function create()
    {
        $companies = Company::all();

        return view('backend.dc.pages.fillingStation.create', compact('companies'));
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

        return view('backend.dc.pages.fillingStation.edit', compact('station', 'companies'));
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
