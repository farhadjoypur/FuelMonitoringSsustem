@extends('backend.tag-officer.layouts.app')

@section('title', 'New Fuel Report')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    :root {
        --primary: #2563eb;
        --dark:    #1e293b;
    }

    .report-container { background-color: #f1f5f9; min-height: 100vh; padding: 30px; }

    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
    .page-header h4 { font-weight: 700; color: var(--dark); margin: 0; font-size: 20px; }
    .page-header h4 i { color: var(--primary); margin-right: 8px; }

    .custom-card { background: #fff; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 18px; overflow: hidden; }

    /* Header card */
    .report-header { text-align: center; padding: 22px 20px 18px; border-bottom: 1px solid #f1f5f9; }
    .report-header h5 { font-weight: 700; color: var(--dark); font-size: 16px; margin-bottom: 0; }
    .report-header h5 i { color: var(--primary); margin-right: 8px; }

    /* Station form */
    .station-form { padding: 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    .form-label-custom { font-size: 11px; font-weight: 700; color: var(--dark); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px; display: block; }
    .form-label-custom i { color: var(--primary); margin-right: 4px; }
    .form-control-custom { width: 100%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 9px 12px; font-size: 13px; color: var(--dark); background: #fff; }
    .form-control-custom:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 2px rgba(37,99,235,.1); }
    .form-control-custom.is-invalid { border-color: #dc2626; }
    .invalid-feedback { font-size: 11px; color: #dc2626; margin-top: 4px; display: block; }

    /* Category bar */
    .category-bar { padding: 11px 18px; font-size: 13px; font-weight: 700; color: var(--dark); border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; }
    .category-bar i { color: var(--primary); }
    .bar-line { flex: 1; height: 1px; background: #e2e8f0; margin-left: 6px; }

    /* Input grid - 6 columns */
    .input-grid { display: grid; grid-template-columns: repeat(6, 1fr); }
    .grid-head { background: #f8fafc; padding: 10px 12px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .3px; border-bottom: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; text-align: center; }
    .grid-head:last-child { border-right: none; }
    .grid-cell { background: #fff; padding: 12px; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; }
    .grid-cell:last-child { border-right: none; }
    .grid-cell-auto { background: #f8fafc; padding: 12px; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; }

    .input-field { width: 100%; border: 1px solid #e2e8f0; border-radius: 5px; padding: 8px 10px; font-size: 13px; color: var(--dark); background: #fff; }
    .input-field:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 2px rgba(37,99,235,.1); }
    .input-field.is-invalid { border-color: #dc2626; }
    .input-field.prev-field { border-color: #bfdbfe; background: #eff6ff; }

    .auto-value { font-size: 17px; font-weight: 700; color: var(--primary); line-height: 1; }
    .auto-label { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: .3px; }
    .prev-note   { font-size: 10px; color: var(--primary); margin-top: 3px; }

    /* Buttons */
    .btn-save { background: var(--primary); color: #fff; border: none; border-radius: 8px; padding: 12px 30px; font-size: 14px; font-weight: 600; }
    .btn-save:hover { background: #1d4ed8; }
    .btn-save i { margin-right: 7px; }
    .btn-cancel { background: #f1f5f9; color: var(--dark); border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 24px; font-size: 14px; font-weight: 600; text-decoration: none; }
    .btn-cancel:hover { background: #e2e8f0; color: var(--dark); }

    .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; border-radius: 8px; padding: 11px 16px; margin-bottom: 18px; font-size: 13px; display: flex; align-items: center; gap: 8px; }

    .hint-text { font-size: 12px; color: #64748b; margin-top: 10px; }
    .hint-text i { color: var(--primary); margin-right: 4px; }
</style>
@endpush

@section('content')
<div class="report-container">

    @if(session('error'))
        <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif

    <div class="page-header">
        <h4><i class="fa-solid fa-gas-pump"></i> New Fuel Report</h4>
        <a href="{{ route('fuel-reports.index') }}" class="btn-cancel">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Back
        </a>
    </div>

    <form action="{{ route('fuel-reports.store') }}" method="POST">
        @csrf

        {{-- Station Info --}}
        <div class="custom-card">
            <div class="report-header">
                <h5><i class="fa-solid fa-file-lines"></i> Fuel Oil Receipt and Distribution Summary Daily Report</h5>
            </div>
            <div class="station-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom"><i class="fa-solid fa-building-columns fa-xs"></i> Filling Station Name</label>
                        <input type="text" name="station_name" id="station_name"
                               class="form-control-custom @error('station_name') is-invalid @enderror"
                               value="{{ old('station_name') }}"
                               placeholder="e.g. Uttara Filling Station"
                               list="station_list" autocomplete="off" required>
                        <datalist id="station_list">
                            @foreach($stations as $s)
                                <option value="{{ $s->station_name }}">{{ $s->thana_upazila }}, {{ $s->district }}</option>
                            @endforeach
                        </datalist>
                        @error('station_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom"><i class="fa-solid fa-map-pin fa-xs"></i> Thana / Upazila</label>
                        <input type="text" name="thana_upazila" id="thana_upazila"
                               class="form-control-custom @error('thana_upazila') is-invalid @enderror"
                               value="{{ old('thana_upazila') }}" placeholder="e.g. Raypura" required>
                        @error('thana_upazila')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom"><i class="fa-solid fa-location-dot fa-xs"></i> District</label>
                        <input type="text" name="district" id="district"
                               class="form-control-custom @error('district') is-invalid @enderror"
                               value="{{ old('district') }}" placeholder="e.g. Mymensingh" required>
                        @error('district')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-custom"><i class="fa-regular fa-calendar fa-xs"></i> Report Date</label>
                        <input type="date" name="report_date" id="report_date"
                               class="form-control-custom @error('report_date') is-invalid @enderror"
                               value="{{ old('report_date', date('Y-m-d')) }}" required>
                        @error('report_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
                <p class="hint-text">
                    <i class="fa-solid fa-circle-info"></i>
                    Station Name লেখার পর Previous Stock স্বয়ংক্রিয়ভাবে গতকালের Closing Stock থেকে পূরণ হবে।
                </p>
            </div>
        </div>

        {{-- PETROL --}}
        <div class="custom-card">
            <div class="category-bar">
                <i class="fa-solid fa-droplet"></i> Petrol
                <span class="bar-line"></span>
            </div>
            <div class="input-grid">
                <div class="grid-head"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Previous Stock (L)</div>
                <div class="grid-head"><i class="fa-solid fa-truck fa-xs"></i> Supply From Depot (L)</div>
                <div class="grid-head"><i class="fa-solid fa-arrow-down fa-xs"></i> Received (L)</div>
                <div class="grid-head"><i class="fa-solid fa-calculator fa-xs"></i> Difference (L)</div>
                <div class="grid-head"><i class="fa-solid fa-chart-line fa-xs"></i> Sales (L)</div>
                <div class="grid-head"><i class="fa-solid fa-warehouse fa-xs"></i> Closing Stock (L)</div>

                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="petrol_prev_stock" id="petrol_prev_stock"
                           class="input-field prev-field @error('petrol_prev_stock') is-invalid @enderror"
                           value="{{ old('petrol_prev_stock', $previousStocks['petrol'] ?? 0) }}"
                           oninput="calcRow('petrol')">
                    <span class="prev-note"><i class="fa-solid fa-rotate fa-xs"></i> Auto from yesterday</span>
                    @error('petrol_prev_stock')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="petrol_supply" id="petrol_supply"
                           class="input-field @error('petrol_supply') is-invalid @enderror"
                           value="{{ old('petrol_supply', 0) }}" oninput="calcRow('petrol')">
                    @error('petrol_supply')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="petrol_received" id="petrol_received"
                           class="input-field @error('petrol_received') is-invalid @enderror"
                           value="{{ old('petrol_received', 0) }}" oninput="calcRow('petrol')">
                    @error('petrol_received')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell-auto">
                    <span class="auto-value" id="petrol_difference_display">0</span>
                    <span class="auto-label">Auto</span>
                </div>
                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="petrol_sales" id="petrol_sales"
                           class="input-field @error('petrol_sales') is-invalid @enderror"
                           value="{{ old('petrol_sales', 0) }}" oninput="calcRow('petrol')">
                    @error('petrol_sales')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell-auto">
                    <span class="auto-value" id="petrol_closing_display">0</span>
                    <span class="auto-label">Auto</span>
                </div>
            </div>
        </div>

        {{-- DIESEL --}}
        <div class="custom-card">
            <div class="category-bar">
                <i class="fa-solid fa-droplet"></i> Diesel
                <span class="bar-line"></span>
            </div>
            <div class="input-grid">
                <div class="grid-head"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Previous Stock (L)</div>
                <div class="grid-head"><i class="fa-solid fa-truck fa-xs"></i> Supply From Depot (L)</div>
                <div class="grid-head"><i class="fa-solid fa-arrow-down fa-xs"></i> Received (L)</div>
                <div class="grid-head"><i class="fa-solid fa-calculator fa-xs"></i> Difference (L)</div>
                <div class="grid-head"><i class="fa-solid fa-chart-line fa-xs"></i> Sales (L)</div>
                <div class="grid-head"><i class="fa-solid fa-warehouse fa-xs"></i> Closing Stock (L)</div>

                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="diesel_prev_stock" id="diesel_prev_stock"
                           class="input-field prev-field @error('diesel_prev_stock') is-invalid @enderror"
                           value="{{ old('diesel_prev_stock', $previousStocks['diesel'] ?? 0) }}"
                           oninput="calcRow('diesel')">
                    <span class="prev-note"><i class="fa-solid fa-rotate fa-xs"></i> Auto from yesterday</span>
                    @error('diesel_prev_stock')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="diesel_supply" id="diesel_supply"
                           class="input-field @error('diesel_supply') is-invalid @enderror"
                           value="{{ old('diesel_supply', 0) }}" oninput="calcRow('diesel')">
                    @error('diesel_supply')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="diesel_received" id="diesel_received"
                           class="input-field @error('diesel_received') is-invalid @enderror"
                           value="{{ old('diesel_received', 0) }}" oninput="calcRow('diesel')">
                    @error('diesel_received')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell-auto">
                    <span class="auto-value" id="diesel_difference_display">0</span>
                    <span class="auto-label">Auto</span>
                </div>
                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="diesel_sales" id="diesel_sales"
                           class="input-field @error('diesel_sales') is-invalid @enderror"
                           value="{{ old('diesel_sales', 0) }}" oninput="calcRow('diesel')">
                    @error('diesel_sales')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell-auto">
                    <span class="auto-value" id="diesel_closing_display">0</span>
                    <span class="auto-label">Auto</span>
                </div>
            </div>
        </div>

        {{-- OCTANE --}}
        <div class="custom-card">
            <div class="category-bar">
                <i class="fa-solid fa-droplet"></i> Octane
                <span class="bar-line"></span>
            </div>
            <div class="input-grid">
                <div class="grid-head"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Previous Stock (L)</div>
                <div class="grid-head"><i class="fa-solid fa-truck fa-xs"></i> Supply From Depot (L)</div>
                <div class="grid-head"><i class="fa-solid fa-arrow-down fa-xs"></i> Received (L)</div>
                <div class="grid-head"><i class="fa-solid fa-calculator fa-xs"></i> Difference (L)</div>
                <div class="grid-head"><i class="fa-solid fa-chart-line fa-xs"></i> Sales (L)</div>
                <div class="grid-head"><i class="fa-solid fa-warehouse fa-xs"></i> Closing Stock (L)</div>

                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="octane_prev_stock" id="octane_prev_stock"
                           class="input-field prev-field @error('octane_prev_stock') is-invalid @enderror"
                           value="{{ old('octane_prev_stock', $previousStocks['octane'] ?? 0) }}"
                           oninput="calcRow('octane')">
                    <span class="prev-note"><i class="fa-solid fa-rotate fa-xs"></i> Auto from yesterday</span>
                    @error('octane_prev_stock')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="octane_supply" id="octane_supply"
                           class="input-field @error('octane_supply') is-invalid @enderror"
                           value="{{ old('octane_supply', 0) }}" oninput="calcRow('octane')">
                    @error('octane_supply')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="octane_received" id="octane_received"
                           class="input-field @error('octane_received') is-invalid @enderror"
                           value="{{ old('octane_received', 0) }}" oninput="calcRow('octane')">
                    @error('octane_received')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell-auto">
                    <span class="auto-value" id="octane_difference_display">0</span>
                    <span class="auto-label">Auto</span>
                </div>
                <div class="grid-cell">
                    <input type="number" step="0.01" min="0" name="octane_sales" id="octane_sales"
                           class="input-field @error('octane_sales') is-invalid @enderror"
                           value="{{ old('octane_sales', 0) }}" oninput="calcRow('octane')">
                    @error('octane_sales')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="grid-cell-auto">
                    <span class="auto-value" id="octane_closing_display">0</span>
                    <span class="auto-label">Auto</span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 mb-5">
            <button type="submit" class="btn-save">
                <i class="fa-solid fa-floppy-disk"></i> Save Report
            </button>
            <a href="{{ route('fuel-reports.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function calcRow(fuel) {
    const prev     = parseFloat(document.getElementById(fuel + '_prev_stock').value)  || 0;
    const supply   = parseFloat(document.getElementById(fuel + '_supply').value)      || 0;
    const received = parseFloat(document.getElementById(fuel + '_received').value)    || 0;
    const sales    = parseFloat(document.getElementById(fuel + '_sales').value)       || 0;

    const diff    = supply - received;
    const closing = prev + received - sales;

    const diffEl    = document.getElementById(fuel + '_difference_display');
    const closingEl = document.getElementById(fuel + '_closing_display');

    diffEl.textContent = diff.toFixed(2);
    diffEl.style.color = diff < 0 ? '#dc2626' : diff > 0 ? '#16a34a' : '#94a3b8';

    closingEl.textContent    = closing.toFixed(2);
    closingEl.style.color    = closing < 0 ? '#dc2626' : 'var(--primary)';
}

let ajaxTimer;
document.getElementById('station_name').addEventListener('input', function () {
    clearTimeout(ajaxTimer);
    const name = this.value.trim();

    // thana & district auto-fill from blade data
    @foreach($stations as $s)
    if (name.toLowerCase() === "{{ strtolower($s->station_name) }}") {
        document.getElementById('thana_upazila').value = "{{ $s->thana_upazila }}";
        document.getElementById('district').value      = "{{ $s->district }}";
    }
    @endforeach

    if (name.length < 3) return;

    ajaxTimer = setTimeout(() => {
        fetch("{{ route('fuel-reports.previous-stocks') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ station_name: name })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('petrol_prev_stock').value = data.stocks.petrol;
                document.getElementById('diesel_prev_stock').value = data.stocks.diesel;
                document.getElementById('octane_prev_stock').value = data.stocks.octane;
                calcRow('petrol'); calcRow('diesel'); calcRow('octane');
            }
        }).catch(() => {});
    }, 500);
});

document.addEventListener('DOMContentLoaded', () => {
    calcRow('petrol'); calcRow('diesel'); calcRow('octane');
});
</script>
@endpush