<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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

        return json_decode($json, true);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $division = $request->input('division');
        $district = $request->input('district');
        $upazila = $request->input('upazila');

        $query = User::where('role', UserRole::UNO)
            ->with('profile')
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

        return view('backend.admin.pages.uno.index', compact('unoOfficers', 'locationData', 'search'));
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
