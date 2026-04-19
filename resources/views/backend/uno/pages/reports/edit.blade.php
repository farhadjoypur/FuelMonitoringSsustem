@extends('backend.uno.layouts.app')

@section('title', 'Edit Fuel Report — Admin')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    :root {
        --primary: #2563eb;
        --dark:    #1e293b;
    }

    .report-container {
        background-color: #f1f5f9;
        min-height: 100vh;
        padding: 30px;
    }

    /* ── Page Header ── */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
    }
    .page-header h4 {
        font-weight: 700;
        color: var(--dark);
        margin: 0;
        font-size: 20px;
    }
    .page-header h4 i { color: var(--primary); margin-right: 8px; }

    /* ── Cards ── */
    .custom-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        margin-bottom: 18px;
        overflow: hidden;
    }

    .report-header {
        text-align: center;
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .report-header h5 {
        font-weight: 700;
        color: var(--dark);
        font-size: 16px;
        margin-bottom: 6px;
    }
    .report-header h5 i { color: var(--primary); margin-right: 8px; }

    .edit-badge {
        font-size: 11px;
        font-weight: 700;
        color: var(--primary);
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 20px;
        padding: 3px 12px;
        letter-spacing: .3px;
    }
    .edit-badge i { margin-right: 4px; }

    /* ── Station Form ── */
    .station-form {
        padding: 20px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .form-label-custom {
        font-size: 11px;
        font-weight: 700;
        color: var(--dark);
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 5px;
        display: block;
    }
    .form-label-custom i { color: var(--primary); margin-right: 4px; }

    .form-control-custom {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 9px 12px;
        font-size: 13px;
        color: var(--dark);
        background: #fff;
        appearance: none;
    }
    .form-control-custom:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 2px rgba(37,99,235,.1);
    }
    .form-control-custom.is-invalid { border-color: #dc2626; }

    .form-control-readonly {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 9px 12px;
        font-size: 13px;
        color: #64748b;
        background: #f1f5f9;
        cursor: not-allowed;
    }

    .invalid-feedback {
        font-size: 11px;
        color: #dc2626;
        margin-top: 4px;
        display: block;
    }

    /* Officer read-only display */
    .officer-readonly-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 6px;
        padding: 9px 13px;
        min-height: 40px;
    }
    .officer-readonly-box i { color: #16a34a; font-size: 15px; flex-shrink: 0; }
    .officer-readonly-box .officer-name {
        font-size: 13px;
        font-weight: 700;
        color: #15803d;
        line-height: 1.3;
    }
    .officer-readonly-box .officer-sub {
        font-size: 10px;
        color: #86efac;
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    /* ── Fuel Grid ── */
    .category-bar {
        padding: 11px 18px;
        font-size: 13px;
        font-weight: 700;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--dark);
    }
    .category-bar.petrol { background:#f0fdf4; color:#16a34a; border-bottom-color:#bbf7d0; }
    .category-bar.diesel { background:#fffbeb; color:#b45309; border-bottom-color:#fde68a; }
    .category-bar.octane { background:#fff1f0; color:#c0392b; border-bottom-color:#fecaca; }
    .category-bar.others { background:#eff6ff; color:#2563eb; border-bottom-color:#bfdbfe; }
    .bar-line { flex:1; height:1px; background:#e2e8f0; margin-left:6px; }

    .input-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
    }
    .grid-head {
        background: #f8fafc;
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .3px;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #e2e8f0;
        text-align: center;
        white-space: nowrap;
    }
    .grid-head:last-child { border-right: none; }

    .grid-cell {
        background: #fff;
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
    }
    .grid-cell:last-child { border-right: none; }

    .grid-cell-auto {
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
    }
    .grid-cell-auto.diff-bg  { background: #fef2f2; }
    .grid-cell-auto.close-bg { background: #eff6ff; }

    .input-field {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        padding: 8px 10px;
        font-size: 13px;
        color: var(--dark);
        background: #fff;
    }
    .input-field:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 2px rgba(37,99,235,.1);
    }
    .input-field.is-invalid { border-color: #dc2626; }

    .auto-value {
        font-size: 18px;
        font-weight: 700;
        line-height: 1;
    }
    .auto-value.diff  { color: #dc2626; }
    .auto-value.close { color: #2563eb; }
    .auto-label {
        font-size: 10px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-top: 2px;
    }

    /* ── Buttons ── */
    .btn-update {
        background: #16a34a;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 28px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-update:hover { background: #15803d; }

    .btn-back {
        background: #f1f5f9;
        color: var(--dark);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 22px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: background .2s;
    }
    .btn-back:hover { background: #e2e8f0; color: var(--dark); }

    /* ── Alerts ── */
    .alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        border-radius: 8px;
        padding: 12px 16px;
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
        padding: 12px 16px;
        margin-bottom: 18px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
    }

    /* ── Admin privilege banner ── */
    .admin-banner {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 11px 16px;
        margin-bottom: 18px;
        font-size: 13px;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
    }

    /* ── Responsive ── */
    @media (max-width: 1100px) {
        .grid-head { display: none; }
        .input-grid { grid-template-columns: repeat(3, 1fr); }
        .grid-cell, .grid-cell-auto {
            position: relative;
            padding-top: 36px !important;
        }
        .grid-cell::before, .grid-cell-auto::before {
            content: attr(data-label);
            position: absolute;
            top: 10px; left: 12px;
            font-size: 10px; font-weight: 800;
            text-transform: uppercase; color: #64748b;
            letter-spacing: .4px; white-space: nowrap;
        }
        .grid-cell-auto { align-items: flex-start; justify-content: flex-start; }
    }
    @media (max-width: 768px) {
        .report-container { padding: 14px; }
        .input-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 480px) {
        .input-grid { grid-template-columns: 1fr; }
        .grid-cell, .grid-cell-auto { border-right: none; }
    }
</style>
@endpush

@section('content')
<div class="report-container">

    {{-- ── Alerts ── --}}
    @if(session('error'))
        <div class="alert-error">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ── Admin Privilege Banner ── --}}
    <div class="admin-banner">
        <i class="fa-solid fa-shield-halved"></i>
        UNO Mode — You can edit reports from your upazila: 
        <strong>{{ Auth::user()->profile?->upazila }}</strong>,
        <strong>{{ Auth::user()->profile?->district }}</strong>
    </div>

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <h4>
            <i class="fa-solid fa-pen-to-square"></i> Edit Fuel Report
        </h4>
        <a href="{{ route('uno.reports.index') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Back to Reports
        </a>
    </div>

    {{-- ══ FORM ══ --}}
    <form action="{{ route('uno.reports.update', $fuelReport) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ── Station & Meta Info Card ── --}}
        <div class="custom-card">
            <div class="report-header">
                <h5>
                    <i class="fa-solid fa-file-lines"></i> Daily Fuel Summary Report
                </h5>
                <span class="edit-badge">
                    <i class="fa-regular fa-pen-to-square"></i> Admin Editing
                </span>
            </div>

            <div class="station-form">
                <div class="row g-3">

                    {{-- Filling Station — read only --}}
                    <div class="col-md-3">
                        <label class="form-label-custom">
                            <i class="fa-solid fa-gas-pump fa-xs"></i> Filling Station
                        </label>
                        <input type="text" class="form-control-readonly"
                               value="{{ $fuelReport->station_name }}" readonly>
                    </div>

                    {{-- Division — read only --}}
                    <div class="col-md-2">
                        <label class="form-label-custom">
                            <i class="fa-solid fa-building fa-xs"></i> Division
                        </label>
                        <input type="text" class="form-control-readonly"
                               value="{{ $fuelReport->division }}" readonly>
                    </div>

                    {{-- District — read only --}}
                    <div class="col-md-2">
                        <label class="form-label-custom">
                            <i class="fa-solid fa-location-dot fa-xs"></i> District
                        </label>
                        <input type="text" class="form-control-readonly"
                               value="{{ $fuelReport->district }}" readonly>
                    </div>

                    {{-- Upazila — read only --}}
                    <div class="col-md-2">
                        <label class="form-label-custom">
                            <i class="fa-solid fa-map-pin fa-xs"></i> Thana / Upazila
                        </label>
                        <input type="text" class="form-control-readonly"
                               value="{{ $fuelReport->thana_upazila }}" readonly>
                    </div>

                    {{-- Report Date — editable --}}
                    <div class="col-md-2">
                        <label class="form-label-custom">
                            <i class="fa-regular fa-calendar fa-xs"></i> Report Date
                        </label>
                        <input type="date" name="report_date"
                               class="form-control-custom @error('report_date') is-invalid @enderror"
                               value="{{ old('report_date', \Carbon\Carbon::parse($fuelReport->report_date)->format('Y-m-d')) }}"
                               required>
                        @error('report_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tag Officer — read only, shows who submitted --}}
                    <div class="col-md-3">
                        <label class="form-label-custom">
                            <i class="fa-solid fa-user-tie fa-xs"></i> Submitted By (Tag Officer)
                        </label>
                        <div class="officer-readonly-box">
                            <i class="fa-solid fa-circle-user"></i>
                            <div>
                                <div class="officer-name">{{ $tagOfficerName }}</div>
                                <div class="officer-sub">Submitted this report</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ══ Fuel Cards ══ --}}
        @php
            $fuelDefs = [
                ['key' => 'octane', 'label' => 'Octane', 'icon' => 'fa-droplet',   'cls' => 'octane'],
                ['key' => 'petrol', 'label' => 'Petrol', 'icon' => 'fa-gas-pump',  'cls' => 'petrol'],
                ['key' => 'diesel', 'label' => 'Diesel', 'icon' => 'fa-cube',      'cls' => 'diesel'],
                ['key' => 'others', 'label' => 'Others', 'icon' => 'fa-industry',  'cls' => 'others'],
            ];
        @endphp

        @foreach($fuelDefs as $fd)
        @php $fk = $fd['key']; @endphp
        <div class="custom-card">
            <div class="category-bar {{ $fd['cls'] }}">
                <i class="fa-solid {{ $fd['icon'] }}"></i> {{ $fd['label'] }}
                <span class="bar-line"></span>
            </div>

            <div class="input-grid">

                {{-- Column Headers --}}
                <div class="grid-head">
                    <i class="fa-solid fa-clock-rotate-left fa-xs"></i> Previous Stock (L)
                </div>
                <div class="grid-head">
                    <i class="fa-solid fa-truck fa-xs"></i> Supply From Depot (L)
                </div>
                <div class="grid-head">
                    <i class="fa-solid fa-arrow-down fa-xs"></i> Received At Station (L)
                </div>
                <div class="grid-head">
                    <i class="fa-solid fa-calculator fa-xs"></i> Difference (L)
                </div>
                <div class="grid-head">
                    <i class="fa-solid fa-chart-line fa-xs"></i> Sales (L)
                </div>
                <div class="grid-head">
                    <i class="fa-solid fa-warehouse fa-xs"></i> Closing Stock (L)
                </div>

                {{-- Previous Stock --}}
                <div class="grid-cell" data-label="Previous Stock (L)">
                    <input type="number" step="0.01" min="0"
                           name="{{ $fk }}_prev_stock"
                           id="{{ $fk }}_prev_stock"
                           class="input-field @error($fk.'_prev_stock') is-invalid @enderror"
                           value="{{ old($fk.'_prev_stock', $fuelReport->{$fk.'_prev_stock'} ?? 0) }}"
                           oninput="calcRow('{{ $fk }}')">
                    @error($fk.'_prev_stock')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Supply --}}
                <div class="grid-cell" data-label="Supply From Depot (L)">
                    <input type="number" step="0.01" min="0"
                           name="{{ $fk }}_supply"
                           id="{{ $fk }}_supply"
                           class="input-field @error($fk.'_supply') is-invalid @enderror"
                           value="{{ old($fk.'_supply', $fuelReport->{$fk.'_supply'} ?? 0) }}"
                           oninput="calcRow('{{ $fk }}')">
                    @error($fk.'_supply')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Received --}}
                <div class="grid-cell" data-label="Received At Station (L)">
                    <input type="number" step="0.01" min="0"
                           name="{{ $fk }}_received"
                           id="{{ $fk }}_received"
                           class="input-field @error($fk.'_received') is-invalid @enderror"
                           value="{{ old($fk.'_received', $fuelReport->{$fk.'_received'} ?? 0) }}"
                           oninput="calcRow('{{ $fk }}')">
                    @error($fk.'_received')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Difference (auto-calculated) --}}
                <div class="grid-cell-auto diff-bg" data-label="Difference (L)">
                    <span class="auto-value diff" id="{{ $fk }}_difference_display">
                        {{ number_format(($fuelReport->{$fk.'_difference'} ?? 0), 2) }}
                    </span>
                    <span class="auto-label">Auto</span>
                </div>

                {{-- Sales --}}
                <div class="grid-cell" data-label="Sales (L)">
                    <input type="number" step="0.01" min="0"
                           name="{{ $fk }}_sales"
                           id="{{ $fk }}_sales"
                           class="input-field @error($fk.'_sales') is-invalid @enderror"
                           value="{{ old($fk.'_sales', $fuelReport->{$fk.'_sales'} ?? 0) }}"
                           oninput="calcRow('{{ $fk }}')">
                    @error($fk.'_sales')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Closing Stock (auto-calculated) --}}
                <div class="grid-cell-auto close-bg" data-label="Closing Stock (L)">
                    <span class="auto-value close" id="{{ $fk }}_closing_display">
                        {{ number_format(($fuelReport->{$fk.'_closing_stock'} ?? 0), 2) }}
                    </span>
                    <span class="auto-label">Auto</span>
                </div>

            </div>
        </div>
        @endforeach

        {{-- ── Comment ── --}}
        <div class="custom-card">
            <div style="padding:10px 16px; font-size:10px; font-weight:700;
                        text-transform:uppercase; letter-spacing:.5px; color:#64748b;
                        border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:6px;">
                <i class="fa-regular fa-comment" style="color:#94a3b8;"></i> Comment
            </div>
            <textarea name="comment"
                      style="width:100%; border:none; padding:14px 16px; font-size:13px;
                             color:var(--dark); resize:vertical; min-height:80px;
                             background:#fff; font-family:inherit; outline:none;"
                      placeholder="Enter any comments or notes here...">{{ old('comment', $fuelReport->comment) }}</textarea>
        </div>

        {{-- ── Submit Buttons ── --}}
        <div style="display:flex; gap:14px; margin-bottom:40px; align-items:center;">
            <button type="submit" class="btn-update">
                <i class="fa-solid fa-floppy-disk"></i> Update Report
            </button>
            <a href="{{ route('uno.reports.index') }}" class="btn-back">Cancel</a>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function calcRow(fuel) {
    const prev     = parseFloat(document.getElementById(fuel + '_prev_stock')?.value)  || 0;
    const supply   = parseFloat(document.getElementById(fuel + '_supply')?.value)      || 0;
    const received = parseFloat(document.getElementById(fuel + '_received')?.value)    || 0;
    const sales    = parseFloat(document.getElementById(fuel + '_sales')?.value)       || 0;

    const diff    = supply - received;
    const closing = prev + received - sales;

    const diffEl    = document.getElementById(fuel + '_difference_display');
    const closingEl = document.getElementById(fuel + '_closing_display');

    if (diffEl) {
        diffEl.textContent = diff.toFixed(2);
        // positive diff = supply > received = loss → red
        // negative diff = received > supply = surplus → green
        diffEl.style.color = diff > 0 ? '#dc2626' : diff < 0 ? '#16a34a' : '#94a3b8';
    }

    if (closingEl) {
        closingEl.textContent = closing.toFixed(2);
        closingEl.style.color = closing < 0 ? '#dc2626' : '#2563eb';
    }
}

// Recalculate all rows on page load so displayed values match inputs
document.addEventListener('DOMContentLoaded', () => {
    ['petrol', 'diesel', 'octane', 'others'].forEach(fuel => calcRow(fuel));
});
</script>
@endpush