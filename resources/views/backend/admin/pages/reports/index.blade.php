@extends('backend.admin.layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #f0f2f7;
            --surface: #fff;
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
            --shadow: 0 1px 3px rgba(0, 0, 0, .08), 0 4px 16px rgba(0, 0, 0, .06);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
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

        /* ── TABS ── */
        .tabs-bar {
            display: flex;
            gap: 2px;
            border-bottom: 1px solid var(--border);
            padding: 0 20px;
            overflow-x: auto;
        }

        .tab-btn {
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

        .tab-btn:hover {
            color: var(--primary);
        }

        .tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 600;
        }

        /* ── FILTER ── */
        .filter-section {
            padding: 18px 22px 22px;
        }

        .filter-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: .88rem;
            color: var(--primary);
            margin-bottom: 14px;
        }

        .filter-header i {
            color: var(--blue);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px 16px;
        }

        .filter-group label {
            display: block;
            font-size: .72rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 9px 11px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: .875rem;
            color: var(--text);
            background: #fafbfc;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            appearance: none;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
            background: #fff;
        }

        select:disabled {
            opacity: .45;
            cursor: not-allowed;
            background: #f1f5f9;
        }

        /* Station Combobox */
        .station-combobox {
            position: relative;
        }

        .station-combobox input {
            padding-right: 32px;
        }

        .station-combobox .clear-btn {
            position: absolute;
            right: 9px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--muted);
            font-size: .8rem;
            padding: 2px;
            line-height: 1;
        }

        .station-combobox .clear-btn:hover {
            color: var(--red);
        }

        .station-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            z-index: 999;
            background: #fff;
            border: 1.5px solid var(--blue);
            border-radius: 9px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
            max-height: 220px;
            overflow-y: auto;
        }

        .station-dropdown-item {
            padding: 9px 13px;
            font-size: .84rem;
            cursor: pointer;
            transition: background .1s;
            border-bottom: 1px solid #f1f5f9;
        }

        .station-dropdown-item:last-child {
            border-bottom: none;
        }

        .station-dropdown-item:hover {
            background: #eff6ff;
            color: var(--primary);
        }

        .station-dropdown-item.selected {
            background: #eff6ff;
            color: var(--primary);
            font-weight: 600;
        }

        .station-dropdown-empty {
            padding: 12px 13px;
            font-size: .82rem;
            color: var(--muted);
            text-align: center;
        }

        /* Filter buttons */
        .filter-btn-row {
            grid-column: 3/5;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 10px;
        }

        .btn-apply {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: .9rem;
            cursor: pointer;
            transition: all .2s;
            box-shadow: 0 4px 12px rgba(37, 99, 235, .25);
        }

        .btn-apply:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, .35);
        }

        .btn-reset {
            background: #f1f5f9;
            color: #475569;
            border: 1.5px solid #cbd5e1;
            padding: 10px 22px;
            border-radius: 10px;
            font-weight: 600;
            font-size: .9rem;
            cursor: pointer;
            transition: all .2s;
        }

        .btn-reset:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        /* ── STAT CARDS ── */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 14px;
            padding: 0 22px 22px;
        }

        .stat-card {
            border-radius: 12px;
            padding: 16px 18px;
            position: relative;
            overflow: hidden;
            color: #fff;
            min-height: 96px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, .18);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #22c55e, #15803d);
        }

        .stat-card.yellow {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        .stat-card.purple {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #f97316, #c2410c);
        }

        .stat-card.teal {
            background: linear-gradient(135deg, #14b8a6, #0f766e);
        }

        .stat-card.red {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            right: -18px;
            bottom: -18px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }

        .stat-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .stat-card-icon {
            width: 34px;
            height: 34px;
            background: rgba(255, 255, 255, .2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
        }

        .stat-card-badge {
            font-size: .7rem;
            background: rgba(255, 255, 255, .15);
            padding: 3px 7px;
            border-radius: 20px;
        }

        .stat-card-label {
            font-size: .72rem;
            font-weight: 500;
            opacity: .88;
            margin-top: 8px;
        }

        .stat-card-value {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -.5px;
            margin-top: 2px;
        }

        /* ── TABLE ── */
        .table-section {
            padding: 0 22px 22px;
        }

        .table-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .table-title {
            font-family: 'Syne', sans-serif;
            font-size: .95rem;
            font-weight: 700;
            color: var(--text);
        }

        .table-meta {
            font-size: .75rem;
            color: var(--muted);
        }

        .table-wrap {
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .82rem;
        }

        thead tr {
            background: #f7f9fc;
        }

        thead th {
            padding: 10px 13px;
            text-align: left;
            font-size: .68rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background .12s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f8faff;
        }

        tbody td {
            padding: 9px 13px;
            color: var(--text);
            vertical-align: middle;
        }

        .date-cell {
            font-size: .78rem;
            color: var(--muted);
            white-space: nowrap;
            font-weight: 500;
        }

        .company-cell {
            font-weight: 600;
            font-size: .80rem;
        }

        .station-cell {
            font-weight: 500;
            font-size: .80rem;
        }

        .received-val {
            color: #16a34a;
            font-weight: 600;
        }

        .sold-val {
            color: #ef4444;
            font-weight: 600;
        }

        .current-val {
            font-weight: 700;
        }

        .status-available {
            color: #16a34a;
            font-weight: 600;
            font-size: .79rem;
        }

        .status-low {
            color: #b45309;
            font-weight: 600;
            font-size: .79rem;
        }

        .status-zero {
            color: #b91c1c;
            font-weight: 600;
            font-size: .79rem;
        }

        .diff-positive {
            color: #ef4444;
            font-weight: 600;
        }

        .diff-negative {
            color: #22c55e;
            font-weight: 600;
        }

        .diff-zero {
            color: var(--muted);
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .70rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-active {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #b91c1c;
        }

        .summary-row td {
            background: #f7f9fc;
            font-weight: 700;
            font-size: .80rem;
            border-top: 2px solid var(--border);
        }

        .empty-state {
            text-align: center;
            padding: 52px 20px;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 2rem;
            opacity: .2;
            display: block;
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: .88rem;
            font-weight: 500;
        }

        .empty-state small {
            font-size: .76rem;
            margin-top: 4px;
            display: block;
        }

        /* ── EXPORT ── */
        .export-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 22px 18px;
            border-top: 1px solid var(--border);
        }

        .btn-export {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            border: 1.5px solid;
            transition: all .15s;
        }

        .btn-pdf {
            border-color: #e2e8f0;
            color: #475569;
            background: #fff;
        }

        .btn-pdf:hover {
            background: #f8f9fa;
        }

        .btn-excel {
            border-color: #16a34a;
            color: #fff;
            background: #16a34a;
        }

        .btn-excel:hover {
            background: #15803d;
        }

        /* Tab panels */
        .tab-content>div {
            display: none;
        }

        .tab-content>div.active {
            display: block;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @media(max-width:1100px) {
            .stat-cards {
                grid-template-columns: repeat(3, 1fr)
            }
        }

        @media(max-width:900px) {
            .filter-grid {
                grid-template-columns: repeat(2, 1fr)
            }

            .filter-btn-row {
                grid-column: 1/3;
                justify-content: flex-start
            }

            .stat-cards {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media(max-width:600px) {
            .stat-cards {
                grid-template-columns: repeat(1, 1fr)
            }

            .filter-grid {
                grid-template-columns: 1fr
            }

            .filter-btn-row {
                grid-column: 1/2
            }
        }
    </style>
@endpush

@section('content')
    {{-- ══ Pass PHP data to Alpine.js (NO fn() inside @json) ══ --}}
    <script>
        window.__REPORTS__ = {
            stock: @json($stockReports),
            officer: @json($officerReports),
            stations: @json($stationsList),
            companies: @json($companiesList),
            depots: @json($depotsList),
            locations: @json($locations['divisions'] ?? []),
        };
    </script>

    <h1 class="page-title">
        <i class="fa-solid fa-chart-mixed" style="font-size:1.1rem; margin-right:8px; opacity:.7;"></i>
        Reports &amp; Analytics
    </h1>

    <div class="card" x-data="reportsApp()" x-init="init()">

        {{-- ── TABS ── --}}
        <div class="tabs-bar">
            <button class="tab-btn" :class="{ active: tab === 'stock' }" @click="tab='stock'">
                <i class="fa-solid fa-box-archive" style="margin-right:5px;font-size:.76rem;"></i>Stock Report
            </button>
            <button class="tab-btn" :class="{ active: tab === 'sales' }" @click="tab='sales'">
                <i class="fa-solid fa-chart-line" style="margin-right:5px;font-size:.76rem;"></i>Sales Report
            </button>
            <button class="tab-btn" :class="{ active: tab === 'officer' }" @click="tab='officer'">
                <i class="fa-solid fa-user-tie" style="margin-right:5px;font-size:.76rem;"></i>Tag Officer Report
            </button>
            <button class="tab-btn" :class="{ active: tab === 'diff' }" @click="tab='diff'">
                <i class="fa-solid fa-percent" style="margin-right:5px;font-size:.76rem;"></i>Difference (%) Report
            </button>
            <button class="tab-btn" :class="{ active: tab === 'due' }" @click="tab='due'">
                <i class="fa-solid fa-clock-rotate-left" style="margin-right:5px;font-size:.76rem;"></i>Due Sales Report
            </button>
        </div>

        <div class="tab-content">

            {{-- ══════════════════════════════════════
         TAB 1 — STOCK REPORT
    ══════════════════════════════════════ --}}
            <div :class="{ active: tab === 'stock' }">
                <div class="filter-section">
                    <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
                    <div class="filter-grid">

                        <div class="filter-group">
                            <label>From Date</label>
                            <input type="date" x-model="f.fromDate">
                        </div>
                        <div class="filter-group">
                            <label>To Date</label>
                            <input type="date" x-model="f.toDate">
                        </div>

                        <div class="filter-group">
                            <label>Division</label>
                            <select x-model="f.division" @change="onDivisionChange()">
                                <option value="">All Divisions</option>
                                <template x-for="d in locations" :key="d.name_en">
                                    <option :value="d.name_en" x-text="d.name_en"></option>
                                </template>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>District</label>
                            <select x-model="f.district" @change="onDistrictChange()" :disabled="!f.division">
                                <option value="">All Districts</option>
                                <template x-for="d in filteredDistricts" :key="d.name_en">
                                    <option :value="d.name_en" x-text="d.name_en"></option>
                                </template>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Upazila</label>
                            <select x-model="f.upazila" :disabled="!f.district">
                                <option value="">All Upazilas</option>
                                <template x-for="u in filteredUpazilas" :key="u.name_en">
                                    <option :value="u.name_en" x-text="u.name_en"></option>
                                </template>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Company</label>
                            <select x-model="f.companyId">
                                <option value="">All Companies</option>
                                <template x-for="c in companies" :key="c.id">
                                    <option :value="String(c.id)" x-text="c.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Depot</label>
                            <select x-model="f.depotName">
                                <option value="">All Depots</option>
                                <template x-for="d in depots" :key="d.id">
                                    <option :value="d.depot_name" x-text="d.depot_name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Filling Station</label>
                            <div class="station-combobox" x-data="stationCombobox()" x-init="initCombo()"
                                @station-selected.window="if($event.target.closest('[x-data]') === $el) $root.f.stationName = $event.detail">
                                <input type="text" :placeholder="selectedLabel || 'Search station...'" x-model="query"
                                    @focus="open=true" @input="open=true" @keydown.escape="open=false"
                                    @keydown.enter.prevent="selectFirst()" autocomplete="off">
                                <button class="clear-btn" x-show="selectedVal || query" @click="clear()" type="button">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                <div class="station-dropdown" x-show="open && results.length > 0"
                                    @click.outside="open=false" x-transition>
                                    <template x-for="s in results" :key="s.name">
                                        <div class="station-dropdown-item" :class="{ selected: selectedVal === s.name }"
                                            @click="select(s); $root.f.stationName = s.name">
                                            <span x-text="s.name"></span>
                                            <span style="font-size:.70rem;color:#94a3b8;margin-left:6px;"
                                                x-text="s.district ? '— '+s.district : ''"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="station-dropdown" x-show="open && query.length > 0 && results.length === 0"
                                    @click.outside="open=false">
                                    <div class="station-dropdown-empty">No stations found</div>
                                </div>
                            </div>
                        </div>

                        <div class="filter-group">
                            <label>Fuel Type</label>
                            <select x-model="f.fuelType">
                                <option value="">All Types</option>
                                <option value="petrol">Petrol</option>
                                <option value="diesel">Diesel</option>
                                <option value="octane">Octane</option>
                                <option value="others">Others</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Stock Status</label>
                            <select x-model="f.stockStatus">
                                <option value="">All Stock</option>
                                <option value="available">Available</option>
                                <option value="low">Low Stock</option>
                                <option value="zero">Zero Stock</option>
                            </select>
                        </div>

                        <div class="filter-btn-row">
                            <button class="btn-apply" @click="applyStockFilter()">
                                <i class="fas fa-filter" style="margin-right:6px;"></i>Apply Filters
                            </button>
                            <button class="btn-reset" @click="resetStock()">
                                <i class="fas fa-undo" style="margin-right:6px;"></i>Reset
                            </button>
                        </div>

                    </div>
                </div>

                <div class="stat-cards">
                    <div class="stat-card blue">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                            <div class="stat-card-badge">Total</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Total Stock</div>
                            <div class="stat-card-value" x-text="fmtL(stockTotals.total)"></div>
                        </div>
                    </div>

                     <div class="stat-card orange">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
                            <div class="stat-card-badge">Octane</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Octane Stock</div>
                            <div class="stat-card-value" x-text="fmtL(stockTotals.octane)"></div>
                        </div>
                    </div>
                   
                    <div class="stat-card purple">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
                            <div class="stat-card-badge">Petrol</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Petrol Stock</div>
                            <div class="stat-card-value" x-text="fmtL(stockTotals.petrol)"></div>
                        </div>
                    </div>
                   
                     <div class="stat-card green">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
                            <div class="stat-card-badge">Diesel</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Diesel Stock</div>
                            <div class="stat-card-value" x-text="fmtL(stockTotals.diesel)"></div>
                        </div>
                    </div>
                    {{-- others --}}
                    <div class="stat-card yellow">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
                            <div class="stat-card-badge">Others</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Others Stock</div>
                            <div class="stat-card-value" x-text="fmtL(stockTotals.others)"></div>
                        </div>
                    </div>
                    
                    <div class="stat-card teal">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-file-lines"></i></div>
                            <div class="stat-card-badge">Records</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Total Records</div>
                            <div class="stat-card-value" x-text="stockFiltered.length"></div>
                        </div>
                    </div>
                </div>

                <div class="table-section">
                    <div class="table-header-row">
                        <div class="table-title">Stock Report by Company &amp; Filling Station</div>
                        <div class="table-meta" x-text="stockFiltered.length + ' record(s) found'"></div>
                    </div>

                    <template x-if="!stockApplied">
                        <div class="empty-state">
                            <i class="fa-solid fa-filter"></i>
                            <p>Apply filters — data will be loaded</p>
                            <small>Filter by date, district, or station</small>
                        </div>
                    </template>

                    <template x-if="stockApplied">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Company</th>
                                        <th>Filling Station</th>
                                        <th>Fuel Type</th>
                                        <th>Opening Stock</th>
                                        <th>Received</th>
                                        <th>Sold</th>
                                        <th>Closing Stock</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-if="stockTableRows.length === 0">
                                        <tr>
                                            <td colspan="9" style="text-align:center;padding:36px;color:var(--muted);">
                                                <i class="fa-solid fa-inbox" style="margin-right:7px;opacity:.3;"></i>No
                                                data found
                                            </td>
                                        </tr>
                                    </template>

                                    <template x-for="(row, idx) in stockTableRows" :key="idx">
                                        <tr :style="row.isFirst ? 'border-top:1px solid #e9eef5' : ''">
                                            <template x-if="row.isFirst">
                                                <td :rowspan="row.rowspan" class="date-cell" x-text="row.report_date">
                                                </td>
                                            </template>
                                            <template x-if="row.isFirst">
                                                <td :rowspan="row.rowspan" class="company-cell"
                                                    x-text="row.company_name"></td>
                                            </template>
                                            <template x-if="row.isFirst">
                                                <td :rowspan="row.rowspan" class="station-cell"
                                                    x-text="row.station_name"></td>
                                            </template>
                                            <td x-text="row.fuelLabel" style="font-size:.78rem;"></td>
                                            <td x-text="fmtL(row.prev)"></td>
                                            <td class="received-val" x-text="fmtL(row.received)"></td>
                                            <td class="sold-val" x-text="fmtL(row.sales)"></td>
                                            <td class="current-val" x-text="fmtL(row.closing)"></td>
                                            <template x-if="row.isFirst">
                                                <td :rowspan="row.rowspan" :class="row.statusClass"
                                                    x-text="row.statusLabel"></td>
                                            </template>
                                        </tr>
                                    </template>

                                    <template x-if="stockFiltered.length > 0">
                                        <tr class="summary-row">
                                            <td colspan="4"
                                                style="text-align:right;padding-right:14px;color:var(--muted);">TOTAL</td>
                                            <td x-text="fmtL(stockTotals.prevSum)"></td>
                                            <td class="received-val" x-text="fmtL(stockTotals.recvSum)"></td>
                                            <td class="sold-val" x-text="fmtL(stockTotals.salesSum)"></td>
                                            <td class="current-val" x-text="fmtL(stockTotals.total)"></td>
                                            <td></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>

                <div class="export-row">
                    <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export PDF</button>
                    <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
                </div>
            </div>{{-- /stock --}}


            {{-- ══════════════════════════════════════
         TAB 2 — SALES REPORT
    ══════════════════════════════════════ --}}
            <div :class="{ active: tab === 'sales' }">
                <div class="filter-section">
                    <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
                    <div class="filter-grid">
                        <div class="filter-group"><label>From Date</label><input type="date" x-model="sf.fromDate">
                        </div>
                        <div class="filter-group"><label>To Date</label><input type="date" x-model="sf.toDate"></div>
                        <div class="filter-group">
                            <label>Division</label>
                            <select x-model="sf.division" @change="onSalesDivisionChange()">
                                <option value="">All Divisions</option>
                                <template x-for="d in locations" :key="d.name_en">
                                    <option :value="d.name_en" x-text="d.name_en"></option>
                                </template>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>District</label>
                            <select x-model="sf.district" :disabled="!sf.division">
                                <option value="">All Districts</option>
                                <template x-for="d in salesDistricts" :key="d.name_en">
                                    <option :value="d.name_en" x-text="d.name_en"></option>
                                </template>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Filling Station</label>
                            <div class="station-combobox" x-data="stationCombobox()" x-init="initCombo()">
                                <input type="text" :placeholder="selectedLabel || 'Search station...'" x-model="query"
                                    @focus="open=true" @input="open=true" @keydown.escape="open=false"
                                    @keydown.enter.prevent="selectFirst()" autocomplete="off">
                                <button class="clear-btn" x-show="selectedVal || query"
                                    @click="clear(); $root.sf.stationName=''" type="button"><i
                                        class="fa-solid fa-xmark"></i></button>
                                <div class="station-dropdown" x-show="open && results.length > 0"
                                    @click.outside="open=false" x-transition>
                                    <template x-for="s in results" :key="s.name">
                                        <div class="station-dropdown-item" :class="{ selected: selectedVal === s.name }"
                                            @click="select(s); $root.sf.stationName = s.name">
                                            <span x-text="s.name"></span>
                                            <span style="font-size:.70rem;color:#94a3b8;margin-left:6px;"
                                                x-text="s.district ? '— '+s.district : ''"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="station-dropdown" x-show="open && query.length > 0 && results.length === 0"
                                    @click.outside="open=false">
                                    <div class="station-dropdown-empty">No stations found</div>
                                </div>
                            </div>
                        </div>
                        <div class="filter-group">
                            <label>Fuel Type</label>
                            <select x-model="sf.fuelType">
                                <option value="">All Types</option>
                                <option value="petrol">Petrol</option>
                                <option value="diesel">Diesel</option>
                                <option value="octane">Octane</option>
                            </select>
                        </div>
                        <div class="filter-btn-row" style="grid-column:3/5;justify-content:flex-end;">
                            <button class="btn-apply" @click="applySalesFilter()"><i class="fas fa-filter"
                                    style="margin-right:6px;"></i>Apply</button>
                            <button class="btn-reset" @click="resetSales()"><i class="fas fa-undo"
                                    style="margin-right:6px;"></i>Reset</button>
                        </div>
                    </div>
                </div>

                <div class="stat-cards">
                    <div class="stat-card blue">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                            <div class="stat-card-badge">Total</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Total Sales</div>
                            <div class="stat-card-value" x-text="fmtL(salesTotals.total)"></div>
                        </div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
                            <div class="stat-card-badge">Diesel</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Diesel Sold</div>
                            <div class="stat-card-value" x-text="fmtL(salesTotals.diesel)"></div>
                        </div>
                    </div>
                    <div class="stat-card purple">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
                            <div class="stat-card-badge">Petrol</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Petrol Sold</div>
                            <div class="stat-card-value" x-text="fmtL(salesTotals.petrol)"></div>
                        </div>
                    </div>
                    <div class="stat-card orange">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
                            <div class="stat-card-badge">Octane</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Octane Sold</div>
                            <div class="stat-card-value" x-text="fmtL(salesTotals.octane)"></div>
                        </div>
                    </div>
                    <div class="stat-card teal">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-chart-bar"></i></div>
                            <div class="stat-card-badge">Records</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Total Records</div>
                            <div class="stat-card-value" x-text="salesFiltered.length"></div>
                        </div>
                    </div>
                </div>

                <div class="table-section">
                    <div class="table-header-row">
                        <div class="table-title">Sales Report by Station</div>
                        <div class="table-meta" x-text="salesFiltered.length + ' record(s) found'"></div>
                    </div>
                    <template x-if="!salesApplied">
                        <div class="empty-state"><i class="fa-solid fa-filter"></i>
                            <p>Apply filters</p>
                            <small>Filter by date or station</small>
                        </div>
                    </template>
                    <template x-if="salesApplied">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
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
                                    <template x-if="salesFiltered.length === 0">
                                        <tr>
                                            <td colspan="11" style="text-align:center;padding:36px;color:var(--muted);">
                                                <i class="fa-solid fa-inbox" style="margin-right:7px;opacity:.3;"></i>No
                                                data found
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(r, i) in salesFiltered" :key="i">
                                        <tr>
                                            <td x-text="i+1"></td>
                                            <td class="station-cell" x-text="r.station_name"></td>
                                            <td x-text="r.district"></td>
                                            <td class="date-cell" x-text="r.report_date"></td>
                                            <td class="received-val" x-text="fmtL(r.diesel_received)"></td>
                                            <td class="sold-val" x-text="fmtL(r.diesel_sales)"></td>
                                            <td class="received-val" x-text="fmtL(r.petrol_received)"></td>
                                            <td class="sold-val" x-text="fmtL(r.petrol_sales)"></td>
                                            <td class="received-val" x-text="fmtL(r.octane_received)"></td>
                                            <td class="sold-val" x-text="fmtL(r.octane_sales)"></td>
                                            <td class="current-val"
                                                x-text="fmtL(r.diesel_sales + r.petrol_sales + r.octane_sales)"></td>
                                        </tr>
                                    </template>
                                    <template x-if="salesFiltered.length > 0">
                                        <tr class="summary-row">
                                            <td colspan="4"
                                                style="text-align:right;padding-right:14px;color:var(--muted);">TOTAL</td>
                                            <td x-text="fmtL(salesTotals.dieselRecv)"></td>
                                            <td x-text="fmtL(salesTotals.diesel)"></td>
                                            <td x-text="fmtL(salesTotals.petrolRecv)"></td>
                                            <td x-text="fmtL(salesTotals.petrol)"></td>
                                            <td x-text="fmtL(salesTotals.octaneRecv)"></td>
                                            <td x-text="fmtL(salesTotals.octane)"></td>
                                            <td x-text="fmtL(salesTotals.total)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
                <div class="export-row">
                    <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export PDF</button>
                    <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
                </div>
            </div>{{-- /sales --}}


            {{-- ══════════════════════════════════════
         TAB 3 — TAG OFFICER REPORT
    ══════════════════════════════════════ --}}
            <div :class="{ active: tab === 'officer' }">
                <div class="filter-section">
                    <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
                    <div class="filter-grid">
                        <div class="filter-group">
                            <label>Division</label>
                            <select x-model="of.division" @change="onOfficerDivisionChange()">
                                <option value="">All Divisions</option>
                                <template x-for="d in locations" :key="d.name_en">
                                    <option :value="d.name_en" x-text="d.name_en"></option>
                                </template>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>District</label>
                            <select x-model="of.district" :disabled="!of.division">
                                <option value="">All Districts</option>
                                <template x-for="d in officerDistricts" :key="d.name_en">
                                    <option :value="d.name_en" x-text="d.name_en"></option>
                                </template>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Assignment Status</label>
                            <select x-model="of.status">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="filter-btn-row" style="grid-column:4/5;justify-content:flex-end;">
                            <button class="btn-apply" @click="applyOfficerFilter()"><i class="fas fa-filter"
                                    style="margin-right:6px;"></i>Apply</button>
                            <button class="btn-reset" @click="resetOfficer()"><i class="fas fa-undo"
                                    style="margin-right:6px;"></i>Reset</button>
                        </div>
                    </div>
                </div>

                <div class="stat-cards">
                    <div class="stat-card blue">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-users"></i></div>
                            <div class="stat-card-badge">Total</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Total Officers</div>
                            <div class="stat-card-value" x-text="officerFiltered.length"></div>
                        </div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-user-check"></i></div>
                            <div class="stat-card-badge">Active</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Active</div>
                            <div class="stat-card-value"
                                x-text="officerFiltered.filter(function(r){return r.status==='active'}).length"></div>
                        </div>
                    </div>
                    <div class="stat-card red">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-user-xmark"></i></div>
                            <div class="stat-card-badge">Inactive</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Inactive</div>
                            <div class="stat-card-value"
                                x-text="officerFiltered.filter(function(r){return r.status==='inactive'}).length"></div>
                        </div>
                    </div>
                    <div class="stat-card teal">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-building"></i></div>
                            <div class="stat-card-badge">Stations</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Stations Covered</div>
                            <div class="stat-card-value"
                                x-text="new Set(officerFiltered.map(function(r){return r.filling_station_id})).size"></div>
                        </div>
                    </div>
                    <div class="stat-card orange">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                            <div class="stat-card-badge">Districts</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Districts Covered</div>
                            <div class="stat-card-value"
                                x-text="new Set(officerFiltered.map(function(r){return r.district}).filter(Boolean)).size">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-section">
                    <div class="table-header-row">
                        <div class="table-title">Tag Officer Assignment Report</div>
                        <div class="table-meta" x-text="officerFiltered.length + ' record(s) found'"></div>
                    </div>
                    <template x-if="!officerApplied">
                        <div class="empty-state"><i class="fa-solid fa-filter"></i>
                            <p>Apply filters</p>
                            <small>Filter by division, district, or status</small>
                        </div>
                    </template>
                    <template x-if="officerApplied">
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
                                    <template x-if="officerFiltered.length === 0">
                                        <tr>
                                            <td colspan="8" style="text-align:center;padding:36px;color:var(--muted);">
                                                <i class="fa-solid fa-inbox" style="margin-right:7px;opacity:.3;"></i>No
                                                data found
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(r, i) in officerFiltered" :key="i">
                                        <tr>
                                            <td x-text="i+1"></td>
                                            <td class="station-cell" style="font-weight:600;" x-text="r.officer_name">
                                            </td>
                                            <td style="font-size:.75rem;color:var(--muted);line-height:1.7;">
                                                <span x-text="r.officer_email"></span><br>
                                                <span x-text="r.officer_phone"></span>
                                            </td>
                                            <td class="station-cell" x-text="r.station_name"></td>
                                            <td x-text="r.district"></td>
                                            <td x-text="r.division"></td>
                                            <td class="date-cell" x-text="r.assigned_at"></td>
                                            <td>
                                                <span class="status-badge"
                                                    :class="r.status === 'active' ? 'badge-active' : 'badge-inactive'"
                                                    x-text="r.status.charAt(0).toUpperCase() + r.status.slice(1)"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
                <div class="export-row">
                    <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export PDF</button>
                    <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
                </div>
            </div>{{-- /officer --}}


            {{-- ══════════════════════════════════════
         TAB 4 — DIFFERENCE (%) REPORT
    ══════════════════════════════════════ --}}
            <div :class="{ active: tab === 'diff' }">
                <div class="filter-section">
                    <div class="filter-header"><i class="fa-solid fa-sliders"></i> Filter Options</div>
                    <div class="filter-grid">
                        <div class="filter-group"><label>From Date</label><input type="date" x-model="df.fromDate">
                        </div>
                        <div class="filter-group"><label>To Date</label><input type="date" x-model="df.toDate"></div>
                        <div class="filter-group">
                            <label>District</label>
                            <select x-model="df.district">
                                <option value="">All Districts</option>
                                <template x-for="d in allDistricts" :key="d">
                                    <option :value="d" x-text="d"></option>
                                </template>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Min Difference (L)</label>
                            <input type="number" x-model.number="df.minDiff" placeholder="e.g. 50" min="0">
                        </div>
                        <div class="filter-btn-row" style="grid-column:3/5;">
                            <button class="btn-apply" @click="applyDiffFilter()"><i class="fas fa-filter"
                                    style="margin-right:6px;"></i>Apply</button>
                            <button class="btn-reset" @click="resetDiff()"><i class="fas fa-undo"
                                    style="margin-right:6px;"></i>Reset</button>
                        </div>
                    </div>
                </div>

                <div class="stat-cards">
                    <div class="stat-card blue">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-scale-balanced"></i></div>
                            <div class="stat-card-badge">All</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Total Records</div>
                            <div class="stat-card-value" x-text="diffFiltered.length"></div>
                        </div>
                    </div>
                    <div class="stat-card red">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <div class="stat-card-badge">Alert</div>
                        </div>
                        <div>
                            <div class="stat-card-label">High Diff (&gt;50L)</div>
                            <div class="stat-card-value"
                                x-text="diffFiltered.filter(function(r){return Math.abs(r.petrol_difference)+Math.abs(r.diesel_difference)+Math.abs(r.octane_difference)>50}).length">
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
                            <div class="stat-card-value"
                                x-text="fmtL(diffFiltered.reduce(function(a,r){return a+Math.abs(r.diesel_difference)},0))">
                            </div>
                        </div>
                    </div>
                    <div class="stat-card purple">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
                            <div class="stat-card-badge">Petrol</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Total Petrol Diff</div>
                            <div class="stat-card-value"
                                x-text="fmtL(diffFiltered.reduce(function(a,r){return a+Math.abs(r.petrol_difference)},0))">
                            </div>
                        </div>
                    </div>
                    <div class="stat-card teal">
                        <div class="stat-card-top">
                            <div class="stat-card-icon"><i class="fa-solid fa-gas-pump"></i></div>
                            <div class="stat-card-badge">Octane</div>
                        </div>
                        <div>
                            <div class="stat-card-label">Total Octane Diff</div>
                            <div class="stat-card-value"
                                x-text="fmtL(diffFiltered.reduce(function(a,r){return a+Math.abs(r.octane_difference)},0))">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-section">
                    <div class="table-header-row">
                        <div class="table-title">Fuel Difference (%) Report</div>
                        <div class="table-meta" x-text="diffFiltered.length + ' record(s) found'"></div>
                    </div>
                    <template x-if="!diffApplied">
                        <div class="empty-state"><i class="fa-solid fa-filter"></i>
                            <p>Filter apply to load data</p>
                        </div>
                    </template>
                    <template x-if="diffApplied">
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Station</th>
                                        <th>District</th>
                                        <th>Date</th>
                                        <th>Petrol Diff (L)</th>
                                        <th>Petrol (%)</th>
                                        <th>Diesel Diff (L)</th>
                                        <th>Diesel (%)</th>
                                        <th>Octane Diff (L)</th>
                                        <th>Octane (%)</th>
                                        <th>Total Diff</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-if="diffFiltered.length === 0">
                                        <tr>
                                            <td colspan="11" style="text-align:center;padding:36px;color:var(--muted);">
                                                <i class="fa-solid fa-inbox" style="margin-right:7px;opacity:.3;"></i>No
                                                data found
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(r, i) in diffFiltered" :key="i">
                                        <tr>
                                            <td x-text="i+1"></td>
                                            <td class="station-cell" x-text="r.station_name"></td>
                                            <td x-text="r.district"></td>
                                            <td class="date-cell" x-text="r.report_date"></td>
                                            <td :class="diffClass(r.petrol_difference)"
                                                x-text="diffFmt(r.petrol_difference) + ' L'"></td>
                                            <td :class="r.petrol_difference > 0 ? 'diff-positive' : 'diff-zero'"
                                                x-text="pct(r.petrol_difference, r.petrol_received) + '%'"></td>
                                            <td :class="diffClass(r.diesel_difference)"
                                                x-text="diffFmt(r.diesel_difference) + ' L'"></td>
                                            <td :class="r.diesel_difference > 0 ? 'diff-positive' : 'diff-zero'"
                                                x-text="pct(r.diesel_difference, r.diesel_received) + '%'"></td>
                                            <td :class="diffClass(r.octane_difference)"
                                                x-text="diffFmt(r.octane_difference) + ' L'"></td>
                                            <td :class="r.octane_difference > 0 ? 'diff-positive' : 'diff-zero'"
                                                x-text="pct(r.octane_difference, r.octane_received) + '%'"></td>
                                            <td :class="diffClass(r.petrol_difference + r.diesel_difference + r.octane_difference)"
                                                style="font-weight:700;"
                                                x-text="diffFmt(r.petrol_difference + r.diesel_difference + r.octane_difference) + ' L'">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
                <div class="export-row">
                    <button class="btn-export btn-pdf"><i class="fa-regular fa-file-pdf"></i> Export PDF</button>
                    <button class="btn-export btn-excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
                </div>
            </div>{{-- /diff --}}


            {{-- ══ TAB 5 — DUE SALES ══ --}}
            <div :class="{ active: tab === 'due' }">
                <div class="empty-state" style="padding:80px 20px;">
                    <i class="fa-solid fa-clock-rotate-left"
                        style="font-size:2.5rem;opacity:.2;display:block;margin-bottom:14px;"></i>
                    <p style="font-size:.95rem;font-weight:600;">Due Sales Report</p>
                    <small>Due sales will be displayed here once the column is configured</small>
                </div>
            </div>

        </div>{{-- /tab-content --}}
    </div>{{-- /card --}}
@endsection

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // ═══════════════════════════════════════════════════════════
        //  Station Combobox — reusable Alpine.js component
        // ═══════════════════════════════════════════════════════════
        function stationCombobox() {
            return {
                query: '',
                open: false,
                selectedVal: '',
                selectedLabel: '',
                allStations: window.__REPORTS__.stations,

                get results() {
                    if (!this.query.trim()) return this.allStations.slice(0, 30);
                    var q = this.query.toLowerCase();
                    return this.allStations.filter(function(s) {
                        return s.name.toLowerCase().includes(q) ||
                            (s.district || '').toLowerCase().includes(q);
                    }).slice(0, 50);
                },

                initCombo() {},

                select(s) {
                    this.selectedVal = s.name;
                    this.selectedLabel = s.name;
                    this.query = '';
                    this.open = false;
                },

                selectFirst() {
                    if (this.results.length > 0) this.select(this.results[0]);
                },

                clear() {
                    this.selectedVal = '';
                    this.selectedLabel = '';
                    this.query = '';
                    this.open = false;
                },
            };
        }

        // ═══════════════════════════════════════════════════════════
        //  Main Reports App
        // ═══════════════════════════════════════════════════════════
        function reportsApp() {
            var D = window.__REPORTS__;

            return {
                tab: 'stock',

                // Raw data
                allStock: D.stock,
                allOfficer: D.officer,
                locations: D.locations,
                companies: D.companies,
                depots: D.depots,

                // Cascade state
                filteredDistricts: [],
                filteredUpazilas: [],
                salesDistricts: [],
                officerDistricts: [],
                allDistricts: [],

                // ── Stock filter ──
                f: {
                    fromDate: '',
                    toDate: '',
                    division: '',
                    district: '',
                    upazila: '',
                    companyId: '',
                    depotName: '',
                    stationName: '',
                    fuelType: '',
                    stockStatus: ''
                },
                stockFiltered: [],
                stockApplied: false,

                // ── Sales filter ──
                sf: {
                    fromDate: '',
                    toDate: '',
                    division: '',
                    district: '',
                    stationName: '',
                    fuelType: ''
                },
                salesFiltered: [],
                salesApplied: false,

                // ── Officer filter ──
                of: {
                    division: '',
                    district: '',
                    status: ''
                },
                officerFiltered: [],
                officerApplied: false,

                // ── Diff filter ──
                df: {
                    fromDate: '',
                    toDate: '',
                    district: '',
                    minDiff: 0
                },
                diffFiltered: [],
                diffApplied: false,

                // ────────────────────────────────────────────────────
                init() {
                    var dists = new Set(this.allStock.map(function(r) {
                        return r.district;
                    }).filter(Boolean));
                    this.allDistricts = Array.from(dists).sort();
                },

                // ── Location Cascade ──────────────────────────────────
                onDivisionChange() {
                    this.f.district = '';
                    this.f.upazila = '';
                    this.filteredUpazilas = [];
                    var div = this.locations.find(function(d) {
                        return d.name_en === this.f.division;
                    }.bind(this));
                    this.filteredDistricts = div ? div.districts : [];
                },
                onDistrictChange() {
                    this.f.upazila = '';
                    var div = this.locations.find(function(d) {
                        return d.name_en === this.f.division;
                    }.bind(this));
                    var dist = div && div.districts ? div.districts.find(function(d) {
                        return d.name_en === this.f.district;
                    }.bind(this)) : null;
                    this.filteredUpazilas = dist ? (dist.police_stations || []) : [];
                },
                onSalesDivisionChange() {
                    this.sf.district = '';
                    var div = this.locations.find(function(d) {
                        return d.name_en === this.sf.division;
                    }.bind(this));
                    this.salesDistricts = div ? div.districts : [];
                },
                onOfficerDivisionChange() {
                    this.of.district = '';
                    var div = this.locations.find(function(d) {
                        return d.name_en === this.of.division;
                    }.bind(this));
                    this.officerDistricts = div ? div.districts : [];
                },

                // ── STOCK FILTER ──────────────────────────────────────
                applyStockFilter() {
                    var self = this;
                    this.stockFiltered = this.allStock.filter(function(r) {
                        if (self.f.fromDate && r.report_date < self.f.fromDate) return false;
                        if (self.f.toDate && r.report_date > self.f.toDate) return false;
                        if (self.f.division && r.division !== self.f.division) return false;
                        if (self.f.district && r.district !== self.f.district) return false;
                        if (self.f.upazila && r.thana_upazila !== self.f.upazila) return false;
                        if (self.f.companyId && r.company_id !== self.f.companyId) return false;
                        if (self.f.depotName && r.depot_name !== self.f.depotName) return false;
                        if (self.f.stationName && r.station_name !== self.f.stationName) return false;
                        if (self.f.stockStatus) {
                            var tot = r.diesel_closing_stock + r.petrol_closing_stock + r.octane_closing_stock;
                            if (self.f.stockStatus === 'available' && tot < 2000) return false;
                            if (self.f.stockStatus === 'low' && !(tot >= 1 && tot < 2000)) return false;
                            if (self.f.stockStatus === 'zero' && tot > 0) return false;
                        }
                        return true;
                    });
                    this.stockApplied = true;
                },

                resetStock() {
                    this.f = {
                        fromDate: '',
                        toDate: '',
                        division: '',
                        district: '',
                        upazila: '',
                        companyId: '',
                        depotName: '',
                        stationName: '',
                        fuelType: '',
                        stockStatus: ''
                    };
                    this.filteredDistricts = [];
                    this.filteredUpazilas = [];
                    this.stockFiltered = [];
                    this.stockApplied = false;
                },

                get stockTableRows() {
                    var rows = [];
                    var fuels = [];
                    if (!this.f.fuelType || this.f.fuelType === 'diesel') fuels.push('diesel');
                    if (!this.f.fuelType || this.f.fuelType === 'petrol') fuels.push('petrol');
                    if (!this.f.fuelType || this.f.fuelType === 'octane') fuels.push('octane');

                    for (var ri = 0; ri < this.stockFiltered.length; ri++) {
                        var r = this.stockFiltered[ri];
                        var tot = r.diesel_closing_stock + r.petrol_closing_stock + r.octane_closing_stock;
                        var statusClass = tot <= 0 ? 'status-zero' : tot < 2000 ? 'status-low' : 'status-available';
                        var statusLabel = tot <= 0 ? 'Zero Stock' : tot < 2000 ? 'Low Stock' : 'Available';

                        for (var fi = 0; fi < fuels.length; fi++) {
                            var fuel = fuels[fi];
                            rows.push({
                                isFirst: fi === 0,
                                rowspan: fuels.length,
                                report_date: r.report_date,
                                company_name: r.company_name,
                                station_name: r.station_name,
                                fuelLabel: fuel.charAt(0).toUpperCase() + fuel.slice(1),
                                prev: r[fuel + '_prev_stock'] || 0,
                                received: r[fuel + '_received'] || 0,
                                sales: r[fuel + '_sales'] || 0,
                                closing: r[fuel + '_closing_stock'] || 0,
                                statusClass: statusClass,
                                statusLabel: statusLabel,
                            });
                        }
                    }
                    return rows;
                },

                get stockTotals() {
                    var s = this.stockFiltered;
                    var diesel = s.reduce(function(a, r) {
                        return a + r.diesel_closing_stock;
                    }, 0);
                    var petrol = s.reduce(function(a, r) {
                        return a + r.petrol_closing_stock;
                    }, 0);
                    var octane = s.reduce(function(a, r) {
                        return a + r.octane_closing_stock;
                    }, 0);
                    var prevSum = s.reduce(function(a, r) {
                        return a + r.diesel_prev_stock + r.petrol_prev_stock + r.octane_prev_stock;
                    }, 0);
                    var recvSum = s.reduce(function(a, r) {
                        return a + r.diesel_received + r.petrol_received + r.octane_received;
                    }, 0);
                    var salesSum = s.reduce(function(a, r) {
                        return a + r.diesel_sales + r.petrol_sales + r.octane_sales;
                    }, 0);
                    return {
                        diesel: diesel,
                        petrol: petrol,
                        octane: octane,
                        total: diesel + petrol + octane,
                        prevSum: prevSum,
                        recvSum: recvSum,
                        salesSum: salesSum
                    };
                },

                // ── SALES FILTER ──────────────────────────────────────
                applySalesFilter() {
                    var self = this;
                    this.salesFiltered = this.allStock.filter(function(r) {
                        if (self.sf.fromDate && r.report_date < self.sf.fromDate) return false;
                        if (self.sf.toDate && r.report_date > self.sf.toDate) return false;
                        if (self.sf.division && r.division !== self.sf.division) return false;
                        if (self.sf.district && r.district !== self.sf.district) return false;
                        if (self.sf.stationName && r.station_name !== self.sf.stationName) return false;
                        if (self.sf.fuelType) {
                            var ft = self.sf.fuelType;
                            if (r[ft + '_sales'] <= 0) return false;
                        }
                        return true;
                    });
                    this.salesApplied = true;
                },

                resetSales() {
                    this.sf = {
                        fromDate: '',
                        toDate: '',
                        division: '',
                        district: '',
                        stationName: '',
                        fuelType: ''
                    };
                    this.salesFiltered = [];
                    this.salesApplied = false;
                    this.salesDistricts = [];
                },

                get salesTotals() {
                    var s = this.salesFiltered;
                    var diesel = s.reduce(function(a, r) {
                        return a + r.diesel_sales;
                    }, 0);
                    var petrol = s.reduce(function(a, r) {
                        return a + r.petrol_sales;
                    }, 0);
                    var octane = s.reduce(function(a, r) {
                        return a + r.octane_sales;
                    }, 0);
                    var dieselRecv = s.reduce(function(a, r) {
                        return a + r.diesel_received;
                    }, 0);
                    var petrolRecv = s.reduce(function(a, r) {
                        return a + r.petrol_received;
                    }, 0);
                    var octaneRecv = s.reduce(function(a, r) {
                        return a + r.octane_received;
                    }, 0);
                    return {
                        diesel: diesel,
                        petrol: petrol,
                        octane: octane,
                        total: diesel + petrol + octane,
                        dieselRecv: dieselRecv,
                        petrolRecv: petrolRecv,
                        octaneRecv: octaneRecv
                    };
                },

                // ── OFFICER FILTER ────────────────────────────────────
                applyOfficerFilter() {
                    var self = this;
                    this.officerFiltered = this.allOfficer.filter(function(r) {
                        if (self.of.division && r.division !== self.of.division) return false;
                        if (self.of.district && r.district !== self.of.district) return false;
                        if (self.of.status && r.status !== self.of.status) return false;
                        return true;
                    });
                    this.officerApplied = true;
                },

                resetOfficer() {
                    this.of = {
                        division: '',
                        district: '',
                        status: ''
                    };
                    this.officerFiltered = [];
                    this.officerApplied = false;
                    this.officerDistricts = [];
                },

                // ── DIFF FILTER ───────────────────────────────────────
                applyDiffFilter() {
                    var self = this;
                    this.diffFiltered = this.allStock.filter(function(r) {
                        if (self.df.fromDate && r.report_date < self.df.fromDate) return false;
                        if (self.df.toDate && r.report_date > self.df.toDate) return false;
                        if (self.df.district && r.district !== self.df.district) return false;
                        if (self.df.minDiff > 0) {
                            var tot = Math.abs(r.petrol_difference) + Math.abs(r.diesel_difference) + Math.abs(r
                                .octane_difference);
                            if (tot < self.df.minDiff) return false;
                        }
                        return true;
                    });
                    this.diffApplied = true;
                },

                resetDiff() {
                    this.df = {
                        fromDate: '',
                        toDate: '',
                        district: '',
                        minDiff: 0
                    };
                    this.diffFiltered = [];
                    this.diffApplied = false;
                },

                // ── HELPERS ───────────────────────────────────────────
                fmt(n) {
                    return Number(n || 0).toLocaleString('en-BD');
                },
                fmtL(n) {
                    return this.fmt(n) + ' L';
                },
                diffFmt(v) {
                    return (v > 0 ? '+' : '') + this.fmt(v);
                },
                pct(diff, recv) {
                    if (!recv || recv <= 0) return '0.00';
                    return (diff / recv * 100).toFixed(2);
                },
                diffClass(v) {
                    if (v > 0) return 'diff-positive';
                    if (v < 0) return 'diff-negative';
                    return 'diff-zero';
                },
            };
        }
    </script>
@endpush
