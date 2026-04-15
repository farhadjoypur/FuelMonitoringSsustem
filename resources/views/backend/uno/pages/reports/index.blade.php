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
        .table-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.export-row {
    display: flex;
    align-items: center;
}

.record-count {
    font-size: 14px;
    color: #666;
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
            min-width: 100%;
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
            /* border-top: 1px solid var(--border); */
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

    <div class="rpt-card" x-data="reportApp()" x-init="init(
        '{{ request('seeall') }}',
        '{{ request('from_date') }}',
        '{{ request('to_date') }}'
    )">

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
            <button class="tab-btn" :class="{ 'is-active': activeTab === 'missing' }" @click="switchTab('missing')">
                <i class="fa-solid fa-clock-rotate-left" style="font-size:.78rem;"></i>
                Tag Officers Missing Reports
            </button>
            <button class="tab-btn" :class="{ 'is-active': activeTab === 'submitted' }" @click="switchTab('submitted')">
                <i class="fa-solid fa-circle-check" style="font-size:.78rem;"></i>
                Tag Officers Submit Reports
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

                    {{-- District — UNO এর নিজের district, locked --}}
                    <div class="form-group">
                        <label>District</label>
                        <input type="text" :value="unoDistrict" readonly
                            style="background:#f0f0f0; cursor:not-allowed;">
                        <input type="hidden" x-model="filters.district">
                    </div>

                    {{-- Upazila — UNO এর নিজের upazila, locked --}}
                    <div class="form-group">
                        <label>Upazila</label>
                        <input type="text" :value="unoUpazila" readonly
                            style="background:#f0f0f0; cursor:not-allowed;">
                        <input type="hidden" x-model="filters.thana_upazila">
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

                    <div class="form-group" style="position: relative;">
                        <label>Filling Station</label>
                        <div style="position: relative;">
                            <input type="text" x-model="stationSearch" @focus="stationOpen = true"
                                @click="stationOpen = true" @keydown.escape="stationOpen = false"
                                placeholder="Search station..." autocomplete="off"
                                style="width:100%; padding-right:30px; box-sizing:border-box;" />
                            <button x-show="stationSelected" @click="clearStation()" type="button"
                                style="position:absolute; right:8px; top:50%; transform:translateY(-50%);
                   background:none; border:none; cursor:pointer; color:#94a3b8;">✕</button>
                        </div>

                        <div x-show="stationOpen" @click.outside="stationOpen = false"
                            style="position:absolute; top:100%; left:0; right:0; background:#fff;
               border:1px solid #e2e8f0; border-radius:6px; z-index:999;
               max-height:220px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,.1);">

                            <div @click="clearStation(); stationOpen = false;"
                                style="padding:8px 12px; font-size:13px; cursor:pointer; color:#64748b;"
                                @mouseover="$el.style.background='#f8fafc'" @mouseleave="$el.style.background=''">All
                                Stations</div>

                            <template x-for="s in filteredStations" :key="s.id">
                                <div @click="selectStation(s)" x-text="s.name"
                                    style="padding:8px 12px; font-size:13px; cursor:pointer;"
                                    @mouseover="$el.style.background='#f8fafc'" @mouseleave="$el.style.background=''"></div>
                            </template>

                            <div x-show="filteredStations.length === 0"
                                style="padding:8px 12px; font-size:13px; color:#94a3b8;">No stations found</div>
                        </div>
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

            <div class="table-section">

    <div class="table-header-row">

        <div class="table-title">
            Stock & Sales Reports
        </div>

        <div class="header-right">

            <div class="record-count" x-text="recordCountText"></div>

            <button class="btn-export btn-export-pdf" @click="exportPdf()">
                <i class="fa-regular fa-file-pdf"></i> Export PDF
            </button>

        </div>

    </div>

    <div class="loading-overlay" x-show="isLoading" style="display:none;">
        <div class="loading-spinner"></div>
        <p style="font-size:.85rem;">Loading data...</p>
    </div>

    <div id="tableContainer"
        x-show="!isLoading"
        x-html="tableHtml"
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

        </div>{{-- /tab-stock --}}


        {{-- ════════════════════════════════════
             TAB 2 — DIFFERENCE REPORT
        ════════════════════════════════════ --}}
        <div class="tab-panel" :class="{ 'is-active': activeTab === 'difference' }">
            @include('backend.uno.pages.reports.difference_table', [
                'divisions' => $divisions,
                'companies' => $companies,
                'stations' => $stations,
            ])
           
        </div>


        {{-- ════════════════════════════════════
             TAB 3 — MISSING REPORT
        ════════════════════════════════════ --}}
        <div class="tab-panel" :class="{ 'is-active': activeTab === 'missing' }">
            @include('backend.uno.pages.reports.missing_report_table', [
                'divisions' => $divisions,
                'companies' => $companies,
                'depots' => $depots,
                'stations' => $stations,
            ])

           
        </div>


        {{-- ════════════════════════════════════
             TAB 4 — SUBMITTED REPORT
        ════════════════════════════════════ --}}
        <div class="tab-panel" :class="{ 'is-active': activeTab === 'submitted' }">
            @include('backend.uno.pages.reports.submit_report_table', [
                'divisions' => $divisions,
                'companies' => $companies,
                'depots' => $depots,
                'stations' => $stations,
            ])

           
        </div>


        {{-- ── MESSAGE MODAL ── --}}
        {{-- <div class="modal-backdrop" x-show="messageModal.isOpen" x-transition @click.self="messageModal.isOpen = false">
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
        </div> --}}

        {{-- ── DELETE MODAL ── --}}
        {{-- <div class="modal-backdrop" x-show="deleteModal.isOpen" x-cloak x-transition
            @click.self="deleteModal.isOpen = false">
            <div class="modal-box">
                <div class="confirm-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
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
        </div> --}}

    </div>{{-- /rpt-card --}}
