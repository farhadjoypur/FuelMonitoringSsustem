@extends('backend.dc.layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    :root {
        --bg: #f0f2f5;
        --white: #ffffff;
        --border: #e8ecf0;
        --text: #1a1f2e;
        --muted: #7c8db5;
        --petrol: #80AA57;
        --diesel: #D19529;
        --octane: #ED4033;
        --others: #199CA1;
        --blue: #2563eb;
        --green: #16a34a;
        --purple: #7c3aed;
        --orange: #ea580c;
        --shadow: 0 1px 3px rgba(0, 0, 0, .07), 0 4px 16px rgba(0, 0, 0, .04);
        --radius: 14px;
    }

    .db-wrap {
        /* background: var(--bg); */
        min-height: 100vh;
        /* padding: 28px; */
    }

    .db-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--text);
        margin: 0 0 3px;
        letter-spacing: -.3px;
    }

    .db-sub {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 26px;
    }

    /* grids */
    .g-5 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .g-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .g-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .g-22 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 14px;
        text-align: center;
    }

    /* stat card */
    .stat-card {
        background: var(--white);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        padding: 20px 18px;
        position: relative;
    }

    .stat-card .sc-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .stat-card .sc-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--text);
        letter-spacing: -.5px;
        line-height: 1.1;
        margin: 6px 0;
    }

    .stat-card .sc-footer {
        font-size: 12px;
        color: var(--muted);
    }

    .stat-card .sc-footer .up {
        color: var(--green);
        font-weight: 600;
    }

    .stat-card .sc-footer .down {
        color: var(--octane);
        font-weight: 600;
    }

    .sc-alert {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 700;
        color: var(--octane);
        margin: 4px 0;
    }

    .sc-icon {
        position: absolute;
        top: 18px;
        right: 18px;
        width: 44px;
        height: 44px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #fff;
    }

    .sc-icon.blue {
        background: var(--blue);
    }

    .sc-icon.green {
        background: var(--green);
    }

    .sc-icon.purple {
        background: var(--purple);
    }

    .sc-icon.orange {
        background: var(--orange);
    }

    .sc-icon.red {
        background: var(--octane);
    }

    /* fuel cards */
    .fuel-card {
        border-radius: var(--radius);
        border: 1px solid transparent;
        padding: 20px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .fuel-card.petrol {
        background: #E1EDDF;
    }

    .fuel-card.diesel {
        background: #EDEADA;
    }

    .fuel-card.octane {
        background: #F1DEDC;
    }

    .fuel-card.others {
        background: #C8EFF0;
    }

    .fuel-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
        flex-shrink: 0;
    }

    .fuel-icon.p {
        background: var(--petrol);
    }

    .fuel-icon.d {
        background: var(--diesel);
    }

    .fuel-icon.o {
        background: var(--octane);
    }

    .fuel-icon.ot {
        background: var(--others);
    }

    .fuel-info .fi-val {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -.5px;
        line-height: 1;
    }

    .fuel-info .fi-name {
        font-size: 13px;
        font-weight: 500;
        margin-top: 3px;
    }

    .fuel-card.petrol .fi-val,
    .fuel-card.petrol .fi-name {
        color: var(--petrol);
    }

    .fuel-card.diesel .fi-val,
    .fuel-card.diesel .fi-name {
        color: var(--diesel);
    }

    .fuel-card.octane .fi-val,
    .fuel-card.octane .fi-name {
        color: var(--octane);
    }

    .fuel-card.others .fi-val,
    .fuel-card.others .fi-name {
        color: var(--others);
    }

    /* summary cards */
    .sum-card {
        border-radius: var(--radius);
        padding: 24px 22px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .sum-card::before {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .1);
    }

    .sum-card.blue-c {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
    }

    .sum-card.green-c {
        background: linear-gradient(135deg, #16a34a, #15803d);
    }

    .sum-card.purple-c {
        background: linear-gradient(135deg, #7c3aed, #6d28d9);
    }

    .sum-card.orange-c {
        background: linear-gradient(135deg, #ea580c, #c2410c);
    }

    .sum-card .sm-icon {
        font-size: 24px;
        margin-bottom: 12px;
        opacity: .9;
    }

    .sum-card .sm-val {
        font-size: 36px;
        font-weight: 800;
        letter-spacing: -1px;
        line-height: 1;
    }

    .sum-card .sm-name {
        font-size: 13px;
        opacity: .85;
        margin-top: 4px;
        font-weight: 500;
    }

    .sum-card .sm-trend {
        font-size: 12px;
        opacity: .8;
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* chart cards */
    .card {
        background: var(--white);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .chart-card {
        padding: 22px;
    }

    .cc-head {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 20px;
    }

    .cc-head i {
        color: var(--blue);
        font-size: 15px;
    }

    .cc-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
    }

    .cc-select {
        margin-left: auto;
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 5px 10px;
        font-size: 12px;
        color: var(--text);
        background: var(--bg);
    }

    .chart-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 16px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: var(--text);
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* depot table */
    .depot-table-card {
        padding: 0;
    }

    .dt-head {
        padding: 18px 22px 14px;
        display: flex;
        align-items: center;
        gap: 9px;
        border-bottom: 1px solid var(--border);
    }

    .dt-head i {
        color: var(--blue);
    }

    .dt-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
    }

    .dep-table {
        width: 100%;
        border-collapse: collapse;
    }

    .dep-table thead th {
        padding: 10px 18px;
        font-size: 10px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .5px;
        background: #fafbfc;
        border-bottom: 1px solid var(--border);
        text-align: left;
    }

    .dep-table tbody td {
        padding: 13px 18px;
        font-size: 13px;
        color: var(--text);
        border-bottom: 1px solid #f5f6fa;
    }

    .dep-table tbody tr:last-child td {
        border-bottom: none;
    }

    .dep-table tbody tr:hover td {
        background: #fafbfc;
    }

    .depot-name {
        font-weight: 700;
    }

    .util-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .util-bar {
        flex: 1;
        height: 6px;
        background: #e8ecf0;
        border-radius: 3px;
        overflow: hidden;
        min-width: 80px;
    }

    .util-fill {
        height: 100%;
        border-radius: 3px;
        transition: width .3s;
    }

    .util-fill.high {
        background: #ef4444;
    }

    .util-fill.medium {
        background: #f59e0b;
    }

    .util-fill.low {
        background: #22c55e;
    }

    .util-fill.zero {
        background: #e2e8f0;
    }

    .util-pct {
        font-size: 12px;
        font-weight: 700;
        color: var(--text);
        min-width: 34px;
    }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .badge.active {
        background: #dcfce7;
        color: var(--green);
    }

    .badge.inactive {
        background: #fee2e2;
        color: var(--octane);
    }

    /* activities */
    .activity-card {
        padding: 0;
    }

    .ac-head {
        padding: 18px 22px 14px;
        display: flex;
        align-items: center;
        gap: 9px;
        border-bottom: 1px solid var(--border);
    }

    .ac-head i {
        color: var(--purple);
    }

    .ac-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
    }

    .act-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 22px;
        border-bottom: 1px solid #f5f6fa;
    }

    .act-item:last-child {
        border-bottom: none;
    }

    .act-dot {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    .act-dot.green {
        background: #dcfce7;
        color: var(--green);
    }

    .act-dot.yellow {
        background: #fef9c3;
        color: #d97706;
    }

    .act-dot.blue {
        background: #dbeafe;
        color: var(--blue);
    }

    .act-dot.red {
        background: #fee2e2;
        color: var(--octane);
    }

    .ai-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
    }

    .ai-sub {
        font-size: 12px;
        color: var(--muted);
        margin-top: 1px;
    }

    .ai-time {
        font-size: 11px;
        color: var(--muted);
        margin-top: 3px;
    }

    /* no-data */
    .no-data {
        text-align: center;
        padding: 30px;
        color: var(--muted);
        font-size: 13px;
    }

    /* ================= Mobile Responsive Styles ================= */

    @media (max-width: 1200px) {
        .g-5 {
            grid-template-columns: repeat(3, 1fr);
        }

        .g-4 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        .db-wrap {
            padding: 0px;
        }

        .g-5 {
            grid-template-columns: repeat(2, 1fr);
        }

        .g-22 {
            grid-template-columns: 1fr;
        }

        /* চার্টগুলো একে অপরের নিচে আসবে */
    }

    @media (max-width: 768px) {

        .g-5,
        .g-4,
        .g-3,
        .g-22 {
            grid-template-columns: 1fr;
            /* সব গ্রিড ১ কলাম হয়ে যাবে */
            gap: 12px;
        }

        .db-title {
            font-size: 18px;
        }

        .stat-card .sc-value {
            font-size: 20px;
        }

        /* টেবিল রেসপন্সিভ করার জন্য */
        .depot-table-card {
            overflow-x: auto;
        }

        .dep-table {
            min-width: 600px;
            /* টেবিল যেন একদম ছোট হয়ে না যায়, স্ক্রল হবে */
        }

        /* চার্টের উচ্চতা মোবাইলে কিছুটা কমানো */
        canvas {
            max-height: 250px;
        }

        /* ফিউয়েল কার্ডের টেক্সট সাইজ ছোট করা */
        .fuel-info .fi-val {
            font-size: 20px;
        }

        .sum-card .sm-val {
            font-size: 28px;
        }
    }

    @media (max-width: 480px) {
        .g-5 {
            grid-template-columns: 1fr;
        }

        .stat-card {
            padding: 15px;
        }

        .sc-icon {
            width: 35px;
            height: 35px;
            top: 12px;
            right: 12px;
            font-size: 14px;
        }
    }
</style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        <div class="db-title">Dashboard Overview</div>
        <!-- <p class="db-sub">Bangladesh Fuel Management System &mdash; Real-time Monitoring</p> -->
        <p class="db-sub">Bangladesh Fuel Management System</p>

        {{-- ============================================================
         ROW 1: TOP STAT CARDS
         ============================================================ --}}
        <!-- <div class="g-5">

            {{-- Total Stock --}}
            <div class="stat-card">
                <div class="sc-icon blue"><i class="fa-solid fa-cube"></i></div>
                <div class="sc-label">Total Stock</div>
                <div class="sc-value">{{ number_format($totalStockToday) }} L</div>
                <div class="sc-footer">Todays</div>
            </div>

            {{-- Received --}}
            <div class="stat-card">
                <div class="sc-icon green"><i class="fa-solid fa-arrow-trend-up"></i></div>
                <div class="sc-label">Received</div>
                <div class="sc-value">{{ number_format($totalReceivedToday) }} L</div>
                <div class="sc-footer">
                    @if ($receivedChangePct >= 0)
                        <span class="up"><i class="fa-solid fa-arrow-trend-up fa-xs"></i>
                            +{{ $receivedChangePct }}%</span>
                    @else
                        <span class="down"><i class="fa-solid fa-arrow-trend-down fa-xs"></i>
                            {{ $receivedChangePct }}%</span>
                    @endif
                    &nbsp;vs last month
                </div>
            </div>

            {{-- Sold --}}
            <div class="stat-card">
                <div class="sc-icon purple"><i class="fa-solid fa-arrow-trend-down"></i></div>
                <div class="sc-label">Sold</div>
                <div class="sc-value">{{ number_format($totalSoldToday) }} L</div>
                <div class="sc-footer">Todays</div>
            </div>

            {{-- Stock Difference (L) --}}
            <div class="stat-card">
                <div class="sc-icon orange"><i class="fa-solid fa-droplet"></i></div>
                <div class="sc-label">Stock Difference</div>
                <div class="sc-value">{{ number_format(abs($totalDiffToday)) }} L</div>
                @if (abs($totalDiffToday) > 0)
                    <div class="sc-alert"><i class="fa-solid fa-triangle-exclamation"></i> Alert</div>
                @endif
                <div class="sc-footer">Todays</div>
            </div>

            {{-- Stock Difference (%) --}}
            <div class="stat-card">
                <div class="sc-icon red"><i class="fa-solid fa-percent"></i></div>
                <div class="sc-label">Stock Difference</div>
                <div class="sc-value">{{ $totalDiffPct }}%</div>
                @if ($totalDiffPct > 2)
                    <div class="sc-alert"><i class="fa-solid fa-triangle-exclamation"></i> Alert</div>
                @endif
                <div class="sc-footer">Todays</div>
            </div>
        </div> -->

        {{-- ============================================================
         ROW 2: TODAY'S STOCK
         ============================================================ --}}
        <p class="section-title">Today's Stock</p>
        <div class="g-4">
            <div class="fuel-card petrol">
                <div class="fuel-icon p"><i class="fa-solid fa-gas-pump"></i></div>
                <div class="fuel-info">
                    <div class="fi-val">{{ number_format($todayPetrolStock) }} L</div>
                    <div class="fi-name">Petrol</div>
                </div>
            </div>
            <div class="fuel-card diesel">
                <div class="fuel-icon d"><i class="fa-solid fa-cube"></i></div>
                <div class="fuel-info">
                    <div class="fi-val">{{ number_format($todayDieselStock) }} L</div>
                    <div class="fi-name">Diesel</div>
                </div>
            </div>
            <div class="fuel-card octane">
                <div class="fuel-icon o"><i class="fa-solid fa-droplet"></i></div>
                <div class="fuel-info">
                    <div class="fi-val">{{ number_format($todayOctaneStock) }} L</div>
                    <div class="fi-name">Octane</div>
                </div>
            </div>
            <div class="fuel-card others">
                <div class="fuel-icon ot"><i class="fa-solid fa-droplet"></i></div>
                <div class="fuel-info">
                    <div class="fi-val">0 L</div>
                    <div class="fi-name">Others</div>
                </div>
            </div>
        </div>

        {{-- ============================================================
         ROW 3: TODAY'S SOLD
         ============================================================ --}}
        <p class="section-title">Today's Sold</p>
        <div class="g-4">
            <div class="fuel-card petrol">
                <div class="fuel-icon p"><i class="fa-solid fa-gas-pump"></i></div>
                <div class="fuel-info">
                    <div class="fi-val">{{ number_format($todayPetrolSold) }} L</div>
                    <div class="fi-name">Petrol</div>
                </div>
            </div>
            <div class="fuel-card diesel">
                <div class="fuel-icon d"><i class="fa-solid fa-cube"></i></div>
                <div class="fuel-info">
                    <div class="fi-val">{{ number_format($todayDieselSold) }} L</div>
                    <div class="fi-name">Diesel</div>
                </div>
            </div>
            <div class="fuel-card octane">
                <div class="fuel-icon o"><i class="fa-solid fa-droplet"></i></div>
                <div class="fuel-info">
                    <div class="fi-val">{{ number_format($todayOctaneSold) }} L</div>
                    <div class="fi-name">Octane</div>
                </div>
            </div>
            <div class="fuel-card others">
                <div class="fuel-icon ot"><i class="fa-solid fa-droplet"></i></div>
                <div class="fuel-info">
                    <div class="fi-val">0 L</div>
                    <div class="fi-name">Others</div>
                </div>
            </div>
        </div>

        {{-- ============================================================
         ROW 4: SUMMARY 4 CARDS
         ============================================================ --}}
        <div class="g-4">
            <div class="sum-card blue-c">
                <div class="sm-icon"><i class="fa-solid fa-warehouse"></i></div>
                <div class="sm-val">{{ $totalDepots }}</div>
                <div class="sm-name">Total Depots</div>
                <!-- <div class="sm-trend">
                    <i class="fa-solid fa-arrow-trend-{{ $newDepots >= 0 ? 'up' : 'down' }}"></i>
                    {{ $newDepots >= 0 ? '+' : '' }}{{ $newDepots }} this month
                </div> -->
            </div>
            <div class="sum-card green-c">
                <div class="sm-icon"><i class="fa-solid fa-gas-pump"></i></div>
                <div class="sm-val">{{ $totalStations }}</div>
                <div class="sm-name">Total Filling Stations</div>
                <!-- <div class="sm-trend">
                    <i class="fa-solid fa-arrow-trend-{{ $newStations >= 0 ? 'up' : 'down' }}"></i>
                    {{ $newStations >= 0 ? '+' : '' }}{{ $newStations }} this month
                </div> -->
            </div>
            <div class="sum-card purple-c">
                <div class="sm-icon"><i class="fa-solid fa-user-group"></i></div>
                <div class="sm-val">{{ $totalOfficers }}</div>
                <div class="sm-name">Total Tag Officers</div>
                <!-- <div class="sm-trend">
                    <i class="fa-solid fa-arrow-trend-{{ $newOfficers >= 0 ? 'up' : 'down' }}"></i>
                    {{ $newOfficers >= 0 ? '+' : '' }}{{ $newOfficers }} this month
                </div> -->
            </div>
            <div class="sum-card orange-c">
                <div class="sm-icon"><i class="fa-solid fa-user-check"></i></div>
                <div class="sm-val">{{ $activeAssignments }}</div>
                <div class="sm-name">Active Assignments</div>
                <!-- <div class="sm-trend">
                    <i class="fa-solid fa-arrow-trend-{{ $assignChange >= 0 ? 'up' : 'down' }}"></i>
                    {{ $assignChange >= 0 ? '+' : '' }}{{ $assignChange }} this month
                </div> -->
            </div>
        </div>

        {{-- ============================================================
         ROW 5: CHARTS
         ============================================================ --}}
        <!-- <div class="g-22">

            {{-- Fuel Type Distribution Pie --}}
            <div class="card chart-card">
                <div class="cc-head">
                    <i class="fa-solid fa-wave-square"></i>
                    <span class="cc-title">Fuel Type Distribution (Stock in Liters)</span>
                </div>
                @php
                    $grandTotal = $totalPetrol + $totalDiesel + $totalOctane + $totalOthers;
                    $pPct = $grandTotal > 0 ? round(($totalPetrol / $grandTotal) * 100) : 0;
                    $dPct = $grandTotal > 0 ? round(($totalDiesel / $grandTotal) * 100) : 0;
                    $oPct = $grandTotal > 0 ? round(($totalOctane / $grandTotal) * 100) : 0;
                    $otPct = $grandTotal > 0 ? 100 - $pPct - $dPct - $oPct : 0;
                @endphp
                <canvas id="fuelPieChart" height="220"></canvas>
                <div class="chart-legend" style="margin-top:18px">
                    <div class="legend-item"><span class="legend-dot" style="background:#1e6fa8"></span>Diesel:
                        {{ number_format($totalDiesel) }} L</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#2d9748"></span>Petrol:
                        {{ number_format($totalPetrol) }} L</div>
                    <div class="legend-item"><span class="legend-dot" style="background:#f97316"></span>Octane:
                        {{ number_format($totalOctane) }} L</div>
                    @if ($totalOthers > 0)
                        <div class="legend-item"><span class="legend-dot" style="background:#c0392b"></span>Others:
                            {{ number_format($totalOthers) }} L</div>
                    @endif
                </div>
            </div>

            {{-- Company Type Distribution Pie --}}
            <div class="card chart-card">
                <div class="cc-head">
                    <i class="fa-solid fa-gas-pump"></i>
                    <span class="cc-title">Company Type Distribution</span>
                </div>
                <canvas id="companyPieChart" height="220"></canvas>
                <div class="chart-legend" style="margin-top:18px">
                    @php $pieColors = ['#1e6fa8','#2d9748','#f97316','#c0392b','#7c3aed']; @endphp
                    @foreach ($companyDistribution as $i => $co)
                        <div class="legend-item">
                            <span class="legend-dot" style="background:{{ $pieColors[$i] ?? '#aaa' }}"></span>
                            {{ $co['name'] }}: {{ $co['total'] }} Stations
                        </div>
                    @endforeach
                </div>
            </div>
        </div> -->

        <!-- <div class="g-22">

            {{-- Division-wise Distribution Bar --}}
            <div class="card chart-card">
                <div class="cc-head">
                    <i class="fa-solid fa-warehouse"></i>
                    <span class="cc-title">Division-wise Distribution</span>
                </div>
                <canvas id="divisionBarChart" height="220"></canvas>
            </div>

            {{-- Fuel Sales Trend Line --}}
            <div class="card chart-card">
                <div class="cc-head">
                    <i class="fa-solid fa-arrow-trend-up" style="color:#f97316"></i>
                    <span class="cc-title">Fuel Sales Trend (in Liters)</span>
                    <select class="cc-select">
                        <option>Month</option>
                    </select>
                </div>
                <canvas id="salesLineChart" height="220"></canvas>
            </div>
        </div> -->

        {{-- ============================================================
         ROW 6: DEPOT TABLE + ACTIVITIES
         ============================================================ --}}
        <div style="margin-bottom: 24px;">
        <!-- <div class="g-22"> -->

            {{-- Recent Depot Entries --}}
            <!-- <div class="card depot-table-card">
                <div class="dt-head">
                    <i class="fa-solid fa-warehouse"></i>
                    <span class="dt-title">Recent Depot Entries</span>
                </div>
                <table class="dep-table">
                    <thead>
                        <tr>
                            <th>Depot Name</th>
                            <th>District</th>
                            <th>Capacity</th>
                            <th>Utilization</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDepots as $depot)
                            @php
                                $u = $depot['utilization'];
                                $fillClass = $u >= 85 ? 'high' : ($u >= 50 ? 'medium' : ($u > 0 ? 'low' : 'zero'));
                            @endphp
                            <tr>
                                <td>
                                    <div class="depot-name">{{ $depot['name'] }}</div>
                                </td>
                                <td><span style="font-size:12px;color:var(--muted)">{{ $depot['district'] }}</span></td>
                                <td>{{ $depot['capacity'] }}</td>
                                <td>
                                    <div class="util-wrap">
                                        <div class="util-bar">
                                            <div class="util-fill {{ $fillClass }}"
                                                style="width:{{ $u }}%"></div>
                                        </div>
                                        <span class="util-pct">{{ $u }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $depot['status'] === 'active' ? 'active' : 'inactive' }}">
                                        {{ ucfirst($depot['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="no-data">কোনো Depot পাওয়া যায়নি।</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div> -->

            {{-- Recent Activities --}}
            <div class="card activity-card">
                <div class="ac-head">
                    <i class="fa-solid fa-wave-square"></i>
                    <span class="ac-title">Recent Activities</span>
                </div>
                @forelse($recentActivities as $act)
                    <div class="act-item">
                        <div class="act-dot {{ $act['color'] }}">
                            <i class="fa-solid {{ $act['icon'] }} fa-xs"></i>
                        </div>
                        <div>
                            <div class="ai-title">{{ $act['title'] }}</div>
                            <div class="ai-sub">{{ $act['sub'] }}</div>
                            <div class="ai-time">
                                <i class="fa-regular fa-clock fa-xs"></i>
                                {{ \Carbon\Carbon::parse($act['time'])->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="no-data">কোনো Activity নেই।</div>
                @endforelse
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        Chart.defaults.font.family = "'Segoe UI', sans-serif";
        Chart.defaults.color = '#7c8db5';

        /* ========== Fuel Type Pie ========== */
        new Chart(document.getElementById('fuelPieChart'), {
            type: 'pie',
            data: {
                labels: ['Diesel', 'Petrol', 'Octane'
                    @if ($totalOthers > 0)
                        , 'Others'
                    @endif
                ],
                datasets: [{
                    data: [
                        {{ $totalDiesel }},
                        {{ $totalPetrol }},
                        {{ $totalOctane }}
                        @if ($totalOthers > 0)
                            , {{ $totalOthers }}
                        @endif
                    ],
                    backgroundColor: ['#1e6fa8', '#2d9748', '#f97316', '#c0392b'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString()} L`
                        }
                    }
                }
            }
        });

        /* ========== Company Pie ========== */
        @php
            $coLabels = $companyDistribution->pluck('name')->toJson();
            $coData = $companyDistribution->pluck('total')->toJson();
        @endphp
        new Chart(document.getElementById('companyPieChart'), {
            type: 'pie',
            data: {
                labels: {!! $coLabels !!},
                datasets: [{
                    data: {!! $coData !!},
                    backgroundColor: ['#1e6fa8', '#2d9748', '#f97316', '#c0392b', '#7c3aed'],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.parsed} Stations`
                        }
                    }
                }
            }
        });

        /* ========== Division Bar ========== */
        @php
            $divLabels = $divisionDistribution->pluck('division')->toJson();
            $divData = $divisionDistribution->pluck('total')->toJson();
        @endphp
        new Chart(document.getElementById('divisionBarChart'), {
            type: 'bar',
            data: {
                labels: {!! $divLabels !!},
                datasets: [{
                    data: {!! $divData !!},
                    backgroundColor: '#2d9748',
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f0f2f5'
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        /* ========== Sales Trend Line ========== */
        @php
            $trendLabels = $salesTrend->pluck('month_label')->toJson();
            $trendData = $salesTrend->pluck('total_sales')->toJson();
        @endphp
        new Chart(document.getElementById('salesLineChart'), {
            type: 'line',
            data: {
                labels: {!! $trendLabels !!},
                datasets: [{
                    data: {!! $trendData !!},
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249,115,22,.08)',
                    pointBackgroundColor: '#f97316',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2.5,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f0f2f5'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: v => v.toLocaleString()
                        }
                    }
                }
            }
        });
    </script>
@endpush
