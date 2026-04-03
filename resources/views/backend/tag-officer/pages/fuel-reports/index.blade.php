@extends('backend.tag-officer.layouts.app')

@section('title', 'Sales Report List')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --dark: #1e293b;
        }

        .report-container {
            background-color: #f1f5f9;
            min-height: 100vh;
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h4 {
            font-weight: 700;
            color: var(--dark);
            margin: 0;
            font-size: 20px;
        }

        .page-header h4 i {
            color: var(--primary);
            margin-right: 8px;
        }

        .custom-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .filter-section {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 18px 20px;
            margin-bottom: 20px;
        }

        .filter-section label {
            font-size: 11px;
            font-weight: 700;
            color: var(--dark);
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 5px;
            display: block;
        }

        .filter-section .form-control {
            font-size: 13px;
            border-color: #e2e8f0;
            border-radius: 6px;
            color: var(--dark);
        }

        .filter-section .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .1);
            outline: none;
        }

        .btn-add {
            background: #006699;;
            color: #fff;
            padding: 10px 20px;
            border-radius: 7px;
            font-weight: 600;
            border: none;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-add:hover {
            background: #0088cc;
            color: #fff;
        }

        .btn-add i {
            margin-right: 6px;
        }

        .btn-filter {
            background: #006699;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-filter:hover {
            background: #0088cc;
        }
        .btn-reset {
            background: #f1f5f9;
            color: var(--dark);
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 9px 13px;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-reset:hover {
            background: #e2e8f0;
        }

        .table-custom thead th {
            background: #f8fafc;
            color: var(--dark);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 14px;
            white-space: nowrap;
        }

        .table-custom tbody td {
            padding: 11px 14px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: var(--dark);
        }

        .table-custom tbody tr:hover td {
            background: #f8fafc;
        }

        .fuel-type {
            font-size: 12px;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .fuel-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--primary);
            display: inline-block;
            flex-shrink: 0;
        }

        .fuel-dot.muted {
            background: #cbd5e1;
        }

        .station-name {
            font-weight: 700;
            font-size: 13px;
            color: var(--dark);
        }

        .station-sub {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .station-sub i {
            margin-right: 3px;
        }

        .diff-neg {
            color: #dc2626;
            font-weight: 700;
        }

        .diff-pos {
            color: #16a34a;
            font-weight: 700;
        }

        .diff-zero {
            color: #94a3b8;
        }

        .action-wrap {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: stretch;
            min-width: 80px;
        }

        .btn-act {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn-act-view {
            background: #f1f5f9;
            color: var(--dark);
            border: 1px solid #e2e8f0;
        }

        .btn-act-edit {
            background:  #006699;
            color: #fff;
        }

        .btn-act-del {
            background: #fff;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .btn-act-view:hover {
            background: #e2e8f0;
            color: var(--dark);
        }

        .btn-act-edit:hover {
            background: #0088cc;
            color: #fff;
        }

        .btn-act-del:hover {
            background: #fef2f2;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            border-radius: 8px;
            padding: 11px 16px;
            margin-bottom: 18px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 8px;
            padding: 11px 16px;
            margin-bottom: 18px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state i {
            font-size: 34px;
            color: #cbd5e1;
            margin-bottom: 12px;
            display: block;
        }

        .empty-state p {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 16px;
        }

        .group-border td {
            border-bottom: 2px solid #e2e8f0 !important;
        }

        .pagination-wrap {
            padding: 14px 18px;
            border-top: 1px solid #f1f5f9;
        }

        .closing-val {
            font-weight: 700;
            color: var(--primary);
        }
    </style>
@endpush

@section('content')
<div class="report-container">

    <div class="page-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <h4 class="mb-0"><i class="fa-solid fa-gas-pump me-2"></i> Fuel Report List</h4>

        <a href="{{ route('fuel-reports.create') }}" class="btn-add text-nowrap">
            <i class="fa-solid fa-plus me-1"></i> New Report
        </a>
    </div>

    @if (session('success'))
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif

    <div class="filter-section">
        <form method="GET" action="{{ route('fuel-reports.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label><i class="fa-solid fa-magnifying-glass fa-xs"></i> Station Name</label>
                    {{-- stationList --}}
                    <select name="station_name" class="form-control">
                        <option value="">Select Station</option>
                        @foreach($stationList as $id => $name)
                            <option value="{{ $name }}" {{ request('station_name') == $name ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label><i class="fa-regular fa-calendar fa-xs"></i> From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label><i class="fa-regular fa-calendar fa-xs"></i> To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn-filter flex-grow-1">
                        <i class="fa-solid fa-filter fa-xs"></i> Filter
                    </button>
                    <a href="{{ route('fuel-reports.index') }}" class="btn-reset">
                        <i class="fa-solid fa-rotate-left fa-xs"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="custom-card">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Station</th>
                        <th>Fuel</th>
                        <th>Prev. Stock (L)</th>
                        <th>Supply (L)</th>
                        <th>Received (L)</th>
                        <th>Diff (L)</th>
                        <th>Sales (L)</th>
                        <th>Closing Stock (L)</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($reports as $report)

                    {{-- Petrol --}}
                    <tr>
                        <td rowspan="3" class="align-middle text-center fw-bold" style="color:#94a3b8;background:#fafafa;">
                            {{ $loop->iteration }}
                        </td>

                        <td rowspan="3" class="align-middle">
                            <div class="fw-bold" style="font-size:13px">{{ $report->report_date->format('d M Y') }}</div>
                            <div style="font-size:11px;color:#94a3b8">{{ $report->report_date->format('l') }}</div>
                        </td>

                        <td rowspan="3" class="align-middle">
                            <div class="station-name">{{ $report->station_name }}</div>
                            <div class="station-sub">
                                <i class="fa-solid fa-location-dot fa-xs"></i>
                                {{ $report->thana_upazila }}, {{ $report->district }}
                            </div>
                        </td>

                        <td><span class="fuel-type"><span class="fuel-dot"></span>Petrol</span></td>

                        <td>{{ number_format($report->petrol_prev_stock, 0) }}</td>
                        <td>{{ number_format($report->petrol_supply, 0) }}</td>
                        <td>{{ number_format($report->petrol_received, 0) }}</td>

                        {{-- ✅ FIXED DIFF --}}
                        <td style="color:#dc2626;font-weight:600;">
                            {{ number_format($report->petrol_difference, 0) }}
                        </td>

                        <td>{{ number_format($report->petrol_sales, 0) }}</td>
                        <td class="closing-val">{{ number_format($report->petrol_closing_stock, 0) }}</td>

                        <td rowspan="3" class="align-middle text-center" style="background:#fafafa;">
                            <div class="action-wrap">
                                <a href="{{ route('fuel-reports.show', $report) }}" class="btn-act btn-act-view">
                                    <i class="fa-regular fa-eye fa-xs"></i> View
                                </a>
                                <a href="{{ route('fuel-reports.edit', $report) }}" class="btn-act btn-act-edit">
                                    <i class="fa-regular fa-pen-to-square fa-xs"></i> Edit
                                </a>
                                <form action="{{ route('fuel-reports.destroy', $report) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this report?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-act btn-act-del">
                                        <i class="fa-regular fa-trash-can fa-xs"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Diesel --}}
                    <tr>
                        <td><span class="fuel-type"><span class="fuel-dot muted"></span>Diesel</span></td>

                        <td>{{ number_format($report->diesel_prev_stock, 0) }}</td>
                        <td>{{ number_format($report->diesel_supply, 0) }}</td>
                        <td>{{ number_format($report->diesel_received, 0) }}</td>

                        {{-- ✅ FIXED --}}
                        <td style="color:#dc2626;font-weight:600;">
                            {{ number_format($report->diesel_difference, 0) }}
                        </td>

                        <td>{{ number_format($report->diesel_sales, 0) }}</td>
                        <td class="closing-val">{{ number_format($report->diesel_closing_stock, 0) }}</td>
                    </tr>

                    {{-- Octane --}}
                    <tr class="group-border">
                        <td><span class="fuel-type"><span class="fuel-dot muted"></span>Octane</span></td>

                        <td>{{ number_format($report->octane_prev_stock, 0) }}</td>
                        <td>{{ number_format($report->octane_supply, 0) }}</td>
                        <td>{{ number_format($report->octane_received, 0) }}</td>

                        {{-- ✅ FIXED --}}
                        <td style="color:#dc2626;font-weight:600;">
                            {{ number_format($report->octane_difference, 0) }}
                        </td>

                        <td>{{ number_format($report->octane_sales, 0) }}</td>
                        <td class="closing-val">{{ number_format($report->octane_closing_stock, 0) }}</td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="11">
                            <div class="empty-state">
                                <i class="fa-regular fa-folder-open"></i>
                                <p>No reports found.</p>
                                
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="pagination-wrap">{{ $reports->links() }}</div>
        @endif
    </div>

</div>
@endsection
