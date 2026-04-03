@extends('backend.tag-officer.layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ================================================
                   ROOT VARIABLES
                   ================================================ */
        :root {
            --primary: #0e6b8e;
            --white: #ffffff;
            --bg: #f4f6f9;
            --border: #e4e8f0;
            --text: #1a2332;
            --muted: #7a8899;
            --green: #16a34a;
            --red: #ef4444;
            --radius: 12px;
            --shadow: 0 1px 3px rgba(0, 0, 0, .06), 0 4px 14px rgba(0, 0, 0, .04);
        }

        /* ================================================
                   CONTENT WRAPPER
                   ================================================ */
        .dash-wrap {
            background: var(--bg);
            padding: 22px 24px;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        /* ================================================
                   PAGE TITLE  — bold "Dashboard" top-left
                   ================================================ */
        .dash-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -.4px;
            margin: 0 0 20px;
        }

        /* ================================================
                   NO-STATION WARNING
                   ================================================ */
        .no-station {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: var(--radius);
            padding: 14px 18px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #92400e;
            font-weight: 600;
        }

        /* ================================================
                   TOP 5 STAT CARDS ROW
                   ================================================ */
        .stat-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            padding: 18px 16px 14px;
            position: relative;
            min-width: 0;
        }

        .sc-label {
            font-size: 10.5px;
            font-weight: 600;
            color: var(--muted);
            line-height: 1.35;
            padding-right: 46px;
        }

        .sc-value {
            font-size: 24px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -.6px;
            line-height: 1.1;
            margin: 8px 0 5px;
        }

        .sc-sub {
            font-size: 11px;
            color: var(--muted);
        }

        .sc-up {
            font-size: 11px;
            font-weight: 700;
            color: var(--green);
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .sc-up i {
            font-size: 10px;
        }

        .sc-alert {
            font-size: 11px;
            font-weight: 700;
            color: var(--red);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 3px;
        }

        .sc-pct-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--green);
            display: flex;
            align-items: center;
            gap: 3px;
            margin-bottom: 3px;
        }

        /* Icon badge — top-right of stat card */
        .sc-icon {
            position: absolute;
            top: 16px;
            right: 14px;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #fff;
        }

        .ic-blue {
            background: #2563eb;
        }

        .ic-green {
            background: #16a34a;
        }

        .ic-purple {
            background: #7c3aed;
        }

        .ic-orange {
            background: #ea580c;
        }

        .ic-teal {
            background: #0e6b8e;
        }

        /* ================================================
                   SECTION TITLE  "Today's Stock" / "Today's Sold"
                   ================================================ */
        .section-label {
            text-align: center;
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text);
            margin: 4px 0 13px;
        }

        /* ================================================
                   FUEL CARDS ROW
                   ================================================ */
        .fuel-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 18px;
        }

        .fuel-card {
            border-radius: var(--radius);
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid transparent;
        }

        .fc-petrol {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .fc-diesel {
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .fc-octane {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .fuel-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
            color: #fff;
            flex-shrink: 0;
        }

        .fi-p {
            background: #22c55e;
        }

        .fi-d {
            background: #f97316;
        }

        .fi-o {
            background: #ef4444;
        }

        .fuel-val {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -.6px;
            line-height: 1;
        }

        .fuel-name {
            font-size: 12px;
            font-weight: 500;
            margin-top: 4px;
        }

        .fc-petrol .fuel-val,
        .fc-petrol .fuel-name {
            color: #16a34a;
        }

        .fc-diesel .fuel-val,
        .fc-diesel .fuel-name {
            color: #ea580c;
        }

        .fc-octane .fuel-val,
        .fc-octane .fuel-name {
            color: #ef4444;
        }

        /* ================================================
                   CHART CARD
                   ================================================ */
        .chart-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            padding: 22px 22px 16px;
            margin-bottom: 16px;
        }

        .chart-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
        }

        .chart-legend {
            display: flex;
            justify-content: center;
            gap: 26px;
            margin-top: 14px;
        }

        .cl-item {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
        }

        .cl-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        /* ================================================
                   BOTTOM 2-COL
                   ================================================ */
        .bottom-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .bottom-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            padding: 20px 22px;
        }

        .bc-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 14px;
        }

        /* weekly rows */
        .ws-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 0;
            border-bottom: 1px solid #f1f4f8;
            font-size: 13px;
        }

        .ws-row:last-child {
            border-bottom: none;
        }

        .ws-lbl {
            color: var(--muted);
        }

        .ws-val {
            font-weight: 700;
            color: var(--text);
        }

        .ws-val.red {
            color: #ef4444;
        }

        /* alert rows */
        .al-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 0;
            border-bottom: 1px solid #f1f4f8;
            font-size: 13px;
            color: var(--text);
        }

        .al-row:last-child {
            border-bottom: none;
        }

        .al-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .al-red {
            background: #ef4444;
        }

        .al-yellow {
            background: #f59e0b;
        }

        .al-blue {
            background: #2563eb;
        }

        /* ================================================
               MOBILE RESPONSIVENESS
               ================================================ */

        /* ১০২৪ পিক্সেলের নিচে (ট্যাবলেটের জন্য) */
        @media (max-width: 1024px) {
            .stat-row {
                grid-template-columns: repeat(3, 1fr);
            }

            .fuel-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dash-wrap {
                padding: 15px 15px;
            }

            .stat-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .bottom-row {
                grid-template-columns: 1fr;

            }
        }

        @media (max-width: 480px) {
            .stat-row {
                grid-template-columns: 1fr;
            }

            .fuel-row {
                grid-template-columns: 1fr;
            }

            .sc-value {
                font-size: 20px;
            }

            .fuel-val {
                font-size: 22px;
            }

            .dash-title {
                font-size: 18px;
                margin-bottom: 15px;
            }

            .chart-card {
                padding: 15px 10px;
            }

            .chart-legend {
                flex-wrap: wrap;
                gap: 15px;
            }
        }

        .stat-card,
        .fuel-card {
            min-width: 0;
            word-wrap: break-word;
        }
    </style>
