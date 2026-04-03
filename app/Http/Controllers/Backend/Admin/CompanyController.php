<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
class CompanyController extends Controller
{

    // LIST
    public function index()
    {
        $companies = Company::latest()->paginate(10);
        return view('backend.admin.pages.companies.index', compact('companies'));
    }

    // CREATE FORM
    public function create()
    {
        return view('backend.admin.pages.companies.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:companies',
        ]);

        Company::create($request->all());

        return redirect()->route('companies.index')
            ->with('success', 'Company created successfully');
    }

    // SHOW (optional)
    public function show(string $id)
    {
        $company = Company::findOrFail($id);
        return response()->json($company);
    }

    // EDIT FORM
    public function edit(string $id)
    {
        $company = Company::findOrFail($id);
        return response()->json($company);
    }

    // UPDATE
    public function update(Request $request, string $id)
    {
        $company = Company::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:companies,code,' . $id,
        ]);

        $company->update($request->all());

        return response()->json(['success' => true, 'message' => 'Company updated successfully']);
    }

    // DELETE
    public function destroy(string $id)
    {
        Company::findOrFail($id)->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully');
    }
}
