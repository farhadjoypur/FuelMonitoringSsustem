<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use Illuminate\Http\Request;

class DepotController extends Controller
{
    /**
     * GET /admin/depots/{id}/get
     * Returns a single depot as JSON (used by edit modal via AJAX)
     */
    public function getDepot($id)
    {
        $depot = Depot::findOrFail($id);

        return response()->json($depot);
    }

    /**
     * GET /admin/depots
     */
    public function index(Request $request)
    {
        $path = resource_path('data/location.json');

        if (! file_exists($path)) {
            dd('Location file not found at: ' . $path);
        }

        $locations = json_decode(file_get_contents($path), true);

        $query = Depot::query();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('depot_name', 'like', "%{$search}%")
                    ->orWhere('depot_code', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%");
            });
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        // FIX #2 — pagination 25 so records don't swap between pages
        $depots = $query->latest()->paginate(10)->withQueryString();

        $totalDepots    = Depot::count();
        $activeDepots   = Depot::where('status', 'active')->count();
        $inactiveDepots = Depot::where('status', 'inactive')->count();

        return view('backend.admin.pages.depots.index', compact(
            'depots',
            'totalDepots',
            'activeDepots',
            'inactiveDepots',
            'locations'
        ));
    }

    /**
     * POST /admin/depots
     */
    public function store(Request $request)
    {
        // FIX #4 — validation errors are returned as JSON (422) so JS can show them
        $request->validate([
            'depot_name'    => 'required|string|max:255',
            'depot_code'    => 'required|string|unique:depots,depot_code|max:50',
            'district'      => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'capacity'      => 'required|numeric|min:1',
        ]);

        Depot::create([
            'depot_name'      => $request->depot_name,
            'depot_code'      => $request->depot_code,
            'district'        => $request->district,
            'contact_number'  => $request->contact_number,
            'email'           => $request->email,
            'capacity'        => $request->capacity,
            'number_of_tanks' => $request->number_of_tanks,
            'full_address'    => $request->full_address,
            'status'          => $request->status ?? 'inactive',
            'remarks'         => $request->remarks,
        ]);

        return response()->json(['success' => true, 'message' => 'Depot created successfully!']);
    }

    /**
     * POST /admin/depots/{depot}  (method-spoofed PUT)
     */
    public function update(Request $request, Depot $depot)
    {
        // FIX #4 — same JSON validation response
        $request->validate([
            'depot_name'    => 'required|string|max:255',
            'depot_code'    => 'required|string|max:50|unique:depots,depot_code,' . $depot->id,
            'district'      => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'capacity'      => 'required|numeric|min:1',
        ]);

        $depot->update([
            'depot_name'      => $request->depot_name,
            'depot_code'      => $request->depot_code,
            'district'        => $request->district,
            'contact_number'  => $request->contact_number,
            'email'           => $request->email,
            'capacity'        => $request->capacity,
            'number_of_tanks' => $request->number_of_tanks,
            'full_address'    => $request->full_address,
            'status'          => $request->status ?? 'inactive',
            'remarks'         => $request->remarks,
        ]);

        return response()->json(['success' => true, 'message' => 'Depot updated successfully!']);
    }

    /**
     * DELETE /admin/depots/{depot}
     */
    public function destroy(Depot $depot)
    {
        $depot->delete();

        return back()->with('success', 'Depot deleted successfully!');
    }
}
