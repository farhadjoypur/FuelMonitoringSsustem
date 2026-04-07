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
    <div class="card" style="padding:0">
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
    </div>

</div>
@endsection