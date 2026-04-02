<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class DcOfficerController extends Controller
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
        $dcOfficers = User::where('role', UserRole::DC)
            ->with('profile')
            ->latest()
            ->get();

        $locationData = $this->getLocationData();

        return view('backend.admin.pages.dcOfficer.index', compact('dcOfficers', 'locationData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'designation' => 'required|string|max:150',
            'department' => 'required|string|max:150',
            'phone' => 'required|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'division' => 'required',
            'district' => 'required',
            'upazilla' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('123456789'),
                'role' => UserRole::DC,
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = 'profile_'.$request->phone.'_'.time().'.'.$file->getClientOriginalExtension();
                $photoPath = $file->storeAs('uploads/officers', $fileName, 'public');
            }

            $user->profile()->create([
                'designation' => $request->designation,
                'department' => $request->department,
                'division' => $request->division,
                'district' => $request->district,
                'upazila' => $request->upazilla,
                'photo' => $photoPath,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'DC Officer added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong!')->withInput();
        }
    }

    /**
     * Store a newly created resource in storage.
     */

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
