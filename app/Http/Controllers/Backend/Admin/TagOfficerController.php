<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class TagOfficerController extends Controller
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
        $tagOfficers = User::where('role', UserRole::TAG_OFFICER)
            ->with('profile')
            ->latest()
            ->get();

        $locationData = $this->getLocationData();

        return view('backend.admin.pages.tagOfficer.index', compact('tagOfficers', 'locationData'));
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
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('123456789'),
                'role' => UserRole::TAG_OFFICER,
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
                'upazila' => $request->upazilla,
                'photo' => $photoPath,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Tag Officer added successfully!');

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

        $user->update([
            // 'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $profileData = [
            'name' => $request->name,
            'designation' => $request->designation,
            'division' => $request->division,
            'district' => $request->district,
            'upazilla' => $request->upazilla,
        ];

        if ($request->hasFile('photo')) {

            if ($user->profile->photo && File::exists(public_path($user->profile->photo))) {
                File::delete(public_path($user->profile->photo));
            }

            $fileName = time().'.'.$request->photo->extension();
            $request->photo->move(public_path('uploads/officers'), $fileName);
            $profileData['photo'] = 'uploads/officers/'.$fileName;
        }

        $user->profile()->update($profileData);

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

        return redirect()->back()->with('success', 'DC Officer deleted successfully!');
    }
}
