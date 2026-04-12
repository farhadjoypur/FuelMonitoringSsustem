@extends('backend.tag-officer.layouts.app')

@section('title', 'Sales Report')

@push('styles')
    <style>
        .report-container {
            background-color: #f0f4f8;
            min-height: 100vh;
            padding: 30px;
        }

        /* কার্ড ডিজাইন */
        .custom-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            margin-bottom: 25px;
            overflow: hidden;
        }

        /* হেডার সেকশন */
        .report-header {
            text-align: center;
            padding: 20px;
        }

        .report-header h5 {
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .report-header p {
            font-size: 13px;
            color: #4a5568;
            margin-bottom: 2px;
        }

        /* ক্যাটাগরি বার (Petrol, Diesel, Octane) */
        .category-bar {
            background-color: #cbd5e0;
            color: #2d3748;
            font-weight: 700;
            text-align: center;
            padding: 10px;
            font-size: 15px;
        }

        /* ইনপুট গ্রিড */
        .input-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1px;
            background-color: #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .input-grid-header {
            background-color: #f8fafc;
            padding: 12px 5px;
            text-align: center;
            font-size: 12px;
            font-weight: 700;
            color: #4a5568;
            border-right: 1px solid #e2e8f0;
        }

        .input-grid-body {
            background-color: #ffffff;
            padding: 15px;
            border-right: 1px solid #e2e8f0;
        }

        .form-input-custom {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px;
            font-size: 13px;
            color: #718096;
            background-color: #ffffff;
        }

        .auto-calc {
            background-color: #f1f5f9;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* সেভ বাটন */
        .btn-save-custom {
            background-color: #2563eb;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            margin-top: 10px;
        }

        /* সেভড রিপোর্ট টেবিল */
        .saved-reports-title {
            font-weight: 700;
            font-size: 18px;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .report-table-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #4a5568;
            font-size: 12px;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 20px;
        }

        .table-custom tbody td {
            padding: 12px 20px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }

        .diff-red {
            color: #e53e3e;
            font-weight: 700;
        }

        .btn-edit-small {
            background-color: #006699;
            color: white;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 12px;
            text-decoration: none;
        }
    </style>
@endpush

@section('content')
    <div class="report-container">
        <form action="#" method="POST">
            @csrf
            <div class="custom-card report-header">
                <h5>Fuel Oil Receipt and Distribution Summary Daily Report</h5>
                <p><strong>Filling Station Name:</strong> Uttara Filling Station</p>
                <p><strong>Thana/Upazila:</strong> Raypura | <strong>District:</strong> Maymansing</p>
                <p><strong>Date:</strong> 10 June 2026</p>
            </div>

            <div class="custom-card">
                <div class="category-bar">Petrol</div>
                <div class="input-grid">
                    <div class="input-grid-header">Previous Stock (Liters)</div>
                    <div class="input-grid-header">Supply From Dipot (Liters)</div>
                    <div class="input-grid-header">Received at Filling Station (Liters)</div>
                    <div class="input-grid-header">Difference (Liters)</div>
                    <div class="input-grid-header">Sales (Liters)</div>

                    <div class="input-grid-body"><input type="text" name="petrol_prev" class="form-input-custom"
                            placeholder="Entry Field"></div>
                    <div class="input-grid-body"><input type="text" name="petrol_supply" class="form-input-custom"
                            placeholder="Entry Field"></div>
                    <div class="input-grid-body"><input type="text" name="petrol_received" class="form-input-custom"
                            placeholder="Entry Field"></div>
                    <div class="input-grid-body auto-calc">
                        <span class="fw-bold">0</span>
                        <span class="text-muted small" style="font-size: 10px;">Auto Calculated</span>
                    </div>
                    <div class="input-grid-body border-0"><input type="text" name="petrol_sales"
                            class="form-input-custom" placeholder="Entry Field"></div>
                </div>
            </div>

            <div class="custom-card">
                <div class="category-bar">Disel</div>
                <div class="input-grid">
                    <div class="input-grid-header">Previous Stock (Liters)</div>
                    <div class="input-grid-header">Supply From Dipot (Liters)</div>
                    <div class="input-grid-header">Received at Filling Station (Liters)</div>
                    <div class="input-grid-header">Difference (Liters)</div>
                    <div class="input-grid-header">Sales (Liters)</div>

                    <div class="input-grid-body"><input type="text" name="disel_prev" class="form-input-custom"
                            placeholder="Entry Field"></div>
                    <div class="input-grid-body"><input type="text" name="disel_supply" class="form-input-custom"
                            placeholder="Entry Field"></div>
                    <div class="input-grid-body"><input type="text" name="disel_received" class="form-input-custom"
                            placeholder="Entry Field"></div>
                    <div class="input-grid-body auto-calc">
                        <span class="fw-bold">0</span>
                        <span class="text-muted small" style="font-size: 10px;">Auto Calculated</span>
                    </div>
                    <div class="input-grid-body border-0"><input type="text" name="disel_sales" class="form-input-custom"
                            placeholder="Entry Field"></div>
                </div>
            </div>

            <div class="custom-card">
                <div class="category-bar">Octan</div>
                <div class="input-grid">
                    <div class="input-grid-header">Previous Stock (Liters)</div>
                    <div class="input-grid-header">Supply From Dipot (Liters)</div>
                    <div class="input-grid-header">Received at Filling Station (Liters)</div>
                    <div class="input-grid-header">Difference (Liters)</div>
                    <div class="input-grid-header">Sales (Liters)</div>

                    <div class="input-grid-body"><input type="text" name="octan_prev" class="form-input-custom"
                            placeholder="Entry Field"></div>
                    <div class="input-grid-body"><input type="text" name="octan_supply" class="form-input-custom"
                            placeholder="Entry Field"></div>
                    <div class="input-grid-body"><input type="text" name="octan_received" class="form-input-custom"
                            placeholder="Entry Field"></div>
                    <div class="input-grid-body auto-calc">
                        <span class="fw-bold">0</span>
                        <span class="text-muted small" style="font-size: 10px;">Auto Calculated</span>
                    </div>
                    <div class="input-grid-body border-0"><input type="text" name="octan_sales" class="form-input-custom"
                            placeholder="Entry Field"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-save-custom shadow-sm mb-5">Save</button>
        </form>

        <h5 class="saved-reports-title">Saved Reports</h5>

        <div class="custom-card">
            <div class="report-table-header">
                <span class="fw-bold" style="font-size: 14px;">Report Date: 08 Jun 2026</span>
                <span class="text-muted small">Uttara Filling Station - Maymansing</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>Fuel Type</th>
                            <th>Previous Stock (L)</th>
                            <th>Supply From Depot (L)</th>
                            <th>Received At Station (L)</th>
                            <th>Difference (L)</th>
                            <th>Sales (L)</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">Petrol</td>
                            <td>5000</td>
                            <td>10000</td>
                            <td>9950</td>
                            <td class="diff-red">-50</td>
                            <td>6500</td>
                            <td rowspan="3" class="text-center align-middle" style="background: white;">
                                <a href="#" class="btn-edit-small">Edit</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Diesel</td>
                            <td>8000</td>
                            <td>15000</td>
                            <td>14900</td>
                            <td class="diff-red">-100</td>
                            <td>9200</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Octane</td>
                            <td>3000</td>
                            <td>5000</td>
                            <td>4980</td>
                            <td class="diff-red">-20</td>
                            <td>3800</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
