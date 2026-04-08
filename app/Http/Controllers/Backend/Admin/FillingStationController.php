<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Depot;
use App\Models\FillingStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

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

    public function index(Request $request)
    {
        $query = FillingStation::with('company', 'depot');

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('station_name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('station_code', 'like', '%'.$searchTerm.'%')
                    ->orWhere('owner_phone', 'like', '%'.$searchTerm.'%');
            });
        }

        if ($request->filled('station_name')) {
            $query->where('station_name', $request->station_name);
        }

        if ($request->filled('division')) {
            $query->where('division', $request->division);
        }
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }
        if ($request->filled('upazila')) {
            $query->where('upazila', $request->upazila);
        }

        $stations = $query->latest()->paginate(10)->withQueryString();

        $companies = Company::orderBy('name', 'desc')->get(['id', 'name']);
        $allStationNames = FillingStation::orderBy('station_name')->get(['id', 'station_name']);
        $depots = Depot::orderBy('depot_name')->get(['id', 'depot_name']);
        $locationData = $this->getLocationData();

        return view('backend.admin.pages.fillingStation.index', compact('stations', 'locationData', 'companies', 'depots', 'allStationNames'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'station_name' => 'required|string|max:255|unique:filling_stations,station_name',
            'station_code' => 'nullable|string|max:50|unique:filling_stations,station_code',
            'owner_phone' => 'nullable|string|max:20',
            'division' => 'required|string',
            'district' => 'required|string',
            'upazila' => 'required|string',
            'address' => 'nullable|string',
            'linked_depot' => 'nullable|exists:depots,id',
            'tank_capacity' => 'nullable|numeric',
            'fuel_types' => 'nullable|array',
            'license_file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fill up all required fields correctly.');
        }

        DB::beginTransaction();
        try {
            $data = $request->except('license_file');
            if ($request->hasFile('license_file')) {
                $file = $request->file('license_file');
                $fileName = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/licenses'), $fileName);
                $data['license_file'] = 'uploads/licenses/'.$fileName;
            }

            FillingStation::create($data);

            DB::commit();

            return redirect()->back()->with('success', 'Filling Station added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong! Please try again.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $station = FillingStation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'station_name' => 'required|string|max:255|unique:filling_stations,station_name,'.$station->id,
            'station_code' => 'nullable|string|max:50|unique:filling_stations,station_code,'.$station->id,
            'owner_phone' => 'nullable|string|max:20',
            'division' => 'required|string',
            'district' => 'required|string',
            'upazila' => 'required|string',
            'address' => 'nullable|string',
            'linked_depot' => 'nullable|exists:depots,id',
            'tank_capacity' => 'nullable|numeric',
            'fuel_types' => 'nullable|array',
            'license_file' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fill up all required fields correctly.');
        }

        DB::beginTransaction();
        try {
            $data = $request->except('license_file');

            if ($request->hasFile('license_file')) {
                if ($station->license_file && file_exists(public_path($station->license_file))) {
                    unlink(public_path($station->license_file));
                }
                $file = $request->file('license_file');
                $fileName = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/licenses'), $fileName);
                $data['license_file'] = 'uploads/licenses/'.$fileName;
            }

            $station->update($data);

            DB::commit();

            return redirect()->back()->with('success', 'Filling Station updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong! Please try again.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $station = FillingStation::findOrFail($id);
            if ($station->license_file && file_exists(public_path($station->license_file))) {
                File::delete(public_path($station->license_file));
            }
            $station->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Filling Station deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong! Could not delete the station.');
        }
    }
}
