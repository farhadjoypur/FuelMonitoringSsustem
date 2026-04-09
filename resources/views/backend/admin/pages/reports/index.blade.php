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

                    <div class="form-group">
                        <label>Division</label>
                        <select x-model="filters.division" @change="onDivisionChange()">
                            <option value="">All Divisions</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division['name_en'] }}">{{ $division['name_en'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>District</label>
                        <select x-model="filters.district" @change="onDistrictChange()" :disabled="!filters.division">
                            <option value="">All Districts</option>
                            <template x-for="district in availableDistricts" :key="district.name_en">
                                <option :value="district.name_en" x-text="district.name_en"></option>
                            </template>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Upazila</label>
                        <select x-model="filters.thana_upazila" :disabled="!filters.district">
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

                {{-- Loading overlay --}}
                <div class="loading-overlay" x-show="isLoading" style="display:none;">
                    <div class="loading-spinner"></div>
                    <p style="font-size:.85rem;">Loading data...</p>
                </div>

                {{--
                Table container.
                Initial load: @include renders server-side empty state.
                After filter:  Alpine replaces via x-html="tableHtml".
                x-show hides container while loading.
            --}}
                <div id="tableContainer" x-show="!isLoading" x-html="tableHtml"
                    style="flex:1; min-height:0; overflow-x:auto; overflow-y:auto;">
                    @include('backend.admin.pages.reports.table', [
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

            {{-- difference_table includes --}}
            @include('backend.admin.pages.reports.difference_table', [
                'divisions' => $divisions,
            ])
        </div>{{-- /tab-difference --}}


        {{-- ════════════════════════════════════
         TAB 3 — PENDING (placeholder)
    ════════════════════════════════════ --}}
        <div class="tab-panel" :class="{ 'is-active': activeTab === 'missing' }">
            @include('backend.admin.pages.reports.missing_report_table', [
                'divisions' => $divisions,
                'companies' => $companies,
                'depots' => $depots,
                'stations' => $stations,
            ])
        </div>

        <div class="tab-panel" :class="{ 'is-active': activeTab === 'submitted' }">
            @include('backend.admin.pages.reports.submit_report_table', [
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
        {{-- <div class="modal-backdrop" x-show="deleteModal.isOpen" x-transition @click.self="deleteModal.isOpen = false">
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
        </div> --}}

    </div>{{-- /rpt-card --}}
@endsection
{{-- ═══════════════════════════════════════════════════════════════
     Replace the entire @push('scripts') block in index.blade.php
═══════════════════════════════════════════════════════════════ --}}
@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function reportApp() {
            return {

                // ── Active Tab ──────────────────────────────────────────────
                activeTab: 'stock',

                // ═══════════════════════════════════════════════════════════
                // TAB 1 — SALES & STOCK STATE
                // ═══════════════════════════════════════════════════════════

                isLoading: false,
                tableHtml: '',
                recordCountText: '',
                currentPage: 1,

                // Stock tab filters
                filters: {
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

                // Stock tab cascade dropdowns
                availableDistricts: [],
                availableUpazilas: [],

                // ═══════════════════════════════════════════════════════════
                // TAB 2 — DIFFERENCE REPORT STATE
                // ═══════════════════════════════════════════════════════════

                isDiffLoading: false,
                diffReportRows: [],
                diffTotalRecords: 0,
                diffCurrentPage: 1,
                diffPerPage: 10,
                diffTotalPages: 1,

                // Difference tab filters
                diffFilter: {
                    fromDate: '',
                    toDate: '',
                    minDifferenceL: '',
                    minDifferencePercent: '',
                    division: '',
                    district: '',
                    thanaUpazila: '',
                    companyId: '',
                    stationId: '',
                    tagOfficer: '',
                    diffStatus: '',
                },

                // Difference tab cascade dropdowns
                diffAvailableDistricts: [],
                diffAvailableUpazilas: [],

                // ═══════════════════════════════════════════════════════════
                // SHARED
                // ═══════════════════════════════════════════════════════════

                // Full location JSON (shared by both tabs)
                allDivisions: @json($divisions),

                // Modal states (shared by both tabs)
                messageModal: {
                    isOpen: false,
                    reportId: null,
                    stationName: '',
                    text: '',
                },
                deleteModal: {
                    isOpen: false,
                    reportId: null,
                    stationName: '',
                },

                // ── Init ───────────────────────────────────────────────────
                // init() {
                //     // Keep server-rendered HTML for stock tab on first load
                //     const container = document.getElementById('tableContainer');
                //     if (container) {
                //         this.tableHtml = container.innerHTML;
                //     }
                // },
                init(seeall = '', fromDate = '', toDate = '') {

                    if (seeall === 'difference') {
                        // ✅ diffFilter (s নেই)
                        if (fromDate) this.diffFilter.fromDate = fromDate;
                        if (toDate) this.diffFilter.toDate = toDate;
                        if (fromDate && !toDate) this.diffFilter.toDate = fromDate;

                        this.activeTab = 'difference';
                        this.$nextTick(() => this.applyDiffFilter()); // ✅ this. দিয়ে call

                    } else if (seeall === 'missing') {
                        this.activeTab = 'missing';
                        this.$nextTick(() => this.applyMissingFilter());

                    } else if (seeall === 'submitted') {
                        this.activeTab = 'submitted';
                        this.$nextTick(() => this.applySubmitFilter());

                    } else {
                        this.activeTab = 'stock';
                        // stock tab server-side render হয়, তাই load লাগবে না
                    }
                },

                // ── Tab Switch ─────────────────────────────────────────────
                switchTab(tab) {
                    this.activeTab = tab;

                    if (tab === 'difference' && this.diffReportRows.length === 0) {
                        // this.applyDiffFilter();
                    }
                    if (tab === 'missing' && this.missingReportRows.length === 0) {
                        // this.applyMissingFilter();
                    }
                    if (tab === 'submitted' && this.submitReportRows.length === 0) {
                        // this.applySubmitFilter();
                    }
                },
                // ═══════════════════════════════════════════════════════════
                // TAB 1 — STOCK METHODS
                // ═══════════════════════════════════════════════════════════

                onDivisionChange() {
                    this.filters.district = '';
                    this.filters.thana_upazila = '';
                    this.availableUpazilas = [];

                    const found = this.allDivisions.find(d => d.name_en === this.filters.division);
                    this.availableDistricts = found?.districts ?? [];
                },

                onDistrictChange() {
                    this.filters.thana_upazila = '';

                    const division = this.allDivisions.find(d => d.name_en === this.filters.division);
                    const district = division?.districts?.find(d => d.name_en === this.filters.district);
                    this.availableUpazilas = district?.police_stations ?? [];
                },

                buildStockQueryParams(page = 1) {
                    const params = new URLSearchParams();
                    Object.entries(this.filters).forEach(([key, value]) => {
                        if (value !== '' && value !== null) params.append(key, value);
                    });
                    params.append('page', page);
                    return params;
                },

                async applyFilter(page = 1) {
                    this.isLoading = true;
                    this.currentPage = page;

                    try {
                        const response = await fetch(
                            `{{ route('admin.reports.index') }}?${this.buildStockQueryParams(page)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                            }
                        );

                        if (!response.ok) throw new Error(`Server error ${response.status}`);

                        const data = await response.json();

                        if (data.success) {
                            this.tableHtml = data.html;
                            this.recordCountText = data.total > 0 ?
                                `${data.total} station(s) found` :
                                '';
                        }

                    } catch (error) {
                        this.tableHtml = `
                            <div style="padding:40px; text-align:center; color:#ef4444;">
                                <i class="fa-solid fa-circle-exclamation"
                                   style="font-size:1.5rem; margin-bottom:8px; display:block;"></i>
                                <p>Failed to load data. Please try again.</p>
                                <small style="color:#94a3b8;">${error.message}</small>
                            </div>`;
                    } finally {
                        this.isLoading = false;
                    }
                },

                goToPage(page) {
                    if (page < 1) return;
                    this.applyFilter(page);
                },

                async resetFilter() {
                    this.filters = {
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
                    };
                    this.availableDistricts = [];
                    this.availableUpazilas = [];
                    this.recordCountText = '';
                    this.currentPage = 1;
                    await this.applyFilter(1);
                },

                // ═══════════════════════════════════════════════════════════
                // TAB 2 — DIFFERENCE REPORT METHODS
                // ═══════════════════════════════════════════════════════════

                // Cascade: Division → Districts
                onDiffDivisionChange() {
                    this.diffFilter.district = '';
                    this.diffFilter.thanaUpazila = '';
                    this.diffAvailableDistricts = [];
                    this.diffAvailableUpazilas = [];

                    const found = this.allDivisions.find(d => d.name_en === this.diffFilter.division);
                    this.diffAvailableDistricts = found?.districts ?? [];
                },

                // Cascade: District → Upazilas
                onDiffDistrictChange() {
                    this.diffFilter.thanaUpazila = '';
                    this.diffAvailableUpazilas = [];

                    const division = this.allDivisions.find(d => d.name_en === this.diffFilter.division);
                    const district = division?.districts?.find(d => d.name_en === this.diffFilter.district);
                    // location.json key — police_stations or upazilas
                    this.diffAvailableUpazilas = district?.upazilas ?? district?.police_stations ?? [];
                },

                // Apply difference filter — AJAX to differenceReport()
                async applyDiffFilter(page = 1) {
                    this.isDiffLoading = true;
                    this.diffCurrentPage = page;

                    const params = new URLSearchParams();

                    // Only append non-empty values to keep URL clean
                    const filterMap = {
                        page: page,
                        from_date: this.diffFilter.fromDate,
                        to_date: this.diffFilter.toDate,
                        division: this.diffFilter.division,
                        district: this.diffFilter.district,
                        thana_upazila: this.diffFilter.thanaUpazila,
                        company_id: this.diffFilter.companyId,
                        station_id: this.diffFilter.stationId,
                        tag_officer: this.diffFilter.tagOfficer,
                        diff_status: this.diffFilter.diffStatus,
                        min_diff_l: this.diffFilter.minDifferenceL,
                        min_diff_pct: this.diffFilter.minDifferencePercent,
                    };

                    Object.entries(filterMap).forEach(([key, value]) => {
                        if (value !== '' && value !== null && value !== undefined) {
                            params.append(key, value);
                        }
                    });

                    try {
                        const response = await fetch(
                            `{{ route('admin.reports.difference') }}?${params}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                            }
                        );

                        if (!response.ok) throw new Error(`Server error ${response.status}`);

                        const json = await response.json();

                        if (json.success) {
                            this.diffReportRows = json.rows;
                            this.diffTotalRecords = json.total;
                            this.diffTotalPages = json.lastPage;
                            this.diffCurrentPage = json.currentPage;
                        }

                    } catch (error) {
                        console.error('Difference report fetch failed:', error);
                        this.diffReportRows = [];
                        this.diffTotalRecords = 0;
                    } finally {
                        this.isDiffLoading = false;
                    }
                },

                // Reset difference filters & clear table
                resetDiffFilter() {
                    this.diffFilter = {
                        fromDate: '',
                        toDate: '',
                        minDifferenceL: '',
                        minDifferencePercent: '',
                        division: '',
                        district: '',
                        thanaUpazila: '',
                        companyId: '',
                        stationId: '',
                        tagOfficer: '',
                        diffStatus: '',
                    };
                    this.diffAvailableDistricts = [];
                    this.diffAvailableUpazilas = [];
                    this.diffReportRows = [];
                    this.diffTotalRecords = 0;
                    this.diffCurrentPage = 1;
                    this.diffTotalPages = 1;
                },

                // Pagination for difference tab
                changeDiffPage(newPage) {
                    if (newPage < 1 || newPage > this.diffTotalPages) return;
                    this.applyDiffFilter(newPage);
                },

                // View single report
                viewDiffReport(reportId) {
                    window.location.href = `/admin/reports/${reportId}`;
                },

                // Export difference report as PDF
                exportDiffPdf() {
                    const params = new URLSearchParams();
                    const exportFields = {
                        from_date: this.diffFilter.fromDate,
                        to_date: this.diffFilter.toDate,
                        division: this.diffFilter.division,
                        district: this.diffFilter.district,
                        company_id: this.diffFilter.companyId,
                        station_id: this.diffFilter.stationId,
                    };
                    Object.entries(exportFields).forEach(([k, v]) => {
                        if (v) params.append(k, v);
                    });
                    window.open(`{{ route('admin.reports.difference.export-pdf') }}?${params}`, '_blank');
                },

                // ═══════════════════════════════════════════════════════════
                // SHARED MODAL METHODS
                // ═══════════════════════════════════════════════════════════

                openMessageModal(reportId, stationName) {
                    this.messageModal = {
                        isOpen: true,
                        reportId,
                        stationName,
                        text: '',
                    };
                },

                async submitMessage() {
                    if (!this.messageModal.text.trim()) {
                        alert('Please enter a message.');
                        return;
                    }
                    try {
                        const csrf = document.querySelector('meta[name="csrf-token"]').content;
                        const response = await fetch('{{ route('admin.reports.message') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({
                                report_id: this.messageModal.reportId,
                                message: this.messageModal.text,
                            }),
                        });
                        const data = await response.json();
                        this.messageModal.isOpen = false;
                        alert(data.success ? 'Message sent successfully!' : 'Failed to send message.');
                    } catch {
                        alert('Network error. Please try again.');
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
                        const csrf = document.querySelector('meta[name="csrf-token"]').content;
                        const deleteUrl = '{{ route('admin.reports.destroy', ':id') }}'
                            .replace(':id', this.deleteModal.reportId);

                        const response = await fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                        });
                        const data = await response.json();

                        if (data.success) {
                            this.deleteModal.isOpen = false;

                            // Refresh whichever tab triggered the delete
                            if (this.activeTab === 'stock') {
                                await this.applyFilter(this.currentPage);
                            } else {
                                await this.applyDiffFilter(this.diffCurrentPage);
                            }
                        } else {
                            alert('Failed to delete report.');
                        }
                    } catch {
                        alert('Network error. Please try again.');
                    }
                },


                isMissingLoading: false,
                missingReportRows: [],
                missingTotalRecords: 0,
                missingCurrentPage: 1,
                missingPerPage: 10,
                missingTotalPages: 1,

                missingFilter: {
                    fromDate: '',
                    toDate: '',
                    division: '',
                    district: '',
                    thanaUpazila: '',
                    companyId: '',
                    depotId: '',
                    stationId: '',
                },

                missingAvailableDistricts: [],
                missingAvailableUpazilas: [],

                // ═══════════════════════════════════════════════════════════════
                // TAB 4 — TAG OFFICERS SUBMITTED REPORT STATE
                // ═══════════════════════════════════════════════════════════════

                isSubmitLoading: false,
                submitReportRows: [],
                submitTotalRecords: 0,
                submitCurrentPage: 1,
                submitPerPage: 10,
                submitTotalPages: 1,

                submitFilter: {
                    fromDate: '',
                    toDate: '',
                    division: '',
                    district: '',
                    thanaUpazila: '',
                    companyId: '',
                    depotId: '',
                    stationId: '',
                },

                submitAvailableDistricts: [],
                submitAvailableUpazilas: [],


                // ═══════════════════════════════════════════════════════════════
                // TAB 3 — MISSING REPORT METHODS
                // ═══════════════════════════════════════════════════════════════

                // Cascade: Division → Districts
                onMissingDivisionChange() {
                    this.missingFilter.district = '';
                    this.missingFilter.thanaUpazila = '';
                    this.missingAvailableDistricts = [];
                    this.missingAvailableUpazilas = [];

                    const found = this.allDivisions.find(d => d.name_en === this.missingFilter.division);
                    this.missingAvailableDistricts = found?.districts ?? [];
                },

                // Cascade: District → Upazilas
                onMissingDistrictChange() {
                    this.missingFilter.thanaUpazila = '';
                    this.missingAvailableUpazilas = [];

                    const division = this.allDivisions.find(d => d.name_en === this.missingFilter.division);
                    const district = division?.districts?.find(d => d.name_en === this.missingFilter.district);
                    this.missingAvailableUpazilas = district?.upazilas ?? district?.police_stations ?? [];
                },

                // Apply missing filter — AJAX
                async applyMissingFilter(page = 1) {
                    this.isMissingLoading = true;
                    this.missingCurrentPage = page;

                    const params = new URLSearchParams();
                    const filterMap = {
                        page: page,
                        from_date: this.missingFilter.fromDate,
                        to_date: this.missingFilter.toDate,
                        division: this.missingFilter.division,
                        district: this.missingFilter.district,
                        thana_upazila: this.missingFilter.thanaUpazila,
                        company_id: this.missingFilter.companyId,
                        depot_id: this.missingFilter.depotId,
                        station_id: this.missingFilter.stationId,
                    };

                    Object.entries(filterMap).forEach(([key, value]) => {
                        if (value !== '' && value !== null && value !== undefined) {
                            params.append(key, value);
                        }
                    });

                    try {
                        const response = await fetch(`{{ route('admin.reports.missing') }}?${params}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) throw new Error(`Server error ${response.status}`);

                        const json = await response.json();

                        if (json.success) {
                            this.missingReportRows = json.rows;
                            this.missingTotalRecords = json.total;
                            this.missingTotalPages = json.lastPage;
                            this.missingCurrentPage = json.currentPage;
                        }

                    } catch (error) {
                        console.error('Missing report fetch failed:', error);
                        this.missingReportRows = [];
                        this.missingTotalRecords = 0;
                    } finally {
                        this.isMissingLoading = false;
                    }
                },

                // Reset missing filters
                resetMissingFilter() {
                    this.missingFilter = {
                        fromDate: '',
                        toDate: '',
                        division: '',
                        district: '',
                        thanaUpazila: '',
                        companyId: '',
                        depotId: '',
                        stationId: '',
                    };
                    this.missingAvailableDistricts = [];
                    this.missingAvailableUpazilas = [];
                    this.missingReportRows = [];
                    this.missingTotalRecords = 0;
                    this.missingCurrentPage = 1;
                    this.missingTotalPages = 1;
                },

                // Pagination
                changeMissingPage(newPage) {
                    if (newPage < 1 || newPage > this.missingTotalPages) return;
                    this.applyMissingFilter(newPage);
                },

                // Export
                exportMissingPdf() {
                    const params = new URLSearchParams();
                    const fields = {
                        from_date: this.missingFilter.fromDate,
                        to_date: this.missingFilter.toDate,
                        division: this.missingFilter.division,
                        district: this.missingFilter.district,
                        company_id: this.missingFilter.companyId,
                        station_id: this.missingFilter.stationId,
                    };
                    Object.entries(fields).forEach(([k, v]) => {
                        if (v) params.append(k, v);
                    });
                    window.open(`{{ route('admin.reports.missing.export-pdf') }}?${params}`, '_blank');
                },


                // ═══════════════════════════════════════════════════════════════
                // TAB 4 — SUBMITTED REPORT METHODS
                // ═══════════════════════════════════════════════════════════════

                // Cascade: Division → Districts
                onSubmitDivisionChange() {
                    this.submitFilter.district = '';
                    this.submitFilter.thanaUpazila = '';
                    this.submitAvailableDistricts = [];
                    this.submitAvailableUpazilas = [];

                    const found = this.allDivisions.find(d => d.name_en === this.submitFilter.division);
                    this.submitAvailableDistricts = found?.districts ?? [];
                },

                // Cascade: District → Upazilas
                onSubmitDistrictChange() {
                    this.submitFilter.thanaUpazila = '';
                    this.submitAvailableUpazilas = [];

                    const division = this.allDivisions.find(d => d.name_en === this.submitFilter.division);
                    const district = division?.districts?.find(d => d.name_en === this.submitFilter.district);
                    this.submitAvailableUpazilas = district?.upazilas ?? district?.police_stations ?? [];
                },

                // Apply submit filter — AJAX
                async applySubmitFilter(page = 1) {
                    this.isSubmitLoading = true;
                    this.submitCurrentPage = page;

                    const params = new URLSearchParams();
                    const filterMap = {
                        page: page,
                        from_date: this.submitFilter.fromDate,
                        to_date: this.submitFilter.toDate,
                        division: this.submitFilter.division,
                        district: this.submitFilter.district,
                        thana_upazila: this.submitFilter.thanaUpazila,
                        company_id: this.submitFilter.companyId,
                        depot_id: this.submitFilter.depotId,
                        station_id: this.submitFilter.stationId,
                    };

                    Object.entries(filterMap).forEach(([key, value]) => {
                        if (value !== '' && value !== null && value !== undefined) {
                            params.append(key, value);
                        }
                    });

                    try {
                        const response = await fetch(`{{ route('admin.reports.submitted') }}?${params}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) throw new Error(`Server error ${response.status}`);

                        const json = await response.json();

                        if (json.success) {
                            this.submitReportRows = json.rows;
                            this.submitTotalRecords = json.total;
                            this.submitTotalPages = json.lastPage;
                            this.submitCurrentPage = json.currentPage;
                        }

                    } catch (error) {
                        console.error('Submit report fetch failed:', error);
                        this.submitReportRows = [];
                        this.submitTotalRecords = 0;
                    } finally {
                        this.isSubmitLoading = false;
                    }
                },

                // Reset submit filters
                resetSubmitFilter() {
                    this.submitFilter = {
                        fromDate: '',
                        toDate: '',
                        division: '',
                        district: '',
                        thanaUpazila: '',
                        companyId: '',
                        depotId: '',
                        stationId: '',
                    };
                    this.submitAvailableDistricts = [];
                    this.submitAvailableUpazilas = [];
                    this.submitReportRows = [];
                    this.submitTotalRecords = 0;
                    this.submitCurrentPage = 1;
                    this.submitTotalPages = 1;
                },

                // Pagination
                changeSubmitPage(newPage) {
                    if (newPage < 1 || newPage > this.submitTotalPages) return;
                    this.applySubmitFilter(newPage);
                },

                // Export
                exportSubmitPdf() {
                    const params = new URLSearchParams();
                    const fields = {
                        from_date: this.submitFilter.fromDate,
                        to_date: this.submitFilter.toDate,
                        division: this.submitFilter.division,
                        district: this.submitFilter.district,
                        company_id: this.submitFilter.companyId,
                        station_id: this.submitFilter.stationId,
                    };
                    Object.entries(fields).forEach(([k, v]) => {
                        if (v) params.append(k, v);
                    });
                    window.open(`{{ route('admin.reports.submitted.export-pdf') }}?${params}`, '_blank');
                },

            }; // end return
        } // end reportApp
    </script>
@endpush
