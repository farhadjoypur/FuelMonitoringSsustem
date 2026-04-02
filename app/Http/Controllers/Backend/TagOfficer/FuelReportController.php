<?php

namespace App\Http\Controllers\Backend\TagOfficer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\Fuelreport;

class FuelReportController extends Controller
{

    /**
     * সব রিপোর্টের লিস্ট
     */
    public function index(Request $request)
    {
        $query = FuelReport::orderBy('report_date', 'desc');
 
        // ফিল্টার: স্টেশন নাম বা তারিখ দিয়ে খোঁজা
        if ($request->filled('station_name')) {
            $query->where('station_name', 'like', '%' . $request->station_name . '%');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('report_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('report_date', '<=', $request->to_date);
        }
 
        $reports = $query->paginate(15)->withQueryString();
 
        return view('backend.tag-officer.pages.fuel-reports.index', compact('reports'));
    }
 
    /**
     * নতুন রিপোর্ট তৈরির ফর্ম
     * আগের রিপোর্ট থেকে Previous Stock Auto-fill হবে
     */
    public function create(Request $request)
    {
        // যদি station_name query string এ থাকে তাহলে সেটা দিয়ে previous stocks আনো
        $stationName = $request->get('station_name', '');
        $previousStocks = [];
 
        if ($stationName) {
            $previousStocks = FuelReport::getPreviousStocks($stationName);
        }
 
        // সব distinct station নাম (dropdown এর জন্য)
        $stations = FuelReport::select('station_name', 'thana_upazila', 'district')
            ->distinct()
            ->orderBy('station_name')
            ->get();
 
        return view('backend.tag-officer.pages.fuel-reports.create', compact('previousStocks', 'stations', 'stationName'));
    }
 
    /**
     * নতুন রিপোর্ট সেভ করা
     */
    public function store(Request $request)
    {
        $request->validate([
            'station_name'    => 'required|string|max:255',
            'thana_upazila'   => 'required|string|max:255',
            'district'        => 'required|string|max:255',
            'report_date'     => 'required|date',
 
            'petrol_prev_stock' => 'required|numeric|min:0',
            'petrol_supply'     => 'required|numeric|min:0',
            'petrol_received'   => 'required|numeric|min:0',
            'petrol_sales'      => 'required|numeric|min:0',
 
            'diesel_prev_stock' => 'required|numeric|min:0',
            'diesel_supply'     => 'required|numeric|min:0',
            'diesel_received'   => 'required|numeric|min:0',
            'diesel_sales'      => 'required|numeric|min:0',
 
            'octane_prev_stock' => 'required|numeric|min:0',
            'octane_supply'     => 'required|numeric|min:0',
            'octane_received'   => 'required|numeric|min:0',
            'octane_sales'      => 'required|numeric|min:0',
        ], [
            'report_date.unique' => 'এই তারিখে এই স্টেশনের রিপোর্ট ইতোমধ্যে সেভ করা আছে।',
        ]);
 
        // Duplicate check
        $exists = FuelReport::where('station_name', $request->station_name)
            ->where('report_date', $request->report_date)
            ->exists();
 
        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'এই তারিখে "' . $request->station_name . '" স্টেশনের রিপোর্ট ইতোমধ্যে সেভ করা আছে। Edit করুন।');
        }
 
        // Auto Calculate
        $petrolDiff    = $request->petrol_supply - $request->petrol_received;
        $petrolClosing = $request->petrol_prev_stock + $request->petrol_received - $request->petrol_sales;
 
        $dieselDiff    = $request->diesel_supply - $request->diesel_received;
        $dieselClosing = $request->diesel_prev_stock + $request->diesel_received - $request->diesel_sales;
 
        $octaneDiff    = $request->octane_supply - $request->octane_received;
        $octaneClosing = $request->octane_prev_stock + $request->octane_received - $request->octane_sales;
 
        FuelReport::create([
            'station_name'  => $request->station_name,
            'thana_upazila' => $request->thana_upazila,
            'district'      => $request->district,
            'report_date'   => $request->report_date,
 
            'petrol_prev_stock'    => $request->petrol_prev_stock,
            'petrol_supply'        => $request->petrol_supply,
            'petrol_received'      => $request->petrol_received,
            'petrol_difference'    => $petrolDiff,
            'petrol_sales'         => $request->petrol_sales,
            'petrol_closing_stock' => $petrolClosing,
 
            'diesel_prev_stock'    => $request->diesel_prev_stock,
            'diesel_supply'        => $request->diesel_supply,
            'diesel_received'      => $request->diesel_received,
            'diesel_difference'    => $dieselDiff,
            'diesel_sales'         => $request->diesel_sales,
            'diesel_closing_stock' => $dieselClosing,
 
            'octane_prev_stock'    => $request->octane_prev_stock,
            'octane_supply'        => $request->octane_supply,
            'octane_received'      => $request->octane_received,
            'octane_difference'    => $octaneDiff,
            'octane_sales'         => $request->octane_sales,
            'octane_closing_stock' => $octaneClosing,
        ]);
 
