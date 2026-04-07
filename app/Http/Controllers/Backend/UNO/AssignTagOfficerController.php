<?php

namespace App\Http\Controllers\Backend\UNO;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AssignTagOfficer;
use App\Models\FillingStation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AssignTagOfficerController extends Controller
{
    private function getLocationData()
    {
        $path = resource_path('data/location.json');

        if (! File::exists($path)) {
            return [];
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        $userProfile = Auth::user()->profile;
        $targetDivision = $userProfile->division;
        $targetDistrict = $userProfile->district;
        $targetUpazila = $userProfile->upazila;

        $filteredData = ['divisions' => []];

        foreach ($data['divisions'] as $division) {
            if ($division['name_en'] === $targetDivision) {
                $newDivision = $division;
                $newDivision['districts'] = [];

                foreach ($division['districts'] as $district) {
                    if ($district['name_en'] === $targetDistrict) {
                        $newDistrict = $district;
                        $newDistrict['police_stations'] = [];

                        foreach ($district['police_stations'] as $upazila) {
                            if ($upazila['name_en'] === $targetUpazila) {
                                $newDistrict['police_stations'][] = $upazila;
                            }
                        }

                        $newDivision['districts'][] = $newDistrict;
                    }
                }
                $filteredData['divisions'][] = $newDivision;
            }
        }

        return $filteredData;
    }

    public function index(Request $request)
    {
        $unoProfile = Auth::user()->profile;

        if (! $unoProfile || ! $unoProfile->district || ! $unoProfile->upazila) {
            return redirect()->back()->with('error', 'Upazila is not set to your profile. please contact with admin!');
        }

        $unoDistrict = $unoProfile->district;
        $unoUpazila = $unoProfile->upazila;
        $search = $request->input('search');

        $query = AssignTagOfficer::with(['officer.profile', 'fillingStation'])
            ->whereHas('fillingStation', function ($q) use ($unoDistrict, $unoUpazila) {
                $q->where('district', $unoDistrict)
                    ->where('upazila', $unoUpazila); 
            });

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->whereHas('officer.profile', function ($profileQuery) use ($search) {
                    $profileQuery->where('name', 'like', '%'.$search.'%');
                })
                    ->orWhereHas('fillingStation', function ($stationQuery) use ($search) {
                        $stationQuery->where('station_name', 'like', '%'.$search.'%');
                    });
            });
        });

        $query->when($request->upazila, function ($q) use ($request) {
            $q->whereHas('fillingStation', function ($sq) use ($request) {
                $sq->where('upazila', $request->upazila);
            });
        });

        $assignments = $query->latest()->paginate(10)->withQueryString();

        $officers = User::where('role', UserRole::TAG_OFFICER)
            ->whereHas('profile', function ($q) use ($unoDistrict, $unoUpazila) {
                $q->where('district', $unoDistrict)
                    ->where('upazila', $unoUpazila);
            })
            ->with('profile:id,user_id,name,upazila,district')
            ->get();

        $stations = FillingStation::where('district', $unoDistrict)
            ->where('upazila', $unoUpazila) 
            ->select('id', 'station_name', 'upazila', 'district', 'division')
            ->get();

        $locationData = $this->getLocationData();

        return view('backend.uno.pages.assignTagOfficer.index', compact(
            'assignments',
            'officers',
            'stations',
            'locationData',
            'search' // সার্চ ভ্যালু পাস করা হলো
        ));
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
            'officer_id' => 'required|exists:users,id',
            'filling_station_id' => 'required|exists:filling_stations,id',
            'assign_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fill in all required fields correctly.');
        }

        try {
            $alreadyAssigned = AssignTagOfficer::where('filling_station_id', $request->filling_station_id)
                ->where('officer_id', $request->officer_id)
                ->exists();

            if ($alreadyAssigned) {
                return back()
                    ->withInput()
                    ->with('error', 'This officer is already assigned to this station!');
            }

            if ($request->status == 'active') {
                $existsActive = AssignTagOfficer::where('filling_station_id', $request->filling_station_id)
                    ->where('status', 'active')
                    ->exists();

                if ($existsActive) {
                    return back()
                        ->withInput()
                        ->with('error', 'An active officer is already assigned to this filling station!');
                }
            }

            AssignTagOfficer::create([
                'officer_id' => $request->officer_id,
                'filling_station_id' => $request->filling_station_id,
                'assign_date' => $request->assign_date,
                'remarks' => $request->remarks,
                'status' => $request->status,
            ]);

            return redirect()->route('uno.assign-tag-officer.index')
                ->with('success', 'Tag Officer assigned successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Something went wrong: '.$e->getMessage());
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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'officer_id' => 'required|exists:users,id',
            'filling_station_id' => 'required|exists:filling_stations,id',
            'assign_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $assignment = AssignTagOfficer::findOrFail($id);
            $assignment->update($request->all());

            return redirect()->back()->with('success', 'Assignment update successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $assignment = AssignTagOfficer::findOrFail($id);
            $assignment->delete();

            return redirect()->back()->with('success', 'Assignment deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong '.$e->getMessage());
        }
    }
}
