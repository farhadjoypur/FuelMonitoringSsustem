<?php

namespace App\Http\Controllers\Backend\DC;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UnoController extends Controller
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
            $user = Auth::user();
            if (! $user || ! $user->profile || ! $user->profile->district) {
                return redirect()->back()->with('error', 'Location information is missing in your profile. Please contact administrator.');
            }

            $dcDistrict = $user->profile->district;

            $search = $request->input('search');
            $division = $request->input('division');
            $district = $request->input('district');
            $upazila = $request->input('upazila');

            $query = User::where('role', UserRole::UNO)
                ->with('profile')
                ->whereHas('profile', function ($q) use ($dcDistrict) {
                    $q->where('district', $dcDistrict);
                })
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('phone', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhereHas('profile', function ($pq) use ($search) {
                                $pq->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('department', 'LIKE', "%{$search}%")
                                    ->orWhere('designation', 'LIKE', "%{$search}%");
                            });
                    });
                })
                ->when($division, function ($query) use ($division) {
                    $query->whereHas('profile', function ($q) use ($division) {
                        $q->where('division', $division);
                    });
                })
                ->when($district, function ($query) use ($district) {
                    $query->whereHas('profile', function ($q) use ($district) {
                        $q->where('district', $district);
                    });
                })
                ->when($upazila, function ($query) use ($upazila) {
                    $query->whereHas('profile', function ($q) use ($upazila) {
                        $q->where('upazila', $upazila);
                    });
                });

            $unoOfficers = $query->latest()->paginate(10)->withQueryString();

            $locationData = $this->getLocationData();

            return view('backend.dc.pages.uno.index', compact('unoOfficers', 'locationData', 'search'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong! Unable to load UNO list.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'designation' => 'nullable|string|max:150',
            'department' => 'nullable|string|max:150',
            'phone' => 'required|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'division' => 'required',
            'district' => 'required',
            'upazila' => 'required',
            'password' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => 'active',
                'password' => Hash::make($request->password),
                'role' => UserRole::UNO,
            ]);

            $photoPath = null;

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = 'profile_'.$request->phone.'_'.time().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/officers'), $fileName);
                $photoPath = 'uploads/officers/'.$fileName;
            }

            $user->profile()->create([
                'name' => $request->name,
                'designation' => $request->designation,
                'department' => $request->department,
                'division' => $request->division,
                'district' => $request->district,
                'upazila' => $request->upazila,
                'photo' => $photoPath,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'UNO Officer added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
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
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.$id,
            'phone' => 'required|string|unique:users,phone,'.$id,
            'designation' => 'required|string',
            'department' => 'required|string',
            'division' => 'required',
            'district' => 'required',
            'upazila' => 'nullable',
            'status' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|min:6',
        ]);

        $userData = [
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        $profileData = [
            'name' => $request->name,
            'designation' => $request->designation,
            'department' => $request->department,
            'division' => $request->division,
            'district' => $request->district,
            'upazila' => $request->upazila,
        ];

        if ($request->hasFile('photo')) {
            if ($user->profile && $user->profile->photo && File::exists(public_path($user->profile->photo))) {
                File::delete(public_path($user->profile->photo));
            }

            $fileName = time().'.'.$request->photo->extension();
            $request->photo->move(public_path('uploads/officers'), $fileName);
            $profileData['photo'] = 'uploads/officers/'.$fileName;
        }

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }

        return redirect()->back()->with('success', 'Officer updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->profile && $user->profile->photo) {
            $fullPath = public_path($user->profile->photo);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
        $user->delete();

        return redirect()->back()->with('success', 'UNO Officer deleted successfully!');
    }
}
