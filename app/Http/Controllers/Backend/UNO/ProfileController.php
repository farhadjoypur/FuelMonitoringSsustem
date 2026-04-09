<?php

namespace App\Http\Controllers\Backend\UNO;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.uno.pages.profile.index');
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
        //
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
        $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:150',
                'regex:/^[^0-9!@#$%^&*()_+={}\[\]:;\"\'<>,?\/\\|`~]+$/u',
            ],
            'phone' => [
                'required',
                'string',
                'digits:11',
                'regex:/^(01[3-9]\d{8})$/',
                'unique:users,phone,'.$id,
            ],
            'email' => 'required|email:rfc,dns|max:255|unique:users,email,'.$id,
            'password' => 'nullable|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $user->email = $request->email;
            $user->phone = $request->phone;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            $profileData = ['name' => $request->name];

            if ($request->hasFile('photo')) {
                $profile = $user->profile;

                if ($profile && $profile->photo && file_exists(public_path($profile->photo))) {
                    unlink(public_path($profile->photo));
                }

                $file = $request->file('photo');
                $fileName = 'profile_'.time().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/profile'), $fileName);
                $profileData['photo'] = 'uploads/profile/'.$fileName;
            }

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            DB::commit();

            return redirect()->back()->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
