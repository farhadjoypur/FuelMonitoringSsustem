<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Depot;

class DepotController extends Controller
{
    public function getDepot($id)
    {
        $depot = Depot::findOrFail($id);
        return response()->json($depot);
    }
    
    public function index()
    {
        $depots = Depot::latest()->paginate(15);
    
        // Only these 3 counts as requested
        $totalDepots   = Depot::count();
        $activeDepots  = Depot::where('status', 'active')->count();
        $inactiveDepots = Depot::where('status', 'inactive')->count();
    
        $divisions = Depot::whereNotNull('district')
                          ->distinct()
                          ->pluck('district');
    
        return view('backend.admin.pages.depots.index', compact(
            'depots',
            'totalDepots',
            'activeDepots',
            'inactiveDepots',
            'divisions'
        ));
    }

    public function create()
    {
        return view('depots.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'depot_name' => 'required',
            'depot_code' => 'required|unique:depots',
            'district' => 'required',
            'contact_number' => 'required',
            'capacity' => 'required|numeric',
        ]);

        Depot::create($request->all());

        return redirect()->route('depots.index')->with('success', 'Created Successfully');
    }

    public function edit(Depot $depot)
    {
        return view('depots.edit', compact('depot'));
    }

    public function update(Request $request, Depot $depot)
    {
        $request->validate([
            'depot_name' => 'required',
            'depot_code' => 'required|unique:depots,depot_code,' . $depot->id,
            'district' => 'required',
            'contact_number' => 'required',
            'capacity' => 'required|numeric',
        ]);

        $depot->update($request->all());

        return redirect()->route('depots.index')->with('success', 'Updated Successfully');
    }

    public function destroy(Depot $depot)
    {
        $depot->delete();

        return back()->with('success', 'Deleted Successfully');
    }
}