        return redirect()
            ->route('fuel-reports.index')
            ->with('success', 'রিপোর্ট সফলভাবে সেভ হয়েছে!');
    }
 
    /**
     * একটি রিপোর্টের বিস্তারিত দেখা
     */
    public function show(FuelReport $fuelReport)
    {
        return view('backend.tag-officer.pages.fuel-reports.show', compact('fuelReport'));
    }
 
    /**
     * রিপোর্ট Edit ফর্ম
     */
    public function edit(FuelReport $fuelReport)
    {
        return view('backend.tag-officer.pages.fuel-reports.edit', compact('fuelReport'));
    }
 
    /**
     * রিপোর্ট Update করা
     */
    public function update(Request $request, FuelReport $fuelReport)
    {
        $request->validate([
            'station_name'  => 'required|string|max:255',
            'thana_upazila' => 'required|string|max:255',
            'district'      => 'required|string|max:255',
            'report_date'   => 'required|date',
 
            'petrol_prev_stock' => 'required|numeric|min:0',
            'petrol_supply'     => 'required|numeric|min:0',
            'petrol_received'   => 'required|numeric|min:0',
            'petrol_sales'      => 'required|numeric|min:0',
 
            'diesel_prev_stock' => 'required|numeric|min:0',
            'diesel_supply'     => 'required|numeric|min:0',
            'diesel_received'   => 'required|numeric|min:0',
            'diesel_sales'      => 'required|numeric|min:0',
 
            'octane_prev_stock' => 'required|numeric|min:0',
            'octane_supply'     => 'required|numeric|min:0',
            'octane_received'   => 'required|numeric|min:0',
            'octane_sales'      => 'required|numeric|min:0',
        ]);
 
        // Duplicate check (নিজের ID বাদ দিয়ে)
        $exists = FuelReport::where('station_name', $request->station_name)
            ->where('report_date', $request->report_date)
            ->where('id', '!=', $fuelReport->id)
            ->exists();
 
        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'এই তারিখে এই স্টেশনের অন্য একটি রিপোর্ট ইতোমধ্যে আছে।');
        }
 
        // Auto Calculate
        $petrolDiff    = $request->petrol_supply - $request->petrol_received;
        $petrolClosing = $request->petrol_prev_stock + $request->petrol_received - $request->petrol_sales;
 
        $dieselDiff    = $request->diesel_supply - $request->diesel_received;
        $dieselClosing = $request->diesel_prev_stock + $request->diesel_received - $request->diesel_sales;
 
        $octaneDiff    = $request->octane_supply - $request->octane_received;
        $octaneClosing = $request->octane_prev_stock + $request->octane_received - $request->octane_sales;
 
        $fuelReport->update([
            'station_name'  => $request->station_name,
            'thana_upazila' => $request->thana_upazila,
            'district'      => $request->district,
            'report_date'   => $request->report_date,
 
            'petrol_prev_stock'    => $request->petrol_prev_stock,
            'petrol_supply'        => $request->petrol_supply,
            'petrol_received'      => $request->petrol_received,
            'petrol_difference'    => $petrolDiff,
            'petrol_sales'         => $request->petrol_sales,
            'petrol_closing_stock' => $petrolClosing,
 
            'diesel_prev_stock'    => $request->diesel_prev_stock,
            'diesel_supply'        => $request->diesel_supply,
            'diesel_received'      => $request->diesel_received,
            'diesel_difference'    => $dieselDiff,
            'diesel_sales'         => $request->diesel_sales,
            'diesel_closing_stock' => $dieselClosing,
 
            'octane_prev_stock'    => $request->octane_prev_stock,
            'octane_supply'        => $request->octane_supply,
            'octane_received'      => $request->octane_received,
            'octane_difference'    => $octaneDiff,
            'octane_sales'         => $request->octane_sales,
            'octane_closing_stock' => $octaneClosing,
        ]);
 
        return redirect()
            ->route('fuel-reports.index')
            ->with('success', 'রিপোর্ট সফলভাবে আপডেট হয়েছে!');
    }
 
    /**
     * রিপোর্ট Delete করা
     */
    public function destroy(FuelReport $fuelReport)
    {
        $fuelReport->delete();
 
        return redirect()
            ->route('fuel-reports.index')
            ->with('success', 'রিপোর্ট মুছে ফেলা হয়েছে।');
    }
 
    /**
     * AJAX: Station select করলে Previous Stocks auto-fill
     */
    public function getPreviousStocks(Request $request)
    {
        $request->validate(['station_name' => 'required|string']);
 
        $stocks = FuelReport::getPreviousStocks($request->station_name);
 
        return response()->json([
            'success' => true,
            'stocks'  => $stocks,
        ]);
    }
}
