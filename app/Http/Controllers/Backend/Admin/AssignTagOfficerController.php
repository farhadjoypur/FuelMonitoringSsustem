<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AssignTagOfficer;
use App\Models\FillingStation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignTagOfficerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = AssignTagOfficer::with(['officer.profile', 'fillingStation'])
            ->latest()
            ->get();

        $officers = User::with('profile')->where('role', UserRole::TAG_OFFICER)->get();
        $stations = FillingStation::all();

        return view('backend.admin.pages.assignTagOfficer.index', compact('assignments', 'officers', 'stations'));
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
            return back()->withErrors($validator)->withInput()->with('error', 'দয়া করে সব ফিল্ড ঠিকঠাক পূরণ করুন।');
        }

        try {
            if ($request->status == 'active') {
                $exists = AssignTagOfficer::where('filling_station_id', $request->filling_station_id)
                    ->where('status', 'active')
                    ->exists();

                if ($exists) {
                    return back()->with('error', 'This station already has an active officer assigned!');
                }
            }

            AssignTagOfficer::create([
                'officer_id' => $request->officer_id,
                'filling_station_id' => $request->filling_station_id,
                'assign_date' => $request->assign_date,
                'remarks' => $request->remarks,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.assign-tag-officer.index')
                ->with('success', 'Tag Officer assigned successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: '.$e->getMessage());
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

            return redirect()->route('admin.assign-tag-officer.index')
                ->with('success', 'অ্যাসাইনমেন্ট সফলভাবে আপডেট করা হয়েছে।');

        } catch (\Exception $e) {
            return back()->with('error', 'আপডেট করতে সমস্যা হয়েছে: '.$e->getMessage());
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

            return redirect()->back()->with('success', 'অ্যাসাইনমেন্টটি সফলভাবে মুছে ফেলা হয়েছে।');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'মুছে ফেলার সময় কোনো সমস্যা হয়েছে: '.$e->getMessage());
        }
    }
}
