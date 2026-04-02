@extends('backend.tag-officer.layouts.app')

@section('title', 'Report Details')

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
    .report-header { padding: 24px; border-bottom: 1px solid #f1f5f9; }
    .report-header h5 { font-weight: 700; color: var(--dark); font-size: 16px; margin-bottom: 14px; }
    .report-header h5 i { color: var(--primary); margin-right: 8px; }
    .meta-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    .meta-item { background: #f8fafc; border-radius: 7px; padding: 12px 14px; border: 1px solid #e2e8f0; }
    .meta-item .meta-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px; display: flex; align-items: center; gap: 5px; }
    .meta-item .meta-label i { color: var(--primary); }
    .meta-item .meta-value { font-size: 14px; font-weight: 700; color: var(--dark); }

    /* Category bar */
    .category-bar { padding: 11px 18px; font-size: 13px; font-weight: 700; color: var(--dark); border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; }
    .category-bar i { color: var(--primary); }
    .bar-line { flex: 1; height: 1px; background: #e2e8f0; margin-left: 6px; }

    /* Stats grid */
    .stats-grid { display: grid; grid-template-columns: repeat(6, 1fr); }
    .stat-head { background: #f8fafc; padding: 10px 8px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .3px; border-bottom: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0; text-align: center; }
    .stat-head:last-child { border-right: none; }
    .stat-cell { background: #fff; padding: 16px 8px; text-align: center; border-right: 1px solid #f1f5f9; }
    .stat-cell:last-child { border-right: none; }
    .stat-cell .val { font-size: 20px; font-weight: 700; color: var(--dark); display: block; }
    .stat-cell .lbl { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: .3px; margin-top: 3px; display: block; }
    .stat-cell.closing { background: #eff6ff; }
    .stat-cell.closing .val { color: var(--primary); }
    .diff-neg { color: #dc2626 !important; }
    .diff-pos { color: #16a34a !important; }

    /* Action buttons */
    .btn-back   { background: #f1f5f9; color: var(--dark); border: 1px solid #e2e8f0; border-radius: 7px; padding: 10px 20px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; }
    .btn-back:hover { background: #e2e8f0; color: var(--dark); }
    .btn-edit   { background: var(--primary); color: #fff; border-radius: 7px; padding: 10px 20px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; }
    .btn-edit:hover { background: #1d4ed8; color: #fff; }
    .btn-delete { background: #fff; color: #dc2626; border: 1px solid #fecaca; border-radius: 7px; padding: 10px 20px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 7px; cursor: pointer; }
    .btn-delete:hover { background: #fef2f2; }
</style>
@endpush

@section('content')
<div class="report-container">

    <div class="page-header">
        <h4><i class="fa-solid fa-file-lines"></i> Report Details</h4>
        <a href="{{ route('fuel-reports.index') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Back to List
        </a>
    </div>

    {{-- Station Info --}}
    <div class="custom-card">
        <div class="report-header">
            <h5><i class="fa-solid fa-gas-pump"></i> Fuel Oil Receipt and Distribution Summary Daily Report</h5>
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-label"><i class="fa-solid fa-building-columns fa-xs"></i> Filling Station</div>
                    <div class="meta-value">{{ $fuelReport->station_name }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label"><i class="fa-solid fa-map-pin fa-xs"></i> Thana / Upazila</div>
                    <div class="meta-value">{{ $fuelReport->thana_upazila }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label"><i class="fa-solid fa-location-dot fa-xs"></i> District</div>
                    <div class="meta-value">{{ $fuelReport->district }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label"><i class="fa-regular fa-calendar fa-xs"></i> Report Date</div>
                    <div class="meta-value">{{ $fuelReport->report_date->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Petrol --}}
    <div class="custom-card">
        <div class="category-bar">
            <i class="fa-solid fa-droplet"></i> Petrol
            <span class="bar-line"></span>
        </div>
        <div class="stats-grid">
            <div class="stat-head"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Prev. Stock (L)</div>
            <div class="stat-head"><i class="fa-solid fa-truck fa-xs"></i> Supply (L)</div>
            <div class="stat-head"><i class="fa-solid fa-arrow-down fa-xs"></i> Received (L)</div>
            <div class="stat-head"><i class="fa-solid fa-calculator fa-xs"></i> Difference (L)</div>
            <div class="stat-head"><i class="fa-solid fa-chart-line fa-xs"></i> Sales (L)</div>
            <div class="stat-head"><i class="fa-solid fa-warehouse fa-xs"></i> Closing Stock (L)</div>

            <div class="stat-cell">
                <span class="val">{{ number_format($fuelReport->petrol_prev_stock, 2) }}</span>
                <span class="lbl">Previous</span>
            </div>
            <div class="stat-cell">
                <span class="val">{{ number_format($fuelReport->petrol_supply, 2) }}</span>
                <span class="lbl">Supply</span>
            </div>
            <div class="stat-cell">
                <span class="val">{{ number_format($fuelReport->petrol_received, 2) }}</span>
                <span class="lbl">Received</span>
            </div>
            <div class="stat-cell">
                <span class="val {{ $fuelReport->petrol_difference < 0 ? 'diff-neg' : 'diff-pos' }}">
                    {{ $fuelReport->petrol_difference >= 0 ? '+' : '' }}{{ number_format($fuelReport->petrol_difference, 2) }}
                </span>
                <span class="lbl">Diff</span>
            </div>
            <div class="stat-cell">
                <span class="val">{{ number_format($fuelReport->petrol_sales, 2) }}</span>
                <span class="lbl">Sales</span>
            </div>
            <div class="stat-cell closing">
                <span class="val">{{ number_format($fuelReport->petrol_closing_stock, 2) }}</span>
                <span class="lbl">Closing</span>
            </div>
        </div>
    </div>

    {{-- Diesel --}}
    <div class="custom-card">
        <div class="category-bar">
            <i class="fa-solid fa-droplet"></i> Diesel
            <span class="bar-line"></span>
        </div>
        <div class="stats-grid">
            <div class="stat-head"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Prev. Stock (L)</div>
            <div class="stat-head"><i class="fa-solid fa-truck fa-xs"></i> Supply (L)</div>
            <div class="stat-head"><i class="fa-solid fa-arrow-down fa-xs"></i> Received (L)</div>
            <div class="stat-head"><i class="fa-solid fa-calculator fa-xs"></i> Difference (L)</div>
            <div class="stat-head"><i class="fa-solid fa-chart-line fa-xs"></i> Sales (L)</div>
            <div class="stat-head"><i class="fa-solid fa-warehouse fa-xs"></i> Closing Stock (L)</div>

            <div class="stat-cell"><span class="val">{{ number_format($fuelReport->diesel_prev_stock, 2) }}</span><span class="lbl">Previous</span></div>
            <div class="stat-cell"><span class="val">{{ number_format($fuelReport->diesel_supply, 2) }}</span><span class="lbl">Supply</span></div>
            <div class="stat-cell"><span class="val">{{ number_format($fuelReport->diesel_received, 2) }}</span><span class="lbl">Received</span></div>
            <div class="stat-cell">
                <span class="val {{ $fuelReport->diesel_difference < 0 ? 'diff-neg' : 'diff-pos' }}">
                    {{ $fuelReport->diesel_difference >= 0 ? '+' : '' }}{{ number_format($fuelReport->diesel_difference, 2) }}
                </span>
                <span class="lbl">Diff</span>
            </div>
            <div class="stat-cell"><span class="val">{{ number_format($fuelReport->diesel_sales, 2) }}</span><span class="lbl">Sales</span></div>
            <div class="stat-cell closing"><span class="val">{{ number_format($fuelReport->diesel_closing_stock, 2) }}</span><span class="lbl">Closing</span></div>
        </div>
    </div>

    {{-- Octane --}}
    <div class="custom-card">
        <div class="category-bar">
            <i class="fa-solid fa-droplet"></i> Octane
            <span class="bar-line"></span>
        </div>
        <div class="stats-grid">
            <div class="stat-head"><i class="fa-solid fa-clock-rotate-left fa-xs"></i> Prev. Stock (L)</div>
            <div class="stat-head"><i class="fa-solid fa-truck fa-xs"></i> Supply (L)</div>
            <div class="stat-head"><i class="fa-solid fa-arrow-down fa-xs"></i> Received (L)</div>
            <div class="stat-head"><i class="fa-solid fa-calculator fa-xs"></i> Difference (L)</div>
            <div class="stat-head"><i class="fa-solid fa-chart-line fa-xs"></i> Sales (L)</div>
            <div class="stat-head"><i class="fa-solid fa-warehouse fa-xs"></i> Closing Stock (L)</div>

            <div class="stat-cell"><span class="val">{{ number_format($fuelReport->octane_prev_stock, 2) }}</span><span class="lbl">Previous</span></div>
            <div class="stat-cell"><span class="val">{{ number_format($fuelReport->octane_supply, 2) }}</span><span class="lbl">Supply</span></div>
            <div class="stat-cell"><span class="val">{{ number_format($fuelReport->octane_received, 2) }}</span><span class="lbl">Received</span></div>
            <div class="stat-cell">
                <span class="val {{ $fuelReport->octane_difference < 0 ? 'diff-neg' : 'diff-pos' }}">
                    {{ $fuelReport->octane_difference >= 0 ? '+' : '' }}{{ number_format($fuelReport->octane_difference, 2) }}
                </span>
                <span class="lbl">Diff</span>
            </div>
            <div class="stat-cell"><span class="val">{{ number_format($fuelReport->octane_sales, 2) }}</span><span class="lbl">Sales</span></div>
            <div class="stat-cell closing"><span class="val">{{ number_format($fuelReport->octane_closing_stock, 2) }}</span><span class="lbl">Closing</span></div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="d-flex gap-3 mb-5">
        <a href="{{ route('fuel-reports.index') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left fa-xs"></i> Back
        </a>
        <a href="{{ route('fuel-reports.edit', $fuelReport) }}" class="btn-edit">
            <i class="fa-regular fa-pen-to-square fa-xs"></i> Edit
        </a>
        <form action="{{ route('fuel-reports.destroy', $fuelReport) }}" method="POST"
              onsubmit="return confirm('এই রিপোর্ট মুছে ফেলবেন?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-delete">
                <i class="fa-regular fa-trash-can fa-xs"></i> Delete
            </button>
        </form>
    </div>

</div>
@endsection