@endsection


@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function reportApp() {
            return {

                // ─────────────────────────────────────────────────────────
                // SHARED STATE
                // ─────────────────────────────────────────────────────────

                activeTab: 'stock',
                unoDistrict: '{{ $uno_district }}',
                unoUpazila: '{{ $uno_upazila }}',
                allDivisions: @json($divisions),

                messageModal: {
                    isOpen: false,
                    reportId: null,
                    stationName: '',
                    text: ''
                },
                deleteModal: {
                    isOpen: false,
                    reportId: null,
                    stationName: ''
                },

                // ─────────────────────────────────────────────────────────
                // TAB 1 — STOCK STATE
                // ─────────────────────────────────────────────────────────

                isLoading: false,
                tableHtml: '',
                recordCountText: '',
                currentPage: 1,

                filters: {
                    from_date: '',
                    to_date: '',
                    division: '',
                    district: '', // init এ unoDistrict দিয়ে set হবে
                    thana_upazila: '', // init এ unoUpazila দিয়ে set হবে
                    company_id: '',
                    depot_id: '',
                    station_id: '',
                    fuel_type: '',
                    stock_status: '',
                },

                // ─────────────────────────────────────────────────────────
                // TAB 2 — DIFFERENCE STATE
                // ─────────────────────────────────────────────────────────

                isDiffLoading: false,
                diffReportRows: [],
                diffTotalRecords: 0,
                diffCurrentPage: 1,
                diffPerPage: 10,
                diffTotalPages: 1,

                diffFilter: {
                    from_date: '',
                    to_date: '',
                    district: '', // init এ unoDistrict set হবে
                    thana_upazila: '', // init এ unoUpazila set হবে
                    company_id: '',
                    station_id: '',
                    tag_officer: '',
                    diff_status: '',
                    min_diff_l: '',
                    min_diff_pct: '',
                },
                stationSearch: '',
                stationOpen: false,
                stationSelected: null,
                allStations: @json($stations->map(fn($s) => ['id' => $s->id, 'name' => $s->station_name])),

                diffAvailableUpazilas: [], // init এ load হবে

                // ─────────────────────────────────────────────────────────
                // TAB 3 — MISSING STATE
                // ─────────────────────────────────────────────────────────

                isMissingLoading: false,
                missingReportRows: [],
                missingTotalRecords: 0,
                missingCurrentPage: 1,
                missingPerPage: 10,
                missingTotalPages: 1,

                missingFilter: {
                    from_date: '',
                    to_date: '',
                    district: '', // init এ unoDistrict set হবে
                    thana_upazila: '', // init এ unoUpazila set হবে
                    company_id: '',
                    depot_id: '',
                    station_id: '',
                },

                missingAvailableUpazilas: [], // init এ load হবে

                // ─────────────────────────────────────────────────────────
                // TAB 4 — SUBMITTED STATE
                // ─────────────────────────────────────────────────────────

                isSubmitLoading: false,
                submitReportRows: [],
                submitTotalRecords: 0,
                submitCurrentPage: 1,
                submitPerPage: 10,
                submitTotalPages: 1,

                submitFilter: {
                    from_date: '',
                    to_date: '',
                    district: '', // init এ unoDistrict set হবে
                    thana_upazila: '', // init এ unoUpazila set হবে
                    company_id: '',
                    depot_id: '',
                    station_id: '',
                },

                submitAvailableUpazilas: [], // init এ load হবে


                // ═════════════════════════════════════════════════════════
                // INIT
                // ═════════════════════════════════════════════════════════

                init(seeall = '', fromDate = '', toDate = '') {
                    document.addEventListener('click', (e) => {
                        if (!e.target.closest('.form-group')) {
                            this.stationOpen = false;
                        }
                    });

                    const container = document.getElementById('tableContainer');
                    if (container) this.tableHtml = container.innerHTML;

                    // ── UNO district + upazila lock — সব tab এ ──
                    this.filters.district = this.unoDistrict;
                    this.filters.thana_upazila = this.unoUpazila;
                    this.diffFilter.district = this.unoDistrict;
                    this.diffFilter.thana_upazila = this.unoUpazila;
                    this.missingFilter.district = this.unoDistrict;
                    this.missingFilter.thana_upazila = this.unoUpazila;
                    this.submitFilter.district = this.unoDistrict;
                    this.submitFilter.thana_upazila = this.unoUpazila;

                    // ── আজকের date default set করো সব tab এ ──
                    const today = new Date().toISOString().split('T')[0];

                    if (!this.filters.from_date) this.filters.from_date = today;
                    if (!this.filters.to_date) this.filters.to_date = today;

                    if (!fromDate) {
                        this.diffFilter.from_date = today;
                        this.diffFilter.to_date = today;
                    }

                    this.missingFilter.from_date = today;
                    this.missingFilter.to_date = today;
                    this.submitFilter.from_date = today;
                    this.submitFilter.to_date = today;

                    // ── seeall param দিয়ে tab switch ──
                    if (seeall === 'difference') {
                        if (fromDate) this.diffFilter.from_date = fromDate;
                        if (toDate) this.diffFilter.to_date = toDate;
                        if (fromDate && !toDate) this.diffFilter.to_date = fromDate;

                        this.activeTab = 'difference';
                        this.$nextTick(() => this.applyDiffFilter());

                    } else if (seeall === 'missing') {
                        this.activeTab = 'missing';
                        this.$nextTick(() => this.applyMissingFilter());

                    } else if (seeall === 'submitted') {
                        this.activeTab = 'submitted';
                        this.$nextTick(() => this.applySubmitFilter());

                    } else {
                        this.activeTab = 'stock';
                        this.recordCountText = '';
                    }
                },

                get filteredStations() {
                    if (!this.stationSearch) return this.allStations;
                    return this.allStations.filter(s =>
                        s.name.toLowerCase().includes(this.stationSearch.toLowerCase())
                    );
                },

                selectStation(s) {
                    this.stationSelected = s;
                    this.stationSearch = s.name;
                    this.filters.station_id = s.id;
                    this.stationOpen = false;
                },

                clearStation() {
                    this.stationSelected = null;
                    this.stationSearch = '';
                    this.filters.station_id = '';
                    this.stationOpen = true;
                },

                // ─────────────────────────────────────────────────────────
                // TAB SWITCH
                // ─────────────────────────────────────────────────────────

                switchTab(tab) {
                    this.activeTab = tab;

                    if (tab === 'difference' && this.diffTotalRecords === 0) {
                        this.applyDiffFilter();
                    }
                    if (tab === 'missing' && this.missingTotalRecords === 0) {
                        this.applyMissingFilter();
                    }
                    if (tab === 'submitted' && this.submitTotalRecords === 0) {
                        this.applySubmitFilter();
                    }
                },


                // ═════════════════════════════════════════════════════════
                // TAB 1 — STOCK METHODS
                // ═════════════════════════════════════════════════════════

                buildStockParams(page = 1) {
                    const params = new URLSearchParams({
                        page
                    });
                    Object.entries(this.filters).forEach(([key, value]) => {
                        if (value !== '' && value !== null) params.append(key, value);
                    });
                    return params;
                },

                async applyFilter(page = 1) {
                    this.isLoading = true;
                    this.currentPage = page;
                    try {
                        const res = await fetch(
                            `{{ route('uno.reports.index') }}?${this.buildStockParams(page)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            }
                        );
                        if (!res.ok) throw new Error(`Server error ${res.status}`);
                        const data = await res.json();
                        if (data.success) {
                            this.tableHtml = data.html;
                            this.recordCountText = data.total > 0 ? `${data.total} station(s) found` : '';
                        }
                    } catch (err) {
                        this.tableHtml = `<div style="padding:40px;text-align:center;color:#ef4444;">
                            <i class="fa-solid fa-circle-exclamation" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>
                            <p>Failed to load data.</p><small>${err.message}</small></div>`;
                    } finally {
                        this.isLoading = false;
                    }
                },

                goToPage(page) {
                    if (page < 1) return;
                    this.applyFilter(page);
                },

                resetFilter() {
                    // district + upazila reset করবে না — UNO এর jurisdiction সবসময় locked
                    this.filters.from_date = '';
                    this.filters.to_date = '';
                    this.filters.company_id = '';
                    this.filters.depot_id = '';
                    this.filters.station_id = '';
                    this.filters.fuel_type = '';
                    this.filters.stock_status = '';
                    this.recordCountText = '';
                    this.currentPage = 1;
                },


                // ═════════════════════════════════════════════════════════
                // TAB 2 — DIFFERENCE METHODS
                // ═════════════════════════════════════════════════════════

                buildDiffParams(page = 1) {
                    const params = new URLSearchParams({
                        page
                    });
                    Object.entries(this.diffFilter).forEach(([key, value]) => {
                        if (value !== '' && value !== null && value !== undefined) {
                            params.append(key, value);
                        }
                    });
                    return params;
                },

                async applyDiffFilter(page = 1) {
                    this.isDiffLoading = true;
                    this.diffCurrentPage = page;
                    try {
                        const res = await fetch(
                            `{{ route('uno.reports.difference') }}?${this.buildDiffParams(page)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            }
                        );
                        if (!res.ok) throw new Error(`Server error ${res.status}`);
                        const json = await res.json();
                        if (json.success) {
                            this.diffReportRows = json.rows;
                            this.diffTotalRecords = json.total;
                            this.diffTotalPages = json.lastPage;
                            this.diffCurrentPage = json.currentPage;
                        }
                    } catch (err) {
                        console.error('Difference report error:', err);
                        this.diffReportRows = [];
                        this.diffTotalRecords = 0;
                    } finally {
                        this.isDiffLoading = false;
                    }
                },

                resetDiffFilter() {
                    // district + upazila reset করবে না
                    this.diffFilter.from_date = '';
                    this.diffFilter.to_date = '';
                    this.diffFilter.company_id = '';
                    this.diffFilter.station_id = '';
                    this.diffFilter.tag_officer = '';
                    this.diffFilter.diff_status = '';
                    this.diffFilter.min_diff_l = '';
                    this.diffFilter.min_diff_pct = '';
                    this.diffReportRows = [];
                    this.diffTotalRecords = 0;
                    this.diffCurrentPage = 1;
                    this.diffTotalPages = 1;
                },

                changeDiffPage(newPage) {
                    if (newPage < 1 || newPage > this.diffTotalPages) return;
                    this.applyDiffFilter(newPage);
                },



                // ═════════════════════════════════════════════════════════
                // TAB 3 — MISSING METHODS
                // ═════════════════════════════════════════════════════════

                buildMissingParams(page = 1) {
                    const params = new URLSearchParams({
                        page
                    });
                    Object.entries(this.missingFilter).forEach(([key, value]) => {
                        if (value !== '' && value !== null && value !== undefined) {
                            params.append(key, value);
                        }
                    });
                    return params;
                },

                async applyMissingFilter(page = 1) {
                    this.isMissingLoading = true;
                    this.missingCurrentPage = page;
                    try {
                        const res = await fetch(
                            `{{ route('uno.reports.missing') }}?${this.buildMissingParams(page)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            }
                        );
                        if (!res.ok) throw new Error(`Server error ${res.status}`);
                        const json = await res.json();
                        if (json.success) {
                            this.missingReportRows = json.rows;
                            this.missingTotalRecords = json.total;
                            this.missingTotalPages = json.lastPage;
                            this.missingCurrentPage = json.currentPage;
                        }
                    } catch (err) {
                        console.error('Missing report error:', err);
                        this.missingReportRows = [];
                        this.missingTotalRecords = 0;
                    } finally {
                        this.isMissingLoading = false;
                    }
                },

                resetMissingFilter() {
                    // district + upazila reset করবে না
                    this.missingFilter.from_date = '';
                    this.missingFilter.to_date = '';
                    this.missingFilter.company_id = '';
                    this.missingFilter.depot_id = '';
                    this.missingFilter.station_id = '';
                    this.missingReportRows = [];
                    this.missingTotalRecords = 0;
                    this.missingCurrentPage = 1;
                    this.missingTotalPages = 1;
                },

                changeMissingPage(newPage) {
                    if (newPage < 1 || newPage > this.missingTotalPages) return;
                    this.applyMissingFilter(newPage);
                },




                // ═════════════════════════════════════════════════════════
                // TAB 4 — SUBMITTED METHODS
                // ═════════════════════════════════════════════════════════

                buildSubmitParams(page = 1) {
                    const params = new URLSearchParams({
                        page
                    });
                    Object.entries(this.submitFilter).forEach(([key, value]) => {
                        if (value !== '' && value !== null && value !== undefined) {
                            params.append(key, value);
                        }
                    });
                    return params;
                },

                async applySubmitFilter(page = 1) {
                    this.isSubmitLoading = true;
                    this.submitCurrentPage = page;
                    try {
                        const res = await fetch(
                            `{{ route('uno.reports.submitted') }}?${this.buildSubmitParams(page)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            }
                        );
                        if (!res.ok) throw new Error(`Server error ${res.status}`);
                        const json = await res.json();
                        if (json.success) {
                            this.submitReportRows = json.rows;
                            this.submitTotalRecords = json.total;
                            this.submitTotalPages = json.lastPage;
                            this.submitCurrentPage = json.currentPage;
                        }
                    } catch (err) {
                        console.error('Submitted report error:', err);
                        this.submitReportRows = [];
                        this.submitTotalRecords = 0;
                    } finally {
                        this.isSubmitLoading = false;
                    }
                },

                resetSubmitFilter() {
                    // district + upazila reset করবে না
                    this.submitFilter.from_date = '';
                    this.submitFilter.to_date = '';
                    this.submitFilter.company_id = '';
                    this.submitFilter.depot_id = '';
                    this.submitFilter.station_id = '';
                    this.submitReportRows = [];
                    this.submitTotalRecords = 0;
                    this.submitCurrentPage = 1;
                    this.submitTotalPages = 1;
                },

                changeSubmitPage(newPage) {
                    if (newPage < 1 || newPage > this.submitTotalPages) return;
                    this.applySubmitFilter(newPage);
                },




                // ═════════════════════════════════════════════════════════
                // SHARED MODAL METHODS
                // ═════════════════════════════════════════════════════════

                openMessageModal(reportId, stationName) {
                    this.messageModal = {
                        isOpen: true,
                        reportId,
                        stationName,
                        text: ''
                    };
                },

                async submitMessage() {
                    if (!this.messageModal.text.trim()) {
                        alert('Please enter a message.');
                        return;
                    }
                    try {
                        const csrf = document.querySelector('meta[name="csrf-token"]').content;
                        const res = await fetch('{{ route('uno.reports.message') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf
                            },
                            body: JSON.stringify({
                                report_id: this.messageModal.reportId,
                                message: this.messageModal.text
                            }),
                        });
                        const data = await res.json();
                        this.messageModal.isOpen = false;
                        alert(data.success ? 'Message sent!' : 'Failed to send message.');
                    } catch {
                        alert('Network error.');
                    }
                },

                openDeleteModal(reportId, stationName) {
                    this.deleteModal = {
                        isOpen: true,
                        reportId,
                        stationName
                    };
                },

                async submitDelete() {
                    try {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrf = csrfMeta ?
                            csrfMeta.getAttribute('content') :
                            document.cookie.split('; ')
                            .find(row => row.startsWith('XSRF-TOKEN='))
                            ?.split('=')[1];

                        if (!csrf) {
                            alert('CSRF token not found. Please refresh the page.');
                            return;
                        }

                        const url = `/uno/reports/${this.deleteModal.reportId}`;

                        const res = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': decodeURIComponent(csrf),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                        });

                        if (!res.ok) throw new Error(`Server error ${res.status}`);

                        const data = await res.json();
                        if (data.success) {
                            this.deleteModal.isOpen = false;
                            if (this.activeTab === 'stock') this.applyFilter(this.currentPage);
                            else if (this.activeTab === 'difference') this.applyDiffFilter(this.diffCurrentPage);
                            else if (this.activeTab === 'missing') this.applyMissingFilter(this.missingCurrentPage);
                            else if (this.activeTab === 'submitted') this.applySubmitFilter(this.submitCurrentPage);
                        } else {
                            alert('Failed to delete.');
                        }
                    } catch (err) {
                        alert('Network error: ' + err.message);
                    }
                },

                exportPdf() {
                    const params = new URLSearchParams();

                    if (this.filters.from_date) params.append('from_date', this.filters.from_date);
                    if (this.filters.to_date) params.append('to_date', this.filters.to_date);
                    if (this.filters.division) params.append('division', this.filters.division);
                    if (this.filters.district) params.append('district', this.filters.district);
                    if (this.filters.thana_upazila) params.append('thana_upazila', this.filters.thana_upazila);
                    if (this.filters.company_id) params.append('company_id', this.filters.company_id);
                    if (this.filters.depot_id) params.append('depot_id', this.filters.depot_id);
                    if (this.filters.station_id) params.append('station_id', this.filters.station_id);
                    if (this.filters.fuel_type) params.append('fuel_type', this.filters.fuel_type);
                    if (this.filters.stock_status) params.append('stock_status', this.filters.stock_status);

                    window.open('{{ route('uno.reports.export.pdf') }}?' + params.toString(), '_blank');
                },

                exportDiffPdf() {
                    const params = new URLSearchParams();
                    if (this.diffFilter.fromDate) params.append('from_date', this.diffFilter.fromDate);
                    if (this.diffFilter.toDate) params.append('to_date', this.diffFilter.toDate);
                    if (this.diffFilter.division) params.append('division', this.diffFilter.division);
                    if (this.diffFilter.district) params.append('district', this.diffFilter.district);
                    if (this.diffFilter.thanaUpazila) params.append('thana_upazila', this.diffFilter.thanaUpazila);
                    if (this.diffFilter.companyId) params.append('company_id', this.diffFilter.companyId);
                    if (this.diffFilter.stationId) params.append('station_id', this.diffFilter.stationId);
                    if (this.diffFilter.tagOfficer) params.append('tag_officer', this.diffFilter.tagOfficer);
                    if (this.diffFilter.diffStatus) params.append('diff_status', this.diffFilter.diffStatus);
                    if (this.diffFilter.minDifferenceL) params.append('min_diff_l', this.diffFilter.minDifferenceL);
                    if (this.diffFilter.minDifferencePercent) params.append('min_diff_pct', this.diffFilter
                        .minDifferencePercent);
                    window.open(`{{ route('uno.reports.export.difference.pdf') }}?${params}`, '_blank');
                },

                exportMissingPdf() {
                    const params = new URLSearchParams();
                    if (this.missingFilter.fromDate) params.append('from_date', this.missingFilter.fromDate);
                    if (this.missingFilter.toDate) params.append('to_date', this.missingFilter.toDate);
                    if (this.missingFilter.division) params.append('division', this.missingFilter.division);
                    if (this.missingFilter.district) params.append('district', this.missingFilter.district);
                    if (this.missingFilter.thanaUpazila) params.append('thana_upazila', this.missingFilter.thanaUpazila);
                    if (this.missingFilter.companyId) params.append('company_id', this.missingFilter.companyId);
                    if (this.missingFilter.depotId) params.append('depot_id', this.missingFilter.depotId);
                    if (this.missingFilter.stationId) params.append('station_id', this.missingFilter.stationId);
                    window.open('{{ route('uno.reports.export.missing.pdf') }}?' + params.toString(), '_blank');
                },

                exportSubmitPdf() {
                    const params = new URLSearchParams();
                    if (this.submitFilter.fromDate) params.append('from_date', this.submitFilter.fromDate);
                    if (this.submitFilter.toDate) params.append('to_date', this.submitFilter.toDate);
                    if (this.submitFilter.division) params.append('division', this.submitFilter.division);
                    if (this.submitFilter.district) params.append('district', this.submitFilter.district);
                    if (this.submitFilter.thanaUpazila) params.append('thana_upazila', this.submitFilter.thanaUpazila);
                    if (this.submitFilter.companyId) params.append('company_id', this.submitFilter.companyId);
                    if (this.submitFilter.depotId) params.append('depot_id', this.submitFilter.depotId);
                    if (this.submitFilter.stationId) params.append('station_id', this.submitFilter.stationId);
                    window.open('{{ route('uno.reports.export.submitted.pdf') }}?' + params.toString(), '_blank');
                },
                // ═══════════════════════════════════════════════════════════
