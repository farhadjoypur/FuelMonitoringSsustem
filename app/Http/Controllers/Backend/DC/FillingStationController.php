<?php

namespace App\Http\Controllers\Backend\DC;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Depot;
use App\Models\FillingStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

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
        $data = json_decode($json, true);

        $dcProfile = Auth::user()->profile;
        $dcDistrictName = $dcProfile->district ?? '';
        $dcDivisionName = $dcProfile->division ?? '';

        $filteredData = ['divisions' => []];

        foreach ($data['divisions'] as $division) {
            if ($division['name_en'] === $dcDivisionName) {

                $tempDivision = $division;
                $tempDivision['districts'] = [];

                foreach ($division['districts'] as $district) {
                    if ($district['name_en'] === $dcDistrictName) {
                        $tempDivision['districts'][] = $district;
                    }
                }

                $filteredData['divisions'][] = $tempDivision;
                break;
            }
        }

        return $filteredData;
    }

    public function index(Request $request)
    {
        try {
            $dcProfile = Auth::user()->profile;
            if (! $dcProfile || ! $dcProfile->district || ! $dcProfile->division) {
                return redirect()->back()
                    ->with('error', 'Location information is missing in your profile. Please contact administrator.');
            }

            $dcDistrict = $dcProfile->district;
            $dcDivision = $dcProfile->division;

            $query = FillingStation::with('company', 'depot')
                ->where('division', $dcDivision)
                ->where('district', $dcDistrict);

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

            if ($request->filled('upazila')) {
                $query->where('upazila', $request->upazila);
            }

            $stations = $query->latest()->paginate(10)->withQueryString();
            $companies = Company::orderBy('name', 'desc')->get(['id', 'name']);
            $allStationNames = FillingStation::where('district', $dcDistrict)
                ->orderBy('station_name')
                ->get(['id', 'station_name']);

            $depots = Depot::orderBy('depot_name')->get(['id', 'depot_name']);
            $locationData = $this->getLocationData();

            return view('backend.dc.pages.fillingStation.index', compact(
                'stations',
                'locationData',
                'companies',
                'depots',
                'allStationNames'
            ));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading data. Please try again later.');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'station_name' => 'required|string|min:3|max:150|regex:/^[\pL0-9\s\-()]+$/u|unique:filling_stations,station_name',
            'station_code' => 'nullable|string|max:50|unique:filling_stations,station_code',
            'owner_phone' => [
                'nullable',
                'unique:users,phone',
                'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/',
            ],
            'division' => 'required|string',
            'district' => 'required|string',
            'upazila' => 'required|string',
            'address' => 'nullable|string',
            'linked_depot' => 'nullable|exists:depots,id',
            'tank_capacity' => 'nullable|numeric|gt:0',
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

    public function edit($id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $station = FillingStation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'station_name' => 'required|string|min:3|max:150|regex:/^[\pL0-9\s\-()]+$/u|unique:filling_stations,station_name,'.$station->id,
            'station_code' => 'nullable|string|max:50|unique:filling_stations,station_code,'.$station->id,
            'owner_phone' => [
                'nullable',
                'unique:users,phone',
                'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/',
            ],
            'division' => 'required|string',
            'district' => 'required|string',
            'upazila' => 'required|string',
            'address' => 'nullable|string',
            'linked_depot' => 'nullable|exists:depots,id',
            'tank_capacity' => 'nullable|numeric|gt:0',
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
