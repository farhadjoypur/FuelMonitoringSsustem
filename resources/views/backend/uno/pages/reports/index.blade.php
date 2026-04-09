@extends('backend.uno.layouts.app')

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
            --bg: #f0f2f5;
            --surface: #ffffff;
            --border: #e2e8f0;
            --text: #1a202c;
            --muted: #718096;
            --primary: #0f4c81;
            --shadow: 0 1px 3px rgba(0, 0, 0, .07), 0 4px 16px rgba(0, 0, 0, .05);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rpt-card {
            background: var(--surface);
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        /* ── Tabs ── */
        .tabs-bar {
            display: flex;
            border-bottom: 1.5px solid var(--border);
            padding: 0 24px;
        }

        .tab-btn {
            background: none;
            border: none;
            border-bottom: 2.5px solid transparent;
            padding: 15px 22px;
            font-size: .875rem;
            font-weight: 500;
            color: var(--muted);
            cursor: pointer;
            white-space: nowrap;
            margin-bottom: -1.5px;
            transition: color .18s, border-color .18s;
            display: flex;
            align-items: center;
            gap: 7px;
            flex-shrink: 0;
            justify-content: flex-start;
        }

        .tab-btn:hover {
            color: var(--primary);
        }

        .tab-btn.is-active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 600;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.is-active {
            display: block;
        }

        @media (max-width: 600px) {
            .tabs-bar {
                flex-direction: column;
                padding: 0;
                border-bottom: none;
            }

            .tab-btn {
                border-bottom: none;
                border-left: 3px solid transparent;
                margin-bottom: 0;
                padding: 13px 18px;
                justify-content: flex-start;
                border-bottom: 1px solid var(--border);
                font-size: .85rem;
                flex: unset;
                white-space: normal;
            }

            .tab-btn.is-active {
                border-left-color: var(--primary);
                border-bottom-color: var(--border);
                background: #f0f6ff;
            }
        }

        /* ── Filter ── */
        .filter-section {
            padding: 18px 24px 20px;
            border-bottom: 1px solid var(--border);
        }

        .filter-title {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: .82rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 14px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px 14px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .form-group label {
            font-size: .70rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .form-group input,
        .form-group select {
            padding: 8px 10px;
            border: 1.5px solid #e2e8f0;
            border-radius: 7px;
            font-family: 'DM Sans', sans-serif;
            font-size: .845rem;
            color: var(--text);
            background: #f8fafc;
            outline: none;
            appearance: none;
            transition: border-color .14s, box-shadow .14s;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .1);
            background: #fff;
        }

        .form-group select:disabled {
            opacity: .4;
            background: #f1f5f9;
            cursor: not-allowed;
        }

        .filter-actions {
            grid-column: 3 / 5;
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-apply {
            background: #006796;
            color: #fff;
            border: none;
            padding: 9px 26px;
            border-radius: 8px;
            font-weight: 600;
            font-size: .875rem;
            cursor: pointer;
            transition: background .18s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-apply:hover:not(:disabled) {
            background: #004f72;
        }

        .btn-apply:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .btn-reset {
            background: #f1f5f9;
            color: #475569;
            border: 1.5px solid #cbd5e1;
            padding: 9px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: .875rem;
            cursor: pointer;
            transition: background .18s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-reset:hover {
            background: #e2e8f0;
        }

        /* ── Table Section ── */
        .table-section {
            padding: 0 24px 24px;
        }

        .table-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 0 12px;
        }

        .table-title {
            font-size: .90rem;
            font-weight: 700;
            color: var(--text);
        }

        .record-count {
            font-size: .75rem;
            color: var(--muted);
        }

        .loading-overlay {
            padding: 60px 20px;
            text-align: center;
            color: #94a3b8;
        }

        .loading-spinner {
            width: 36px;
            height: 36px;
            margin: 0 auto 12px;
            border: 3px solid #e2e8f0;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Difference Report Table ── */
        .diff-table-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            margin-top: 16px;
        }

        .diff-table {
            width: max-content;
            /* KEY FIX */
            min-width: 100%;
            /* keep full width on desktop */
            border-collapse: collapse;
            font-size: .775rem;
            background: var(--surface);
        }

        .diff-table thead {
            background: #f8fafc;
            border-bottom: 2px solid var(--border);
        }

        .diff-table th {
            padding: 10px 6px;
            text-align: center;
            font-weight: 600;
            color: #475569;
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .diff-table tbody tr {
            border-bottom: 2px solid #e2e8f0;
            transition: background .12s;
        }

        .diff-table tbody tr:hover {
            background: #fafbfc;
        }

        .diff-table td {
            padding: 0;
            color: var(--text);
            vertical-align: middle;
            text-align: center;
            overflow: hidden;
        }

        .diff-table td.row-number {
            font-weight: 600;
            color: var(--muted);
            padding: 0 4px;
            text-align: center;
        }

        .diff-table td.td-station,
        .diff-table td.td-officer,
        .diff-table td.td-designation,
        .diff-table td.td-phone,
        .diff-table td.td-district,
        .diff-table td.td-upazila,
        .diff-table td.td-date {
            padding: 10px 5px;
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
            padding: 7px 4px;
            min-height: 38px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .fuel-row:last-child {
            border-bottom: none;
        }

        .fuel-type {
            font-size: .70rem;
            color: var(--muted);
            font-weight: 500;
        }

        .fuel-value {
            font-weight: 700;
            font-size: .78rem;
        }

        .fuel-percent {
            font-weight: 700;
            font-size: .78rem;
        }

        .alert-text {
            font-size: .68rem;
            color: #64748b;
            text-align: center;
            line-height: 1.3;
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
            gap: 5px;
            padding: 8px 4px;
            align-items: center;
        }

        .action-btn {
            padding: 5px 6px;
            border-radius: 5px;
            font-size: .68rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
            text-align: center;
            width: 68px;
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
        }

        .date-day {
            font-size: .65rem;
            color: var(--muted);
            display: block;
            margin-top: 2px;
        }

        /* ── Export ── */
        .export-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 24px 20px;
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

        .btn-export-pdf {
            border-color: #e2e8f0;
            color: #475569;
            background: #fff;
        }

        .btn-export-pdf:hover {
            background: #f8fafc;
        }

        .btn-export-excel {
            border-color: #16a34a;
            color: #fff;
            background: #16a34a;
        }

        .btn-export-excel:hover {
            background: #15803d;
        }

        /* ── Modals ── */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .4);
            z-index: 9990;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-box {
            background: #fff;
            border-radius: 14px;
            padding: 28px;
            width: 440px;
            max-width: 95vw;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .18);
        }

        .modal-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-subtitle {
            font-size: .82rem;
            color: var(--muted);
            margin-bottom: 12px;
        }

        .modal-box textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: .875rem;
            resize: vertical;
            outline: none;
        }

        .modal-box textarea:focus {
            border-color: #3b82f6;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 14px;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
            border: 1.5px solid #cbd5e1;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: .875rem;
        }

        .btn-send {
            background: #22c55e;
            color: #fff;
            border: none;
            padding: 8px 22px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: .875rem;
        }

        .btn-send:hover {
            background: #16a34a;
        }

        .confirm-icon {
            text-align: center;
            margin-bottom: 12px;
        }

        .confirm-icon i {
            font-size: 2rem;
            color: #ef4444;
        }

        .confirm-message {
            text-align: center;
            font-size: .90rem;
            color: #334155;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .btn-delete-confirm {
            background: #ef4444;
            color: #fff;
            border: none;
            padding: 8px 22px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-delete-confirm:hover {
            background: #dc2626;
        }

        /* ── Responsive ── */
        @media (max-width: 1200px) {
            .filter-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .filter-actions {
                grid-column: 1 / 4;
                justify-content: flex-start;
            }
        }

        @media (max-width: 900px) {
            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-actions {
                grid-column: 1 / 3;
            }
        }

        @media (max-width: 600px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                grid-column: 1;
                flex-direction: column;
            }

            .btn-apply,
            .btn-reset {
                width: 100%;
                justify-content: center;
            }

            .export-row {
                flex-direction: column;
            }

            .btn-export {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush
@section('content')
    <h1 class="page-title">
        <i class="fa-solid fa-chart-bar" style="color: var(--primary);"></i>
        Reports &amp; Analytics
    </h1>

    <div class="rpt-card" x-data="reportApp()" x-init="init()">

        {{-- ── TABS ── --}}
        <div class="tabs-bar">
            <button class="tab-btn" :class="{ 'is-active': activeTab === 'stock' }" @click="switchTab('stock')">
                <i class="fa-solid fa-chart-line" style="font-size:.78rem;"></i>
                Sales &amp; Stock Report
            </button>
            <button class="tab-btn" :class="{ 'is-active': activeTab === 'difference' }" @click="switchTab('difference')">
                <i class="fa-solid fa-chart-column" style="font-size:.78rem;"></i>
                Difference Report
            </button>
            <button class="tab-btn" :class="{ 'is-active': activeTab === 'pending' }" @click="switchTab('pending')">
                <i class="fa-solid fa-clock-rotate-left" style="font-size:.78rem;"></i>
                Pending Reports
            </button>
        </div>


        {{-- ════════════════════════════════════
     TAB 1 — SALES & STOCK
════════════════════════════════════ --}}
        <div class="tab-panel" :class="{ 'is-active': activeTab === 'stock' }">

            <div class="filter-section">
                <div class="filter-title">
                    <i class="fa-solid fa-sliders"></i> Filter Options
                </div>

                <div class="filter-grid">

                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" x-model="filters.from_date">
                    </div>

                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" x-model="filters.to_date">
                    </div>

                    {{-- ★ DISTRICT — DC এর নিজের district, locked, read-only --}}
                    <div class="form-group">
                        <label>District</label>
                        <input type="text" value="{{ $dc_district }}" readonly
                            style="background:#f0f0f0; cursor:not-allowed;">
                        {{-- hidden input দিয়ে filter এ district value পাঠানো হচ্ছে --}}
                        <input type="hidden" x-model="filters.district">
                    </div>

                    <div class="form-group">
                        <label>Upazila</label>
                        <select x-model="filters.thana_upazila">
                            <option value="">All Upazilas</option>
                            <template x-for="upazila in availableUpazilas" :key="upazila.name_en">
                                <option :value="upazila.name_en" x-text="upazila.name_en"></option>
                            </template>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Company</label>
                        <select x-model="filters.company_id">
                            <option value="">All Companies</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Depot</label>
                        <select x-model="filters.depot_id">
                            <option value="">All Depots</option>
                            @foreach ($depots as $depot)
                                <option value="{{ $depot->id }}">{{ $depot->depot_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Filling Station</label>
                        <select x-model="filters.station_id">
                            <option value="">All Stations</option>
                            @foreach ($stations as $station)
                                <option value="{{ $station->id }}">{{ $station->station_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fuel Type</label>
                        <select x-model="filters.fuel_type">
                            <option value="">All Types</option>
                            <option value="octane">Octane</option>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="others">Others</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Stock Status</label>
                        <select x-model="filters.stock_status">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="low">Low Stock</option>
                            <option value="zero">Zero Stock</option>
                            <option value="highdiff">High Difference</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button class="btn-apply" @click="applyFilter()" :disabled="isLoading">
                            <template x-if="isLoading">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </template>
                            <template x-if="!isLoading">
                                <i class="fa-solid fa-filter"></i>
                            </template>
                            <span x-text="isLoading ? 'Loading...' : 'Apply Filters'"></span>
                        </button>
                        <button class="btn-reset" @click="resetFilter()">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </button>
                    </div>

                </div>
            </div>

            {{-- Table section --}}
            <div class="table-section">
                <div class="table-header-row">
                    <div class="table-title">Stock &amp; Sales Reports</div>
                    <div class="record-count" x-text="recordCountText"></div>
                </div>

                <div class="loading-overlay" x-show="isLoading" style="display:none;">
                    <div class="loading-spinner"></div>
                    <p style="font-size:.85rem;">Loading data...</p>
                </div>

                <div id="tableContainer" x-show="!isLoading" x-html="tableHtml"
                    style="flex:1; min-height:0; overflow-x:auto; overflow-y:auto;">
                    @include('backend.uno.pages.reports.table', [
                        'reports' => $reports,
                        'totalRow' => null,
                        'currentPage' => 1,
                        'lastPage' => 1,
                        'total' => 0,
                        'filters' => [],
                    ])
                </div>
            </div>

            <!-- <div class="export-row">
                <button class="btn-export btn-export-pdf">
                    <i class="fa-regular fa-file-pdf"></i> Export PDF
                </button>
                <button class="btn-export btn-export-excel">
                    <i class="fa-regular fa-file-excel"></i> Export Excel
                </button>
            </div> -->

        </div>{{-- /tab-stock --}}


        {{-- ════════════════════════════════════
         TAB 2 — DIFFERENCE REPORT
    ════════════════════════════════════ --}}
        <div class="tab-panel" :class="{ 'is-active': activeTab === 'difference' }">

            <div class="filter-section">
                <div class="filter-title">
                    <i class="fa-solid fa-sliders"></i> Filter Options
                </div>

                <div class="filter-grid">

                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" x-model="differenceFilters.from_date">
                    </div>

                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" x-model="differenceFilters.to_date">
                    </div>

                    <div class="form-group">
                        <label>Division</label>
                        <select x-model="differenceFilters.division" @change="onDifferenceDivisionChange()">
                            <option value="">All Divisions</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division['name_en'] }}">{{ $division['name_en'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>District</label>
                        <select x-model="differenceFilters.district" @change="onDifferenceDistrictChange()"
                            :disabled="!differenceFilters.division">
                            <option value="">All Districts</option>
                            <template x-for="district in differenceAvailableDistricts" :key="district.name_en">
                                <option :value="district.name_en" x-text="district.name_en"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Upazila</label>
                        <select x-model="differenceFilters.thana_upazila" :disabled="!differenceFilters.district">
                            <option value="">All Upazilas</option>
                            <template x-for="upazila in differenceAvailableUpazilas" :key="upazila.name_en">
                                <option :value="upazila.name_en" x-text="upazila.name_en"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Company</label>
                        <select x-model="differenceFilters.company_id">
                            <option value="">All Companies</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Depot</label>
                        <select x-model="differenceFilters.depot_id">
                            <option value="">All Depots</option>
                            @foreach ($depots as $depot)
                                <option value="{{ $depot->id }}">{{ $depot->depot_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Filling Station</label>
                        <select x-model="differenceFilters.station_id">
                            <option value="">All Stations</option>
                            @foreach ($stations as $station)
                                <option value="{{ $station->id }}">{{ $station->station_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fuel Type</label>
                        <select x-model="differenceFilters.fuel_type">
                            <option value="">All Types</option>
                            <option value="octane">Octane</option>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="others">Others</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Stock Status</label>
                        <select x-model="differenceFilters.stock_status">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="low">Low Stock</option>
                            <option value="zero">Zero Stock</option>
                            <option value="highdiff">High Difference</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button class="btn-apply" @click="applyDifferenceFilter()" :disabled="isDifferenceLoading">
                            <template x-if="isDifferenceLoading">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </template>
                            <template x-if="!isDifferenceLoading">
                                <i class="fa-solid fa-filter"></i>
                            </template>
                            <span x-text="isDifferenceLoading ? 'Loading...' : 'Apply Filters'"></span>
                        </button>
                        <button class="btn-reset" @click="resetDifferenceFilter()">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </button>
                    </div>

                </div>
            </div>

            {{-- Table section --}}
            <div class="table-section">
                <div class="table-header-row">
                    <div class="table-title">Difference Report</div>
                    <div class="record-count">2 records found</div>
                </div>

                {{-- Static Difference Report Table --}}
                <div class="diff-table-wrapper"
                    style="overflow-y: auto; overflow-x: auto; flex: 1; min-height: 0; max-height: 100%;">
                    <table class="diff-table">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th style="width:72px;">DATE</th>
                                <th style="width:110px;">STATION</th>
                                <th style="width:90px;">TAG OFFICER</th>
                                <th style="width:90px;">DESIGNATION</th>
                                <th style="width:90px;">PHONE</th>
                                <th style="width:70px;">DISTRICT</th>
                                <th style="width:70px;">UPAZILA</th>
                                <th style="width:60px;">FUEL</th>
                                <th style="width: 80px;">DIFFERENCE(L)</th>
                                <th style="width:80px;">DIFFERENCE(%)</th>
                                <th style="width:90px;">ALERT MESSAGE</th>
                                <th style="width:90px;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Row 1 --}}
                            <tr>
                                <td class="row-number">1</td>
                                <td class="td-date">
                                    <div class="date-cell">
                                        08 Jun<br>2026
                                        <span class="date-day">Friday</span>
                                    </div>
                                </td>
                                <td class="td-station">Uttara Filling Station</td>
                                <td class="td-officer">Manik Mia</td>
                                <td class="td-designation">Live Stock Officer</td>
                                <td class="td-phone">01628312158</td>
                                <td class="td-district">Rangpur</td>
                                <td class="td-upazila">Shatkania</td>
                                <td>
                                    <div class="fuel-rows">
                                        <div class="fuel-row"><span class="fuel-type">Octane</span></div>
                                        <div class="fuel-row"><span class="fuel-type">Petrol</span></div>
                                        <div class="fuel-row"><span class="fuel-type">Diesel</span></div>
                                        <div class="fuel-row"><span class="fuel-type">Other</span></div>
                                    </div>
                                </td>
                                <td class="diff-column">
                                    <div class="fuel-rows">
                                        <div class="fuel-row"><span class="fuel-value">200</span></div>
                                        <div class="fuel-row"><span class="fuel-value">800</span></div>
                                        <div class="fuel-row"><span class="fuel-value">00</span></div>
                                        <div class="fuel-row"><span class="fuel-value">180</span></div>
                                    </div>
                                </td>
                                <td class="diff-column">
                                    <div class="fuel-rows">
                                        <div class="fuel-row"><span class="fuel-percent">2%</span></div>
                                        <div class="fuel-row"><span class="fuel-percent">8%</span></div>
                                        <div class="fuel-row"><span class="fuel-percent">00</span></div>
                                        <div class="fuel-row"><span class="fuel-percent">1.8%</span></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fuel-rows">
                                        <div class="fuel-row"><span class="alert-text">Stock Zero</span></div>
                                        <div class="fuel-row"><span class="alert-text">Low stock</span></div>
                                        <div class="fuel-row"><span class="alert-text">High Diff</span></div>
                                        <div class="fuel-row"><span class="alert-text">-</span></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn btn-view">View</button>
                                        <button class="action-btn btn-message"
                                            @click="openMessageModal(1, 'Uttara Filling Station')">Message</button>
                                        <button class="action-btn btn-delete"
                                            @click="openDeleteModal(1, 'Uttara Filling Station')">Delete</button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Row 2 --}}
                            <tr>
                                <td class="row-number">2</td>
                                <td class="td-date">
                                    <div class="date-cell">
                                        08 Jun<br>2026
                                        <span class="date-day">Friday</span>
                                    </div>
                                </td>
                                <td class="td-station">Uttara Filling Station</td>
                                <td class="td-officer">Manik Mia</td>
                                <td class="td-designation">Live Stock Officer</td>
                                <td class="td-phone">01628312158</td>
                                <td class="td-district">Rangpur</td>
                                <td class="td-upazila">Shatkania</td>
                                <td>
                                    <div class="fuel-rows">
                                        <div class="fuel-row"><span class="fuel-type">Octane</span></div>
                                        <div class="fuel-row"><span class="fuel-type">Petrol</span></div>
                                        <div class="fuel-row"><span class="fuel-type">Diesel</span></div>
                                        <div class="fuel-row"><span class="fuel-type">Other</span></div>
                                    </div>
                                </td>
                                <td class="diff-column">
                                    <div class="fuel-rows">
                                        <div class="fuel-row"><span class="fuel-value">200</span></div>
                                        <div class="fuel-row"><span class="fuel-value">800</span></div>
                                        <div class="fuel-row"><span class="fuel-value">00</span></div>
                                        <div class="fuel-row"><span class="fuel-value">180</span></div>
                                    </div>
                                </td>
                                <td class="diff-column">
                                    <div class="fuel-rows">
                                        <div class="fuel-row"><span class="fuel-percent">2%</span></div>
                                        <div class="fuel-row"><span class="fuel-percent">8%</span></div>
                                        <div class="fuel-row"><span class="fuel-percent">00</span></div>
                                        <div class="fuel-row"><span class="fuel-percent">1.8%</span></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fuel-rows">
                                        <div class="fuel-row"><span class="alert-text">Stock Zero</span></div>
                                        <div class="fuel-row"><span class="alert-text">Low stock</span></div>
                                        <div class="fuel-row"><span class="alert-text">High Diff</span></div>
                                        <div class="fuel-row"><span class="alert-text">-</span></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn btn-view">View</button>
                                        <button class="action-btn btn-message"
                                            @click="openMessageModal(2, 'Uttara Filling Station')">Message</button>
                                        <button class="action-btn btn-delete"
                                            @click="openDeleteModal(2, 'Uttara Filling Station')">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Static Difference Report Table --}}
            <div class="diff-table-wrapper">
                <table class="diff-table">
                    <thead>
                        <tr>
                            <th style="width:28px;">#</th>
                            <th style="width:62px;">DATE</th>
                            <th style="width:90px;">STATION</th>
                            <th style="width:72px;">TAG OFFICER</th>
                            <th style="width:80px;">DESIGNATION</th>
                            <th style="width:82px;">PHONE</th>
                            <th style="width:62px;">DISTRICT</th>
                            <th style="width:62px;">UPAZILA</th>
                            <th style="width:50px;">FUEL</th>
                            <th style="width:72px;">DIFFERENCE(L)</th>
                            <th style="width:72px;">DIFFERENCE(%)</th>
                            <th style="width:76px;">ALERT MESSAGE</th>
                            <th style="width:76px;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Row 1 --}}
                        <tr>
                            <td class="row-number">1</td>
                            <td class="td-date">
                                <div class="date-cell">
                                    08 Jun<br>2026
                                    <span class="date-day">Friday</span>
                                </div>
                            </td>
                            <td class="td-station">Uttara Filling Station</td>
                            <td class="td-officer">Manik Mia</td>
                            <td class="td-designation">Live Stock Officer</td>
                            <td class="td-phone">01628312158</td>
                            <td class="td-district">Rangpur</td>
                            <td class="td-upazila">Shatkania</td>
                            <td>
                                <div class="fuel-rows">
                                    <div class="fuel-row"><span class="fuel-type">Octane</span></div>
                                    <div class="fuel-row"><span class="fuel-type">Petrol</span></div>
                                    <div class="fuel-row"><span class="fuel-type">Diesel</span></div>
                                    <div class="fuel-row"><span class="fuel-type">Other</span></div>
                                </div>
                            </td>
                            <td class="diff-column">
                                <div class="fuel-rows">
                                    <div class="fuel-row"><span class="fuel-value">200</span></div>
                                    <div class="fuel-row"><span class="fuel-value">800</span></div>
                                    <div class="fuel-row"><span class="fuel-value">00</span></div>
                                    <div class="fuel-row"><span class="fuel-value">180</span></div>
                                </div>
                            </td>
                            <td class="diff-column">
                                <div class="fuel-rows">
                                    <div class="fuel-row"><span class="fuel-percent">2%</span></div>
                                    <div class="fuel-row"><span class="fuel-percent">8%</span></div>
                                    <div class="fuel-row"><span class="fuel-percent">00</span></div>
                                    <div class="fuel-row"><span class="fuel-percent">1.8%</span></div>
                                </div>
                            </td>
                            <td>
                                <div class="fuel-rows">
                                    <div class="fuel-row"><span class="alert-text">Stock Zero</span></div>
                                    <div class="fuel-row"><span class="alert-text">Low stock</span></div>
                                    <div class="fuel-row"><span class="alert-text">High Diff</span></div>
                                    <div class="fuel-row"><span class="alert-text">-</span></div>
                                </div>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn btn-view">View</button>
                                    <button class="action-btn btn-message"
                                        @click="openMessageModal(1, 'Uttara Filling Station')">Message</button>
                                    <button class="action-btn btn-delete"
                                        @click="openDeleteModal(1, 'Uttara Filling Station')">Delete</button>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 2 --}}
                        <tr>
                            <td class="row-number">2</td>
                            <td class="td-date">
                                <div class="date-cell">
                                    08 Jun<br>2026
                                    <span class="date-day">Friday</span>
                                </div>
                            </td>
                            <td class="td-station">Uttara Filling Station</td>
                            <td class="td-officer">Manik Mia</td>
                            <td class="td-designation">Live Stock Officer</td>
                            <td class="td-phone">01628312158</td>
                            <td class="td-district">Rangpur</td>
                            <td class="td-upazila">Shatkania</td>
                            <td>
                                <div class="fuel-rows">
                                    <div class="fuel-row"><span class="fuel-type">Octane</span></div>
                                    <div class="fuel-row"><span class="fuel-type">Petrol</span></div>
                                    <div class="fuel-row"><span class="fuel-type">Diesel</span></div>
                                    <div class="fuel-row"><span class="fuel-type">Other</span></div>
                                </div>
                            </td>
                            <td class="diff-column">
                                <div class="fuel-rows">
                                    <div class="fuel-row"><span class="fuel-value">200</span></div>
                                    <div class="fuel-row"><span class="fuel-value">800</span></div>
                                    <div class="fuel-row"><span class="fuel-value">00</span></div>
                                    <div class="fuel-row"><span class="fuel-value">180</span></div>
                                </div>
                            </td>
                            <td class="diff-column">
                                <div class="fuel-rows">
                                    <div class="fuel-row"><span class="fuel-percent">2%</span></div>
                                    <div class="fuel-row"><span class="fuel-percent">8%</span></div>
                                    <div class="fuel-row"><span class="fuel-percent">00</span></div>
                                    <div class="fuel-row"><span class="fuel-percent">1.8%</span></div>
                                </div>
                            </td>
                            <td>
                                <div class="fuel-rows">
                                    <div class="fuel-row"><span class="alert-text">Stock Zero</span></div>
                                    <div class="fuel-row"><span class="alert-text">Low stock</span></div>
                                    <div class="fuel-row"><span class="alert-text">High Diff</span></div>
                                    <div class="fuel-row"><span class="alert-text">-</span></div>
                                </div>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn btn-view">View</button>
                                    <button class="action-btn btn-message"
                                        @click="openMessageModal(2, 'Uttara Filling Station')">Message</button>
                                    <button class="action-btn btn-delete"
                                        @click="openDeleteModal(2, 'Uttara Filling Station')">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>{{-- /tab-difference --}}


        {{-- ════════════════════════════════════
         TAB 3 — PENDING (placeholder)
    ════════════════════════════════════ --}}
        <div class="tab-panel" :class="{ 'is-active': activeTab === 'pending' }">
            <div class="loading-overlay" style="padding: 80px 20px;">
                <i class="fa-solid fa-clock-rotate-left"
                    style="font-size:2rem; opacity:.2; display:block; margin-bottom:10px;"></i>
                <p style="font-size:.95rem; font-weight:700; color: var(--muted);">Pending Sales Reports</p>
                <p style="font-size:.80rem; color: #94a3b8; margin-top:4px;">Pending sales will be shown here once
                    configured</p>
            </div>
        </div>


        {{-- ── MESSAGE MODAL ── --}}
        <div class="modal-backdrop" x-show="messageModal.isOpen" x-transition @click.self="messageModal.isOpen = false">
            <div class="modal-box">
                <div class="modal-title">
                    <i class="fa-solid fa-paper-plane" style="color:#22c55e;"></i>
                    Send Message
                </div>
                <p class="modal-subtitle">To: <strong x-text="messageModal.stationName"></strong></p>
                <textarea x-model="messageModal.text" placeholder="Type your message here..."></textarea>
                <div class="modal-footer">
                    <button class="btn-cancel" @click="messageModal.isOpen = false">Cancel</button>
                    <button class="btn-send" @click="submitMessage()">
                        <i class="fa-solid fa-paper-plane" style="margin-right:5px;"></i> Send
                    </button>
                </div>
            </div>
        </div>

        {{-- ── DELETE MODAL ── --}}
        <div class="modal-backdrop" x-show="deleteModal.isOpen" x-transition @click.self="deleteModal.isOpen = false">
            <div class="modal-box">
                <div class="confirm-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="confirm-message">
                    Are you sure you want to delete the report for<br>
                    <strong x-text="deleteModal.stationName"></strong>?<br>
                    <span style="font-size:.80rem; color:#94a3b8;">This action cannot be undone.</span>
                </div>
                <div class="modal-footer" style="justify-content:center;">
                    <button class="btn-cancel" @click="deleteModal.isOpen = false">Cancel</button>
                    <button class="btn-delete-confirm" @click="submitDelete()">
                        <i class="fa-solid fa-trash" style="margin-right:5px;"></i> Delete
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /rpt-card --}}
@endsection

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
       function reportApp() {
    return {
 
        // ── State ──────────────────────────────────────────
        activeTab: 'stock',
        isLoading: false,
        tableHtml: '',
        recordCountText: '',
 
        filters: {
            from_date: '',
            to_date: '',
            division: '',
            district: '',       // ★ init() এ DC district দিয়ে set হবে
            thana_upazila: '',
            company_id: '',
            depot_id: '',
            station_id: '',
            fuel_type: '',
            stock_status: '',
        },
 
        currentPage: 1,
 
        availableDistricts: [],
        availableUpazilas: [],  // ★ init() এ DC district এর upazilas দিয়ে fill হবে
        allDivisions: @json($divisions),
 
        // ★ DC এর district — blade থেকে একবার নেওয়া, পরে সবসময় এটাই ব্যবহার হবে
        dcDistrict: '{{ $dc_district }}',
 
        // Difference Report Filters
        isDifferenceLoading: false,
        differenceFilters: {
            from_date: '',
            to_date: '',
            division: '',
            district: '',
            thana_upazila: '',
            company_id: '',
            depot_id: '',
            station_id: '',
            fuel_type: '',
            stock_status: '',
        },
        differenceAvailableDistricts: [],
        differenceAvailableUpazilas: [],
 
        // Modal states
        messageModal: { isOpen: false, reportId: null, stationName: '', text: '' },
        deleteModal:  { isOpen: false, reportId: null, stationName: '' },
 
        // ── Init (একটাই) ───────────────────────────────────
        init() {
            // 1. Initial server-rendered HTML capture
            const container = document.getElementById('tableContainer');
            if (container) {
                this.tableHtml = container.innerHTML;
            }
 
            // 2. ★ DC এর district সবসময় locked
            this.filters.district = this.dcDistrict;
 
            // 3. ★ DC district এর upazilas বের করে availableUpazilas এ set করো
            for (const div of this.allDivisions) {
                const found = (div.districts ?? []).find(
                    d => d.name_en.toLowerCase() === this.dcDistrict.toLowerCase()
                );
                if (found) {
                    // location.json এ key হলো 'upazilas' অথবা 'police_stations' — দুটোই চেক
                    this.availableUpazilas = found.upazilas ?? found.police_stations ?? [];
                    break;
                }
            }
        },
 
        // ── Tab switch ─────────────────────────────────────
        switchTab(tabName) {
            this.activeTab = tabName;
        },
 
        // ── DC তে division/district cascade দরকার নেই ──────
        onDivisionChange() {},
        onDistrictChange() {},
 
        // ── Difference Report: Division → Districts ─────────
        onDifferenceDivisionChange() {
            this.differenceFilters.district      = '';
            this.differenceFilters.thana_upazila = '';
            this.differenceAvailableUpazilas     = [];
 
            const found = this.allDivisions.find(d => d.name_en === this.differenceFilters.division);
            this.differenceAvailableDistricts = found?.districts ?? [];
        },
 
        // ── Difference Report: District → Upazilas ──────────
        onDifferenceDistrictChange() {
            this.differenceFilters.thana_upazila = '';
 
            const division = this.allDivisions.find(d => d.name_en === this.differenceFilters.division);
            const district = division?.districts?.find(d => d.name_en === this.differenceFilters.district);
            this.differenceAvailableUpazilas = district?.upazilas ?? district?.police_stations ?? [];
        },
 
        // ── Build query params ──────────────────────────────
        buildQueryParams(page = 1) {
            const params = new URLSearchParams();
            Object.entries(this.filters).forEach(([key, value]) => {
                if (value !== '' && value !== null) {
                    params.append(key, value);
                }
            });
            params.append('page', page);
            return params;
        },
 
        // ── Apply Filter (AJAX) ─────────────────────────────
        async applyFilter(page = 1) {
            this.isLoading    = true;
            this.currentPage  = page;
 
            try {
                const response = await fetch(
                    `{{ route('uno.reports.index') }}?${this.buildQueryParams(page).toString()}`,
                    {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    }
                );
 
                if (!response.ok) throw new Error(`Server error ${response.status}`);
 
                const data = await response.json();
 
                if (data.success) {
                    this.tableHtml       = data.html;
                    this.recordCountText = data.total > 0 ? `${data.total} station(s) found` : '';
                }
 
            } catch (error) {
                this.tableHtml = `
                    <div style="padding:40px; text-align:center; color:#ef4444;">
                        <i class="fa-solid fa-circle-exclamation" style="font-size:1.5rem; margin-bottom:8px; display:block;"></i>
                        <p>Failed to load data. Please try again.</p>
                        <small style="color:#94a3b8;">${error.message}</small>
                    </div>
                `;
            } finally {
                this.isLoading = false;
            }
        },
 
        goToPage(page) {
            if (page < 1) return;
            this.applyFilter(page);
        },
 
        // ── Reset Filter ────────────────────────────────────
        // ★ district reset করলেও dcDistrict দিয়ে আবার set হচ্ছে
        async resetFilter() {
            this.filters = {
                from_date:     '',
                to_date:       '',
                division:      '',
                district:      this.dcDistrict,  // ★ সবসময় DC এর district
                thana_upazila: '',
                company_id:    '',
                depot_id:      '',
                station_id:    '',
                fuel_type:     '',
                stock_status:  '',
            };
 
            this.recordCountText = '';
            this.currentPage     = 1;
            // availableUpazilas reset করতে হবে না — DC এর district fixed তাই list same থাকবে
 
            await this.applyFilter(1);
        },
 
        // ── Difference Report: Apply Filter ─────────────────
        async applyDifferenceFilter() {
            this.isDifferenceLoading = true;
            setTimeout(() => {
                this.isDifferenceLoading = false;
                console.log('Difference filters applied:', this.differenceFilters);
            }, 500);
        },
 
        resetDifferenceFilter() {
            this.differenceFilters = {
                from_date: '', to_date: '', division: '', district: '',
                thana_upazila: '', company_id: '', depot_id: '',
                station_id: '', fuel_type: '', stock_status: '',
            };
            this.differenceAvailableDistricts = [];
            this.differenceAvailableUpazilas  = [];
        },
 
        // ── Message modal ───────────────────────────────────
        openMessageModal(reportId, stationName) {
            this.messageModal = { isOpen: true, reportId, stationName, text: '' };
        },
 
        async submitMessage() {
            if (!this.messageModal.text.trim()) {
                alert('Please enter a message.');
                return;
            }
            try {
                const csrf     = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch('{{ route('uno.reports.message') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ report_id: this.messageModal.reportId, message: this.messageModal.text }),
                });
                const data = await response.json();
                this.messageModal.isOpen = false;
                alert(data.success ? 'Message sent successfully!' : 'Failed to send message.');
            } catch {
                alert('Network error. Please try again.');
            }
        },
 
        // ── Delete modal ────────────────────────────────────
        openDeleteModal(reportId, stationName) {
            this.deleteModal = { isOpen: true, reportId, stationName };
        },
 
        async submitDelete() {
            try {
                const csrf      = document.querySelector('meta[name="csrf-token"]').content;
                const deleteUrl = '{{ route('uno.reports.destroy', ':id') }}'.replace(':id', this.deleteModal.reportId);
                const response  = await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                const data = await response.json();
                if (data.success) {
                    this.deleteModal.isOpen = false;
                    await this.applyFilter(this.currentPage);
                } else {
                    alert('Failed to delete report.');
                }
            } catch {
                alert('Network error. Please try again.');
            }
        },
 
    };
}
    </script>
@endpush
