@extends('backend.admin.layouts.app')

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
            --petrol-color: #80AA57;
            --diesel-color: #D19529;
            --octane-color: #ED4033;
            --blue: #2563eb;
            --green: #16a34a;
            --purple: #7c3aed;
            --orange: #ea580c;
            --shadow: 0 1px 3px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.04);
            --radius: 14px;
        }

        .db-wrap { min-height: 100vh; padding: 28px; }

        .db-title {
            font-size: 22px; font-weight: 800; color: var(--text);
            margin: 0 0 3px; letter-spacing: -.3px;
        }
        .db-sub { font-size: 13px; color: var(--muted); margin-bottom: 26px; }

        .section-title {
            font-size: 15px; font-weight: 700; color: var(--text);
            margin: 0 0 14px; text-align: center;
        }

        /* ===== FUEL GRIDS (3 col) ===== */
        .fuel-grid,
        .fuel-diff-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 10px;
        }

        /* Main fuel card */
        .fuel-main-card {
            border-radius: var(--radius);
            border: 1px solid transparent;
            padding: 18px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .fuel-main-card.petrol { background: #E1EDDF; }
        .fuel-main-card.diesel { background: #EDEADA; }
        .fuel-main-card.octane { background: #F1DEDC; }

        .fuel-icon-circle {
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: #fff; flex-shrink: 0;
        }
        .fuel-icon-circle.p { background: var(--petrol-color); }
        .fuel-icon-circle.d { background: var(--diesel-color); }
        .fuel-icon-circle.o { background: var(--octane-color); }

        .fuel-main-info .fmi-val {
            font-size: 22px; font-weight: 800;
            letter-spacing: -.5px; line-height: 1;
        }
        .fuel-main-info .fmi-name { font-size: 12px; font-weight: 500; margin-top: 3px; }

        .fuel-main-card.petrol .fmi-val,
        .fuel-main-card.petrol .fmi-name { color: var(--petrol-color); }
        .fuel-main-card.diesel .fmi-val,
        .fuel-main-card.diesel .fmi-name { color: var(--diesel-color); }
        .fuel-main-card.octane .fmi-val,
        .fuel-main-card.octane .fmi-name { color: var(--octane-color); }

        /* Difference pill cards */
        .diff-pill {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px; font-weight: 600;
            text-align: center;
        }
        .diff-pill.petrol { background: #F0FDF4; color: var(--petrol-color); border: 1px solid #bbf7d0; }
        .diff-pill.diesel { background: #FFF7ED; color: var(--diesel-color); border: 1px solid #fde68a; }
        .diff-pill.octane { background: #FEF2F2; color: var(--octane-color); border: 1px solid #fecaca; }

        .section-gap { margin-bottom: 28px; }

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
            content: ''; position: absolute;
            top: -24px; right: -24px;
            width: 100px; height: 100px;
            border-radius: 50%; background: rgba(255,255,255,.12);
        }
        .sum-card::after {
            content: ''; position: absolute;
            bottom: -30px; left: -10px;
            width: 80px; height: 80px;
            border-radius: 50%; background: rgba(255,255,255,.07);
        }
        .sum-card.blue-c   { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
        .sum-card.green-c  { background: linear-gradient(135deg, #16a34a, #15803d); }
        .sum-card.purple-c { background: linear-gradient(135deg, #7c3aed, #6d28d9); }

        .sum-card .sm-icon { font-size: 26px; margin-bottom: 12px; opacity: .9; }
        .sum-card .sm-val  { font-size: 40px; font-weight: 800; letter-spacing: -1px; line-height: 1; }
        .sum-card .sm-name { font-size: 13px; opacity: .85; margin-top: 4px; font-weight: 500; }
        .sum-card .sm-trend {
            font-size: 12px; opacity: .8; margin-top: 12px;
            display: flex; align-items: center; gap: 4px;
        }

        /* ===== CHART / ACTIVITY CARD ===== */
        .card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .chart-card { padding: 22px; }

        .cc-head { display: flex; align-items: center; gap: 9px; margin-bottom: 20px; }
        .cc-head i { color: var(--orange); font-size: 15px; }
        .cc-title  { font-size: 14px; font-weight: 700; color: var(--text); }

        /* ===== ACTIVITIES ===== */
        .ac-head {
            padding: 18px 22px 14px;
            display: flex; align-items: center; gap: 9px;
            border-bottom: 1px solid var(--border);
            background: #fafbff;
        }
        .ac-head i { color: var(--purple); }
        .ac-title  { font-size: 14px; font-weight: 700; color: var(--text); }

        .act-item {
            display: flex; align-items: flex-start; gap: 14px;
            padding: 14px 22px;
            border-bottom: 1px solid #f5f6fa;
        }
        .act-item:last-child { border-bottom: none; }

        .act-dot {
            width: 34px; height: 34px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
        }
        .act-dot.green  { background: #dcfce7; color: var(--green); }
        .act-dot.yellow { background: #fef9c3; color: #d97706; }
        .act-dot.blue   { background: #dbeafe; color: var(--blue); }
        .act-dot.red    { background: #fee2e2; color: var(--octane-color); }

        .ai-title { font-size: 13px; font-weight: 700; color: var(--text); }
        .ai-sub   { font-size: 12px; color: var(--muted); margin-top: 1px; }
        .ai-time  { font-size: 11px; color: var(--muted); margin-top: 3px; }

        .no-data { text-align: center; padding: 30px; color: var(--muted); font-size: 13px; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) { .db-wrap { padding: 16px; } }
        @media (max-width: 768px) {
            .fuel-grid, .fuel-diff-grid, .sum-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 480px) {
            .fuel-grid, .fuel-diff-grid, .sum-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
<div class="db-wrap">

    <div class="db-title">Dashboard Overview</div>
    <p class="db-sub">Bangladesh Fuel Management System &mdash; Real-time Monitoring</p>

    {{-- ============================================================
         TODAY'S STOCK
    ============================================================ --}}
    <p class="section-title">Today's Stock</p>

    {{-- Row 1: Stock values --}}
    <div class="fuel-grid">
        <div class="fuel-main-card petrol">
            <div class="fuel-icon-circle p"><i class="fa-solid fa-gas-pump"></i></div>
            <div class="fuel-main-info">
                <div class="fmi-val">{{ number_format($todayPetrolStock) }} L</div>
                <div class="fmi-name">Petrol</div>
            </div>
        </div>
        <div class="fuel-main-card diesel">
            <div class="fuel-icon-circle d"><i class="fa-solid fa-cube"></i></div>
            <div class="fuel-main-info">
                <div class="fmi-val">{{ number_format($todayDieselStock) }} L</div>
                <div class="fmi-name">Diesel</div>
            </div>
        </div>
        <div class="fuel-main-card octane">
            <div class="fuel-icon-circle o"><i class="fa-solid fa-droplet"></i></div>
            <div class="fuel-main-info">
                <div class="fmi-val">{{ number_format($todayOctaneStock) }} L</div>
                <div class="fmi-name">Octane</div>
            </div>
        </div>
    </div>

    {{-- Row 2: Difference % --}}
    @php
        $petrolDiffPct = $todayPetrolReceived > 0
            ? round(($todayPetrolDiff / $todayPetrolReceived) * 100, 1) : 0;
        $dieselDiffPct = $todayDieselReceived > 0
            ? round(($todayDieselDiff / $todayDieselReceived) * 100, 1) : 0;
        $octaneDiffPct = $todayOctaneReceived > 0
            ? round(($todayOctaneDiff / $todayOctaneReceived) * 100, 1) : 0;
    @endphp
<div class="fuel-diff-grid">
    <div class="diff-pill petrol">
        Petrol Difference: <strong>{{ $petrolDiffPct }}%</strong>
    </div>
    <div class="diff-pill diesel">
        Diesel Difference: <strong>{{ $dieselDiffPct }}%</strong>
    </div>
    <div class="diff-pill octane">
        Octane Difference: <strong>{{ $octaneDiffPct }}%</strong>
    </div>
</div>

    {{-- Row 3: Difference in Litres --}}
    <div class="fuel-diff-grid section-gap">
        <div class="diff-pill petrol">
            Petrol Difference: <strong>{{ number_format(abs($todayPetrolDiff)) }}L</strong>
        </div>
        <div class="diff-pill diesel">
            Diesel Difference: <strong>{{ number_format(abs($todayDieselDiff)) }}L</strong>
        </div>
        <div class="diff-pill octane">
            Octane Difference: <strong>{{ number_format(abs($todayOctaneDiff)) }}L</strong>
        </div>
    </div>

    {{-- ============================================================
         TODAY'S SOLD
    ============================================================ --}}
    <p class="section-title">Today's Sold</p>
    <div class="fuel-grid section-gap">
        <div class="fuel-main-card petrol">
            <div class="fuel-icon-circle p"><i class="fa-solid fa-gas-pump"></i></div>
            <div class="fuel-main-info">
                <div class="fmi-val">{{ number_format($todayPetrolSold) }} L</div>
                <div class="fmi-name">Petrol</div>
            </div>
        </div>
        <div class="fuel-main-card diesel">
            <div class="fuel-icon-circle d"><i class="fa-solid fa-cube"></i></div>
            <div class="fuel-main-info">
                <div class="fmi-val">{{ number_format($todayDieselSold) }} L</div>
                <div class="fmi-name">Diesel</div>
            </div>
        </div>
        <div class="fuel-main-card octane">
            <div class="fuel-icon-circle o"><i class="fa-solid fa-droplet"></i></div>
            <div class="fuel-main-info">
                <div class="fmi-val">{{ number_format($todayOctaneSold) }} L</div>
                <div class="fmi-name">Octane</div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SUMMARY 3 CARDS
    ============================================================ --}}
    <div class="sum-grid">
        <div class="sum-card blue-c">
            <div class="sm-icon"><i class="fa-solid fa-building"></i></div>
            <div class="sm-val">{{ $totalDepots }}</div>
            <div class="sm-name">Total Depots</div>
            {{-- <div class="sm-trend">
                <i class="fa-solid fa-arrow-trend-{{ $newDepots >= 0 ? 'up' : 'down' }}"></i>
                {{ $newDepots >= 0 ? '+' : '' }}{{ $newDepots }} this month
            </div> --}}
        </div>
        <div class="sum-card green-c">
            <div class="sm-icon"><i class="fa-solid fa-gas-pump"></i></div>
            <div class="sm-val">{{ $totalStations }}</div>
            <div class="sm-name">Total Filling Stations</div>
            {{-- <div class="sm-trend">
                <i class="fa-solid fa-arrow-trend-{{ $newStations >= 0 ? 'up' : 'down' }}"></i>
                {{ $newStations >= 0 ? '+' : '' }}{{ $newStations }} this month
            </div> --}}
        </div>
        <div class="sum-card purple-c">
            <div class="sm-icon"><i class="fa-solid fa-user-group"></i></div>
            <div class="sm-val">{{ $totalOfficers }}</div>
            <div class="sm-name">Total Tag Officers</div>
            {{-- <div class="sm-trend">
                <i class="fa-solid fa-arrow-trend-{{ $newOfficers >= 0 ? 'up' : 'down' }}"></i>
                {{ $newOfficers >= 0 ? '+' : '' }}{{ $newOfficers }} this month
            </div> --}}
        </div>
    </div>

    {{-- ============================================================
         DIVISION-WISE FUEL SALES (Today's)
    ============================================================ --}}
    {{-- <div class="card chart-card">
        <div class="cc-head">
            <i class="fa-solid fa-building"></i>
            <span class="cc-title">Division-wise Fuel Sales (Today's)</span>
        </div>
        <canvas id="divisionSalesChart" height="120"></canvas>
    </div> --}}

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

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        Chart.defaults.font.family = "'Segoe UI', sans-serif";
        Chart.defaults.color = '#7c8db5';

        /* ========== Division-wise Fuel Sales (Today's) ========== */
        @php
            $divLabels   = $divisionSalesToday->pluck('division')->toJson();
            $divStations = $divisionSalesToday->pluck('total_stations')->toJson();
            $divFuel     = $divisionSalesToday->pluck('total_fuel_liters')->toJson();
        @endphp

        new Chart(document.getElementById('divisionSalesChart'), {
            type: 'bar',
            data: {
                labels: {!! $divLabels !!},
                datasets: [
                    {
                        label: 'Division',
                        data: {!! $divStations !!},
                        backgroundColor: '#1e6fa8',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                    {
                        label: 'Fuel',
                        data: {!! $divFuel !!},
                        backgroundColor: '#2d9748',
                        borderRadius: 5,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'bottom' }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        grid: { color: '#f0f2f5' },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    </script>
@endpush