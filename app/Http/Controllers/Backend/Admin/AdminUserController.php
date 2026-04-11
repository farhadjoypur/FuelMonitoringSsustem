<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $baseQuery = User::where('role', UserRole::ADMIN);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('status', 'active')->count(),
            'divisions' => Profile::whereIn('user_id', (clone $baseQuery)->pluck('id'))
                ->distinct('division')->count('division'),
            'districts' => Profile::whereIn('user_id', (clone $baseQuery)->pluck('id'))
                ->distinct('district')->count('district'),
        ];

        $admins = User::where('role', UserRole::ADMIN)
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
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('backend.admin.pages.adminUser.index', compact('admins', 'search', 'stats'));
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
            'name' => [
                'required',
                'string',
                'min:3',
                'max:150',
                'regex:/^[^0-9!@#$%^&*()_+={}\[\]:;\"\'<>,?\/\\|`~]+$/u',
            ],
            'designation' => 'nullable|string|min:2|max:150|regex:/^[\pL\s.\-()]+$/u',
            'department' => 'nullable|string|min:2|max:150|regex:/^[\pL\s.\-()]+$/u',
            'phone' => [
                'required',
                'unique:users,phone',
                'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/',
            ],
            'email' => 'nullable|email:rfc,dns|unique:users,email',
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
                'role' => UserRole::ADMIN,
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
                'photo' => $photoPath,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Admin added successfully!');

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
            'name' => [
                'required',
                'string',
                'min:3',
                'max:150',
                'regex:/^[^0-9!@#$%^&*()_+={}\[\]:;\"\'<>,?\/\\|`~]+$/u',
            ],
            'email' => 'nullable|email:rfc,dns|unique:users,email,'.$id,
            'phone' => [
                'required',
                'string',
                'digits:11',
                'regex:/^(01[3-9]\d{8})$/',
                'unique:users,phone,'.$id,
            ],
            'designation' => 'nullable|string|min:2|max:150|regex:/^[\pL\s.\-()]+$/u',
            'department' => 'nullable|string|min:2|max:150|regex:/^[\pL\s.\-()]+$/u',
            'status' => 'required|string',
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

        return redirect()->back()->with('success', 'Admin updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($id == 1 && $user->role == UserRole::ADMIN) {
            return redirect()->back()->with('error', 'Administrator cannot be deleted for security reasons!');
        }

        if ($user->profile && $user->profile->photo) {
            $fullPath = public_path($user->profile->photo);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
        $user->delete();

        return redirect()->back()->with('success', 'Admin has been deleted successfully!');
    }
}
