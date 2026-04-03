@extends('backend.admin.layouts.app')

@push('styles')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #f0f2f7;
    --surface: #ffffff;
    --border: #e2e8f0;
    --text: #1a202c;
    --muted: #718096;
    --primary: #0f4c81;
    --blue: #3b82f6;
    --green: #22c55e;
    --purple: #8b5cf6;
    --orange: #f97316;
    --red: #ef4444;
    --yellow: #f59e0b;
    --radius: 12px;
    --shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    /* padding: 28px; */
  }

  .page-title {
    font-family: 'Syne', sans-serif;
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--primary);
    margin-bottom: 22px;
    letter-spacing: -.3px;
  }

  .card {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    animation: fadeUp .3s ease both;
  }

  /* TABS */
  .tabs-bar {
    display: flex;
    gap: 2px;
    border-bottom: 1px solid var(--border);
    padding: 0 20px;
    overflow-x: auto;
  }
  .tab-btn {
    font-family: 'DM Sans', sans-serif;
    font-size: .875rem;
    font-weight: 500;
    color: var(--muted);
    background: none;
    border: none;
    border-bottom: 2.5px solid transparent;
    padding: 14px 18px;
    cursor: pointer;
    white-space: nowrap;
    transition: color .2s, border-color .2s;
  }
  .tab-btn:hover { color: var(--primary); }
  .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); font-weight: 600; }

  .tab-panel { display: none; }
  .tab-panel.active { display: block; }

  /* FILTER */
  .filter-section { padding: 20px 24px 24px; }
  .filter-header {
    display: flex; align-items: center; gap: 8px;
    font-weight: 600; font-size: .9rem; color: var(--primary); margin-bottom: 16px;
  }
  .filter-header i { color: var(--blue); }
  .filter-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px 18px;
  }
  .filter-group label {
    display: block; font-size: .78rem; font-weight: 500;
    color: var(--muted); margin-bottom: 5px;
    text-transform: uppercase; letter-spacing: .4px;
  }
  .filter-group input,
  .filter-group select {
    width: 100%; padding: 9px 12px;
    border: 1px solid var(--border); border-radius: 8px;
    font-family: 'DM Sans', sans-serif; font-size: .875rem;
    color: var(--text); background: #fafbfc; outline: none;
    transition: border-color .2s, box-shadow .2s; appearance: none;
  }
  .filter-group input:focus,
  .filter-group select:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(59,130,246,.12);
    background: #fff;
  }
  .filter-apply-row {
    grid-column: 3 / 5;
    display: flex; align-items: flex-end; justify-content: flex-end; gap: 10px;
  }
  .btn-apply {
    background: linear-gradient(135deg, #0f4c81, #1e6fb5);
    color: #fff; border: none; border-radius: 8px;
    padding: 10px 28px; font-family: 'DM Sans', sans-serif;
    font-size: .875rem; font-weight: 600; cursor: pointer;
    transition: transform .15s, box-shadow .15s;
    box-shadow: 0 4px 12px rgba(15,76,129,.25);
  }
  .btn-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(15,76,129,.3); }
  .btn-reset {
    background: transparent; color: var(--muted);
    border: 1px solid var(--border); border-radius: 8px;
    padding: 10px 18px; font-family: 'DM Sans', sans-serif;
    font-size: .875rem; font-weight: 500; cursor: pointer; transition: all .15s;
  }
  .btn-reset:hover { background: var(--bg); color: var(--text); }

  /* STAT CARDS */
  .stat-cards {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 16px; padding: 0 24px 24px;
  }
  .stat-card {
    border-radius: 12px; padding: 18px 20px;
    position: relative; overflow: hidden; color: #fff;
    min-height: 100px; display: flex; flex-direction: column;
    justify-content: space-between; cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    animation: fadeUp .35s ease both;
  }
  .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,.18); }
  .stat-card.blue   { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .stat-card.green  { background: linear-gradient(135deg, #22c55e, #15803d); }
  .stat-card.purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
  .stat-card.orange { background: linear-gradient(135deg, #f97316, #c2410c); }
  .stat-card.red    { background: linear-gradient(135deg, #ef4444, #b91c1c); }
  .stat-card.teal   { background: linear-gradient(135deg, #14b8a6, #0f766e); }
  .stat-card:nth-child(1) { animation-delay: .05s; }
  .stat-card:nth-child(2) { animation-delay: .10s; }
  .stat-card:nth-child(3) { animation-delay: .15s; }
  .stat-card:nth-child(4) { animation-delay: .20s; }
  .stat-card-top { display: flex; align-items: flex-start; justify-content: space-between; }
  .stat-card-icon {
    width: 36px; height: 36px; background: rgba(255,255,255,.2);
    border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1rem;
  }
  .stat-card-trend {
    display: flex; align-items: center; gap: 4px; font-size: .75rem;
    background: rgba(255,255,255,.15); padding: 3px 8px; border-radius: 20px;
  }
  .stat-card-label { font-size: .78rem; font-weight: 500; opacity: .85; margin-top: 10px; letter-spacing: .2px; }
  .stat-card-value { font-family: 'Syne', sans-serif; font-size: 1.65rem; font-weight: 800; letter-spacing: -.5px; margin-top: 2px; }
  .stat-card::after {
    content: ''; position: absolute; right: -20px; bottom: -20px;
    width: 90px; height: 90px; border-radius: 50%; background: rgba(255,255,255,.08);
  }

  /* TABLE */
  .table-section { padding: 0 24px 24px; }
  .table-header-row {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;
  }
  .table-title { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--primary); }
  .table-meta { font-size: .78rem; color: var(--muted); }
  .table-wrap { border: 1px solid var(--border); border-radius: 10px; overflow: hidden; overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; font-size: .84rem; }
  thead tr { background: #f7f9fc; }
  thead th {
    padding: 11px 14px; text-align: left; font-size: .72rem;
    font-weight: 700; color: var(--muted); text-transform: uppercase;
    letter-spacing: .5px; border-bottom: 1px solid var(--border); white-space: nowrap;
  }
  tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .15s; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover { background: #f8faff; }
  tbody td { padding: 11px 14px; color: var(--text); vertical-align: middle; }
  .depot-cell { font-weight: 600; color: var(--primary); font-size: .82rem; }
  .station-cell { font-weight: 500; font-size: .82rem; }
  .fuel-type-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; }
  .fuel-diesel  { background: #eff6ff; color: #2563eb; }
  .fuel-petrol  { background: #f0fdf4; color: #16a34a; }
  .fuel-octane  { background: #fdf4ff; color: #9333ea; }
  .received-val { color: #16a34a; font-weight: 600; }
  .sold-val     { color: #ef4444; font-weight: 600; }
  .current-val  { font-weight: 700; color: var(--text); }
  .diff-positive { color: #ef4444; font-weight: 600; }
  .diff-negative { color: #22c55e; font-weight: 600; }
  .diff-zero     { color: var(--muted); font-weight: 500; }
  .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; white-space: nowrap; }
  .status-available { background: #dcfce7; color: #15803d; }
  .status-low       { background: #fef9c3; color: #b45309; }
  .status-zero      { background: #fee2e2; color: #b91c1c; }
  .status-active    { background: #dcfce7; color: #15803d; }
  .status-inactive  { background: #fee2e2; color: #b91c1c; }
  .officer-name     { font-weight: 600; font-size: .82rem; }
  .empty-row td { text-align: center; padding: 40px; color: var(--muted); font-size: .88rem; }

  /* EXPORT */
  .export-row {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 16px 24px 20px; border-top: 1px solid var(--border);
  }
  .btn-export {
    display: flex; align-items: center; gap: 8px; padding: 9px 20px;
    border-radius: 8px; font-family: 'DM Sans', sans-serif;
    font-size: .84rem; font-weight: 600; cursor: pointer; border: 1.5px solid; transition: all .15s;
  }
  .btn-pdf  { border-color: #e2e8f0; color: #475569; background: #fff; }
  .btn-pdf:hover { background: #f8f9fa; border-color: #cbd5e1; }
  .btn-excel { border-color: #16a34a; color: #fff; background: #16a34a; }
  .btn-excel:hover { background: #15803d; }

  /* COMING SOON */
  .coming-soon { text-align: center; padding: 60px 20px; color: var(--muted); }
  .coming-soon i { font-size: 2.5rem; margin-bottom: 14px; opacity: .3; display: block; }
  .coming-soon p { font-size: .9rem; }

  /* SUMMARY ROW */
  .summary-row td { background: #f7f9fc; font-weight: 700; font-size: .83rem; border-top: 2px solid var(--border); }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 900px) {
    .filter-grid  { grid-template-columns: repeat(2, 1fr); }
    .stat-cards   { grid-template-columns: repeat(2, 1fr); }
    .filter-apply-row { grid-column: 1 / 3; justify-content: flex-start; }
  }
</style>
@endpush

@section('content')

<h1 class="page-title">
  <i class="fa-solid fa-chart-mixed" style="font-size:1.2rem; margin-right:8px; opacity:.7;"></i>
  Reports &amp; Analytics
</h1>

<div class="card">

  {{-- ══════════════ TABS ══════════════ --}}
  <div class="tabs-bar">
    <button class="tab-btn active" onclick="switchTab('stock', this)">
      <i class="fa-solid fa-box-archive" style="margin-right:6px; font-size:.8rem;"></i>Stock Report
    </button>
    <button class="tab-btn" onclick="switchTab('sales', this)">
      <i class="fa-solid fa-chart-line" style="margin-right:6px; font-size:.8rem;"></i>Sales Report
    </button>
    <button class="tab-btn" onclick="switchTab('officer', this)">
      <i class="fa-solid fa-user-tie" style="margin-right:6px; font-size:.8rem;"></i>Tag Officer Report
    </button>
    <button class="tab-btn" onclick="switchTab('diff', this)">
      <i class="fa-solid fa-percent" style="margin-right:6px; font-size:.8rem;"></i>Difference (%) Report
    </button>
    <button class="tab-btn" onclick="switchTab('due', this)">
      <i class="fa-solid fa-clock-rotate-left" style="margin-right:6px; font-size:.8rem;"></i>Due Sales Report
    </button>
  </div>

  {{-- ══════════════════════════════════════
       TAB 1 — STOCK REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-stock" class="tab-panel active">

    {{-- Filters --}}
    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('admin.reports.index') }}" id="form-stock">
        <input type="hidden" name="tab" value="stock">
        <div class="filter-grid">
          <div class="filter-group">
            <label>From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}">
          </div>
          <div class="filter-group">
            <label>To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}">
          </div>
          <div class="filter-group">
            <label>Division</label>
            <select name="division">
              <option value="">All Divisions</option>
              @foreach($divisions as $div)
                <option value="{{ $div }}" {{ request('division') == $div ? 'selected' : '' }}>{{ $div }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>District</label>
            <select name="district">
              <option value="">All Districts</option>
              @foreach($districts as $dist)
                <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Depot</label>
            <select name="depot_id">
              <option value="">All Depots</option>
              @foreach($depots as $depot)
                <option value="{{ $depot->id }}" {{ request('depot_id') == $depot->id ? 'selected' : '' }}>
                  {{ $depot->depot_name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Filling Station</label>
            <select name="station_name">
              <option value="">All Stations</option>
              @foreach($stations as $station)
                <option value="{{ $station->station_name }}" {{ request('station_name') == $station->station_name ? 'selected' : '' }}>
                  {{ $station->station_name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Company</label>
            <select name="company_id">
              <option value="">All Companies</option>
              @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                  {{ $company->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Fuel Type</label>
            <select name="fuel_type">
              <option value="">All Types</option>
              <option value="diesel"  {{ request('fuel_type') == 'diesel'  ? 'selected' : '' }}>Diesel</option>
              <option value="petrol"  {{ request('fuel_type') == 'petrol'  ? 'selected' : '' }}>Petrol</option>
              <option value="octane"  {{ request('fuel_type') == 'octane'  ? 'selected' : '' }}>Octane</option>
            </select>
          </div>
          <div class="filter-group">
            <label>Stock Status</label>
            <select name="stock_status">
              <option value="">All Stock</option>
              <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Available</option>
              <option value="low"       {{ request('stock_status') == 'low'       ? 'selected' : '' }}>Low Stock</option>
              <option value="zero"      {{ request('stock_status') == 'zero'      ? 'selected' : '' }}>Zero Stock</option>
            </select>
          </div>
          <div class="filter-apply-row">
            <a href="{{ route('admin.reports.index') }}?tab=stock" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:5px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">
              <i class="fa-solid fa-magnifying-glass" style="margin-right:6px;"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-cards">
      @php
        $fuelType = request('fuel_type');
        $showAll  = !$fuelType;
      @endphp

      @if($showAll || $fuelType === 'diesel')
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
          <div class="stat-card-trend">
            <i class="fa-solid fa-database"></i> Total
          </div>
        </div>
        <div>
          <div class="stat-card-label">Total Closing Stock</div>
          <div class="stat-card-value">
            {{ number_format($stockReports->sum('petrol_closing_stock') + $stockReports->sum('diesel_closing_stock') + $stockReports->sum('octane_closing_stock')) }} L
          </div>
        </div>
      </div>
      @endif

      @if($showAll || $fuelType === 'diesel')
      <div class="stat-card green">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend">Diesel</div>
        </div>
        <div>
          <div class="stat-card-label">Diesel Stock</div>
          <div class="stat-card-value">{{ number_format($stockReports->sum('diesel_closing_stock')) }} L</div>
        </div>
      </div>
      @endif

      @if($showAll || $fuelType === 'petrol')
      <div class="stat-card purple">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend">Petrol</div>
        </div>
        <div>
          <div class="stat-card-label">Petrol Stock</div>
          <div class="stat-card-value">{{ number_format($stockReports->sum('petrol_closing_stock')) }} L</div>
        </div>
      </div>
      @endif

      @if($showAll || $fuelType === 'octane')
      <div class="stat-card orange">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend">Octane</div>
        </div>
        <div>
          <div class="stat-card-label">Octane Stock</div>
          <div class="stat-card-value">{{ number_format($stockReports->sum('octane_closing_stock')) }} L</div>
        </div>
      </div>
      @endif
    </div>

    {{-- Table --}}
    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Stock Report by Depot &amp; Filling Station</div>
        <div class="table-meta">{{ $stockReports->count() }} record(s) found</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Depot</th>
              <th>Filling Station</th>
              <th>District</th>
              <th>Report Date</th>
              @if($showAll || $fuelType === 'diesel')
              <th>Diesel Opening</th>
              <th>Diesel Received</th>
              <th>Diesel Sold</th>
              <th>Diesel Closing</th>
              @endif
              @if($showAll || $fuelType === 'petrol')
              <th>Petrol Opening</th>
              <th>Petrol Received</th>
              <th>Petrol Sold</th>
              <th>Petrol Closing</th>
              @endif
              @if($showAll || $fuelType === 'octane')
              <th>Octane Opening</th>
              <th>Octane Received</th>
              <th>Octane Sold</th>
              <th>Octane Closing</th>
              @endif
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($stockReports as $i => $report)
              @php
                $totalClosing = $report->diesel_closing_stock + $report->petrol_closing_stock + $report->octane_closing_stock;
                if ($totalClosing <= 0) {
                  $statusClass = 'status-zero';
                  $statusLabel = 'Zero Stock';
                } elseif ($totalClosing < 2000) {
                  $statusClass = 'status-low';
                  $statusLabel = 'Low Stock';
                } else {
                  $statusClass = 'status-available';
                  $statusLabel = 'Available';
                }
              @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="depot-cell">{{ $report->depot->depot_name ?? $report->depot_name ?? '—' }}</td>
                <td class="station-cell">{{ $report->station_name }}</td>
                <td>{{ $report->district }}</td>
                <td>{{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}</td>
                @if($showAll || $fuelType === 'diesel')
                <td>{{ number_format($report->diesel_opening_stock) }} L</td>
                <td class="received-val">+{{ number_format($report->diesel_received) }} L</td>
                <td class="sold-val">-{{ number_format($report->diesel_sales) }} L</td>
                <td class="current-val">{{ number_format($report->diesel_closing_stock) }} L</td>
                @endif
                @if($showAll || $fuelType === 'petrol')
                <td>{{ number_format($report->petrol_opening_stock) }} L</td>
                <td class="received-val">+{{ number_format($report->petrol_received) }} L</td>
                <td class="sold-val">-{{ number_format($report->petrol_sales) }} L</td>
                <td class="current-val">{{ number_format($report->petrol_closing_stock) }} L</td>
                @endif
                @if($showAll || $fuelType === 'octane')
                <td>{{ number_format($report->octane_opening_stock) }} L</td>
                <td class="received-val">+{{ number_format($report->octane_received) }} L</td>
                <td class="sold-val">-{{ number_format($report->octane_sales) }} L</td>
                <td class="current-val">{{ number_format($report->octane_closing_stock) }} L</td>
                @endif
                <td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
              </tr>
            @empty
              <tr class="empty-row">
                <td colspan="20"><i class="fa-solid fa-inbox" style="margin-right:8px; opacity:.4;"></i>No stock report data found.</td>
              </tr>
            @endforelse

            {{-- Summary Row --}}
            @if($stockReports->count() > 0)
            <tr class="summary-row">
              <td colspan="5" style="text-align:right; padding-right:14px;">TOTAL</td>
              @if($showAll || $fuelType === 'diesel')
              <td>{{ number_format($stockReports->sum('diesel_opening_stock')) }} L</td>
              <td>{{ number_format($stockReports->sum('diesel_received')) }} L</td>
              <td>{{ number_format($stockReports->sum('diesel_sales')) }} L</td>
              <td>{{ number_format($stockReports->sum('diesel_closing_stock')) }} L</td>
              @endif
              @if($showAll || $fuelType === 'petrol')
              <td>{{ number_format($stockReports->sum('petrol_opening_stock')) }} L</td>
              <td>{{ number_format($stockReports->sum('petrol_received')) }} L</td>
              <td>{{ number_format($stockReports->sum('petrol_sales')) }} L</td>
              <td>{{ number_format($stockReports->sum('petrol_closing_stock')) }} L</td>
              @endif
              @if($showAll || $fuelType === 'octane')
              <td>{{ number_format($stockReports->sum('octane_opening_stock')) }} L</td>
              <td>{{ number_format($stockReports->sum('octane_received')) }} L</td>
              <td>{{ number_format($stockReports->sum('octane_sales')) }} L</td>
              <td>{{ number_format($stockReports->sum('octane_closing_stock')) }} L</td>
              @endif
              <td></td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

    <div class="export-row">
      {{-- <a href="{{ route('admin.reports.export', ['type' => 'stock', 'format' => 'pdf']) . '?' . http_build_query(request()->all()) }}" class="btn-export btn-pdf">
        <i class="fa-regular fa-file-pdf"></i> Export to PDF
      </a>
      <a href="{{ route('admin.reports.export', ['type' => 'stock', 'format' => 'excel']) . '?' . http_build_query(request()->all()) }}" class="btn-export btn-excel">
        <i class="fa-regular fa-file-excel"></i> Export to Excel
      </a> --}}
    </div>
  </div>{{-- /tab-stock --}}


  {{-- ══════════════════════════════════════
       TAB 2 — SALES REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-sales" class="tab-panel">
    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('admin.reports.index') }}" id="form-sales">
        <input type="hidden" name="tab" value="sales">
        <div class="filter-grid">
          <div class="filter-group">
            <label>From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}">
          </div>
          <div class="filter-group">
            <label>To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}">
          </div>
          <div class="filter-group">
            <label>Division</label>
            <select name="division">
              <option value="">All Divisions</option>
              @foreach($divisions as $div)
                <option value="{{ $div }}" {{ request('division') == $div ? 'selected' : '' }}>{{ $div }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>District</label>
            <select name="district">
              <option value="">All Districts</option>
              @foreach($districts as $dist)
                <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Depot</label>
            <select name="depot_id">
              <option value="">All Depots</option>
              @foreach($depots as $depot)
                <option value="{{ $depot->id }}" {{ request('depot_id') == $depot->id ? 'selected' : '' }}>{{ $depot->depot_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Filling Station</label>
            <select name="station_name">
              <option value="">All Stations</option>
              @foreach($stations as $station)
                <option value="{{ $station->station_name }}" {{ request('station_name') == $station->station_name ? 'selected' : '' }}>{{ $station->station_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Fuel Type</label>
            <select name="fuel_type">
              <option value="">All Types</option>
              <option value="diesel" {{ request('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
              <option value="petrol" {{ request('fuel_type') == 'petrol' ? 'selected' : '' }}>Petrol</option>
              <option value="octane" {{ request('fuel_type') == 'octane' ? 'selected' : '' }}>Octane</option>
            </select>
          </div>
          <div class="filter-apply-row" style="grid-column: 4 / 5;">
            <a href="{{ route('admin.reports.index') }}?tab=sales" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:5px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">
              <i class="fa-solid fa-magnifying-glass" style="margin-right:6px;"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    {{-- Sales Stat Cards --}}
    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-cart-shopping"></i></div>
          <div class="stat-card-trend">Total</div>
        </div>
        <div>
          <div class="stat-card-label">Total Sales</div>
          <div class="stat-card-value">
            {{ number_format($salesReports->sum('petrol_sales') + $salesReports->sum('diesel_sales') + $salesReports->sum('octane_sales')) }} L
          </div>
        </div>
      </div>
      <div class="stat-card green">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend">Diesel</div>
        </div>
        <div>
          <div class="stat-card-label">Diesel Sold</div>
          <div class="stat-card-value">{{ number_format($salesReports->sum('diesel_sales')) }} L</div>
        </div>
      </div>
      <div class="stat-card purple">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend">Petrol</div>
        </div>
        <div>
          <div class="stat-card-label">Petrol Sold</div>
          <div class="stat-card-value">{{ number_format($salesReports->sum('petrol_sales')) }} L</div>
        </div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend">Octane</div>
        </div>
        <div>
          <div class="stat-card-label">Octane Sold</div>
          <div class="stat-card-value">{{ number_format($salesReports->sum('octane_sales')) }} L</div>
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Sales Report by Station</div>
        <div class="table-meta">{{ $salesReports->count() }} record(s) found</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Station</th>
              <th>District</th>
              <th>Report Date</th>
              <th>Diesel Received</th>
              <th>Diesel Sold</th>
              <th>Petrol Received</th>
              <th>Petrol Sold</th>
              <th>Octane Received</th>
              <th>Octane Sold</th>
              <th>Total Sold</th>
            </tr>
          </thead>
          <tbody>
            @forelse($salesReports as $i => $report)
              @php $totalSold = $report->diesel_sales + $report->petrol_sales + $report->octane_sales; @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="station-cell">{{ $report->station_name }}</td>
                <td>{{ $report->district }}</td>
                <td>{{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}</td>
                <td class="received-val">{{ number_format($report->diesel_received) }} L</td>
                <td class="sold-val">{{ number_format($report->diesel_sales) }} L</td>
                <td class="received-val">{{ number_format($report->petrol_received) }} L</td>
                <td class="sold-val">{{ number_format($report->petrol_sales) }} L</td>
                <td class="received-val">{{ number_format($report->octane_received) }} L</td>
                <td class="sold-val">{{ number_format($report->octane_sales) }} L</td>
                <td class="current-val">{{ number_format($totalSold) }} L</td>
              </tr>
            @empty
              <tr class="empty-row">
                <td colspan="11"><i class="fa-solid fa-inbox" style="margin-right:8px; opacity:.4;"></i>No sales data found.</td>
              </tr>
            @endforelse
            @if($salesReports->count() > 0)
            <tr class="summary-row">
              <td colspan="4" style="text-align:right; padding-right:14px;">TOTAL</td>
              <td>{{ number_format($salesReports->sum('diesel_received')) }} L</td>
              <td>{{ number_format($salesReports->sum('diesel_sales')) }} L</td>
              <td>{{ number_format($salesReports->sum('petrol_received')) }} L</td>
              <td>{{ number_format($salesReports->sum('petrol_sales')) }} L</td>
              <td>{{ number_format($salesReports->sum('octane_received')) }} L</td>
              <td>{{ number_format($salesReports->sum('octane_sales')) }} L</td>
              <td>{{ number_format($salesReports->sum('petrol_sales') + $salesReports->sum('diesel_sales') + $salesReports->sum('octane_sales')) }} L</td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

    <div class="export-row">
      {{-- <a href="{{ route('admin.reports.export', ['type' => 'sales', 'format' => 'pdf']) . '?' . http_build_query(request()->all()) }}" class="btn-export btn-pdf">
        <i class="fa-regular fa-file-pdf"></i> Export to PDF
      </a>
      <a href="{{ route('admin.reports.export', ['type' => 'sales', 'format' => 'excel']) . '?' . http_build_query(request()->all()) }}" class="btn-export btn-excel">
        <i class="fa-regular fa-file-excel"></i> Export to Excel
      </a> --}}
    </div>
  </div>{{-- /tab-sales --}}


  {{-- ══════════════════════════════════════
       TAB 3 — TAG OFFICER REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-officer" class="tab-panel">
    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('admin.reports.index') }}">
        <input type="hidden" name="tab" value="officer">
        <div class="filter-grid">
          <div class="filter-group">
            <label>Division</label>
            <select name="division">
              <option value="">All Divisions</option>
              @foreach($divisions as $div)
                <option value="{{ $div }}" {{ request('division') == $div ? 'selected' : '' }}>{{ $div }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>District</label>
            <select name="district">
              <option value="">All Districts</option>
              @foreach($districts as $dist)
                <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Assignment Status</label>
            <select name="assign_status">
              <option value="">All</option>
              <option value="active"   {{ request('assign_status') == 'active'   ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ request('assign_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="filter-apply-row" style="grid-column: 4 / 5;">
            <a href="{{ route('admin.reports.index') }}?tab=officer" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:5px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">
              <i class="fa-solid fa-magnifying-glass" style="margin-right:6px;"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    {{-- Officer Stat Cards --}}
    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-users"></i></div>
          <div class="stat-card-trend">Total</div>
        </div>
        <div>
          <div class="stat-card-label">Total Officers</div>
          <div class="stat-card-value">{{ $officerReports->count() }}</div>
        </div>
      </div>
      <div class="stat-card green">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-user-check"></i></div>
          <div class="stat-card-trend">Active</div>
        </div>
        <div>
          <div class="stat-card-label">Active Assignments</div>
          <div class="stat-card-value">{{ $officerReports->where('status', 'active')->count() }}</div>
        </div>
      </div>
      <div class="stat-card red">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-user-xmark"></i></div>
          <div class="stat-card-trend">Inactive</div>
        </div>
        <div>
          <div class="stat-card-label">Inactive Assignments</div>
          <div class="stat-card-value">{{ $officerReports->where('status', 'inactive')->count() }}</div>
        </div>
      </div>
      <div class="stat-card teal">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-building"></i></div>
          <div class="stat-card-trend">Stations</div>
        </div>
        <div>
          <div class="stat-card-label">Stations Covered</div>
          <div class="stat-card-value">{{ $officerReports->pluck('filling_station_id')->unique()->count() }}</div>
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Tag Officer Assignment Report</div>
        <div class="table-meta">{{ $officerReports->count() }} record(s) found</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Officer Name</th>
              <th>Email / Phone</th>
              <th>Filling Station</th>
              <th>District</th>
              <th>Division</th>
              <th>Assigned At</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($officerReports as $i => $assign)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="officer-name">{{ $assign->user->name ?? '—' }}</td>
                <td style="font-size:.78rem; color:var(--muted);">
                  {{ $assign->user->email ?? '' }}<br>
                  {{ $assign->user->phone ?? '' }}
                </td>
                <td class="station-cell">{{ $assign->fillingStation->station_name ?? '—' }}</td>
                <td>{{ $assign->fillingStation->district ?? '—' }}</td>
                <td>{{ $assign->fillingStation->division ?? '—' }}</td>
                <td>{{ $assign->created_at ? $assign->created_at->format('d M Y') : '—' }}</td>
                <td>
                  <span class="status-badge {{ $assign->status === 'active' ? 'status-active' : 'status-inactive' }}">
                    {{ ucfirst($assign->status) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr class="empty-row">
                <td colspan="8"><i class="fa-solid fa-inbox" style="margin-right:8px; opacity:.4;"></i>No officer assignment data found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="export-row">
      {{-- <a href="{{ route('admin.reports.export', ['type' => 'officer', 'format' => 'pdf']) . '?' . http_build_query(request()->all()) }}" class="btn-export btn-pdf">
        <i class="fa-regular fa-file-pdf"></i> Export to PDF
      </a>
      <a href="{{ route('admin.reports.export', ['type' => 'officer', 'format' => 'excel']) . '?' . http_build_query(request()->all()) }}" class="btn-export btn-excel">
        <i class="fa-regular fa-file-excel"></i> Export to Excel
      </a> --}}
    </div>
  </div>{{-- /tab-officer --}}


  {{-- ══════════════════════════════════════
       TAB 4 — DIFFERENCE (%) REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-diff" class="tab-panel">
    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('admin.reports.index') }}">
        <input type="hidden" name="tab" value="diff">
        <div class="filter-grid">
          <div class="filter-group">
            <label>From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}">
          </div>
          <div class="filter-group">
            <label>To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}">
          </div>
          <div class="filter-group">
            <label>District</label>
            <select name="district">
              <option value="">All Districts</option>
              @foreach($districts as $dist)
                <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Min Difference (L)</label>
            <input type="number" name="min_diff" value="{{ request('min_diff', 0) }}" placeholder="e.g. 50">
          </div>
          <div class="filter-apply-row" style="grid-column: 3 / 5;">
            <a href="{{ route('admin.reports.index') }}?tab=diff" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:5px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">
              <i class="fa-solid fa-magnifying-glass" style="margin-right:6px;"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    {{-- Difference Stat Cards --}}
    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-scale-balanced"></i></div>
          <div class="stat-card-trend">All</div>
        </div>
        <div>
          <div class="stat-card-label">Total Records</div>
          <div class="stat-card-value">{{ $diffReports->count() }}</div>
        </div>
      </div>
      <div class="stat-card red">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <div class="stat-card-trend">Alert</div>
        </div>
        <div>
          <div class="stat-card-label">High Diff (&gt; 50L)</div>
          <div class="stat-card-value">
            {{ $diffReports->filter(fn($r) => abs($r->petrol_difference) + abs($r->diesel_difference) + abs($r->octane_difference) > 50)->count() }}
          </div>
        </div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-minus-plus"></i></div>
          <div class="stat-card-trend">Net</div>
        </div>
        <div>
          <div class="stat-card-label">Total Diesel Diff</div>
          <div class="stat-card-value">{{ number_format(abs($diffReports->sum('diesel_difference'))) }} L</div>
        </div>
      </div>
      <div class="stat-card purple">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-percent"></i></div>
          <div class="stat-card-trend">Net</div>
        </div>
        <div>
          <div class="stat-card-label">Total Petrol Diff</div>
          <div class="stat-card-value">{{ number_format(abs($diffReports->sum('petrol_difference'))) }} L</div>
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Fuel Difference (%) Report</div>
        <div class="table-meta">{{ $diffReports->count() }} record(s) found</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Station</th>
              <th>District</th>
              <th>Report Date</th>
              <th>Petrol Diff (L)</th>
              <th>Petrol Diff (%)</th>
              <th>Diesel Diff (L)</th>
              <th>Diesel Diff (%)</th>
              <th>Octane Diff (L)</th>
              <th>Octane Diff (%)</th>
              <th>Total Diff</th>
            </tr>
          </thead>
          <tbody>
            @forelse($diffReports as $i => $report)
              @php
                $petrolDiffPct = $report->petrol_received > 0 ? round($report->petrol_difference / $report->petrol_received * 100, 2) : 0;
                $dieselDiffPct = $report->diesel_received > 0 ? round($report->diesel_difference / $report->diesel_received * 100, 2) : 0;
                $octaneDiffPct = $report->octane_received > 0 ? round($report->octane_difference / $report->octane_received * 100, 2) : 0;
                $totalDiff = $report->petrol_difference + $report->diesel_difference + $report->octane_difference;
              @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="station-cell">{{ $report->station_name }}</td>
                <td>{{ $report->district }}</td>
                <td>{{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}</td>
                <td class="{{ $report->petrol_difference > 0 ? 'diff-positive' : ($report->petrol_difference < 0 ? 'diff-negative' : 'diff-zero') }}">
                  {{ $report->petrol_difference > 0 ? '+' : '' }}{{ number_format($report->petrol_difference) }} L
                </td>
                <td class="{{ $petrolDiffPct > 0 ? 'diff-positive' : 'diff-zero' }}">{{ $petrolDiffPct }}%</td>
                <td class="{{ $report->diesel_difference > 0 ? 'diff-positive' : ($report->diesel_difference < 0 ? 'diff-negative' : 'diff-zero') }}">
                  {{ $report->diesel_difference > 0 ? '+' : '' }}{{ number_format($report->diesel_difference) }} L
                </td>
                <td class="{{ $dieselDiffPct > 0 ? 'diff-positive' : 'diff-zero' }}">{{ $dieselDiffPct }}%</td>
                <td class="{{ $report->octane_difference > 0 ? 'diff-positive' : ($report->octane_difference < 0 ? 'diff-negative' : 'diff-zero') }}">
                  {{ $report->octane_difference > 0 ? '+' : '' }}{{ number_format($report->octane_difference) }} L
                </td>
                <td class="{{ $octaneDiffPct > 0 ? 'diff-positive' : 'diff-zero' }}">{{ $octaneDiffPct }}%</td>
                <td class="{{ $totalDiff > 0 ? 'diff-positive' : ($totalDiff < 0 ? 'diff-negative' : 'diff-zero') }}" style="font-weight:700;">
                  {{ $totalDiff > 0 ? '+' : '' }}{{ number_format($totalDiff) }} L
                </td>
              </tr>
            @empty
              <tr class="empty-row">
                <td colspan="11"><i class="fa-solid fa-inbox" style="margin-right:8px; opacity:.4;"></i>No difference data found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="export-row">
      {{-- <a href="{{ route('admin.reports.export', ['type' => 'diff', 'format' => 'pdf']) . '?' . http_build_query(request()->all()) }}" class="btn-export btn-pdf">
        <i class="fa-regular fa-file-pdf"></i> Export to PDF
      </a>
      <a href="{{ route('admin.reports.export', ['type' => 'diff', 'format' => 'excel']) . '?' . http_build_query(request()->all()) }}" class="btn-export btn-excel">
        <i class="fa-regular fa-file-excel"></i> Export to Excel
      </a> --}}
    </div>
  </div>{{-- /tab-diff --}}


  {{-- ══════════════════════════════════════
       TAB 5 — DUE SALES REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-due" class="tab-panel">
    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('admin.reports.index') }}">
        <input type="hidden" name="tab" value="due">
        <div class="filter-grid">
          <div class="filter-group">
            <label>From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}">
          </div>
          <div class="filter-group">
            <label>To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}">
          </div>
          <div class="filter-group">
            <label>District</label>
            <select name="district">
              <option value="">All Districts</option>
              @foreach($districts as $dist)
                <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-apply-row" style="grid-column: 4 / 5;">
            <a href="{{ route('admin.reports.index') }}?tab=due" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:5px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">
              <i class="fa-solid fa-magnifying-glass" style="margin-right:6px;"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    {{-- Due Sales Stat Cards --}}
    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
          <div class="stat-card-trend">Total</div>
        </div>
        <div>
          <div class="stat-card-label">Due Records</div>
          {{-- <div class="stat-card-value">{{ $dueReports->count() }}</div> --}}
        </div>
      </div>
      <div class="stat-card red">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend">Diesel</div>
        </div>
        <div>
          <div class="stat-card-label">Diesel Due Sales</div>
          {{-- <div class="stat-card-value">{{ number_format($dueReports->sum('diesel_due_sales')) }} L</div> --}}
        </div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend">Petrol</div>
        </div>
        <div>
          <div class="stat-card-label">Petrol Due Sales</div>
          {{-- <div class="stat-card-value">{{ number_format($dueReports->sum('petrol_due_sales')) }} L</div> --}}
        </div>
      </div>
      <div class="stat-card purple">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend">Octane</div>
        </div>
        <div>
          <div class="stat-card-label">Octane Due Sales</div>
          {{-- <div class="stat-card-value">{{ number_format($dueReports->sum('octane_due_sales')) }} L</div> --}}
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Due Sales Report</div>
        {{-- <div class="table-meta">{{ $dueReports->count() }} record(s) found</div> --}}
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Station</th>
              <th>District</th>
              <th>Report Date</th>
              <th>Diesel Due Sales</th>
              <th>Petrol Due Sales</th>
              <th>Octane Due Sales</th>
              <th>Total Due</th>
            </tr>
          </thead>
          <tbody>
            {{-- @forelse($dueReports as $i => $report)
              @php $totalDue = $report->diesel_due_sales + $report->petrol_due_sales + $report->octane_due_sales; @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="station-cell">{{ $report->station_name }}</td>
                <td>{{ $report->district }}</td>
                <td>{{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}</td>
                <td class="sold-val">{{ number_format($report->diesel_due_sales) }} L</td>
                <td class="sold-val">{{ number_format($report->petrol_due_sales) }} L</td>
                <td class="sold-val">{{ number_format($report->octane_due_sales) }} L</td>
                <td class="current-val" style="color: var(--red);">{{ number_format($totalDue) }} L</td>
              </tr>
            @empty
              <tr class="empty-row">
                <td colspan="8"><i class="fa-solid fa-inbox" style="margin-right:8px; opacity:.4;"></i>No due sales data found.</td>
              </tr>
            @endforelse
            @if($dueReports->count() > 0)
            <tr class="summary-row">
              <td colspan="4" style="text-align:right; padding-right:14px;">TOTAL</td>
              <td>{{ number_format($dueReports->sum('diesel_due_sales')) }} L</td>
              <td>{{ number_format($dueReports->sum('petrol_due_sales')) }} L</td>
              <td>{{ number_format($dueReports->sum('octane_due_sales')) }} L</td>
              <td>{{ number_format($dueReports->sum('diesel_due_sales') + $dueReports->sum('petrol_due_sales') + $dueReports->sum('octane_due_sales')) }} L</td>
            </tr>
            @endif --}}
          </tbody>
        </table>
      </div>
    </div>

    <div class="export-row">
      {{-- <a href="{{ route('admin.reports.export', ['type' => 'due', 'format' => 'pdf']) . '?' . http_build_query(request()->all()) }}" class="btn-export btn-pdf">
        <i class="fa-regular fa-file-pdf"></i> Export to PDF
      </a>
      <a href="{{ route('admin.reports.export', ['type' => 'due', 'format' => 'excel']) . '?' . http_build_query(request()->all()) }}" class="btn-export btn-excel">
        <i class="fa-regular fa-file-excel"></i> Export to Excel
      </a> --}}
    </div>
  </div>{{-- /tab-due --}}

</div>{{-- /.card --}}

<script>
  // Active tab from URL param (tab reload এর পর সঠিক tab দেখাবে)
  (function () {
    const params = new URLSearchParams(window.location.search);
    const activeTab = params.get('tab') || 'stock';
    const btn = document.querySelector(`.tab-btn[onclick*="'${activeTab}'"]`);
    if (btn) switchTab(activeTab, btn);
  })();

  function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
  }
</script>

@endsection