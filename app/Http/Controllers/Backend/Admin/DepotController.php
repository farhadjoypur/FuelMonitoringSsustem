<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use Illuminate\Http\Request;

class DepotController extends Controller
{
    public function getDepot($id)
    {
        $depot = Depot::findOrFail($id);

        return response()->json($depot);
    }

    public function index(Request $request)
    {
        $path = resource_path('data/location.json');

        if (! file_exists($path)) {
            dd('Location file not found at: '.$path);
        }

        $locations = json_decode(file_get_contents($path), true);
        $query = Depot::query();

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }
        $depots = $query->latest()->paginate(15)->withQueryString();

        // Only these 3 counts as requested
        // $totalDepots   = Depot::count();
        // $activeDepots  = Depot::where('status', 'active')->count();
        // $inactiveDepots = Depot::where('status', 'inactive')->count();

        $divisions = Depot::whereNotNull('district')
            ->distinct()
            ->pluck('district');

        return view('backend.admin.pages.depots.index', compact(
            'depots',
            // 'totalDepots',
            // 'activeDepots',
            // 'inactiveDepots',
            'divisions',
            'locations'
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

        return response()->json(['success' => true, 'message' => 'Created Successfully']);
    }

    public function edit(Depot $depot)
    {
        return view('depots.edit', compact('depot'));
    }

    public function update(Request $request, Depot $depot)
    {
        $request->validate([
            'depot_name' => 'required',
            'depot_code' => 'required|unique:depots,depot_code,'.$depot->id,
            'district' => 'required',
            'contact_number' => 'required',
            'capacity' => 'required|numeric',
        ]);

        $depot->update($request->all());

        return response()->json(['success' => true, 'message' => 'Updated Successfully']);
    }

    public function destroy(Depot $depot)
    {
        $depot->delete();

        return back()->with('success', 'Deleted Successfully');
    }
}
