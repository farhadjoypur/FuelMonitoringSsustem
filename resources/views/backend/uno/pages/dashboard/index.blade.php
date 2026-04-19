@extends('backend.uno.layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg: #f0f2f5;
            --white: #ffffff;
            --border: #e4e8f0;
            --text: #1a2332;
            --muted: #7a8899;
            --octane-c: #ED4033;
            --petrol-c: #80AA57;
            --diesel-c: #D19529;
            --others-c: #10888c;
            --blue: #2563eb;
            --green: #16a34a;
            --purple: #7c3aed;
            --orange: #ea580c;
            --radius: 12px;
            --shadow: 0 1px 3px rgba(0, 0, 0, .06), 0 4px 14px rgba(0, 0, 0, .04);
        }

        body {
            background: var(--bg);
        }

        .dash-wrap {
            padding: 24px;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
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

        /* ══════════════════════════════════════
               DATA ROWS (Stock / Received / Sales…)
            ══════════════════════════════════════ */
        .data-section {
            margin-bottom: 24px;
        }

        .data-row {
            display: grid;
            grid-template-columns: 180px 1fr;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .row-label {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.35;
        }

        /* 4-fuel card grid */
        .fuel-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .fuel-card {
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid transparent;
            transition: transform .15s;
        }

        .fuel-card:hover {
            transform: translateY(-2px);
        }

        /* Color themes */
        .fc-octane {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .fc-petrol {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .fc-diesel {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .fc-others {
            background: #ecfeff;
            border-color: #a5f3fc;
        }

        .fuel-icon-circle {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            color: #fff;
            flex-shrink: 0;
        }

        .fic-octane {
            background: var(--octane-c);
        }

        .fic-petrol {
            background: var(--petrol-c);
        }

        .fic-diesel {
            background: var(--diesel-c);
        }

        .fic-others {
            background: var(--others-c);
        }

        .fuel-info .fv {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -.5px;
            line-height: 1;
        }

        .fuel-info .fn {
            font-size: 12px;
            font-weight: 500;
            margin-top: 3px;
        }

        .fc-octane .fv,
        .fc-octane .fn {
            color: var(--octane-c);
        }

        .fc-petrol .fv,
        .fc-petrol .fn {
            color: var(--petrol-c);
        }

        .fc-diesel .fv,
        .fc-diesel .fn {
            color: var(--diesel-c);
        }

        .fc-others .fv,
        .fc-others .fn {
            color: var(--others-c);
        }

        /* divider between rows */
        .row-divider {
            border: none;
            border-top: 1px dashed #e4e8f0;
            margin: 4px 0 14px;
        }

        /* ===== SUMMARY 3 CARDS ===== */
        .sum-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .sum-card {
            border-radius: var(--radius);
            padding: 26px 24px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .sum-card::before {
            content: '';
            position: absolute;
            top: -24px;
            right: -24px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
        }

        .sum-card::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -10px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .07);
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

        .sum-card .sm-icon {
            font-size: 26px;
            margin-bottom: 12px;
            opacity: .9;
            position: relative;
            z-index: 1;
        }

        .sum-card .sm-val {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
            position: relative;
            z-index: 1;
        }

        .sum-card .sm-name {
            font-size: 13px;
            opacity: .85;
            margin-top: 4px;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }

        .sum-card .sm-trend {
            font-size: 12px;
            opacity: .8;
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            position: relative;
            z-index: 1;
        }

        /* ===== ACTIVITIES ===== */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .ac-head {
            padding: 18px 22px 14px;
            display: flex;
            align-items: center;
            gap: 9px;
            border-bottom: 1px solid var(--border);
            background: #fafbff;
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
            color: var(--octane-c);
        }

        .act-dot.orange {
            background: #ffedd5;
            color: var(--orange);
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

        .no-data {
            text-align: center;
            padding: 30px;
            color: var(--muted);
            font-size: 13px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1100px) {
            .fuel-cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .sum-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dash-wrap {
                padding: 14px;
            }

            .data-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .row-label {
                font-size: 13px;
            }

            .sum-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .fuel-cards {
                grid-template-columns: 1fr 1fr;
            }

            .fuel-card {
                padding: 12px 10px;
                gap: 10px;
            }

            .fuel-info .fv {
                font-size: 17px;
            }
        }


        /* ── Difference Report Table ── */

        /* Table Header Row */
        .table-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0;
            padding: 16px 20px;
            background: #fff;
            border-radius: 8px 8px 0 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #dc2626;
        }

        /* Export PDF Button */
        .export-pdf-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .export-pdf-btn:hover {
            background: #15803d;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
        }

        .export-pdf-btn:active {
            transform: translateY(0);
        }

        .diff-table-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            background: #fff;
            border-radius: 0 0 8px 8px;
        }

        .diff-table {
            width: max-content;
            min-width: 100%;
            border-collapse: collapse;
            font-size: .775rem;
            background: #fff;
        }

        .diff-table thead {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .diff-table th {
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            color: #1e293b;
            font-size: .70rem;
            text-transform: uppercase;
            letter-spacing: .3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            background: #f8fafc;
        }

        .diff-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background .12s;
            background: #fff;
        }

        .diff-table tbody tr:hover {
            background: #f9fafb;
        }

        .diff-table td {
            padding: 0;
            color: #1e293b;
            vertical-align: middle;
            text-align: center;
            overflow: hidden;
            background: #fff;
        }

        .diff-table td.row-number {
            font-weight: 600;
            color: #64748b;
            padding: 0 8px;
            text-align: center;
        }

        .diff-table td.td-station,
        .diff-table td.td-officer,
        .diff-table td.td-designation,
        .diff-table td.td-phone,
        .diff-table td.td-district,
        .diff-table td.td-upazila,
        .diff-table td.td-date {
            padding: 12px 8px;
            vertical-align: middle;
            text-align: center;
            word-break: break-word;
        }

        .diff-table th,
        .diff-table td {
            white-space: nowrap;
        }

        .td-station,
        .td-officer,
        .td-designation {
            white-space: normal;
        }

        /* Fuel rows inside cells */
        .fuel-rows {
            display: flex;
            flex-direction: column;
        }

        .fuel-row {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 6px;
            min-height: 40px;
            border-bottom: 1px solid #f1f5f9;
        }

        .fuel-row:last-child {
            border-bottom: none;
        }

        .fuel-type {
            font-size: .72rem;
            color: #64748b;
            font-weight: 500;
        }

        .fuel-value {
            font-weight: 700;
            font-size: .80rem;
            color: #1e293b;
        }

        .fuel-percent {
            font-weight: 700;
            font-size: .80rem;
            color: #1e293b;
        }

        .alert-text {
            font-size: .70rem;
            color: #64748b;
            text-align: center;
            line-height: 1.4;
        }

        /* RED DIFFERENCE COLUMNS */
        .diff-table td.diff-column .fuel-value,
        .diff-table td.diff-column .fuel-percent {
            color: #dc2626 !important;
            font-weight: 700;
        }

        /* Action buttons */
        .action-btns {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 10px 6px;
            align-items: center;
        }

        .action-btn {
            padding: 6px 8px;
            border-radius: 5px;
            font-size: .70rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
            text-align: center;
            width: 70px;
        }

        .btn-view {
            background: #22c55e;
            color: #fff;
        }

        .btn-view:hover {
            background: #16a34a;
        }

        .btn-message {
            background: #3b82f6;
            color: #fff;
        }

        .btn-message:hover {
            background: #2563eb;
        }

        .btn-delete {
            background: #ef4444;
            color: #fff;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .date-cell {
            font-weight: 500;
            font-size: .75rem;
            line-height: 1.4;
            color: #1e293b;
        }

        .date-day {
            font-size: .65rem;
            color: #64748b;
            display: block;
            margin-top: 2px;
        }

        /* Table Footer */
        .table-footer {
            display: flex;
            justify-content: center;
            padding: 20px 0;
            margin-top: 0;
            background: #fff;
            border-radius: 0 0 8px 8px;
            border-top: 1px solid #e2e8f0;
        }

        /* See All Button */
        .see-all-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 24px;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .see-all-btn:hover {
            background: #15803d;
            box-shadow: 0 4px 6px rgba(22, 163, 74, 0.2);
            transform: translateY(-1px);
        }

        .see-all-btn:active {
            transform: translateY(0);
        }

        .see-all-btn svg {
            transition: transform 0.2s;
        }

        .see-all-btn:hover svg {
            transform: translateX(3px);
        }

        /* Table Section Container */
        .table-section {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <div class="dash-wrap">

        <div class="db-title">Dashboard Overview</div>
        <p class="db-sub">Bangladesh Fuel Management System</p>

        {{-- ══════════════════════════════════════
         DATA ROWS
    ══════════════════════════════════════ --}}
        @php
            $rows = [
                [
                    'label' => "Today's Stock",
                    'fuels' => [
                        [
                            'theme' => 'octane',
                            'icon' => 'fa-droplet',
                            'val' => number_format($todayOctaneStock) . ' L',
                            'name' => 'Octane',
                        ],
                        [
                            'theme' => 'petrol',
                            'icon' => 'fa-gas-pump',
                            'val' => number_format($todayPetrolStock) . ' L',
                            'name' => 'Petrol',
                        ],
                        [
                            'theme' => 'diesel',
                            'icon' => 'fa-cube',
                            'val' => number_format($todayDieselStock) . ' L',
                            'name' => 'Diesel',
                        ],
                        ['theme' => 'others', 'icon' => 'fa-industry', 'val' => '0 L', 'name' => 'Others'],
                    ],
                ],
                [
                    'label' => "Today's Received",
                    'fuels' => [
                        [
                            'theme' => 'octane',
                            'icon' => 'fa-droplet',
                            'val' => number_format($todayOctaneReceived) . ' L',
                            'name' => 'Octane',
                        ],
                        [
                            'theme' => 'petrol',
                            'icon' => 'fa-gas-pump',
                            'val' => number_format($todayPetrolReceived) . ' L',
                            'name' => 'Petrol',
                        ],
                        [
                            'theme' => 'diesel',
                            'icon' => 'fa-cube',
                            'val' => number_format($todayDieselReceived) . ' L',
                            'name' => 'Diesel',
                        ],
                        ['theme' => 'others', 'icon' => 'fa-industry', 'val' => '0 L', 'name' => 'Others'],
                    ],
                ],
                [
                    'label' => "Today's Sales",
                    'fuels' => [
                        [
                            'theme' => 'octane',
                            'icon' => 'fa-droplet',
                            'val' => number_format($todayOctaneSold) . ' L',
                            'name' => 'Octane',
                        ],
                        [
                            'theme' => 'petrol',
                            'icon' => 'fa-gas-pump',
                            'val' => number_format($todayPetrolSold) . ' L',
                            'name' => 'Petrol',
                        ],
                        [
                            'theme' => 'diesel',
                            'icon' => 'fa-cube',
                            'val' => number_format($todayDieselSold) . ' L',
                            'name' => 'Diesel',
                        ],
                        ['theme' => 'others', 'icon' => 'fa-industry', 'val' => '0 L', 'name' => 'Others'],
                    ],
                ],
                [
                    'label' => "Today's Difference (L)",
                    'fuels' => [
                        [
                            'theme' => 'octane',
                            'icon' => 'fa-droplet',
                            'val' => number_format(abs($todayOctaneDiff)) . ' L',
                            'name' => 'Octane',
                        ],
                        [
                            'theme' => 'petrol',
                            'icon' => 'fa-gas-pump',
                            'val' => number_format(abs($todayPetrolDiff)) . ' L',
                            'name' => 'Petrol',
                        ],
                        [
                            'theme' => 'diesel',
                            'icon' => 'fa-cube',
                            'val' => number_format(abs($todayDieselDiff)) . ' L',
                            'name' => 'Diesel',
                        ],
                        ['theme' => 'others', 'icon' => 'fa-industry', 'val' => '0 L', 'name' => 'Others'],
                    ],
                ],
                [
                    'label' => "Today's Percentage (%)",
                    'fuels' => [
                        [
                            'theme' => 'octane',
                            'icon' => 'fa-droplet',
                            'val' => $todayOctaneDiffPct . '%',
                            'name' => 'Octane',
                        ],
                        [
                            'theme' => 'petrol',
                            'icon' => 'fa-gas-pump',
                            'val' => $todayPetrolDiffPct . '%',
                            'name' => 'Petrol',
                        ],
                        [
                            'theme' => 'diesel',
                            'icon' => 'fa-cube',
                            'val' => $todayDieselDiffPct . '%',
                            'name' => 'Diesel',
                        ],
                        ['theme' => 'others', 'icon' => 'fa-industry', 'val' => '0%', 'name' => 'Others'],
                    ],
                ],
            ];

            $iconMap = [
                'octane' => 'fic-octane',
                'petrol' => 'fic-petrol',
                'diesel' => 'fic-diesel',
                'others' => 'fic-others',
            ];
        @endphp

        {{-- ============================================================
         SUMMARY 3 CARDS
    ============================================================ --}}
        <div class="sum-grid">
            <div class="sum-card blue-c">
                <div class="sm-icon"><i class="fa-solid fa-building"></i></div>
                <div class="sm-val">{{ $totalDepots }}</div>
                <div class="sm-name">Total Depots</div>
            </div>
            <div class="sum-card green-c">
                <div class="sm-icon"><i class="fa-solid fa-gas-pump"></i></div>
                <div class="sm-val">{{ $totalStations }}</div>
                <div class="sm-name">Total Filling Stations</div>
            </div>
            <div class="sum-card purple-c">
                <div class="sm-icon"><i class="fa-solid fa-user-group"></i></div>
                <div class="sm-val">{{ $totalOfficers }}</div>
                <div class="sm-name">Total Tag Officers</div>
            </div>
        </div>

        <div class="data-section pt-3">
            @foreach ($rows as $i => $row)
                @if ($i > 0)
                    <hr class="row-divider">
                @endif
                <div class="data-row">
                    <div class="row-label">{{ $row['label'] }}</div>
                    <div class="fuel-cards">
                        @foreach ($row['fuels'] as $fuel)
                            <div class="fuel-card fc-{{ $fuel['theme'] }}">
                                <div class="fuel-icon-circle {{ $iconMap[$fuel['theme']] }}">
                                    <i class="fa-solid {{ $fuel['icon'] }}"></i>
                                </div>
                                <div class="fuel-info">
                                    <div class="fv">{{ $fuel['val'] }}</div>
                                    <div class="fn">{{ $fuel['name'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ============================================================
         RECENT ACTIVITIES
    ============================================================ --}}
        <!-- <div class="card" style="padding:0">
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
                    <div class="no-data">No activity found।</div>
    @endforelse
            </div> -->

        {{-- ============================================================
        Difference Report Table
    ============================================================ --}}
        {{-- Today's Difference Report Table --}}
        <div class="table-section" style="margin-bottom:24px;">
            <div class="table-header-row">
                <div class="table-title">
                    Today's Difference Report
                    <span style="font-size:.85rem; color:#94a3b8; font-weight:500;">
                        ({{ $highDifferenceReports->count() }} records)
                    </span>
                </div>
                <!-- <button class="export-pdf-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                        </svg>
                        Export to PDF
                    </button> -->
            </div>

            <div class="diff-table-wrapper">
                @if ($highDifferenceReports->isEmpty())
                    <div style="padding:50px; text-align:center; color:#94a3b8;">
                        <i class="fa-solid fa-circle-check"
                            style="font-size:2rem; display:block; margin-bottom:10px; color:#22c55e;"></i>
                        <p style="font-weight:600;">No significant differences found today</p>
                        <small>All fuel stocks are within normal range</small>
                    </div>
                @else
                    <table class="diff-table">
                        <thead>
                            <tr>
                                <th style="width:28px;">#</th>
                                <th style="width:62px;">DATE</th>
                                <th style="width:100px;">STATION</th>
                                <th style="width:80px;">TAG OFFICER</th>
                                <th style="width:82px;">PHONE</th>
                                <th style="width:62px;">DISTRICT</th>
                                <th style="width:62px;">UPAZILA</th>
                                <th style="width:50px;">FUEL</th>
                                <th style="width:80px;">DIFFERENCE (L)</th>
                                <th style="width:80px;">DIFFERENCE (%)</th>
                                <th style="width:80px;">ALERT</th>
                                <th style="width:76px;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($highDifferenceReports as $i => $report)
                                @php
                                    $fuels = [
                                        'Octane' => [
                                            'diff' => $report->octane_difference,
                                            'received' => $report->octane_received,
                                            'status' => $report->octane_status,
                                        ],
                                        'Petrol' => [
                                            'diff' => $report->petrol_difference,
                                            'received' => $report->petrol_received,
                                            'status' => $report->petrol_status,
                                        ],
                                        'Diesel' => [
                                            'diff' => $report->diesel_difference,
                                            'received' => $report->diesel_received,
                                            'status' => $report->diesel_status,
                                        ],
                                        'Others' => [
                                            'diff' => $report->others_difference,
                                            'received' => $report->others_received,
                                            'status' => $report->others_status,
                                        ],
                                    ];

                                    $officerName = $report->tagOfficer?->profile?->name ?? '—';
                                    $officerPhone = $report->tagOfficer?->phone ?? '—';
                                    $reportDate = \Carbon\Carbon::parse($report->report_date);

                                    // Status label helper
                                    $statusLabel = function (string $status): string {
                                        // $fuelData['diff']
                                        return match ($status) {
                                            'available' => 'Normal',
                                            'low' => 'Low Stock',
                                            'zero' => 'Zero Stock',
                                            'highdiff' => 'High Diff',
                                            default => '—',
                                        };
                                    };
                                @endphp
                                <tr>
                                    {{-- # --}}
                                    <td class="row-number">{{ $i + 1 }}</td>

                                    {{-- Date --}}
                                    <td class="td-date">
                                        <div class="date-cell">
                                            {{ $reportDate->format('d M') }}<br>{{ $reportDate->format('Y') }}
                                            <span class="date-day">{{ $reportDate->format('l') }}</span>
                                        </div>
                                    </td>

                                    {{-- Station --}}
                                    <td class="td-station">{{ $report->station_name }}</td>

                                    {{-- Officer --}}
                                    <td class="td-officer">{{ $officerName }}</td>

                                    {{-- Phone --}}
                                    <td class="td-phone">{{ $officerPhone }}</td>

                                    {{-- District --}}
                                    <td class="td-district">{{ $report->district }}</td>

                                    {{-- Upazila --}}
                                    <td class="td-upazila">{{ $report->thana_upazila }}</td>

                                    {{-- Fuel Type --}}
                                    <td>
                                        <div class="fuel-rows">
                                            @foreach ($fuels as $fuelName => $fuelData)
                                                <div class="fuel-row">
                                                    <span class="fuel-type">{{ $fuelName }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Difference (L) --}}
                                    <td class="diff-column">
                                        <div class="fuel-rows">
                                            @foreach ($fuels as $fuelData)
                                                <div class="fuel-row">
                                                    <span class="fuel-value">
                                                        {{ $fuelData['diff'] != 0 ? number_format(abs($fuelData['diff']), 2) : '—' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Difference (%) --}}
                                    {{-- Difference (%) --}}
                                    <td class="diff-column">
                                        <div class="fuel-rows">
                                            @foreach ($fuels as $fuelName => $fuelData)
                                                @php
                                                    $release = match ($fuelName) {
                                                        'Octane' => (float) $report->octane_supply,
                                                        'Petrol' => (float) $report->petrol_supply,
                                                        'Diesel' => (float) $report->diesel_supply,
                                                        'Others' => (float) $report->others_supply,
                                                        default => $fuelData['received'],
                                                    };

                                                    $diffPct =
                                                        $release > 0
                                                            ? round((abs($fuelData['diff']) / $release) * 100, 1)
                                                            : 0;

                                                    $alertText = match (true) {
                                                        $diffPct >= 50 => 'High Diff',
                                                        $diffPct >= 20 => 'Medium Diff',
                                                        $diffPct > 0 => 'Low Diff',
                                                        default => 'Normal',
                                                    };

                                                    $alertClass = match (true) {
                                                        $diffPct >= 50 => 'alert-high',
                                                        $diffPct >= 20 => 'alert-medium',
                                                        $diffPct > 0 => 'alert-low',
                                                        default => 'alert-normal',
                                                    };
                                                @endphp

                                                <div class="fuel-row">
                                                    <span class="fuel-percent">
                                                        {{ $diffPct !== null ? $diffPct . '%' : '—' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Alert --}}
                                    <td>
                                        <div class="fuel-rows">
                                            @foreach ($fuels as $fuelName => $fuelData)
                                                @php
                                                    $diffPct =
                                                        $fuelData['received'] > 0
                                                            ? round(
                                                                (abs($fuelData['diff']) / $fuelData['received']) * 100,
                                                                1,
                                                            )
                                                            : 0;

                                                    // Determine alert based on percentage (more reliable than status field)
                                                    $alertText = match (true) {
                                                        $diffPct >= 50 => 'High Diff',
                                                        $diffPct >= 20 => 'Medium Diff',
                                                        $diffPct > 0 => 'Low Diff',
                                                        default => 'Normal',
                                                    };

                                                    $alertClass = match (true) {
                                                        $diffPct >= 50 => 'alert-high',
                                                        $diffPct >= 20 => 'alert-medium',
                                                        $diffPct > 0 => 'alert-low',
                                                        default => 'alert-normal',
                                                    };
                                                @endphp

                                                <div class="fuel-row">
                                                    <span class="alert-text {{ $alertClass }}">
                                                        {{ $alertText }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="action-btns">
                                            <a href="{{ route('uno.reports.edit', $report->id) }}"
                                            class="action-btn"
                                            style="background:#2563eb; color:#fff; text-decoration:none; border:none;
                                                    display:inline-flex; align-items:center; justify-content:center; gap:4px;">
                                                <i class="fa-solid fa-pen-to-square fa-xs"></i> Edit
                                            </a>
                                            <form id="uno-delete-form-{{ $report->id }}"
                                                action="{{ route('uno.dashboard.report.destroy', $report->id) }}"
                                                method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" class="action-btn btn-delete"
                                                onclick="confirmDelete('uno-delete-form-{{ $report->id }}')">
                                                <i class="fa-solid fa-trash fa-xs"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="table-footer">
                <a href="{{ route('uno.reports.index') }}" class="see-all-btn">
                    See All Reports
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                </a>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(formId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This report will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
@endpush
