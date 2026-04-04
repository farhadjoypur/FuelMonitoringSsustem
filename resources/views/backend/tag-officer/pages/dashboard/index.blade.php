@extends('backend.tag-officer.layouts.app')

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
            --radius: 12px;
            --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 14px rgba(0,0,0,.04);
        }

        body { background: var(--bg); }

        .dash-wrap {
            padding: 24px;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        /* ── NO STATION WARNING ── */
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

        /* ══════════════════════════════════════
           FILLING STATION INFO CARD (top)
        ══════════════════════════════════════ */
        .info-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            padding: 24px 28px 22px;
            margin-bottom: 24px;
        }

        .info-card-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .info-card-title .title-icon {
            width: 38px;
            height: 38px;
            background: #0e6b8e;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
        }

        .info-fields {
            display: grid;
            grid-template-columns: 1fr 1.4fr 1fr;
            gap: 20px;
        }

        .info-field-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 7px;
        }

        .info-field-label i { color: #0e6b8e; font-size: 11px; }

        .info-field-input {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text);
            background: #fafbfc;
            outline: none;
            appearance: none;
            cursor: pointer;
            transition: border-color .15s;
        }

        .info-field-input:focus { border-color: #0e6b8e; background: #fff; }

        .select-wrap { position: relative; }
        .select-wrap::after {
            content: '\f078';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: var(--muted);
            pointer-events: none;
        }

        .info-field-input.location-field {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #15803d;
            font-weight: 600;
            cursor: default;
        }

        .info-field-input.date-field {
            background: #fafbfc;
            color: var(--text);
            font-weight: 600;
            cursor: default;
        }

        /* ══════════════════════════════════════
           DATA ROWS (Stock / Received / Sales…)
        ══════════════════════════════════════ */
        .data-section { margin-bottom: 10px; }

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

        /* ── RESPONSIVE ── */
        @media (max-width: 1100px) {
            .fuel-cards { grid-template-columns: repeat(2, 1fr); }
            .info-fields { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .dash-wrap { padding: 14px; }
            .data-row { grid-template-columns: 1fr; gap: 8px; }
            .row-label { font-size: 13px; }
            .info-fields { grid-template-columns: 1fr; }
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

    {{-- No assignment warning --}}
    @if (!$stationName)
        <div class="no-station">
            <i class="fa-solid fa-triangle-exclamation"></i>
            You do not have any active station assignment. Please contact the admin.
        </div>
    @endif

    {{-- ══════════════════════════════════════
         FILLING STATION INFO CARD
    ══════════════════════════════════════ --}}
    <div class="info-card">
        <div class="info-card-title">
            <div class="title-icon"><i class="fa-solid fa-building-columns"></i></div>
            Filling Station Information
        </div>

        <form method="GET" action="{{ route('tag-officer.dashboard.index') }}" id="stationForm">
            <div class="info-fields">

                {{-- Filling Station dropdown --}}
                <div>
                    <div class="info-field-label">
                        <i class="fa-solid fa-building-columns"></i> Filling Station
                    </div>
                    <div class="select-wrap">
                        <select name="station_id"
                                class="info-field-input"
                                onchange="document.getElementById('stationForm').submit()">
                            @foreach ($assignedStations as $station)
                                <option value="{{ $station->id }}"
                                    {{ $selectedStationId == $station->id ? 'selected' : '' }}>
                                    {{ $station->station_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Location (auto-filled, readonly) --}}
                <div>
                    <div class="info-field-label">
                        <i class="fa-solid fa-location-dot"></i> Location
                    </div>
                    <input type="text"
                           class="info-field-input location-field"
                           value="{{ $location ?: '—' }}"
                           readonly>
                </div>

                {{-- Today's Date (readonly) --}}
                <div>
                    <div class="info-field-label">
                        <i class="fa-regular fa-calendar"></i> Today's Date
                    </div>
                    <input type="text"
                           class="info-field-input date-field"
                           value="{{ $today->format('d F Y') }}"
                           readonly>
                </div>

            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════
         DATA ROWS
    ══════════════════════════════════════ --}}
    @php
        $rows = [
            [
                'label' => "Today's Stock",
                'fuels' => [
                    ['theme' => 'octane', 'icon' => 'fa-droplet',  'val' => number_format($todayOctaneStock),  'name' => 'Octane'],
                    ['theme' => 'petrol', 'icon' => 'fa-gas-pump', 'val' => number_format($todayPetrolStock),  'name' => 'Petrol'],
                    ['theme' => 'diesel', 'icon' => 'fa-cube',     'val' => number_format($todayDieselStock),  'name' => 'Diesel'],
                    ['theme' => 'others', 'icon' => 'fa-droplet',  'val' => '0',                               'name' => 'Others'],
                ],
            ],
            [
                'label' => "Today's Received",
                'fuels' => [
                    ['theme' => 'octane', 'icon' => 'fa-droplet',  'val' => number_format($todayOctaneReceived),  'name' => 'Octane'],
                    ['theme' => 'petrol', 'icon' => 'fa-gas-pump', 'val' => number_format($todayPetrolReceived),  'name' => 'Petrol'],
                    ['theme' => 'diesel', 'icon' => 'fa-cube',     'val' => number_format($todayDieselReceived),  'name' => 'Diesel'],
                    ['theme' => 'others', 'icon' => 'fa-droplet',  'val' => '0',                                  'name' => 'Others'],
                ],
            ],
            [
                'label' => "Today's Sales",
                'fuels' => [
                    ['theme' => 'octane', 'icon' => 'fa-droplet',  'val' => number_format($todayOctaneSold),  'name' => 'Octane'],
                    ['theme' => 'petrol', 'icon' => 'fa-gas-pump', 'val' => number_format($todayPetrolSold),  'name' => 'Petrol'],
                    ['theme' => 'diesel', 'icon' => 'fa-cube',     'val' => number_format($todayDieselSold),  'name' => 'Diesel'],
                    ['theme' => 'others', 'icon' => 'fa-droplet',  'val' => '0',                              'name' => 'Others'],
                ],
            ],
            [
                'label' => "Today's Difference(L)",
                'fuels' => [
                    ['theme' => 'octane', 'icon' => 'fa-droplet',  'val' => number_format(abs($todayOctaneDiff)) . ' L', 'name' => 'Octane'],
                    ['theme' => 'petrol', 'icon' => 'fa-gas-pump', 'val' => number_format(abs($todayPetrolDiff)) . ' L', 'name' => 'Petrol'],
                    ['theme' => 'diesel', 'icon' => 'fa-cube',     'val' => number_format(abs($todayDieselDiff)) . ' L', 'name' => 'Diesel'],
                    ['theme' => 'others', 'icon' => 'fa-droplet',  'val' => '0 L',                                        'name' => 'Others'],
                ],
            ],
            [
                'label' => "Today's Percentage(%)",
                'fuels' => [
                    ['theme' => 'octane', 'icon' => 'fa-droplet',  'val' => $todayOctaneDiffPct . '%', 'name' => 'Octane'],
                    ['theme' => 'petrol', 'icon' => 'fa-gas-pump', 'val' => $todayPetrolDiffPct . '%', 'name' => 'Petrol'],
                    ['theme' => 'diesel', 'icon' => 'fa-cube',     'val' => $todayDieselDiffPct . '%', 'name' => 'Diesel'],
                    ['theme' => 'others', 'icon' => 'fa-droplet',  'val' => '0%',                      'name' => 'Others'],
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

    <div class="data-section">
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

</div>
@endsection