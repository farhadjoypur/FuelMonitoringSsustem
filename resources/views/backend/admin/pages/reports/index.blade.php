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
    --teal: #14b8a6;
    --radius: 12px;
    --shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
  }

  .page-title {
    font-family: 'Syne', sans-serif;
    font-size: 1.6rem; font-weight: 800;
    color: var(--primary); margin-bottom: 22px; letter-spacing: -.3px;
  }

  .card {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    animation: fadeUp .3s ease both;
  }

  /* ── TABS ── */
  .tabs-bar {
    display: flex; gap: 2px;
    border-bottom: 1px solid var(--border);
    padding: 0 20px; overflow-x: auto;
  }
  .tab-btn {
    font-family: 'DM Sans', sans-serif; font-size: .875rem; font-weight: 500;
    color: var(--muted); background: none; border: none;
    border-bottom: 2.5px solid transparent;
    padding: 14px 18px; cursor: pointer; white-space: nowrap;
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
  .filter-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px 16px;
  }
  .filter-group label {
    display: block; font-size: .76rem; font-weight: 500;
    color: var(--muted); margin-bottom: 4px;
    text-transform: uppercase; letter-spacing: .3px;
  }
  .filter-group input,
  .filter-group select {
    width: 100%; padding: 9px 11px;
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

  /* Apply button row — col 3-4 centered */
  .filter-apply-row {
    grid-column: 3 / 5;
    display: flex; align-items: flex-end; justify-content: center; gap: 10px;
  }
  .btn-apply {
    background: #1a3c5e;
    color: #fff; border: none; border-radius: 8px;
    padding: 10px 40px;
    font-family: 'DM Sans', sans-serif; font-size: .875rem; font-weight: 600;
    cursor: pointer; letter-spacing: .2px;
    transition: background .15s, transform .15s;
  }
  .btn-apply:hover { background: #0f4c81; transform: translateY(-1px); }
  .btn-reset {
    background: transparent; color: var(--muted);
    border: 1px solid var(--border); border-radius: 8px;
    padding: 10px 18px; font-family: 'DM Sans', sans-serif;
    font-size: .875rem; font-weight: 500; cursor: pointer;
    text-decoration: none; display: inline-flex; align-items: center;
    transition: all .15s;
  }
  .btn-reset:hover { background: var(--bg); color: var(--text); }

  /* ── STAT CARDS — 5 columns ── */
  .stat-cards {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px; padding: 0 22px 22px;
  }
  .stat-card {
    border-radius: 12px; padding: 16px 18px;
    position: relative; overflow: hidden; color: #fff;
    min-height: 96px; display: flex; flex-direction: column;
    justify-content: space-between; cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    animation: fadeUp .35s ease both;
  }
  .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 28px rgba(0,0,0,.18); }
  .stat-card.blue   { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
  .stat-card.green  { background: linear-gradient(135deg, #22c55e, #15803d); }
  .stat-card.purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
  .stat-card.orange { background: linear-gradient(135deg, #f97316, #c2410c); }
  .stat-card.teal   { background: linear-gradient(135deg, #14b8a6, #0f766e); }
  .stat-card:nth-child(1) { animation-delay: .04s; }
  .stat-card:nth-child(2) { animation-delay: .08s; }
  .stat-card:nth-child(3) { animation-delay: .12s; }
  .stat-card:nth-child(4) { animation-delay: .16s; }
  .stat-card:nth-child(5) { animation-delay: .20s; }
  .stat-card-top { display: flex; align-items: flex-start; justify-content: space-between; }
  .stat-card-icon {
    width: 34px; height: 34px; background: rgba(255,255,255,.2);
    border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .95rem;
  }
  .stat-card-trend {
    display: flex; align-items: center; gap: 4px; font-size: .72rem;
    background: rgba(255,255,255,.15); padding: 3px 7px; border-radius: 20px;
  }
  .stat-card-label { font-size: .74rem; font-weight: 500; opacity: .88; margin-top: 8px; letter-spacing: .1px; }
  .stat-card-value { font-family: 'Syne', sans-serif; font-size: 1.5rem; font-weight: 800; letter-spacing: -.5px; margin-top: 2px; }
  .stat-card::after {
    content: ''; position: absolute; right: -18px; bottom: -18px;
    width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,.08);
  }

  /* ── TABLE ── */
  .table-section { padding: 0 22px 22px; }
  .table-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 13px; }
  .table-title { font-family: 'Syne', sans-serif; font-size: .95rem; font-weight: 700; color: var(--text); }
  .table-meta { font-size: .76rem; color: var(--muted); }
  .table-wrap { border: 1px solid var(--border); border-radius: 10px; overflow: hidden; overflow-x: auto; }

  table { width: 100%; border-collapse: collapse; font-size: .82rem; }
  thead tr { background: #f7f9fc; }
  thead th {
    padding: 10px 13px; text-align: left; font-size: .69rem;
    font-weight: 700; color: var(--muted); text-transform: uppercase;
    letter-spacing: .5px; border-bottom: 1px solid var(--border); white-space: nowrap;
  }
  tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .12s; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover { background: #f8faff; }
  tbody td { padding: 9px 13px; color: var(--text); vertical-align: middle; }

  /* grouped row borders */
  .group-first td { border-top: 1px solid #e9eef5; }

  .date-cell   { font-size: .79rem; color: var(--muted); white-space: nowrap; font-weight: 500; }
  .company-cell { font-weight: 600; font-size: .80rem; color: var(--text); }
  .station-cell { font-weight: 500; font-size: .80rem; }
  .fuel-badge { font-size: .78rem; color: var(--text); }

  .received-val { color: #16a34a; font-weight: 600; }
  .sold-val     { color: #ef4444; font-weight: 600; }
  .current-val  { font-weight: 700; }

  /* Status as plain colored text (no pill) */
  .status-available { color: #16a34a; font-weight: 600; font-size: .80rem; }
  .status-low       { color: #b45309; font-weight: 600; font-size: .80rem; }
  .status-zero      { color: #b91c1c; font-weight: 600; font-size: .80rem; }

  /* badge variants for other tabs */
  .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; white-space: nowrap; }
  .badge-available { background: #dcfce7; color: #15803d; }
  .badge-low       { background: #fef9c3; color: #b45309; }
  .badge-zero      { background: #fee2e2; color: #b91c1c; }
  .badge-active    { background: #dcfce7; color: #15803d; }
  .badge-inactive  { background: #fee2e2; color: #b91c1c; }

  .officer-name { font-weight: 600; font-size: .82rem; }
  .diff-positive { color: #ef4444; font-weight: 600; }
  .diff-negative { color: #22c55e; font-weight: 600; }
  .diff-zero     { color: var(--muted); font-weight: 500; }
  .summary-row td { background: #f7f9fc; font-weight: 700; font-size: .81rem; border-top: 2px solid var(--border); }
  .empty-row td { text-align: center; padding: 40px; color: var(--muted); font-size: .86rem; }

  /* ── EXPORT ── */
  .export-row {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 14px 22px 18px; border-top: 1px solid var(--border);
  }
  .btn-export {
    display: flex; align-items: center; gap: 8px; padding: 9px 20px;
    border-radius: 8px; font-family: 'DM Sans', sans-serif;
    font-size: .84rem; font-weight: 600; cursor: pointer; border: 1.5px solid; transition: all .15s;
    text-decoration: none;
  }
  .btn-pdf   { border-color: #e2e8f0; color: #475569; background: #fff; }
  .btn-pdf:hover { background: #f8f9fa; }
  .btn-excel { border-color: #16a34a; color: #fff; background: #16a34a; }
  .btn-excel:hover { background: #15803d; }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 1100px) {
    .stat-cards { grid-template-columns: repeat(3, 1fr); }
  }
  @media (max-width: 900px) {
    .filter-grid { grid-template-columns: repeat(2, 1fr); }
    .filter-apply-row { grid-column: 1 / 3; justify-content: flex-start; }
    .stat-cards  { grid-template-columns: repeat(2, 1fr); }
  }
</style>
@endpush

@section('content')

<h1 class="page-title">
  <i class="fa-solid fa-chart-mixed" style="font-size:1.2rem; margin-right:8px; opacity:.7;"></i>
  Reports &amp; Analytics
</h1>

<div class="card">

  {{-- ══ TABS ══ --}}
  <div class="tabs-bar">
    <button class="tab-btn active" onclick="switchTab('stock', this)">
      <i class="fa-solid fa-box-archive" style="margin-right:6px; font-size:.78rem;"></i>Stock Report
    </button>
    {{-- <button class="tab-btn" onclick="switchTab('sales', this)">
      <i class="fa-solid fa-chart-line" style="margin-right:6px; font-size:.78rem;"></i>Sales Report
    </button>
    <button class="tab-btn" onclick="switchTab('officer', this)">
      <i class="fa-solid fa-user-tie" style="margin-right:6px; font-size:.78rem;"></i>Tag Officer Report
    </button>
    <button class="tab-btn" onclick="switchTab('diff', this)">
      <i class="fa-solid fa-percent" style="margin-right:6px; font-size:.78rem;"></i>Difference (%) Report
    </button>
    <button class="tab-btn" onclick="switchTab('due', this)">
      <i class="fa-solid fa-clock-rotate-left" style="margin-right:6px; font-size:.78rem;"></i>Due Sales Report
    </button> --}}
  </div>

  {{-- ══════════════════════════════════════
       TAB 1 — STOCK REPORT (Figma updated)
  ══════════════════════════════════════ --}}
  <div id="tab-stock" class="tab-panel active">

    {{-- ── FILTERS ── --}}
    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('admin.reports.index') }}">
        <input type="hidden" name="tab" value="stock">
        <div class="filter-grid">

          {{-- Row 1 --}}
          <div class="filter-group">
            <label>From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" placeholder="dd/mm/yyyy">
          </div>
          <div class="filter-group">
            <label>To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" placeholder="dd/mm/yyyy">
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

          {{-- Row 2 --}}
          <div class="filter-group">
            <label>Upazila</label>
            <select name="upazila">
              <option value="">All Upazilas</option>
              @foreach($upazilas as $upazila)
                <option value="{{ $upazila }}" {{ request('upazila') == $upazila ? 'selected' : '' }}>{{ $upazila }}</option>
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

          {{-- Row 3 --}}
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

          {{-- Apply button — col 3-4, centered --}}
          <div class="filter-apply-row">
            <button type="submit" class="btn-apply">Apply Filters</button>
          </div>

        </div>
      </form>
    </div>

    {{-- ── STAT CARDS (5 cards) ── --}}
    @php
      $fuelType = request('fuel_type');
      $showAll  = !$fuelType;

      $totalStock   = $stockReports->sum('diesel_closing_stock') + $stockReports->sum('petrol_closing_stock') + $stockReports->sum('octane_closing_stock');
      $dieselStock  = $stockReports->sum('diesel_closing_stock');
      $petrolStock  = $stockReports->sum('petrol_closing_stock');
      $octaneStock  = $stockReports->sum('octane_closing_stock');
      $othersStock  = 0; // অন্য fuel type থাকলে এখানে যোগ করুন
    @endphp

    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
          <div class="stat-card-trend"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
        <div>
          <div class="stat-card-label">Total Stock</div>
          <div class="stat-card-value">{{ number_format($totalStock) }} L</div>
        </div>
      </div>

      <div class="stat-card green">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
        <div>
          <div class="stat-card-label">Diesel Stock</div>
          <div class="stat-card-value">{{ number_format($dieselStock) }} L</div>
        </div>
      </div>

      <div class="stat-card purple">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
        <div>
          <div class="stat-card-label">Petrol Stock</div>
          <div class="stat-card-value">{{ number_format($petrolStock) }} L</div>
        </div>
      </div>

      <div class="stat-card orange">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
        <div>
          <div class="stat-card-label">Octane Stock</div>
          <div class="stat-card-value">{{ number_format($octaneStock) }} L</div>
        </div>
      </div>

      <div class="stat-card teal">
        <div class="stat-card-top">
          <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
          <div class="stat-card-trend"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>
        <div>
          <div class="stat-card-label">Others Stock</div>
          <div class="stat-card-value">{{ number_format($othersStock) }} L</div>
        </div>
      </div>
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Stock Report by Company &amp; Filling Station</div>
        <div class="table-meta">{{ $stockReports->count() }} record(s) found</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Company Name</th>
              <th>Filling Station</th>
              <th>Fuel Type</th>
              <th>Opening Stock</th>
              <th>Received</th>
              <th>Sold</th>
              <th>Current Stock</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($stockReports as $report)
              @php
                // {{-- build fuel rows based on filter --}}
                $fuels = [];
                if ($showAll || $fuelType === 'diesel') $fuels[] = 'diesel';
                if ($showAll || $fuelType === 'petrol') $fuels[] = 'petrol';
                if ($showAll || $fuelType === 'octane') $fuels[] = 'octane';
                $rowspan = count($fuels);

                // {{-- company name via relation --}}
                $companyName = $report->fillingStation->company->name
                    ?? $report->fillingStation->company_name
                    ?? '—';

                // {{-- status based on total closing --}}
                $totalClosing = $report->diesel_closing_stock + $report->petrol_closing_stock + $report->octane_closing_stock;
                if ($totalClosing <= 0)       { $sClass = 'status-zero';      $sLabel = 'Zero Stock'; }
                elseif ($totalClosing < 2000) { $sClass = 'status-low';       $sLabel = 'Low Stock'; }
                else                          { $sClass = 'status-available';  $sLabel = 'Available'; }
              @endphp

              @foreach($fuels as $idx => $fuel)
              <tr class="{{ $idx === 0 ? 'group-first' : '' }}">

                {{-- Shared cells — rowspan on first row only --}}
                @if($idx === 0)
                <td rowspan="{{ $rowspan }}" class="date-cell">
                  {{ \Carbon\Carbon::parse($report->report_date)->format('Y-m-d') }}
                </td>
                <td rowspan="{{ $rowspan }}" class="company-cell">{{ $companyName }}</td>
                <td rowspan="{{ $rowspan }}" class="station-cell">
                  {{ $report->station_name }}
                </td>
                @endif

                {{-- Fuel-type specific data --}}
                <td class="fuel-badge">{{ ucfirst($fuel) }}</td>
                <td>{{ number_format($report->{$fuel.'_prev_stock'}) }} L</td>
                <td class="received-val">+{{ number_format($report->{$fuel.'_received'}) }} L</td>
                <td class="sold-val">-{{ number_format($report->{$fuel.'_sales'}) }} L</td>
                <td class="current-val">{{ number_format($report->{$fuel.'_closing_stock'}) }} L</td>

                {{-- Status — plain text, rowspan on first row --}}
                @if($idx === 0)
                <td rowspan="{{ $rowspan }}" class="{{ $sClass }}">{{ $sLabel }}</td>
                @endif

              </tr>
              @endforeach

            @empty
              <tr class="empty-row">
                <td colspan="9">
                  <i class="fa-solid fa-inbox" style="margin-right:8px; opacity:.4;"></i>No stock report data found.
                </td>
              </tr>
            @endforelse

            {{-- Summary row --}}
            @if($stockReports->count() > 0)
            <tr class="summary-row">
              <td colspan="4" style="text-align:right; padding-right:14px; color:var(--muted);">TOTAL</td>
              <td>
                {{ number_format(
                  ($showAll || $fuelType==='diesel' ? $stockReports->sum('diesel_prev_stock') : 0) +
                  ($showAll || $fuelType==='petrol' ? $stockReports->sum('petrol_prev_stock') : 0) +
                  ($showAll || $fuelType==='octane' ? $stockReports->sum('octane_prev_stock') : 0)
                ) }} L
              </td>
              <td class="received-val">
                {{ number_format(
                  ($showAll || $fuelType==='diesel' ? $stockReports->sum('diesel_received') : 0) +
                  ($showAll || $fuelType==='petrol' ? $stockReports->sum('petrol_received') : 0) +
                  ($showAll || $fuelType==='octane' ? $stockReports->sum('octane_received') : 0)
                ) }} L
              </td>
              <td class="sold-val">
                {{ number_format(
                  ($showAll || $fuelType==='diesel' ? $stockReports->sum('diesel_sales') : 0) +
                  ($showAll || $fuelType==='petrol' ? $stockReports->sum('petrol_sales') : 0) +
                  ($showAll || $fuelType==='octane' ? $stockReports->sum('octane_sales') : 0)
                ) }} L
              </td>
              <td class="current-val">{{ number_format($totalStock) }} L</td>
              <td></td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

    {{-- <div class="export-row">
      <button class="btn-export btn-pdf">
        <i class="fa-regular fa-file-pdf"></i> Export to PDF
      </button>
      <button class="btn-export btn-excel">
        <i class="fa-regular fa-file-excel"></i> Export to Excel
      </button>
    </div> --}}
  </div>{{-- /tab-stock --}}


  {{-- ══════════════════════════════════════
       TAB 2 — SALES REPORT
  ══════════════════════════════════════ --}}
  <div id="tab-sales" class="tab-panel">
    <div class="filter-section">
      <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
      <form method="GET" action="{{ route('admin.reports.index') }}">
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
          <div class="filter-apply-row" style="grid-column: 4/5; justify-content:flex-end;">
            <a href="{{ route('admin.reports.index') }}?tab=sales" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:5px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">Apply Filters</button>
          </div>
        </div>
      </form>
    </div>

    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-cart-shopping"></i></div><div class="stat-card-trend">Total</div></div>
        <div><div class="stat-card-label">Total Sales</div>
          <div class="stat-card-value">{{ number_format($salesReports->sum('petrol_sales') + $salesReports->sum('diesel_sales') + $salesReports->sum('octane_sales')) }} L</div>
        </div>
      </div>
      <div class="stat-card green">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div><div class="stat-card-trend">Diesel</div></div>
        <div><div class="stat-card-label">Diesel Sold</div><div class="stat-card-value">{{ number_format($salesReports->sum('diesel_sales')) }} L</div></div>
      </div>
      <div class="stat-card purple">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div><div class="stat-card-trend">Petrol</div></div>
        <div><div class="stat-card-label">Petrol Sold</div><div class="stat-card-value">{{ number_format($salesReports->sum('petrol_sales')) }} L</div></div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div><div class="stat-card-trend">Octane</div></div>
        <div><div class="stat-card-label">Octane Sold</div><div class="stat-card-value">{{ number_format($salesReports->sum('octane_sales')) }} L</div></div>
      </div>
      <div class="stat-card teal">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-chart-bar"></i></div><div class="stat-card-trend">Records</div></div>
        <div><div class="stat-card-label">Total Records</div><div class="stat-card-value">{{ $salesReports->count() }}</div></div>
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
              <th>#</th><th>Station</th><th>District</th><th>Report Date</th>
              <th>Diesel Recv</th><th>Diesel Sold</th>
              <th>Petrol Recv</th><th>Petrol Sold</th>
              <th>Octane Recv</th><th>Octane Sold</th>
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
              <tr class="empty-row"><td colspan="11"><i class="fa-solid fa-inbox" style="margin-right:8px;opacity:.4;"></i>No sales data found.</td></tr>
            @endforelse
            @if($salesReports->count() > 0)
            <tr class="summary-row">
              <td colspan="4" style="text-align:right;padding-right:14px;">TOTAL</td>
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
    {{-- <div class="export-row">
      <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export to PDF</button>
      <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export to Excel</button>
    </div> --}}
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
          <div class="filter-apply-row" style="grid-column: 4/5; justify-content:flex-end;">
            <a href="{{ route('admin.reports.index') }}?tab=officer" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:5px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">Apply Filters</button>
          </div>
        </div>
      </form>
    </div>

    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-users"></i></div><div class="stat-card-trend">Total</div></div>
        <div><div class="stat-card-label">Total Officers</div><div class="stat-card-value">{{ $officerReports->count() }}</div></div>
      </div>
      <div class="stat-card green">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-user-check"></i></div><div class="stat-card-trend">Active</div></div>
        <div><div class="stat-card-label">Active Assignments</div><div class="stat-card-value">{{ $officerReports->where('status', 'active')->count() }}</div></div>
      </div>
      <div class="stat-card red" style="background:linear-gradient(135deg,#ef4444,#b91c1c);">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-user-xmark"></i></div><div class="stat-card-trend">Inactive</div></div>
        <div><div class="stat-card-label">Inactive Assignments</div><div class="stat-card-value">{{ $officerReports->where('status', 'inactive')->count() }}</div></div>
      </div>
      <div class="stat-card teal">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-building"></i></div><div class="stat-card-trend">Stations</div></div>
        <div><div class="stat-card-label">Stations Covered</div><div class="stat-card-value">{{ $officerReports->pluck('filling_station_id')->unique()->count() }}</div></div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-map-location-dot"></i></div><div class="stat-card-trend">Districts</div></div>
        <div><div class="stat-card-label">Districts Covered</div>
          <div class="stat-card-value">{{ $officerReports->map(fn($a) => $a->fillingStation->district ?? '')->unique()->filter()->count() }}</div>
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
              <th>#</th><th>Officer Name</th><th>Email / Phone</th>
              <th>Filling Station</th><th>District</th><th>Division</th>
              <th>Assigned At</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($officerReports as $i => $assign)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="officer-name">{{ $assign->officer->name ?? '—' }}</td>
                <td style="font-size:.76rem; color:var(--muted); line-height:1.7;">
                  {{ $assign->officer->email ?? '' }}<br>{{ $assign->officer->phone ?? '' }}
                </td>
                <td class="station-cell">{{ $assign->fillingStation->station_name ?? '—' }}</td>
                <td>{{ $assign->fillingStation->district ?? '—' }}</td>
                <td>{{ $assign->fillingStation->division ?? '—' }}</td>
                <td>{{ $assign->created_at?->format('d M Y') ?? '—' }}</td>
                <td>
                  <span class="status-badge {{ $assign->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                    {{ ucfirst($assign->status) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr class="empty-row"><td colspan="8"><i class="fa-solid fa-inbox" style="margin-right:8px;opacity:.4;"></i>No officer data found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    {{-- <div class="export-row">
      <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export to PDF</button>
      <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export to Excel</button>
    </div> --}}
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
          <div class="filter-group"><label>From Date</label><input type="date" name="from_date" value="{{ request('from_date') }}"></div>
          <div class="filter-group"><label>To Date</label><input type="date" name="to_date" value="{{ request('to_date') }}"></div>
          <div class="filter-group">
            <label>District</label>
            <select name="district">
              <option value="">All Districts</option>
              @foreach($districts as $dist)
                <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-group"><label>Min Difference (L)</label><input type="number" name="min_diff" value="{{ request('min_diff', 0) }}" placeholder="e.g. 50"></div>
          <div class="filter-apply-row" style="grid-column: 3/5;">
            <a href="{{ route('admin.reports.index') }}?tab=diff" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:5px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">Apply Filters</button>
          </div>
        </div>
      </form>
    </div>

    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-scale-balanced"></i></div><div class="stat-card-trend">All</div></div>
        <div><div class="stat-card-label">Total Records</div><div class="stat-card-value">{{ $diffReports->count() }}</div></div>
      </div>
      <div class="stat-card red" style="background:linear-gradient(135deg,#ef4444,#b91c1c);">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-triangle-exclamation"></i></div><div class="stat-card-trend">Alert</div></div>
        <div><div class="stat-card-label">High Diff (&gt; 50L)</div>
          <div class="stat-card-value">{{ $diffReports->filter(fn($r) => abs($r->petrol_difference) + abs($r->diesel_difference) + abs($r->octane_difference) > 50)->count() }}</div>
        </div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div><div class="stat-card-trend">Net</div></div>
        <div><div class="stat-card-label">Total Diesel Diff</div><div class="stat-card-value">{{ number_format(abs($diffReports->sum('diesel_difference'))) }} L</div></div>
      </div>
      <div class="stat-card purple">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div><div class="stat-card-trend">Net</div></div>
        <div><div class="stat-card-label">Total Petrol Diff</div><div class="stat-card-value">{{ number_format(abs($diffReports->sum('petrol_difference'))) }} L</div></div>
      </div>
      <div class="stat-card teal">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div><div class="stat-card-trend">Net</div></div>
        <div><div class="stat-card-label">Total Octane Diff</div><div class="stat-card-value">{{ number_format(abs($diffReports->sum('octane_difference'))) }} L</div></div>
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
              <th>#</th><th>Station</th><th>District</th><th>Report Date</th>
              <th>Petrol Diff (L)</th><th>Petrol Diff (%)</th>
              <th>Diesel Diff (L)</th><th>Diesel Diff (%)</th>
              <th>Octane Diff (L)</th><th>Octane Diff (%)</th>
              <th>Total Diff</th>
            </tr>
          </thead>
          <tbody>
            @forelse($diffReports as $i => $report)
              @php
                $pPct = $report->petrol_received > 0 ? round($report->petrol_difference / $report->petrol_received * 100, 2) : 0;
                $dPct = $report->diesel_received > 0 ? round($report->diesel_difference / $report->diesel_received * 100, 2) : 0;
                $oPct = $report->octane_received > 0 ? round($report->octane_difference / $report->octane_received * 100, 2) : 0;
                $totalDiff = $report->petrol_difference + $report->diesel_difference + $report->octane_difference;
                $dc = fn($v) => $v > 0 ? 'diff-positive' : ($v < 0 ? 'diff-negative' : 'diff-zero');
              @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td class="station-cell">{{ $report->station_name }}</td>
                <td>{{ $report->district }}</td>
                <td>{{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}</td>
                <td class="{{ $dc($report->petrol_difference) }}">{{ $report->petrol_difference > 0 ? '+' : '' }}{{ number_format($report->petrol_difference) }} L</td>
                <td class="{{ $pPct > 0 ? 'diff-positive' : 'diff-zero' }}">{{ $pPct }}%</td>
                <td class="{{ $dc($report->diesel_difference) }}">{{ $report->diesel_difference > 0 ? '+' : '' }}{{ number_format($report->diesel_difference) }} L</td>
                <td class="{{ $dPct > 0 ? 'diff-positive' : 'diff-zero' }}">{{ $dPct }}%</td>
                <td class="{{ $dc($report->octane_difference) }}">{{ $report->octane_difference > 0 ? '+' : '' }}{{ number_format($report->octane_difference) }} L</td>
                <td class="{{ $oPct > 0 ? 'diff-positive' : 'diff-zero' }}">{{ $oPct }}%</td>
                <td class="{{ $dc($totalDiff) }}" style="font-weight:700;">{{ $totalDiff > 0 ? '+' : '' }}{{ number_format($totalDiff) }} L</td>
              </tr>
            @empty
              <tr class="empty-row"><td colspan="11"><i class="fa-solid fa-inbox" style="margin-right:8px;opacity:.4;"></i>No difference data found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    {{-- <div class="export-row">
      <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export to PDF</button>
      <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export to Excel</button>
    </div> --}}
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
          <div class="filter-group"><label>From Date</label><input type="date" name="from_date" value="{{ request('from_date') }}"></div>
          <div class="filter-group"><label>To Date</label><input type="date" name="to_date" value="{{ request('to_date') }}"></div>
          <div class="filter-group">
            <label>District</label>
            <select name="district">
              <option value="">All Districts</option>
              @foreach($districts as $dist)
                <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
              @endforeach
            </select>
          </div>
          <div class="filter-apply-row" style="grid-column: 4/5; justify-content:flex-end;">
            <a href="{{ route('admin.reports.index') }}?tab=due" class="btn-reset">
              <i class="fa-solid fa-rotate-left" style="margin-right:5px;"></i>Reset
            </a>
            <button type="submit" class="btn-apply">Apply Filters</button>
          </div>
        </div>
      </form>
    </div>

    <div class="stat-cards">
      <div class="stat-card blue">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-clock-rotate-left"></i></div><div class="stat-card-trend">Total</div></div>
        <div><div class="stat-card-label">Due Records</div><div class="stat-card-value">—</div></div>
      </div>
      <div class="stat-card red" style="background:linear-gradient(135deg,#ef4444,#b91c1c);">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div><div class="stat-card-trend">Diesel</div></div>
        <div><div class="stat-card-label">Diesel Due Sales</div><div class="stat-card-value">—</div></div>
      </div>
      <div class="stat-card orange">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div><div class="stat-card-trend">Petrol</div></div>
        <div><div class="stat-card-label">Petrol Due Sales</div><div class="stat-card-value">—</div></div>
      </div>
      <div class="stat-card purple">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div><div class="stat-card-trend">Octane</div></div>
        <div><div class="stat-card-label">Octane Due Sales</div><div class="stat-card-value">—</div></div>
      </div>
      <div class="stat-card teal">
        <div class="stat-card-top"><div class="stat-card-icon"><i class="fa-solid fa-circle-exclamation"></i></div><div class="stat-card-trend">Total</div></div>
        <div><div class="stat-card-label">Total Due Amount</div><div class="stat-card-value">—</div></div>
      </div>
    </div>

    <div class="table-section">
      <div class="table-header-row">
        <div class="table-title">Due Sales Report</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th><th>Station</th><th>District</th><th>Report Date</th>
              <th>Diesel Due Sales</th><th>Petrol Due Sales</th><th>Octane Due Sales</th><th>Total Due</th>
            </tr>
          </thead>
          <tbody>
            <tr class="empty-row">
              <td colspan="8"><i class="fa-solid fa-inbox" style="margin-right:8px;opacity:.4;"></i>Due sales column not yet configured.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    {{-- <div class="export-row">
      <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export to PDF</button>
      <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export to Excel</button>
    </div> --}}
  </div>{{-- /tab-due --}}

</div>{{-- /.card --}}

<script>
  (function () {
    const params    = new URLSearchParams(window.location.search);
    const activeTab = params.get('tab') || 'stock';
    const btn       = document.querySelector(`.tab-btn[onclick*="'${activeTab}'"]`);
    if (btn) switchTab(activeTab, btn);
  })();

  function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b  => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
  }
</script>

@endsection