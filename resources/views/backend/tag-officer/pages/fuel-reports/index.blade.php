@extends('backend.tag-officer.layouts.app')

@section('title', 'Daily Fuel Summary Report')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --dark: #1e293b;
            --radius: 12px;
        }

        * {
            box-sizing: border-box;
        }

        .report-container {
            background: #f1f5f9;
            min-height: 100vh;
            padding: 28px 24px;
            font-family: 'Segoe UI', sans-serif;
        }

        /* ── Edit Mode Banner ── */
        .edit-banner {
            display: none;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 14px;
            font-size: 13px;
            color: #92400e;
            font-weight: 600;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .edit-banner.active {
            display: flex;
        }

        .edit-banner i {
            margin-right: 6px;
        }

        .btn-cancel-edit {
            background: #fff;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #92400e;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-cancel-edit:hover {
            background: #fef3c7;
        }

        /* ── Main Card ── */
        .main-card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
            overflow: hidden;
        }

        /* ── Page Title ── */
        .page-title {
            text-align: center;
            padding: 20px 24px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .page-title h4 {
            font-size: 18px;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
            transition: color .2s;
        }

        .page-title h4 i {
            color: var(--primary);
            margin-right: 8px;
        }

        .page-title h4.edit-mode {
            color: #b45309;
        }

        .page-title h4.edit-mode i {
            color: #f59e0b;
        }

        /* ── Station Info Row ── */
        .station-row {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1.5fr 1.2fr;
            padding: 18px 20px;
            gap: 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        .field-group label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .field-group label i {
            color: var(--primary);
            margin-right: 4px;
        }

        .field-control {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 13px;
            font-size: 13px;
            color: var(--dark);
            background: #fff;
            appearance: none;
        }

        .field-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        .field-control.is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            font-size: 11px;
            color: #ef4444;
            margin-top: 3px;
            display: block;
        }

        /* ── Hint Banner ── */
        .hint-banner {
            margin: 0 20px 18px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 12px;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        /* ── Fuel Section Card ── */
        .fuel-card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid #e2e8f0;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .fuel-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
        }

        .fuel-header.octane {
            background: #fff1f0;
            color: #c0392b;
            border-bottom-color: #fecaca;
        }

        .fuel-header.petrol {
            background: #f0fdf4;
            color: #16a34a;
            border-bottom-color: #bbf7d0;
        }

        .fuel-header.diesel {
            background: #fffbeb;
            color: #b45309;
            border-bottom-color: #fde68a;
        }

        .fuel-header.others {
            background: #eff6ff;
            color: #2563eb;
            border-bottom-color: #bfdbfe;
        }

        .fuel-header .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .fuel-header.octane .dot {
            background: #ef4444;
        }

        .fuel-header.petrol .dot {
            background: #22c55e;
        }

        .fuel-header.diesel .dot {
            background: #f59e0b;
        }

        .fuel-header.others .dot {
            background: #3b82f6;
        }

        /* ── 6-col grid ── */
        .fuel-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
        }

        .col-head {
            padding: 9px 12px;
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .35px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }

        .col-head:last-child {
            border-right: none;
        }

        .col-head i {
            margin-right: 4px;
        }

        .col-cell {
            padding: 12px;
            border-right: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }

        .col-cell:last-child {
            border-right: none;
        }

        .col-cell-auto {
            padding: 12px;
            border-right: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }

        .col-cell-auto:last-child {
            border-right: none;
        }

        .col-cell-auto.closing {
            background: #eff6ff;
        }

        .num-input {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 9px 10px;
            font-size: 13px;
            color: var(--dark);
            background: #fff;
        }

        .num-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .1);
        }

        .num-input.prev {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .num-input.is-invalid {
            border-color: #ef4444;
        }

        .auto-val {
            font-size: 18px;
            font-weight: 700;
            line-height: 1;
        }

        .auto-val.diff {
            color: #ef4444;
        }

        .auto-val.close {
            color: #2563eb;
        }

        .auto-note {
            font-size: 10px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-top: 2px;
        }

        .prev-note {
            font-size: 10px;
            color: #3b82f6;
            margin-top: 4px;
        }

        .warn-text {
            font-size: 10px;
            color: #ef4444;
            margin-top: 3px;
            display: none;
        }

        /* ── Comment ── */
        .comment-card {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid #e2e8f0;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .comment-label {
            padding: 10px 16px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #64748b;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .comment-label i {
            color: #94a3b8;
        }

        .comment-textarea {
            width: 100%;
            border: none;
            padding: 14px 16px;
            font-size: 13px;
            color: var(--dark);
            resize: vertical;
            min-height: 80px;
            background: #fff;
            font-family: inherit;
        }

        .comment-textarea:focus {
            outline: none;
        }

        .comment-textarea::placeholder {
            color: #cbd5e1;
        }

        /* ── Save Button ── */
        .btn-save-full {
            width: 100%;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: var(--radius);
            padding: 15px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .3px;
            margin-bottom: 28px;
            transition: background .2s;
        }

        .btn-save-full:hover {
            background: #1d4ed8;
        }

        .btn-save-full.update-mode {
            background: #b45309;
        }

        .btn-save-full.update-mode:hover {
            background: #92400e;
        }

        /* ── Alerts ── */
        .alert-error {
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

        /* ── Saved Reports ── */
        .saved-section {
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .saved-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .saved-header h5 {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        .export-btns {
            display: flex;
            gap: 10px;
        }

        .btn-export-pdf {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--dark);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-export-pdf:hover {
            background: #f8fafc;
            color: var(--dark);
        }

        .btn-export-pdf i {
            color: #ef4444;
        }

        .btn-export-excel {
            background: #16a34a;
            border: none;
            border-radius: 7px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-export-excel:hover {
            background: #15803d;
            color: #fff;
        }

        /* ── Table ── */
        .reports-table {
            width: 100%;
            border-collapse: collapse;
        }

        .reports-table thead th {
            padding: 10px 12px;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .4px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            white-space: nowrap;
        }

        .reports-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .reports-table tbody tr:last-child {
            border-bottom: none;
        }

        .reports-table td {
            padding: 9px 12px;
            font-size: 12px;
            color: var(--dark);
            vertical-align: middle;
        }

        .td-date .date-main {
            font-weight: 700;
            font-size: 13px;
        }

        .td-date .date-day {
            font-size: 11px;
            color: #94a3b8;
        }

        .td-station .station-name {
            font-weight: 600;
            font-size: 12px;
        }

        .td-station .station-loc {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 1px;
        }

        .fuel-badge {
            font-size: 12px;
            color: var(--dark);
            padding: 2px 0;
            display: block;
        }

        .diff-red {
            color: #ef4444;
            font-weight: 700;
        }

        .status-badge {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 20px;
            white-space: nowrap;
            display: inline-block;
        }

        .status-low {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-highdiff {
            background: #fef2f2;
            color: #991b1b;
        }

        .status-zero {
            background: #f1f5f9;
            color: #475569;
        }

        .status-ok {
            background: #f0fdf4;
            color: #15803d;
        }

        .action-btns {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .btn-view,
        .btn-edit-inline,
        .btn-del {
            border: none;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-view {
            background: #1e293b;
            color: #fff;
        }

        .btn-view:hover {
            background: #0f172a;
            color: #fff;
        }

        .btn-edit-inline {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .btn-edit-inline:hover {
            background: #dbeafe;
            color: #2563eb;
        }

        .btn-del {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fecaca;
        }

        .btn-del:hover {
            background: #fee2e2;
            color: #ef4444;
        }

        .no-comment {
            font-size: 11px;
            color: #cbd5e1;
            font-style: italic;
        }

        .group-first td {
            border-top: 1px solid #e2e8f0;
        }

        tr.editing-row td {
            background: #fffbeb !important;
        }

        /* ── Responsive ── */
        @media (max-width: 1100px) {
            .fuel-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .col-head {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .report-container {
                padding: 14px;
            }

            .station-row {
                grid-template-columns: 1fr 1fr;
            }

            .fuel-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .saved-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .station-row {
                grid-template-columns: 1fr;
            }

            .fuel-grid {
                grid-template-columns: 1fr;
            }

            .col-cell,
            .col-cell-auto {
                border-right: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="report-container">

        @if (session('error'))
            <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif

        {{-- ── Edit Mode Banner ── --}}
        <div class="edit-banner" id="edit-banner">
            <span><i class="fa-solid fa-pen-to-square"></i> Edit mode — You are editing a saved report</span>
            <button type="button" class="btn-cancel-edit" onclick="cancelEdit()">
                <i class="fa-solid fa-xmark fa-xs"></i> Cancel Edit
            </button>
        </div>

        {{-- ══ FORM ══ --}}
        <form id="fuel-form" action="{{ route('fuel-reports.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            <input type="hidden" name="edit_id" id="form-edit-id" value="">

            {{-- Header Card --}}
            <div class="main-card" style="margin-bottom:16px;">
                <div class="page-title">
                    <h4 id="form-title"><i class="fa-solid fa-file-lines"></i> Daily Fuel Summary Report</h4>
                </div>

                <div class="station-row">
                    <div class="field-group">
                        <label><i class="fa-solid fa-gas-pump fa-xs"></i> Filling Station Name</label>
                        <select name="station_name" class="field-control">
                            <option value="{{ $stationName }}">{{ $stationName }}</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label><i class="fa-solid fa-map-pin fa-xs"></i> Thana / Upazila</label>
                        <input type="text" name="thana_upazila" class="field-control"
                            value="{{ $stationInfo->upazila ?? '' }}" readonly>
                    </div>
                    <div class="field-group">
                        <label><i class="fa-solid fa-location-dot fa-xs"></i> District</label>
                        <input type="text" name="district" class="field-control"
                            value="{{ $stationInfo->district ?? '' }}" readonly>
                    </div>
                    <div class="field-group">
                        <label><i class="fa-regular fa-calendar fa-xs"></i> Report Date</label>
                        <input type="date" name="report_date" id="field-report-date"
                            class="field-control @error('report_date') is-invalid @enderror"
                            value="{{ old('report_date', $defaultDate) }}" required>
                        @error('report_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div style="padding: 0 20px 16px;">
                    <div class="hint-banner">
                        <i class="fa-solid fa-circle-info fa-xs"></i>
                        Previous stock is loaded based on your assigned station from the previous day.
                    </div>
                </div>
            </div>

            {{-- Fuel Cards --}}
            @php
                $fuels = [
                    [
                        'key' => 'octane',
                        'label' => 'Octane',
                        'cls' => 'octane',
                        'prev' => $previousStocks['octane'] ?? 0,
                    ],
                    [
                        'key' => 'petrol',
                        'label' => 'Petrol',
                        'cls' => 'petrol',
                        'prev' => $previousStocks['petrol'] ?? 0,
                    ],
                    [
                        'key' => 'diesel',
                        'label' => 'Diesel',
                        'cls' => 'diesel',
                        'prev' => $previousStocks['diesel'] ?? 0,
                    ],
                    [
                        'key' => 'others',
                        'label' => 'Others',
                        'cls' => 'others',
                        'prev' => $previousStocks['others'] ?? 0,
                    ],
                ];
            @endphp

            @foreach ($fuels as $fuel)
                <div class="fuel-card">
                    <div class="fuel-header {{ $fuel['cls'] }}">
                        <span class="dot"></span> {{ $fuel['label'] }}
                    </div>
                    <div class="fuel-grid">
                        <div class="col-head"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Previous Stock (L)</div>
                        <div class="col-head"><i class="fa-solid fa-truck fa-xs"></i> Supply From Depot (L)</div>
                        <div class="col-head"><i class="fa-solid fa-arrow-down fa-xs"></i> Received (L)</div>
                        <div class="col-head"><i class="fa-solid fa-calculator fa-xs"></i> Difference (L)</div>
                        <div class="col-head"><i class="fa-solid fa-chart-line fa-xs"></i> Sales (L)</div>
                        <div class="col-head"><i class="fa-solid fa-warehouse fa-xs"></i> Closing Stock (L)</div>

                        <div class="col-cell" data-label="Previous Stock (L)">
                            <input type="number" step="0.01" min="0" name="{{ $fuel['key'] }}_prev_stock"
                                id="{{ $fuel['key'] }}_prev_stock"
                                class="num-input prev @error($fuel['key'] . '_prev_stock') is-invalid @enderror"
                                value="{{ old($fuel['key'] . '_prev_stock', $fuel['prev']) }}"
                                oninput="calcRow('{{ $fuel['key'] }}')">
                            <div class="prev-note"><i class="fa-solid fa-rotate fa-xs"></i> Auto from yesterday</div>
                            @error($fuel['key'] . '_prev_stock')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-cell" data-label="Supply From Depot (L)">
                            <input type="number" step="0.01" min="0" name="{{ $fuel['key'] }}_supply"
                                id="{{ $fuel['key'] }}_supply"
                                class="num-input @error($fuel['key'] . '_supply') is-invalid @enderror"
                                value="{{ old($fuel['key'] . '_supply', 0) }}" oninput="calcRow('{{ $fuel['key'] }}')">
                            @error($fuel['key'] . '_supply')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-cell" data-label="Received (L)">
                            <input type="number" step="0.01" min="0" name="{{ $fuel['key'] }}_received"
                                id="{{ $fuel['key'] }}_received"
                                class="num-input @error($fuel['key'] . '_received') is-invalid @enderror"
                                value="{{ old($fuel['key'] . '_received', 0) }}"
                                oninput="calcRow('{{ $fuel['key'] }}')">
                            @error($fuel['key'] . '_received')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-cell-auto" data-label="Difference (L)" style="background:#fef2f2;">
                            <span class="auto-val diff" id="{{ $fuel['key'] }}_difference_display">0.00</span>
                            <span class="auto-note">Auto</span>
                        </div>

                        <div class="col-cell" data-label="Sales (L)">
                            <input type="number" step="0.01" min="0" name="{{ $fuel['key'] }}_sales"
                                id="{{ $fuel['key'] }}_sales"
                                class="num-input @error($fuel['key'] . '_sales') is-invalid @enderror"
                                value="{{ old($fuel['key'] . '_sales', 0) }}" oninput="calcRow('{{ $fuel['key'] }}')">
                            <span class="warn-text" id="{{ $fuel['key'] }}_sales_warn"></span>
                            @error($fuel['key'] . '_sales')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-cell-auto closing" data-label="Closing Stock (L)">
                            <span class="auto-val close" id="{{ $fuel['key'] }}_closing_display">0.00</span>
                            <span class="auto-note">Auto</span>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Comment --}}
            <div class="comment-card">
                <div class="comment-label"><i class="fa-regular fa-comment fa-xs"></i> Comment</div>
                <textarea name="comment" id="field-comment" class="comment-textarea"
                    placeholder="Enter any comments or notes here...">{{ old('comment') }}</textarea>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn-save-full" id="btn-submit">
                <i class="fa-solid fa-floppy-disk"></i> Save
            </button>

        </form>

        {{-- ══ Saved Reports Table ══ --}}
        <div class="saved-section">
            <div class="saved-header">
                <h5>Saved Reports</h5>
                <div class="export-btns">
                    @if (Route::has('fuel-reports.export.pdf'))
                        <a href="{{ route('fuel-reports.export.pdf') }}" class="btn-export-pdf">
                        @else
                            <a href="#" class="btn-export-pdf">
                    @endif
                    <i class="fa-solid fa-file-pdf"></i> Export to PDF
                    </a>
                    @if (Route::has('fuel-reports.export.excel'))
                        <a href="{{ route('fuel-reports.export.excel') }}" class="btn-export-excel">
                        @else
                            <a href="#" class="btn-export-excel">
                    @endif
                    <i class="fa-solid fa-file-excel"></i> Export to Excel
                    </a>
                </div>
            </div>

            <table class="reports-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Filling Station</th>
                        <th>Fuel</th>
                        <th>Prev. Stock (L)</th>
                        <th>Supply (L)</th>
                        <th>Received (L)</th>
                        <th>Diff (L)</th>
                        <th>Sales (L)</th>
                        <th>Closing Stock (L)</th>
                        <th>Status</th>
                        <th>Comment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $i => $report)
                        @php
                            $fueltypes = [
                                [
                                    'label' => 'Octane',
                                    'prev' => $report->octane_prev_stock,
                                    'supply' => $report->octane_supply,
                                    'received' => $report->octane_received,
                                    'diff' => $report->octane_difference,
                                    'sales' => $report->octane_sales,
                                    'closing' => $report->octane_closing_stock,
                                    'status' => $report->octane_status ?? null,
                                ],
                                [
                                    'label' => 'Petrol',
                                    'prev' => $report->petrol_prev_stock,
                                    'supply' => $report->petrol_supply,
                                    'received' => $report->petrol_received,
                                    'diff' => $report->petrol_difference,
                                    'sales' => $report->petrol_sales,
                                    'closing' => $report->petrol_closing_stock,
                                    'status' => $report->petrol_status ?? null,
                                ],
                                [
                                    'label' => 'Diesel',
                                    'prev' => $report->diesel_prev_stock,
                                    'supply' => $report->diesel_supply,
                                    'received' => $report->diesel_received,
                                    'diff' => $report->diesel_difference,
                                    'sales' => $report->diesel_sales,
                                    'closing' => $report->diesel_closing_stock,
                                    'status' => $report->diesel_status ?? null,
                                ],
                                [
                                    'label' => 'Others',
                                    'prev' => $report->others_prev_stock,
                                    'supply' => $report->others_supply,
                                    'received' => $report->others_received,
                                    'diff' => $report->others_difference,
                                    'sales' => $report->others_sales,
                                    'closing' => $report->others_closing_stock,
                                    'status' => $report->others_status ?? null,
                                ],
                            ];
                            $rowCount = count($fueltypes);
                        @endphp

                        @foreach ($fueltypes as $fi => $ft)
                            <tr class="{{ $fi === 0 ? 'group-first' : '' }}" data-report-group="{{ $report->id }}">

                                @if ($fi === 0)
                                    <td rowspan="{{ $rowCount }}"
                                        style="font-weight:700;color:#64748b;vertical-align:top;padding-top:14px;">
                                        {{ $reports->firstItem() + $i }}
                                    </td>
                                    <td rowspan="{{ $rowCount }}" style="vertical-align:top;padding-top:12px;">
                                        <div class="td-date">
                                            <div class="date-main">
                                                {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}</div>
                                            <div class="date-day">
                                                {{ \Carbon\Carbon::parse($report->report_date)->format('l') }}</div>
                                        </div>
                                    </td>
                                    <td rowspan="{{ $rowCount }}" style="vertical-align:top;padding-top:12px;">
                                        <div class="td-station">
                                            <div class="station-name">{{ $report->station_name }}</div>
                                            <div class="station-loc">
                                                <i class="fa-solid fa-location-dot fa-xs"></i>
                                                {{ $report->thana_upazila }}, {{ $report->district }}
                                            </div>
                                        </div>
                                    </td>
                                @endif

                                <td><span class="fuel-badge">{{ $ft['label'] }}</span></td>
                                <td>{{ number_format($ft['prev'], 0) }}</td>
                                <td>{{ number_format($ft['supply'], 0) }}</td>
                                <td>{{ number_format($ft['received'], 0) }}</td>
                                <td class="{{ abs($ft['diff']) > 0 ? 'diff-red' : '' }}">
                                    {{ number_format($ft['diff'], 0) }}</td>
                                <td>{{ number_format($ft['sales'], 0) }}</td>
                                <td style="font-weight:600;">{{ number_format($ft['closing'], 0) }}</td>
                                <td>
                                    @if (!empty($ft['status']))
                                        @php
                                            $status = strtolower(trim($ft['status']));
                                            $class = match ($status) {
                                                'low stock' => 'status-low',
                                                'high difference' => 'status-highdiff',
                                                'zero stock' => 'status-zero',
                                                default => 'status-ok',
                                            };
                                            $icon = match ($status) {
                                                'low stock' => 'fa-arrow-down',
                                                'high difference' => 'fa-exclamation-triangle',
                                                'zero stock' => 'fa-ban',
                                                default => 'fa-check-circle',
                                            };
                                        @endphp

                                        <span class="status-badge {{ $class }}">
                                            <i class="fas {{ $icon }} me-1"></i>
                                            {{ ucwords($ft['status']) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                @if ($fi === 0)
                                    <td rowspan="{{ $rowCount }}" style="vertical-align:top;padding-top:12px;">
                                        @if ($report->comment)
                                            <span style="font-size:12px;">{{ $report->comment }}</span>
                                        @else
                                            <span class="no-comment">No Comments</span>
                                        @endif
                                    </td>
                                    <td rowspan="{{ $rowCount }}" style="vertical-align:top;padding-top:10px;">
                                        <div class="action-btns">

                                            <a href="{{ route('fuel-reports.show', $report) }}" class="btn-view">
                                                <i class="fa-solid fa-eye fa-xs"></i> View
                                            </a>

                                            {{-- Inline Edit Button — passes all data via JSON --}}
                                            <button type="button" class="btn-edit-inline"
                                                onclick="loadEditForm({{ json_encode([
                                                    'id' => $report->id,
                                                    'report_date' => $report->report_date,
                                                    'comment' => $report->comment ?? '',
                                                    'octane_prev_stock' => (float) $report->octane_prev_stock,
                                                    'octane_supply' => (float) $report->octane_supply,
                                                    'octane_received' => (float) $report->octane_received,
                                                    'octane_sales' => (float) $report->octane_sales,
                                                    'petrol_prev_stock' => (float) $report->petrol_prev_stock,
                                                    'petrol_supply' => (float) $report->petrol_supply,
                                                    'petrol_received' => (float) $report->petrol_received,
                                                    'petrol_sales' => (float) $report->petrol_sales,
                                                    'diesel_prev_stock' => (float) $report->diesel_prev_stock,
                                                    'diesel_supply' => (float) $report->diesel_supply,
                                                    'diesel_received' => (float) $report->diesel_received,
                                                    'diesel_sales' => (float) $report->diesel_sales,
                                                    'others_prev_stock' => (float) $report->others_prev_stock,
                                                    'others_supply' => (float) $report->others_supply,
                                                    'others_received' => (float) $report->others_received,
                                                    'others_sales' => (float) $report->others_sales,
                                                ]) }})">
                                                <i class="fa-solid fa-pen-to-square fa-xs"></i> Edit
                                            </button>

                                            <form action="{{ route('fuel-reports.destroy', $report) }}" method="POST"
                                                onsubmit="return confirm('Delete this report?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-del">
                                                    <i class="fa-solid fa-trash fa-xs"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="13" style="text-align:center;padding:30px;color:#94a3b8;font-size:13px;">
                                <i class="fa-solid fa-inbox fa-lg" style="display:block;margin-bottom:8px;"></i>
                                No reports found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($reports->hasPages())
                <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const FUELS = ['octane', 'petrol', 'diesel', 'others'];

        const STORE_URL = "{{ route('fuel-reports.store') }}";

        // ✅ FIX: route() না, url() ব্যবহার করো
        const UPDATE_BASE = "{{ url('tag-officer/fuel-reports/') }}";

        const DEFAULT_DATE = "{{ $defaultDate }}";

        // ── Recalculate ─────────────────────────
        function calcRow(fuel) {
            const prev = parseFloat(document.getElementById(fuel + '_prev_stock')?.value) || 0;
            const supply = parseFloat(document.getElementById(fuel + '_supply')?.value) || 0;
            const received = parseFloat(document.getElementById(fuel + '_received')?.value) || 0;
            const salesEl = document.getElementById(fuel + '_sales');
            let sales = parseFloat(salesEl?.value) || 0;

            const maxSellable = prev + received;
            const warnEl = document.getElementById(fuel + '_sales_warn');

            if (sales > maxSellable) {
                if (warnEl) {
                    warnEl.textContent = `⚠ Sales cannot exceed available stock (${maxSellable.toFixed(2)} L).`;
                    warnEl.style.display = 'block';
                }
                sales = maxSellable;
                if (salesEl) {
                    salesEl.value = sales.toFixed(2);
                    salesEl.classList.add('is-invalid');
                }
            } else {
                if (warnEl) warnEl.style.display = 'none';
                if (salesEl) salesEl.classList.remove('is-invalid');
            }

            const diff = supply - received;
            const closing = prev + received - sales;

            document.getElementById(fuel + '_difference_display').textContent = diff.toFixed(2);
            document.getElementById(fuel + '_closing_display').textContent = closing.toFixed(2);
        }

        // ── Load Edit Data ──────────────────────
        function loadEditForm(data) {
            const form = document.getElementById('fuel-form');

            // ✅ FIXED UPDATE URL
            form.action = UPDATE_BASE + '/' + data.id;

            document.getElementById('form-method').value = 'PUT';
            document.getElementById('form-edit-id').value = data.id;

            document.getElementById('field-report-date').value = data.report_date;
            document.getElementById('field-comment').value = data.comment || '';

            FUELS.forEach(fuel => {
                ['prev_stock', 'supply', 'received', 'sales'].forEach(field => {
                    const el = document.getElementById(`${fuel}_${field}`);
                    if (el) el.value = data[`${fuel}_${field}`] ?? 0;
                });
                calcRow(fuel);
            });

            document.getElementById('edit-banner').classList.add('active');
            document.getElementById('btn-submit').innerHTML = 'Update Report';
        }

        // ── Cancel Edit ─────────────────────────
        function cancelEdit() {
            const form = document.getElementById('fuel-form');

            form.action = STORE_URL;
            document.getElementById('form-method').value = 'POST';
            document.getElementById('form-edit-id').value = '';

            document.getElementById('field-report-date').value = DEFAULT_DATE;
            document.getElementById('field-comment').value = '';

            FUELS.forEach(fuel => {
                ['prev_stock', 'supply', 'received', 'sales'].forEach(field => {
                    const el = document.getElementById(`${fuel}_${field}`);
                    if (el) el.value = 0;
                });
                calcRow(fuel);
            });

            document.getElementById('edit-banner').classList.remove('active');
            document.getElementById('btn-submit').innerHTML = 'Save';
        }

        // ── Init ───────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {

            FUELS.forEach(f => calcRow(f));

            FUELS.forEach(fuel => {
                ['prev_stock', 'supply', 'received', 'sales'].forEach(field => {
                    const el = document.getElementById(`${fuel}_${field}`);
                    if (el) el.addEventListener('input', () => calcRow(fuel));
                });
            });

            // Submit validation
            document.getElementById('fuel-form').addEventListener('submit', function(e) {
                let blocked = false;

                FUELS.forEach(fuel => {
                    const prev = parseFloat(document.getElementById(fuel + '_prev_stock')?.value) ||
                        0;
                    const received = parseFloat(document.getElementById(fuel + '_received')
                        ?.value) || 0;
                    const sales = parseFloat(document.getElementById(fuel + '_sales')?.value) || 0;

                    if (sales > prev + received) {
                        blocked = true;
                    }
                });

                if (blocked) {
                    e.preventDefault();
                    alert('Sales cannot exceed available stock!');
                }
            });
        });
    </script>
@endpush