@endpush

@section('content')
    <div class="dash-wrap">

        {{-- ── Page Title ── --}}
        <h1 class="dash-title">Dashboard</h1>

        {{-- ── No station warning ── --}}
        @if (!$stationName)
            <div class="no-station">
                <i class="fa-solid fa-triangle-exclamation"></i>
                আপনার কোনো Active Station Assignment নেই। Admin এর সাথে যোগাযোগ করুন।
            </div>
        @endif

        {{-- ══════════════════════════════════════════
         TOP 5 STAT CARDS
         ══════════════════════════════════════════ --}}
        <div class="stat-row">

            {{-- 1. Today's Total Stock --}}
            <div class="stat-card">
                <div class="sc-icon ic-blue"><i class="fa-solid fa-cube"></i></div>
                <div class="sc-label">Today's Total Stock</div>
                <div class="sc-value">{{ number_format($totalStockToday) }} L</div>
                <div class="sc-sub">Todays</div>
            </div>

            {{-- 2. Today's Total Received --}}
            <div class="stat-card">
                <div class="sc-icon ic-green"><i class="fa-solid fa-arrow-trend-up"></i></div>
                <div class="sc-label">Today's Total Received</div>
                <div class="sc-value">{{ number_format($totalReceivedToday) }} L</div>
                <div class="sc-up">
                    @if ($receivedChangePct >= 0)
                        <i class="fa-solid fa-arrow-trend-up"></i> +{{ $receivedChangePct }}%
                    @else
                        <i class="fa-solid fa-arrow-trend-down" style="color:#ef4444"></i>
                        <span style="color:#ef4444">{{ $receivedChangePct }}%</span>
                    @endif
                </div>
            </div>

            {{-- 3. Today's Total Sold --}}
            <div class="stat-card">
                <div class="sc-icon ic-purple"><i class="fa-solid fa-arrow-trend-down"></i></div>
                <div class="sc-label">Today's Total Sold</div>
                <div class="sc-value">{{ number_format($totalSoldToday) }} L</div>
                <div class="sc-sub">Todays</div>
            </div>

            {{-- 4. Today's Difference (L) --}}
            <div class="stat-card">
                <div class="sc-icon ic-orange"><i class="fa-solid fa-droplet"></i></div>
                <div class="sc-label">Today's Difference</div>
                <div class="sc-value">{{ number_format(abs($totalDiffToday)) }} L</div>
                @if (abs($totalDiffToday) > 0)
                    <div class="sc-alert">
                        <i class="fa-solid fa-arrow-trend-down fa-xs"></i>
                        <i class="fa-solid fa-triangle-exclamation fa-xs"></i> Alert
                    </div>
                @endif
                <div class="sc-sub">Todays</div>
            </div>

            {{-- 5. Today's Difference (%) --}}
            <div class="stat-card">
                <div class="sc-icon ic-teal"><i class="fa-solid fa-percent"></i></div>
                <div class="sc-label">Today's Difference</div>
                <div class="sc-value">{{ $totalDiffPct }}%</div>
                @if ($totalDiffPct > 0)
                    <div class="sc-pct-label">
                        <i class="fa-solid fa-arrow-trend-up fa-xs"></i> Percentage
                    </div>
                @endif
                <div class="sc-sub">Todays</div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════
         TODAY'S STOCK
         ══════════════════════════════════════════ --}}
        <p class="section-label">Today's Stock</p>
        <div class="fuel-row">

            <div class="fuel-card fc-petrol">
                <div class="fuel-icon fi-p"><i class="fa-solid fa-gas-pump"></i></div>
                <div>
                    <div class="fuel-val">{{ number_format($todayPetrolStock) }} L</div>
                    <div class="fuel-name">Petrol</div>
                </div>
            </div>

            <div class="fuel-card fc-diesel">
                <div class="fuel-icon fi-d"><i class="fa-solid fa-cube"></i></div>
                <div>
                    <div class="fuel-val">{{ number_format($todayDieselStock) }} L</div>
                    <div class="fuel-name">Diesel</div>
                </div>
            </div>

            <div class="fuel-card fc-octane">
                <div class="fuel-icon fi-o"><i class="fa-solid fa-droplet"></i></div>
                <div>
                    <div class="fuel-val">{{ number_format($todayOctaneStock) }} L</div>
                    <div class="fuel-name">Octane</div>
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════
         TODAY'S SOLD
         ══════════════════════════════════════════ --}}
        <p class="section-label">Today's Sold</p>
        <div class="fuel-row">

            <div class="fuel-card fc-petrol">
                <div class="fuel-icon fi-p"><i class="fa-solid fa-gas-pump"></i></div>
                <div>
                    <div class="fuel-val">{{ number_format($todayPetrolSold) }} L</div>
                    <div class="fuel-name">Petrol</div>
                </div>
            </div>

            <div class="fuel-card fc-diesel">
                <div class="fuel-icon fi-d"><i class="fa-solid fa-cube"></i></div>
                <div>
                    <div class="fuel-val">{{ number_format($todayDieselSold) }} L</div>
                    <div class="fuel-name">Diesel</div>
                </div>
            </div>

            <div class="fuel-card fc-octane">
                <div class="fuel-icon fi-o"><i class="fa-solid fa-droplet"></i></div>
                <div>
                    <div class="fuel-val">{{ number_format($todayOctaneSold) }} L</div>
                    <div class="fuel-name">Octane</div>
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════
         CHART — Received vs Sold (Last 7 Days)
         ══════════════════════════════════════════ --}}
        <div class="chart-card">
            <div class="chart-title">Received vs Sold (Last 7 Days)</div>
            <canvas id="rvsSoldChart" height="95"></canvas>
            <div class="chart-legend">
                <div class="cl-item">
                    <span class="cl-dot" style="background:#0e6b8e"></span>
                    Received (L)
                </div>
                <div class="cl-item">
                    <span class="cl-dot" style="background:#f97316"></span>
                    Sold (L)
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
         BOTTOM — Weekly Summary + Recent Alerts
         ══════════════════════════════════════════ --}}
        <div class="bottom-row">

            <div class="bottom-card">
                <div class="bc-title">Weekly Summary</div>
                <div class="ws-row">
                    <span class="ws-lbl">Total Received</span>
                    <span class="ws-val">{{ number_format($weeklyReceived) }} L</span>
                </div>
                <div class="ws-row">
                    <span class="ws-lbl">Total Sold</span>
                    <span class="ws-val">{{ number_format($weeklySold) }} L</span>
                </div>
                <div class="ws-row">
                    <span class="ws-lbl">Difference</span>
                    <span class="ws-val {{ $weeklyDifference != 0 ? 'red' : '' }}">
                        {{ number_format(abs($weeklyDifference)) }} L
                    </span>
                </div>
            </div>

            <div class="bottom-card">
                <div class="bc-title">Recent Alerts</div>
                @foreach ($recentAlerts as $alert)
                    <div class="al-row">
                        <span class="al-dot al-{{ $alert['color'] }}"></span>
                        {{ $alert['message'] }}
                    </div>
                @endforeach
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        @php
            $chartLabels = $last7Days->pluck('label')->toJson();
            $chartReceived = $last7Days->pluck('received')->toJson();
            $chartSold = $last7Days->pluck('sold')->toJson();
        @endphp

        new Chart(document.getElementById('rvsSoldChart'), {
            type: 'line',
            data: {
                labels: {!! $chartLabels !!},
                datasets: [{
                        label: 'Received (L)',
                        data: {!! $chartReceived !!},
                        borderColor: '#0e6b8e',
                        backgroundColor: 'rgba(14,107,142,.07)',
                        pointBackgroundColor: '#0e6b8e',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                    },
                    {
                        label: 'Sold (L)',
                        data: {!! $chartSold !!},
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249,115,22,.07)',
                        pointBackgroundColor: '#f97316',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString()} L`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,.04)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#7a8899'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0,0,0,.06)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#7a8899',
                            callback: v => v.toLocaleString()
                        },
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>
@endpush
