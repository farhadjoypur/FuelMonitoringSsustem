@extends('backend.dc.layouts.app')

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
    --teal: #14b8a6;
    --radius: 12px;
    --shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
  }

  body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); }

  .page-title {
    font-family: 'Syne', sans-serif;
    font-size: 1.5rem; font-weight: 800;
    color: var(--primary); margin-bottom: 20px; letter-spacing: -.3px;
  }

  .card {
    background: var(--surface); border-radius: var(--radius);
    box-shadow: var(--shadow); border: 1px solid var(--border);
    animation: fadeUp .3s ease both;
  }

  /* ── TABS ── */
  .tabs-bar {
    display: flex; gap: 2px;
    border-bottom: 1px solid var(--border);
    padding: 0 20px; overflow-x: auto;
  }
  .tab-btn {
    font-size: .875rem; font-weight: 500; color: var(--muted);
    background: none; border: none; border-bottom: 2.5px solid transparent;
    padding: 14px 16px; cursor: pointer; white-space: nowrap;
    transition: color .2s, border-color .2s;
  }
  .tab-btn:hover { color: var(--primary); }
  .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); font-weight: 600; }
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }

  /* ── FILTER ── */
  .filter-section { padding: 18px 22px 22px; }
  .filter-header {
    display: flex; align-items: center; gap: 8px;
    font-weight: 600; font-size: .88rem; color: var(--primary); margin-bottom: 14px;
  }
  .filter-header i { color: var(--blue); }
  .filter-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px 16px; }
  .filter-group label {
    display: block; font-size: .75rem; font-weight: 600;
    color: var(--muted); margin-bottom: 4px;
    text-transform: uppercase; letter-spacing: .4px;
  }
  .filter-group input,
  .filter-group select {
    width: 100%; padding: 8px 11px;
    border: 1px solid var(--border); border-radius: 8px;
    font-size: .875rem; color: var(--text); background: #fafbfc;
    outline: none; transition: border-color .2s, box-shadow .2s; appearance: none;
  }
  .filter-group input:focus,
  .filter-group select:focus {
    border-color: var(--blue); box-shadow: 0 0 0 3px rgba(59,130,246,.12); background: #fff;
  }
  .filter-apply-row {
    display: flex; align-items: flex-end; justify-content: flex-end; gap: 8px;
  }
  .btn-apply {
    background: linear-gradient(135deg, #0f4c81, #1e6fb5); color: #fff;
    border: none; border-radius: 8px; padding: 9px 24px;
    font-size: .875rem; font-weight: 600; cursor: pointer;
    box-shadow: 0 4px 12px rgba(15,76,129,.25); transition: transform .15s, box-shadow .15s;
  }
  .btn-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(15,76,129,.3); }
  .btn-reset {
    background: transparent; color: var(--muted);
    border: 1px solid var(--border); border-radius: 8px;
    padding: 9px 16px; font-size: .875rem; font-weight: 500; cursor: pointer; transition: all .15s;
    text-decoration: none; display: inline-flex; align-items: center;
  }
  .btn-reset:hover { background: var(--bg); color: var(--text); }

  /* ── STAT CARDS ── */
  .stat-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; padding: 0 22px 22px; }
  .stat-card {
    border-radius: 12px; padding: 16px 18px; position: relative; overflow: hidden;
    color: #fff; min-height: 96px; display: flex; flex-direction: column;
    justify-content: space-between; transition: transform .2s, box-shadow .2s;
    animation: fadeUp .35s ease both;
  }
  .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,.18); }
  .stat-card.blue   { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .stat-card.green  { background: linear-gradient(135deg, #22c55e, #15803d); }
  .stat-card.purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
  .stat-card.orange { background: linear-gradient(135deg, #f97316, #c2410c); }
  .stat-card.red    { background: linear-gradient(135deg, #ef4444, #b91c1c); }
  .stat-card.teal   { background: linear-gradient(135deg, #14b8a6, #0f766e); }
  .stat-card:nth-child(1) { animation-delay:.05s; }
  .stat-card:nth-child(2) { animation-delay:.10s; }
  .stat-card:nth-child(3) { animation-delay:.15s; }
  .stat-card:nth-child(4) { animation-delay:.20s; }
  .stat-card-top { display: flex; align-items: flex-start; justify-content: space-between; }
  .stat-card-icon {
    width:34px; height:34px; background:rgba(255,255,255,.2);
    border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.95rem;
  }
  .stat-card-badge {
    font-size:.72rem; background:rgba(255,255,255,.15);
    padding:3px 8px; border-radius:20px;
  }
  .stat-card-label { font-size:.76rem; font-weight:500; opacity:.85; margin-top:8px; }
  .stat-card-value { font-family:'Syne',sans-serif; font-size:1.55rem; font-weight:800; letter-spacing:-.5px; margin-top:2px; }
  .stat-card::after {
    content:''; position:absolute; right:-18px; bottom:-18px;
    width:80px; height:80px; border-radius:50%; background:rgba(255,255,255,.08);
  }

  /* ── TABLE ── */
  .table-section { padding: 0 22px 22px; }
  .table-header-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
  .table-title { font-family:'Syne',sans-serif; font-size:.95rem; font-weight:700; color:var(--primary); }
  .table-meta { font-size:.76rem; color:var(--muted); }
  .table-wrap { border:1px solid var(--border); border-radius:10px; overflow:hidden; overflow-x:auto; }
  table { width:100%; border-collapse:collapse; font-size:.82rem; }
  thead tr { background:#f7f9fc; }
  thead th {
    padding:10px 13px; text-align:left; font-size:.70rem;
    font-weight:700; color:var(--muted); text-transform:uppercase;
    letter-spacing:.5px; border-bottom:1px solid var(--border); white-space:nowrap;
  }
  tbody tr { border-bottom:1px solid #f1f5f9; transition:background .15s; }
  tbody tr:last-child { border-bottom:none; }
  tbody tr:hover { background:#f8faff; }
  tbody td { padding:10px 13px; color:var(--text); vertical-align:middle; }

  .depot-cell { font-weight:600; color:var(--primary); font-size:.80rem; }
  .station-cell { font-weight:500; font-size:.80rem; }
  .officer-name { font-weight:600; font-size:.82rem; }
  .received-val { color:#16a34a; font-weight:600; }
  .sold-val     { color:#ef4444; font-weight:600; }
  .current-val  { font-weight:700; }
  .diff-positive { color:#ef4444; font-weight:600; }
  .diff-negative { color:#16a34a; font-weight:600; }
  .diff-zero     { color:var(--muted); font-weight:500; }

  .fuel-type-badge { display:inline-block; padding:2px 9px; border-radius:20px; font-size:.70rem; font-weight:600; }
  .fuel-diesel  { background:#eff6ff; color:#2563eb; }
  .fuel-petrol  { background:#f0fdf4; color:#16a34a; }
  .fuel-octane  { background:#fdf4ff; color:#9333ea; }

  .status-badge { display:inline-block; padding:2px 9px; border-radius:20px; font-size:.70rem; font-weight:600; white-space:nowrap; }
  .status-available { background:#dcfce7; color:#15803d; }
  .status-low       { background:#fef9c3; color:#b45309; }
  .status-zero      { background:#fee2e2; color:#b91c1c; }
  .status-active    { background:#dcfce7; color:#15803d; }
  .status-inactive  { background:#fee2e2; color:#b91c1c; }

  .summary-row td { background:#f7f9fc; font-weight:700; font-size:.80rem; border-top:2px solid var(--border); }
  .empty-row td { text-align:center; padding:36px; color:var(--muted); font-size:.86rem; }

  /* report count pill */
  .report-count {
    display:inline-block; background:#eff6ff; color:#2563eb;
    font-size:.70rem; font-weight:700; padding:2px 8px; border-radius:20px;
  }
  .report-count.zero { background:#f1f5f9; color:var(--muted); }

  /* ── EXPORT ── */
  .export-row {
    display:flex; justify-content:flex-end; gap:10px;
    padding:14px 22px 18px; border-top:1px solid var(--border);
  }
  .btn-export {
    display:flex; align-items:center; gap:7px; padding:8px 18px;
    border-radius:8px; font-size:.82rem; font-weight:600;
    cursor:pointer; border:1.5px solid; transition:all .15s; text-decoration:none;
  }
  .btn-pdf   { border-color:#e2e8f0; color:#475569; background:#fff; }
  .btn-pdf:hover { background:#f8f9fa; }
  .btn-excel { border-color:#16a34a; color:#fff; background:#16a34a; }
  .btn-excel:hover { background:#15803d; }

  @keyframes fadeUp {
    from { opacity:0; transform:translateY(10px); }
    to   { opacity:1; transform:translateY(0); }
  }
  @media (max-width:900px) {
    .filter-grid { grid-template-columns:repeat(2,1fr); }
    .stat-cards  { grid-template-columns:repeat(2,1fr); }
  }
</style>
@endpush

@section('content')

<h1 class="page-title">
  <i class="fa-solid fa-chart-mixed" style="font-size:1.1rem; margin-right:8px; opacity:.7;"></i>
  Reports &amp; Analytics
</h1>

<div class="card">

  {{-- ══ TABS ══ --}}
  <div class="tabs-bar">
    <button class="tab-btn active" onclick="switchTab('stock',this)">
      <i class="fa-solid fa-box-archive" style="margin-right:5px; font-size:.78rem;"></i>Stock Report
    </button>
    <button class="tab-btn" onclick="switchTab('sales',this)">
      <i class="fa-solid fa-chart-line" style="margin-right:5px; font-size:.78rem;"></i>Sales Report
    </button>
    <button class="tab-btn" onclick="switchTab('officer',this)">
      <i class="fa-solid fa-user-tie" style="margin-right:5px; font-size:.78rem;"></i>Tag Officer Report
    </button>
    <button class="tab-btn" onclick="switchTab('diff',this)">
      <i class="fa-solid fa-percent" style="margin-right:5px; font-size:.78rem;"></i>Difference (%) Report
    </button>
    <button class="tab-btn" onclick="switchTab('due',this)">
      <i class="fa-solid fa-clock-rotate-left" style="margin-right:5px; font-size:.78rem;"></i>Due Sales Report
    </button>
  </div>

  {{-- ══════════════════════════════════════
       TAB 1 — STOCK REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-stock" class="tab-panel active">

    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('dc.reports.index') }}">
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
            <label>Tag Officer</label>
            <select name="officer_id">
              <option value="">All Officers</option>
              @foreach($officers as $officer)
                <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>
                  {{ $officer->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Filling Station</label>
            <select name="station_name">
              <option value="">All Stations</option>
              @foreach($stations as $s)
                <option value="{{ $s->station_name }}" {{ request('station_name') == $s->station_name ? 'selected' : '' }}>
                  {{ $s->station_name }}
                </option>
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
          <div class="filter-apply-row" style="grid-column: 3/5;">
            <a href="{{ route('dc.reports.index') }}?tab=stock" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:4px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">
              <i class="fa-solid fa-magnifying-glass" style="margin-right:5px;"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    {{-- Stat Cards --}}
    @php $fuelType = request('fuel_type'); $showAll = !$fuelType; @endphp
    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
          <div class="stat-card-badge">Total</div>
        </div>
        <div>
          <div class="stat-card-label">Total Closing Stock</div>
          <div class="stat-card-value">
            {{ number_format(
              $stockReports->sum('petrol_closing_stock') +
              $stockReports->sum('diesel_closing_stock') +
              $stockReports->sum('octane_closing_stock')
            ) }} L
          </div>
        </div>
      </div>
      <div class="stat-card green">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Diesel</div>
        </div>
        <div>
          <div class="stat-card-label">Diesel Closing Stock</div>
          <div class="stat-card-value">{{ number_format($stockReports->sum('diesel_closing_stock')) }} L</div>
        </div>
      </div>
      <div class="stat-card purple">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Petrol</div>
        </div>
        <div>
          <div class="stat-card-label">Petrol Closing Stock</div>
          <div class="stat-card-value">{{ number_format($stockReports->sum('petrol_closing_stock')) }} L</div>
        </div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Octane</div>
        </div>
        <div>
          <div class="stat-card-label">Octane Closing Stock</div>
          <div class="stat-card-value">{{ number_format($stockReports->sum('octane_closing_stock')) }} L</div>
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Stock Report — My Tag Officers' Stations</div>
        <div class="table-meta">{{ $stockReports->count() }} record(s) found</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Tag Officer</th>
              <th>Station</th>
              <th>District</th>
              <th>Report Date</th>
              @if($showAll || $fuelType === 'diesel')
              <th>Diesel Prev</th>
              <th>Diesel Recv</th>
              <th>Diesel Sold</th>
              <th>Diesel Closing</th>
              @endif
              @if($showAll || $fuelType === 'petrol')
              <th>Petrol Prev</th>
              <th>Petrol Recv</th>
              <th>Petrol Sold</th>
              <th>Petrol Closing</th>
              @endif
              @if($showAll || $fuelType === 'octane')
              <th>Octane Prev</th>
              <th>Octane Recv</th>
              <th>Octane Sold</th>
              <th>Octane Closing</th>
              @endif
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($stockReports as $i => $r)
              @php
                $totalClosing = $r->diesel_closing_stock + $r->petrol_closing_stock + $r->octane_closing_stock;
                if ($totalClosing <= 0)        { $sClass = 'status-zero';      $sLabel = 'Zero Stock'; }
                elseif ($totalClosing < 2000)  { $sClass = 'status-low';       $sLabel = 'Low Stock'; }
                else                           { $sClass = 'status-available';  $sLabel = 'Available'; }
              @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="officer-name">{{ $r->tagOfficer->name ?? '—' }}</td>
                <td class="station-cell">{{ $r->station_name }}</td>
                <td>{{ $r->district }}</td>
                <td>{{ \Carbon\Carbon::parse($r->report_date)->format('d M Y') }}</td>
                @if($showAll || $fuelType === 'diesel')
                <td>{{ number_format($r->diesel_prev_stock) }} L</td>
                <td class="received-val">+{{ number_format($r->diesel_received) }} L</td>
                <td class="sold-val">-{{ number_format($r->diesel_sales) }} L</td>
                <td class="current-val">{{ number_format($r->diesel_closing_stock) }} L</td>
                @endif
                @if($showAll || $fuelType === 'petrol')
                <td>{{ number_format($r->petrol_prev_stock) }} L</td>
                <td class="received-val">+{{ number_format($r->petrol_received) }} L</td>
                <td class="sold-val">-{{ number_format($r->petrol_sales) }} L</td>
                <td class="current-val">{{ number_format($r->petrol_closing_stock) }} L</td>
                @endif
                @if($showAll || $fuelType === 'octane')
                <td>{{ number_format($r->octane_prev_stock) }} L</td>
                <td class="received-val">+{{ number_format($r->octane_received) }} L</td>
                <td class="sold-val">-{{ number_format($r->octane_sales) }} L</td>
                <td class="current-val">{{ number_format($r->octane_closing_stock) }} L</td>
                @endif
                <td><span class="status-badge {{ $sClass }}">{{ $sLabel }}</span></td>
              </tr>
            @empty
              <tr class="empty-row"><td colspan="20">
                <i class="fa-solid fa-inbox" style="margin-right:7px;opacity:.4;"></i>No stock data found.
              </td></tr>
            @endforelse

            @if($stockReports->count() > 0)
            <tr class="summary-row">
              <td colspan="5" style="text-align:right;padding-right:13px;">TOTAL</td>
              @if($showAll || $fuelType === 'diesel')
              <td>{{ number_format($stockReports->sum('diesel_prev_stock')) }} L</td>
              <td>{{ number_format($stockReports->sum('diesel_received')) }} L</td>
              <td>{{ number_format($stockReports->sum('diesel_sales')) }} L</td>
              <td>{{ number_format($stockReports->sum('diesel_closing_stock')) }} L</td>
              @endif
              @if($showAll || $fuelType === 'petrol')
              <td>{{ number_format($stockReports->sum('petrol_prev_stock')) }} L</td>
              <td>{{ number_format($stockReports->sum('petrol_received')) }} L</td>
              <td>{{ number_format($stockReports->sum('petrol_sales')) }} L</td>
              <td>{{ number_format($stockReports->sum('petrol_closing_stock')) }} L</td>
              @endif
              @if($showAll || $fuelType === 'octane')
              <td>{{ number_format($stockReports->sum('octane_prev_stock')) }} L</td>
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
      <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export PDF</button>
      <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
    </div>
  </div>{{-- /tab-stock --}}


  {{-- ══════════════════════════════════════
       TAB 2 — SALES REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-sales" class="tab-panel">

    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('dc.reports.index') }}">
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
            <label>Tag Officer</label>
            <select name="officer_id">
              <option value="">All Officers</option>
              @foreach($officers as $officer)
                <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>
                  {{ $officer->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Filling Station</label>
            <select name="station_name">
              <option value="">All Stations</option>
              @foreach($stations as $s)
                <option value="{{ $s->station_name }}" {{ request('station_name') == $s->station_name ? 'selected' : '' }}>
                  {{ $s->station_name }}
                </option>
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
            <label>Fuel Type</label>
            <select name="fuel_type">
              <option value="">All Types</option>
              <option value="diesel"  {{ request('fuel_type') == 'diesel'  ? 'selected' : '' }}>Diesel</option>
              <option value="petrol"  {{ request('fuel_type') == 'petrol'  ? 'selected' : '' }}>Petrol</option>
              <option value="octane"  {{ request('fuel_type') == 'octane'  ? 'selected' : '' }}>Octane</option>
            </select>
          </div>
          <div class="filter-apply-row" style="grid-column: 3/5;">
            <a href="{{ route('dc.reports.index') }}?tab=sales" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:4px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">
              <i class="fa-solid fa-magnifying-glass" style="margin-right:5px;"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-cart-shopping"></i></div>
          <div class="stat-card-badge">Total</div>
        </div>
        <div>
          <div class="stat-card-label">Total Sales</div>
          <div class="stat-card-value">
            {{ number_format($salesReports->sum('diesel_sales') + $salesReports->sum('petrol_sales') + $salesReports->sum('octane_sales')) }} L
          </div>
        </div>
      </div>
      <div class="stat-card green">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Diesel</div>
        </div>
        <div>
          <div class="stat-card-label">Diesel Sold</div>
          <div class="stat-card-value">{{ number_format($salesReports->sum('diesel_sales')) }} L</div>
        </div>
      </div>
      <div class="stat-card purple">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Petrol</div>
        </div>
        <div>
          <div class="stat-card-label">Petrol Sold</div>
          <div class="stat-card-value">{{ number_format($salesReports->sum('petrol_sales')) }} L</div>
        </div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Octane</div>
        </div>
        <div>
          <div class="stat-card-label">Octane Sold</div>
          <div class="stat-card-value">{{ number_format($salesReports->sum('octane_sales')) }} L</div>
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Sales Report — My Tag Officers' Stations</div>
        <div class="table-meta">{{ $salesReports->count() }} record(s) found</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Tag Officer</th>
              <th>Station</th>
              <th>District</th>
              <th>Date</th>
              <th>Diesel Recv</th>
              <th>Diesel Sold</th>
              <th>Petrol Recv</th>
              <th>Petrol Sold</th>
              <th>Octane Recv</th>
              <th>Octane Sold</th>
              <th>Total Sold</th>
            </tr>
          </thead>
          <tbody>
            @forelse($salesReports as $i => $r)
              @php $totalSold = $r->diesel_sales + $r->petrol_sales + $r->octane_sales; @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="officer-name">{{ $r->tagOfficer->name ?? '—' }}</td>
                <td class="station-cell">{{ $r->station_name }}</td>
                <td>{{ $r->district }}</td>
                <td>{{ \Carbon\Carbon::parse($r->report_date)->format('d M Y') }}</td>
                <td class="received-val">{{ number_format($r->diesel_received) }} L</td>
                <td class="sold-val">{{ number_format($r->diesel_sales) }} L</td>
                <td class="received-val">{{ number_format($r->petrol_received) }} L</td>
                <td class="sold-val">{{ number_format($r->petrol_sales) }} L</td>
                <td class="received-val">{{ number_format($r->octane_received) }} L</td>
                <td class="sold-val">{{ number_format($r->octane_sales) }} L</td>
                <td class="current-val">{{ number_format($totalSold) }} L</td>
              </tr>
            @empty
              <tr class="empty-row"><td colspan="12">
                <i class="fa-solid fa-inbox" style="margin-right:7px;opacity:.4;"></i>No sales data found.
              </td></tr>
            @endforelse
            @if($salesReports->count() > 0)
            <tr class="summary-row">
              <td colspan="5" style="text-align:right;padding-right:13px;">TOTAL</td>
              <td>{{ number_format($salesReports->sum('diesel_received')) }} L</td>
              <td>{{ number_format($salesReports->sum('diesel_sales')) }} L</td>
              <td>{{ number_format($salesReports->sum('petrol_received')) }} L</td>
              <td>{{ number_format($salesReports->sum('petrol_sales')) }} L</td>
              <td>{{ number_format($salesReports->sum('octane_received')) }} L</td>
              <td>{{ number_format($salesReports->sum('octane_sales')) }} L</td>
              <td>{{ number_format($salesReports->sum('diesel_sales') + $salesReports->sum('petrol_sales') + $salesReports->sum('octane_sales')) }} L</td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

    <div class="export-row">
      <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export PDF</button>
      <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
    </div>
  </div>{{-- /tab-sales --}}


  {{-- ══════════════════════════════════════
       TAB 3 — TAG OFFICER REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-officer" class="tab-panel">

    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('dc.reports.index') }}">
        <input type="hidden" name="tab" value="officer">
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
            <label>Tag Officer</label>
            <select name="officer_id">
              <option value="">All Officers</option>
              @foreach($officers as $officer)
                <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>
                  {{ $officer->name }}
                </option>
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
          <div class="filter-apply-row" style="grid-column: 3/5;">
            <a href="{{ route('dc.reports.index') }}?tab=officer" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:4px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">
              <i class="fa-solid fa-magnifying-glass" style="margin-right:5px;"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-users"></i></div>
          <div class="stat-card-badge">Total</div>
        </div>
        <div>
          <div class="stat-card-label">Total Assignments</div>
          <div class="stat-card-value">{{ $officerReports->count() }}</div>
        </div>
      </div>
      <div class="stat-card green">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-user-check"></i></div>
          <div class="stat-card-badge">Active</div>
        </div>
        <div>
          <div class="stat-card-label">Active Officers</div>
          <div class="stat-card-value">{{ $officerReports->where('status','active')->count() }}</div>
        </div>
      </div>
      <div class="stat-card red">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-user-xmark"></i></div>
          <div class="stat-card-badge">Inactive</div>
        </div>
        <div>
          <div class="stat-card-label">Inactive Officers</div>
          <div class="stat-card-value">{{ $officerReports->where('status','inactive')->count() }}</div>
        </div>
      </div>
      <div class="stat-card teal">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-building"></i></div>
          <div class="stat-card-badge">Stations</div>
        </div>
        <div>
          <div class="stat-card-label">Stations Covered</div>
          <div class="stat-card-value">{{ $officerReports->pluck('filling_station_id')->unique()->count() }}</div>
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Tag Officer Report — Under My Supervision</div>
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
              <th>Reports Submitted</th>
              <th>Assigned At</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($officerReports as $i => $assign)
              @php
                $count = $officerSubmitCounts[$assign->officer_id] ?? 0;
              @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="officer-name">{{ $assign->officer->name ?? '—' }}</td>
                <td style="font-size:.76rem; color:var(--muted); line-height:1.6;">
                  {{ $assign->officer->email ?? '' }}<br>
                  {{ $assign->officer->phone ?? '' }}
                </td>
                <td class="station-cell">{{ $assign->fillingStation->station_name ?? '—' }}</td>
                <td>{{ $assign->fillingStation->district ?? '—' }}</td>
                <td>{{ $assign->fillingStation->division ?? '—' }}</td>
                <td>
                  <span class="report-count {{ $count == 0 ? 'zero' : '' }}">
                    {{ $count }} report{{ $count != 1 ? 's' : '' }}
                  </span>
                </td>
                <td>{{ $assign->created_at?->format('d M Y') ?? '—' }}</td>
                <td>
                  <span class="status-badge {{ $assign->status === 'active' ? 'status-active' : 'status-inactive' }}">
                    {{ ucfirst($assign->status) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr class="empty-row"><td colspan="9">
                <i class="fa-solid fa-inbox" style="margin-right:7px;opacity:.4;"></i>No tag officer data found.
              </td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="export-row">
      <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export PDF</button>
      <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
    </div>
  </div>{{-- /tab-officer --}}


  {{-- ══════════════════════════════════════
       TAB 4 — DIFFERENCE (%) REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-diff" class="tab-panel">

    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('dc.reports.index') }}">
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
            <label>Tag Officer</label>
            <select name="officer_id">
              <option value="">All Officers</option>
              @foreach($officers as $officer)
                <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>
                  {{ $officer->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="filter-group">
            <label>Min Difference (L)</label>
            <input type="number" name="min_diff" value="{{ request('min_diff', 0) }}" placeholder="e.g. 50">
          </div>
          <div class="filter-apply-row" style="grid-column: 3/5;">
            <a href="{{ route('dc.reports.index') }}?tab=diff" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:4px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">
              <i class="fa-solid fa-magnifying-glass" style="margin-right:5px;"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-scale-balanced"></i></div>
          <div class="stat-card-badge">All</div>
        </div>
        <div>
          <div class="stat-card-label">Total Records</div>
          <div class="stat-card-value">{{ $diffReports->count() }}</div>
        </div>
      </div>
      <div class="stat-card red">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
          <div class="stat-card-badge">Alert</div>
        </div>
        <div>
          <div class="stat-card-label">High Diff (&gt; 50L)</div>
          <div class="stat-card-value">
            {{ $diffReports->filter(fn($r) =>
                abs($r->petrol_difference) + abs($r->diesel_difference) + abs($r->octane_difference) > 50
              )->count() }}
          </div>
        </div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Diesel</div>
        </div>
        <div>
          <div class="stat-card-label">Total Diesel Diff</div>
          <div class="stat-card-value">{{ number_format(abs($diffReports->sum('diesel_difference'))) }} L</div>
        </div>
      </div>
      <div class="stat-card purple">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Petrol</div>
        </div>
        <div>
          <div class="stat-card-label">Total Petrol Diff</div>
          <div class="stat-card-value">{{ number_format(abs($diffReports->sum('petrol_difference'))) }} L</div>
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Difference (%) Report — My Officers' Stations</div>
        <div class="table-meta">{{ $diffReports->count() }} record(s) found</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Tag Officer</th>
              <th>Station</th>
              <th>District</th>
              <th>Date</th>
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
            @forelse($diffReports as $i => $r)
              @php
                $petrolDiffPct = $r->petrol_received > 0 ? round($r->petrol_difference / $r->petrol_received * 100, 2) : 0;
                $dieselDiffPct = $r->diesel_received > 0 ? round($r->diesel_difference / $r->diesel_received * 100, 2) : 0;
                $octaneDiffPct = $r->octane_received > 0 ? round($r->octane_difference / $r->octane_received * 100, 2) : 0;
                $totalDiff = $r->petrol_difference + $r->diesel_difference + $r->octane_difference;
                $diffClass = fn($v) => $v > 0 ? 'diff-positive' : ($v < 0 ? 'diff-negative' : 'diff-zero');
              @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="officer-name">{{ $r->tagOfficer->name ?? '—' }}</td>
                <td class="station-cell">{{ $r->station_name }}</td>
                <td>{{ $r->district }}</td>
                <td>{{ \Carbon\Carbon::parse($r->report_date)->format('d M Y') }}</td>
                <td class="{{ $diffClass($r->petrol_difference) }}">
                  {{ $r->petrol_difference > 0 ? '+' : '' }}{{ number_format($r->petrol_difference) }} L
                </td>
                <td class="{{ $petrolDiffPct > 0 ? 'diff-positive' : 'diff-zero' }}">{{ $petrolDiffPct }}%</td>
                <td class="{{ $diffClass($r->diesel_difference) }}">
                  {{ $r->diesel_difference > 0 ? '+' : '' }}{{ number_format($r->diesel_difference) }} L
                </td>
                <td class="{{ $dieselDiffPct > 0 ? 'diff-positive' : 'diff-zero' }}">{{ $dieselDiffPct }}%</td>
                <td class="{{ $diffClass($r->octane_difference) }}">
                  {{ $r->octane_difference > 0 ? '+' : '' }}{{ number_format($r->octane_difference) }} L
                </td>
                <td class="{{ $octaneDiffPct > 0 ? 'diff-positive' : 'diff-zero' }}">{{ $octaneDiffPct }}%</td>
                <td class="{{ $diffClass($totalDiff) }}" style="font-weight:700;">
                  {{ $totalDiff > 0 ? '+' : '' }}{{ number_format($totalDiff) }} L
                </td>
              </tr>
            @empty
              <tr class="empty-row"><td colspan="12">
                <i class="fa-solid fa-inbox" style="margin-right:7px;opacity:.4;"></i>No difference data found.
              </td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="export-row">
      <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export PDF</button>
      <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
    </div>
  </div>{{-- /tab-diff --}}


  {{-- ══════════════════════════════════════
       TAB 5 — DUE SALES REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-due" class="tab-panel">

    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('dc.reports.index') }}">
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
            <label>Tag Officer</label>
            <select name="officer_id">
              <option value="">All Officers</option>
              @foreach($officers as $officer)
                <option value="{{ $officer->id }}" {{ request('officer_id') == $officer->id ? 'selected' : '' }}>
                  {{ $officer->name }}
                </option>
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
          <div class="filter-apply-row" style="grid-column: 3/5;">
            <a href="{{ route('dc.reports.index') }}?tab=due" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:4px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">
              <i class="fa-solid fa-magnifying-glass" style="margin-right:5px;"></i>Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
          <div class="stat-card-badge">Total</div>
        </div>
        <div>
          <div class="stat-card-label">Due Records</div>
          <div class="stat-card-value">{{ $dueSalesReports->count() }}</div>
        </div>
      </div>
      <div class="stat-card red">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Diesel</div>
        </div>
        <div>
          <div class="stat-card-label">Diesel Excess Sold</div>
          <div class="stat-card-value">
            {{ number_format($dueSalesReports->sum(fn($r) => max(0, $r->diesel_sales - ($r->diesel_received + $r->diesel_prev_stock)))) }} L
          </div>
        </div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Petrol</div>
        </div>
        <div>
          <div class="stat-card-label">Petrol Excess Sold</div>
          <div class="stat-card-value">
            {{ number_format($dueSalesReports->sum(fn($r) => max(0, $r->petrol_sales - ($r->petrol_received + $r->petrol_prev_stock)))) }} L
          </div>
        </div>
      </div>
      <div class="stat-card purple">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-badge">Octane</div>
        </div>
        <div>
          <div class="stat-card-label">Octane Excess Sold</div>
          <div class="stat-card-value">
            {{ number_format($dueSalesReports->sum(fn($r) => max(0, $r->octane_sales - ($r->octane_received + $r->octane_prev_stock)))) }} L
          </div>
        </div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Due Sales Report — Sales Exceeding Available Stock</div>
        <div class="table-meta">{{ $dueSalesReports->count() }} record(s) found</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Tag Officer</th>
              <th>Station</th>
              <th>District</th>
              <th>Date</th>
              <th>Diesel Available</th>
              <th>Diesel Sold</th>
              <th>Diesel Due</th>
              <th>Petrol Available</th>
              <th>Petrol Sold</th>
              <th>Petrol Due</th>
              <th>Octane Available</th>
              <th>Octane Sold</th>
              <th>Octane Due</th>
            </tr>
          </thead>
          <tbody>
            @forelse($dueSalesReports as $i => $r)
              @php
                $dAvail   = $r->diesel_prev_stock  + $r->diesel_received;
                $pAvail   = $r->petrol_prev_stock  + $r->petrol_received;
                $oAvail   = $r->octane_prev_stock  + $r->octane_received;
                $dDue     = max(0, $r->diesel_sales  - $dAvail);
                $pDue     = max(0, $r->petrol_sales  - $pAvail);
                $oDue     = max(0, $r->octane_sales  - $oAvail);
              @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="officer-name">{{ $r->tagOfficer->name ?? '—' }}</td>
                <td class="station-cell">{{ $r->station_name }}</td>
                <td>{{ $r->district }}</td>
                <td>{{ \Carbon\Carbon::parse($r->report_date)->format('d M Y') }}</td>
                <td>{{ number_format($dAvail) }} L</td>
                <td class="sold-val">{{ number_format($r->diesel_sales) }} L</td>
                <td class="{{ $dDue > 0 ? 'diff-positive' : 'diff-zero' }}" style="font-weight:700;">
                  {{ $dDue > 0 ? number_format($dDue).' L' : '—' }}
                </td>
                <td>{{ number_format($pAvail) }} L</td>
                <td class="sold-val">{{ number_format($r->petrol_sales) }} L</td>
                <td class="{{ $pDue > 0 ? 'diff-positive' : 'diff-zero' }}" style="font-weight:700;">
                  {{ $pDue > 0 ? number_format($pDue).' L' : '—' }}
                </td>
                <td>{{ number_format($oAvail) }} L</td>
                <td class="sold-val">{{ number_format($r->octane_sales) }} L</td>
                <td class="{{ $oDue > 0 ? 'diff-positive' : 'diff-zero' }}" style="font-weight:700;">
                  {{ $oDue > 0 ? number_format($oDue).' L' : '—' }}
                </td>
              </tr>
            @empty
              <tr class="empty-row"><td colspan="14">
                <i class="fa-solid fa-inbox" style="margin-right:7px;opacity:.4;"></i>No due sales data found.
              </td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="export-row">
      <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export PDF</button>
      <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
    </div>
  </div>{{-- /tab-due --}}

</div>{{-- /.card --}}

@push('scripts')
<script>
  // Tab reload এর পর সঠিক tab active রাখো
  (function () {
    const params  = new URLSearchParams(window.location.search);
    const active  = params.get('tab') || 'stock';
    const btn     = document.querySelector(`.tab-btn[onclick*="'${active}'"]`);
    if (btn) switchTab(active, btn);
  })();

  function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
  }
</script>
@endpush

@endsection