// DELETE REPORT WITH SWEETALERT2 (নতুন)
// ═══════════════════════════════════════════════════════════

async deleteReport(reportId, stationName = '') {
    const result = await Swal.fire({
        title: 'Are you sure?',
        html: stationName 
            ? `Do you want to delete the report for <strong>${stationName}</strong>?` 
            : 'Do you want to delete this report?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: true
    });

    if (result.isConfirmed) {
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const deleteUrl = `{{ route('admin.reports.destroy', ':id') }}`.replace(':id', reportId);

            const response = await fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'The report has been successfully deleted.',
                    icon: 'success',
                    timer: 1800,
                    showConfirmButton: false
                });

                // Refresh the current active tab automatically
                await this.$nextTick(async () => {
                    if (this.activeTab === 'stock') {
                        await this.applyFilter(this.currentPage);
                    } else if (this.activeTab === 'difference') {
                        await this.applyDiffFilter(this.diffCurrentPage);
                    } else if (this.activeTab === 'missing') {
                        await this.applyMissingFilter(this.missingCurrentPage);
                    } else if (this.activeTab === 'submitted') {
                        await this.applySubmitFilter(this.submitCurrentPage);
                    }
                });
            } else {
                Swal.fire('Failed!', data.message || 'Could not delete the report.', 'error');
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error!', 'Something went wrong. Please try again later.', 'error');
        }
    }
},

            }; // end return
        } // end reportApp
    </script>
@endpush
