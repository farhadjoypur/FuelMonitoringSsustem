@extends('backend.admin.layouts.app')

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
        --octane-c:  #ED4033;
        --petrol-c:  #80AA57;
        --diesel-c:  #D19529;
        --others-c:  #10888c;
        --blue: #2563eb;
        --green: #16a34a;
        --purple: #7c3aed;
        --orange: #ea580c;
        --radius: 12px;
        --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 14px rgba(0,0,0,.04);
    }

    body { background: var(--bg); }

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
    .data-section { margin-bottom: 24px; }

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
    .fuel-card:hover { transform: translateY(-2px); }

    /* Color themes */
    .fc-octane { background: #fef2f2; border-color: #fecaca; }
    .fc-petrol  { background: #f0fdf4; border-color: #bbf7d0; }
    .fc-diesel  { background: #fffbeb; border-color: #fde68a; }
    .fc-others  { background: #ecfeff; border-color: #a5f3fc; }

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
    .fic-octane { background: var(--octane-c); }
    .fic-petrol { background: var(--petrol-c); }
    .fic-diesel { background: var(--diesel-c); }
    .fic-others { background: var(--others-c); }

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

    .fc-octane .fv, .fc-octane .fn { color: var(--octane-c); }
    .fc-petrol  .fv, .fc-petrol  .fn { color: var(--petrol-c); }
    .fc-diesel  .fv, .fc-diesel  .fn { color: var(--diesel-c); }
    .fc-others  .fv, .fc-others  .fn { color: var(--others-c); }

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
        background: rgba(255,255,255,.12);
    }
    .sum-card::after {
        content: '';
        position: absolute;
        bottom: -30px;
        left: -10px;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,.07);
    }
    .sum-card.blue-c   { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
    .sum-card.green-c  { background: linear-gradient(135deg, #16a34a, #15803d); }
    .sum-card.purple-c { background: linear-gradient(135deg, #7c3aed, #6d28d9); }

    .sum-card .sm-icon { font-size: 26px; margin-bottom: 12px; opacity: .9; position: relative; z-index: 1; }
    .sum-card .sm-val  { font-size: 40px; font-weight: 800; letter-spacing: -1px; line-height: 1; position: relative; z-index: 1; }
    .sum-card .sm-name { font-size: 13px; opacity: .85; margin-top: 4px; font-weight: 500; position: relative; z-index: 1; }
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
    .ac-head i { color: var(--purple); }
    .ac-title  { font-size: 14px; font-weight: 700; color: var(--text); }

    .act-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 22px;
        border-bottom: 1px solid #f5f6fa;
    }
    .act-item:last-child { border-bottom: none; }

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
    .act-dot.green  { background: #dcfce7; color: var(--green); }
    .act-dot.yellow { background: #fef9c3; color: #d97706; }
    .act-dot.blue   { background: #dbeafe; color: var(--blue); }
    .act-dot.red    { background: #fee2e2; color: var(--octane-c); }
    .act-dot.orange { background: #ffedd5; color: var(--orange); }

    .ai-title { font-size: 13px; font-weight: 700; color: var(--text); }
    .ai-sub   { font-size: 12px; color: var(--muted); margin-top: 1px; }
    .ai-time  { font-size: 11px; color: var(--muted); margin-top: 3px; }

    .no-data {
        text-align: center;
        padding: 30px;
        color: var(--muted);
        font-size: 13px;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
        .fuel-cards { grid-template-columns: repeat(2, 1fr); }
        .sum-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .dash-wrap { padding: 14px; }
        .data-row { grid-template-columns: 1fr; gap: 8px; }
        .row-label { font-size: 13px; }
        .sum-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) {
        .fuel-cards { grid-template-columns: 1fr 1fr; }
        .fuel-card { padding: 12px 10px; gap: 10px; }
        .fuel-info .fv { font-size: 17px; }
    }

    /* difference report table */
    /* ─── Card ─── */
.today-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
    padding: 16px 18px;
}

/* ─── Header ─── */
.today-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}
.today-title {
    font-size: 16px;
    font-weight: 700;
    color: #dc2626;
}
.btn-export {
    background: #166534;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
}
.btn-export:hover {
    background: #14532d;
}

/* ─── Table ─── */
.today-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.today-table thead {
    background: #f3f4f6;
}
.today-table th {
    padding: 10px 6px;
    text-align: center;
    font-size: 11px;
    color: #6b7280;
    text-transform: uppercase;
}
.today-table td {
    padding: 10px 6px;
    text-align: center;
    border-top: 1px solid #e5e7eb;
}

/* ─── Fuel rows ─── */
.fuel-box {
    display: flex;
    flex-direction: column;
}
.fuel-row {
    padding: 6px 0;
    border-bottom: 1px dashed #e5e7eb;
}
.fuel-row:last-child {
    border-bottom: none;
}

.red {
    color: #dc2626;
    font-weight: 700;
}

/* ─── Actions ─── */
.action-btn {
    display: block;
    width: 70px;
    margin: 4px auto;
    padding: 5px;
    border-radius: 4px;
    font-size: 12px;
    border: none;
    cursor: pointer;
    color: #fff;
}
.btn-view { background: #16a34a; }
.btn-msg { background: #0284c7; }
.btn-del { background: #ef4444; }

/* ─── Footer ─── */
.today-footer {
    text-align: center;
    margin-top: 12px;
}
.btn-see-all {
    padding: 8px 18px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}
.btn-see-all:hover {
    background: #e2e8f0;
}

/* ─── Wrapper for Scroll ─── */
.table-responsive {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}

/* Smooth scrollbar (optional modern UI) */
.table-responsive::-webkit-scrollbar {
    height: 6px;
}
.table-responsive::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

/* Prevent table break */
.today-table {
    min-width: 1100px; /* KEY: forces scroll on small devices */
}

/* Optional: Sticky header */
.today-table thead th {
    position: sticky;
    top: 0;
    background: #f3f4f6;
    z-index: 2;
}

/* Better mobile text */
@media (max-width: 768px) {
    .today-title {
        font-size: 14px;
    }
    .btn-export {
        padding: 6px 10px;
        font-size: 12px;
    }
}

/* Stack header nicely on mobile */
@media (max-width: 500px) {
    .today-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .btn-export {
        width: 100%;
        text-align: center;
    }
}

/* Fix Actions column */
/* ─── REMOVE Sticky Behavior from Actions Column ─── */
.today-table td:last-child,
.today-table th:last-child {
    position: static;   /* ❌ remove sticky */
    right: auto;
    z-index: auto;
    background: transparent;
    border-left: none;
}

/* ─── Actions Button Layout (Clean & Centered) ─── */
.action-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

/* Buttons full width inside column */
.action-btn {
    width: 100%;
    max-width: 80px;
    padding: 6px;
    border-radius: 4px;
    font-size: 12px;
    border: none;
    cursor: pointer;
    color: #fff;
    text-align: center;
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
                    ['theme' => 'octane', 'icon' => 'fa-droplet',  'val' => number_format($todayOctaneStock) . ' L',  'name' => 'Octane'],
                    ['theme' => 'petrol', 'icon' => 'fa-gas-pump', 'val' => number_format($todayPetrolStock) . ' L',  'name' => 'Petrol'],
                    ['theme' => 'diesel', 'icon' => 'fa-cube',     'val' => number_format($todayDieselStock) . ' L',  'name' => 'Diesel'],
                    ['theme' => 'others', 'icon' => 'fa-industry',  'val' => '0 L',                                    'name' => 'Others'],
                ],
            ],
            [
                'label' => "Today's Received",
                'fuels' => [
                    ['theme' => 'octane', 'icon' => 'fa-droplet',  'val' => number_format($todayOctaneReceived) . ' L',  'name' => 'Octane'],
                    ['theme' => 'petrol', 'icon' => 'fa-gas-pump', 'val' => number_format($todayPetrolReceived) . ' L',  'name' => 'Petrol'],
                    ['theme' => 'diesel', 'icon' => 'fa-cube',     'val' => number_format($todayDieselReceived) . ' L',  'name' => 'Diesel'],
                    ['theme' => 'others', 'icon' => 'fa-industry',  'val' => '0 L',                                       'name' => 'Others'],
                ],
            ],
            [
                'label' => "Today's Sales",
                'fuels' => [
                    ['theme' => 'octane', 'icon' => 'fa-droplet',  'val' => number_format($todayOctaneSold) . ' L',  'name' => 'Octane'],
                    ['theme' => 'petrol', 'icon' => 'fa-gas-pump', 'val' => number_format($todayPetrolSold) . ' L',  'name' => 'Petrol'],
                    ['theme' => 'diesel', 'icon' => 'fa-cube',     'val' => number_format($todayDieselSold) . ' L',  'name' => 'Diesel'],
                    ['theme' => 'others', 'icon' => 'fa-industry',  'val' => '0 L',                                   'name' => 'Others'],
                ],
            ],
            [
                'label' => "Today's Difference (L)",
                'fuels' => [
                    ['theme' => 'octane', 'icon' => 'fa-droplet',  'val' => number_format(abs($todayOctaneDiff)) . ' L', 'name' => 'Octane'],
                    ['theme' => 'petrol', 'icon' => 'fa-gas-pump', 'val' => number_format(abs($todayPetrolDiff)) . ' L', 'name' => 'Petrol'],
                    ['theme' => 'diesel', 'icon' => 'fa-cube',     'val' => number_format(abs($todayDieselDiff)) . ' L', 'name' => 'Diesel'],
                    ['theme' => 'others', 'icon' => 'fa-industry',  'val' => '0 L',                                        'name' => 'Others'],
                ],
            ],
            [
                'label' => "Today's Percentage (%)",
                'fuels' => [
                    ['theme' => 'octane', 'icon' => 'fa-droplet',  'val' => $todayOctaneDiffPct . '%', 'name' => 'Octane'],
                    ['theme' => 'petrol', 'icon' => 'fa-gas-pump', 'val' => $todayPetrolDiffPct . '%', 'name' => 'Petrol'],
                    ['theme' => 'diesel', 'icon' => 'fa-cube',     'val' => $todayDieselDiffPct . '%', 'name' => 'Diesel'],
                    ['theme' => 'others', 'icon' => 'fa-industry',  'val' => '0%',                      'name' => 'Others'],
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
    <div class="today-card">

    <!-- Header -->
    <div class="today-header">
        <div class="today-title">Today's Difference Report (20)</div>
        <button class="btn-export">Export to PDF</button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="today-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Station</th>
                    <th>Tag Officer</th>
                    <th>Designation</th>
                    <th>Phone</th>
                    <th>District</th>
                    <th>Upazila</th>
                    <th>Fuel</th>
                    <th>Difference(L)</th>
                    <th>Difference(%)</th>
                    <th>Alert</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

            <!-- Row 1 -->
            <tr>
                <td>1</td>
                <td>
                    08 Jun 2026<br>
                    <small>Friday</small>
                </td>
                <td>Uttara Filling Station</td>
                <td>Manik Mia</td>
                <td>Live Stock Officer</td>
                <td>01628312158</td>
                <td>Rangpur</td>
                <td>Shatkania</td>

                <!-- Fuel -->
                <td>
                    <div class="fuel-box">
                        <div class="fuel-row">Octane</div>
                        <div class="fuel-row">Petrol</div>
                        <div class="fuel-row">Diesel</div>
                        <div class="fuel-row">Other</div>
                    </div>
                </td>

                <!-- Difference L -->
                <td>
                    <div class="fuel-box red">
                        <div class="fuel-row">200</div>
                        <div class="fuel-row">800</div>
                        <div class="fuel-row">00</div>
                        <div class="fuel-row">180</div>
                    </div>
                </td>

                <!-- Difference % -->
                <td>
                    <div class="fuel-box red">
                        <div class="fuel-row">2%</div>
                        <div class="fuel-row">8%</div>
                        <div class="fuel-row">00</div>
                        <div class="fuel-row">1.8%</div>
                    </div>
                </td>

                <!-- Alert -->
                <td>
                    <div class="fuel-box">
                        <div class="fuel-row">Stock Zero</div>
                        <div class="fuel-row">Low stock</div>
                        <div class="fuel-row">High Diff</div>
                        <div class="fuel-row">-</div>
                    </div>
                </td>

                <!-- Actions -->
               <td>
                    <div class="action-wrap">
                        <button class="action-btn btn-view">View</button>
                        <button class="action-btn btn-msg">Message</button>
                        <button class="action-btn btn-del">Delete</button>
                    </div>
                </td>
            </tr>

            <!-- Row 2 -->
            <tr>
                <td>2</td>
                <td>
                    08 Jun 2026<br>
                    <small>Friday</small>
                </td>
                <td>Uttara Filling Station</td>
                <td>Manik Mia</td>
                <td>Live Stock Officer</td>
                <td>01628312158</td>
                <td>Rangpur</td>
                <td>Shatkania</td>

                <td>
                    <div class="fuel-box">
                        <div class="fuel-row">Octane</div>
                        <div class="fuel-row">Petrol</div>
                        <div class="fuel-row">Diesel</div>
                        <div class="fuel-row">Other</div>
                    </div>
                </td>

                <td>
                    <div class="fuel-box red">
                        <div class="fuel-row">200</div>
                        <div class="fuel-row">800</div>
                        <div class="fuel-row">00</div>
                        <div class="fuel-row">180</div>
                    </div>
                </td>

                <td>
                    <div class="fuel-box red">
                        <div class="fuel-row">2%</div>
                        <div class="fuel-row">8%</div>
                        <div class="fuel-row">00</div>
                        <div class="fuel-row">1.8%</div>
                    </div>
                </td>

                <td>
                    <div class="fuel-box">
                        <div class="fuel-row">Stock Zero</div>
                        <div class="fuel-row">Low stock</div>
                        <div class="fuel-row">High Diff</div>
                        <div class="fuel-row">-</div>
                    </div>
                </td>

                <td>
                    <button class="action-btn btn-view">View</button>
                    <button class="action-btn btn-msg">Message</button>
                    <button class="action-btn btn-del">Delete</button>
                </td>
            </tr>

            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="today-footer">
        <button class="btn-see-all">See All</button>
    </div>

</div>

</div>
@endsection