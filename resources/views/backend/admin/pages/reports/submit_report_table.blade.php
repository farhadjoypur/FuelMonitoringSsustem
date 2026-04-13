{{-- ════════════════════════════════════════════════════════════════
     submit_report_table.blade.php
     @include('backend.admin.pages.reports.submit_report_table', [
         'divisions' => $divisions,
         'companies' => $companies,
         'depots'    => $depots,
         'stations'  => $stations,
     ])
     Alpine state lives in parent reportApp()
     Columns: Submit Date, Officer Name, Phone, Division,
              District, Upazila, Filling Station, Fuel Type, Status
════════════════════════════════════════════════════════════════ --}}

{{-- ── Filter Section ──────────────────────────────────────────── --}}
<div class="filter-section">
    <div class="filter-title">
        <i class="fa-solid fa-sliders"></i> Filter Options
    </div>

    <div class="filter-grid">

        <div class="form-group">
            <label>From Date</label>
            <input type="date" x-model="submitFilter.fromDate">
        </div>

        <div class="form-group">
            <label>To Date</label>
            <input type="date" x-model="submitFilter.toDate">
        </div>

        <div class="form-group">
            <label>Division</label>
            <select x-model="submitFilter.division" @change="onSubmitDivisionChange()">
                <option value="">All Divisions</option>
                @foreach ($divisions as $division)
                    <option value="{{ $division['name_en'] }}">{{ $division['name_en'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>District</label>
            <select x-model="submitFilter.district" @change="onSubmitDistrictChange()"
                :disabled="!submitFilter.division">
                <option value="">All Districts</option>
                <template x-for="district in submitAvailableDistricts" :key="district.name_en">
                    <option :value="district.name_en" x-text="district.name_en"></option>
                </template>
            </select>
        </div>

        <div class="form-group">
            <label>Upazila</label>
            <select x-model="submitFilter.thanaUpazila" :disabled="!submitFilter.district">
                <option value="">All Upazilas</option>
                <template x-for="upazila in submitAvailableUpazilas" :key="upazila.name_en">
                    <option :value="upazila.name_en" x-text="upazila.name_en"></option>
                </template>
            </select>
        </div>

        <div class="form-group">
            <label>Company</label>
            <select x-model="submitFilter.companyId">
                <option value="">All Companies</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Depot</label>
            <select x-model="submitFilter.depotId">
                <option value="">All Depots</option>
                @foreach ($depots as $depot)
                    <option value="{{ $depot->id }}">{{ $depot->depot_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="position: relative;">
    <label>Filling Station</label>
    <div style="position: relative;">
        <input
            type="text"
            x-model="stationSearch"
            @input="stationOpen = true"
            @focus="stationOpen = true"
            @keydown.escape="stationOpen = false"
            placeholder="Search station..."
            autocomplete="off"
            style="width:100%; padding-right:30px; box-sizing:border-box;"
        />
        <span x-show="stationSelected" @click="clearStation()" 
            style="position:absolute; right:8px; top:50%; transform:translateY(-50%);
                   cursor:pointer; color:#94a3b8; font-size:13px; user-select:none;">✕</span>
    </div>

    <div x-show="stationOpen"
        style="position:absolute; top:100%; left:0; right:0; background:#fff;
               border:1px solid #e2e8f0; border-radius:6px; z-index:9999;
               max-height:220px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,.12);">

        {{-- All Stations --}}
        <div
            @mousedown.prevent="clearStation(); stationOpen = false;"
            style="padding:9px 12px; font-size:13px; cursor:pointer;
                   color:#64748b; border-bottom:1px solid #f1f5f9;"
            @mouseover="$el.style.background='#f8fafc'"
            @mouseleave="$el.style.background=''">
            All Stations
        </div>

        {{-- Filtered list --}}
        <template x-for="s in filteredStations" :key="s.id">
            <div
                @mousedown.prevent="selectStation(s)"
                x-text="s.name"
                style="padding:9px 12px; font-size:13px; cursor:pointer;"
                @mouseover="$el.style.background='#f8fafc'"
                @mouseleave="$el.style.background=''">
            </div>
        </template>

        {{-- No result --}}
        <div x-show="filteredStations.length === 0"
            style="padding:9px 12px; font-size:13px; color:#94a3b8; text-align:center;">
            No result found
        </div>
    </div>
</div>

        <div class="filter-actions" style="grid-column: 1 / -1;">
            <button class="btn-apply" @click="applySubmitFilter()" :disabled="isSubmitLoading">
                <template x-if="isSubmitLoading">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </template>
                <template x-if="!isSubmitLoading">
                    <i class="fa-solid fa-filter"></i>
                </template>
                <span x-text="isSubmitLoading ? 'Loading...' : 'Apply Filters'"></span>
            </button>
            <button class="btn-reset" @click="resetSubmitFilter()">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </button>
        </div>

    </div>{{-- /filter-grid --}}
</div>{{-- /filter-section --}}


{{-- ── Table Section ────────────────────────────────────────────── --}}
<div class="table-section">

    {{-- Header --}}
    <div class="table-header-row">
        <div class="table-title">
            Tag Officer Submitted Reports
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <span class="record-count" x-show="submitTotalRecords > 0" x-text="submitTotalRecords + ' records found'">
            </span>
            <button class="btn-submit-export-pdf" @click="exportSubmitPdf()">
                <i class="fa-solid fa-file-pdf"></i> Export to PDF
            </button>
        </div>
    </div>

    {{-- Empty state --}}
    <div x-show="!isSubmitLoading && submitTotalRecords === 0"
        style="text-align:center; padding:60px 24px; color:#94a3b8; font-size:13px;">
        <i class="fa-solid fa-inbox fa-2x" style="display:block; margin-bottom:12px; opacity:.4;"></i>
        No submitted reports found. Apply filters to search.
    </div>

    {{-- Loading state --}}
    <div x-show="isSubmitLoading" style="text-align:center; padding:60px; color:#64748b; font-size:13px;">
        <div class="loading-spinner" style="margin:0 auto 14px;"></div>
        Loading data...
    </div>

    {{-- ── Data Table ── --}}
    <div x-show="!isSubmitLoading && submitTotalRecords > 0" style="overflow-x:auto; overflow-y:auto;">

        <table class="submit-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th style="width:100px;">SUBMIT DATE</th>
                    <th style="width:130px;">OFFICER NAME</th>
                    <th style="width:110px;">PHONE</th>
                    <th style="width:90px;">DIVISION</th>
                    <th style="width:90px;">DISTRICT</th>
                    <th style="width:90px;">UPAZILA</th>
                    <th style="width:150px;">FILLING STATION</th>
                    {{-- Company --}}
                    <th style="width:80px;">COMPANY</th>
                    {{-- Depot --}}
                    <th style="width:80px;">DEPOT</th>
                    <th style="width:80px;">FUEL</th>
                    <th style="width:90px;">CLOSING STOCK</th>
                    <th style="width:80px;">STATUS</th>
                </tr>
            </thead>

            <tbody>
                <template x-for="(row, rowIndex) in submitReportRows" :key="row.id + '-' + rowIndex">
                    <tr>

                        {{-- # --}}
                        <td class="row-number" x-text="(submitCurrentPage - 1) * submitPerPage + rowIndex + 1">
                        </td>

                        {{-- Submit Date --}}
                        <td class="td-submit-date">
                            <div style="display:flex; flex-direction:column; line-height:1.4;">
                                <span x-text="row.submitDateFormatted" style="font-weight:600;"></span>
                                <span x-text="row.submitDayName"
                                    style="font-size:.65rem; color:#94a3b8; margin-top:2px;"></span>
                            </div>
                        </td>

                        {{-- Officer Name --}}
                        <td class="td-officer-name" x-text="row.officerName"></td>

                        {{-- Phone --}}
                        <td x-text="row.officerPhone || '—'"></td>

                        {{-- Division --}}
                        <td x-text="row.division || '—'"></td>

                        {{-- District --}}
                        <td x-text="row.district || '—'"></td>

                        {{-- Upazila --}}
                        <td x-text="row.thanaUpazila || '—'"></td>

                        {{-- Filling Station --}}
                        <td class="td-station-name" x-text="row.stationName"></td>

                        {{-- Company --}}
                        <td x-text="row.companyName"></td>

                        {{-- Depot --}}
                        <td x-text="row.depotName"></td>

                        {{-- Fuel Type — 4 sub-rows --}}
                        <td>
                            <div class="fuel-rows">
                                <template x-for="fuelRow in row.fuelBreakdown" :key="fuelRow.fuelType">
                                    <div class="fuel-row">
                                        <span class="fuel-type" x-text="fuelRow.fuelType"></span>
                                    </div>
                                </template>
                            </div>
                        </td>

                        {{-- Closing Stock per fuel --}}
                        <td>
                            <div class="fuel-rows">
                                <template x-for="fuelRow in row.fuelBreakdown" :key="fuelRow.fuelType">
                                    <div class="fuel-row">
                                        <span style="font-weight:600; font-size:.78rem;"
                                            x-text="fuelRow.closingStock + ' L'">
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </td>

                        {{-- Status badge --}}
                        <td>
                            <div class="fuel-rows">
                                <div class="fuel-row">
                                    <span class="submit-status-badge"
                                        :class="{
                                            'submit-status-submitted': row.status === 'Submitted',
                                            'submit-status-pending': row.status === 'Pending'
                                        }"
                                        x-text="row.status">
                                    </span>
                                </div>
                                <div class="fuel-row"></div>
                                <div class="fuel-row"></div>
                                <div class="fuel-row"></div>
                            </div>
                        </td>

                    </tr>
                </template>
            </tbody>
        </table>

    </div>{{-- /table scroll wrapper --}}
    {{-- ── Pagination ── --}}
    <div x-show="submitTotalPages > 1" style="display:none;">
        <div
            style="display:flex; align-items:center; justify-content:space-between;
                padding:14px 20px; border-top:1px solid #e2e8f0;
                font-size:12px; color:#64748b; flex-wrap:wrap; gap:10px;">

            {{-- Records info --}}
            <span
                x-text="`Showing page ${submitCurrentPage} of ${submitTotalPages} — ${submitTotalRecords} records`"></span>

            {{-- Pagination numbers --}}
            <nav>
                <ul class="pagination pagination-sm mb-0">

                    {{-- First --}}
                    <li class="page-item" :class="{ disabled: submitCurrentPage <= 1 }">
                        <button class="page-link" @click="changeSubmitPage(1)" :disabled="submitCurrentPage <= 1">
                            <i class="fa-solid fa-angles-left fa-xs"></i>
                        </button>
                    </li>

                    {{-- Prev --}}
                    <li class="page-item" :class="{ disabled: submitCurrentPage <= 1 }">
                        <button class="page-link" @click="changeSubmitPage(submitCurrentPage - 1)"
                            :disabled="submitCurrentPage <= 1">
                            <i class="fa-solid fa-chevron-left fa-xs"></i>
                        </button>
                    </li>

                    {{-- Page Numbers (window of 5) --}}
                    <template
                        x-for="page in (() => {
                    let pages = [];
                    let start = Math.max(1, submitCurrentPage - 2);
                    let end   = Math.min(submitTotalPages, start + 4);
                    start     = Math.max(1, end - 4);
                    for (let i = start; i <= end; i++) pages.push(i);
                    return pages;
                })()"
                        :key="page">
                        <li class="page-item" :class="{ active: page === submitCurrentPage }">
                            <button class="page-link" @click="changeSubmitPage(page)" x-text="page"></button>
                        </li>
                    </template>

                    {{-- Next --}}
                    <li class="page-item" :class="{ disabled: submitCurrentPage >= submitTotalPages }">
                        <button class="page-link" @click="changeSubmitPage(submitCurrentPage + 1)"
                            :disabled="submitCurrentPage >= submitTotalPages">
                            <i class="fa-solid fa-chevron-right fa-xs"></i>
                        </button>
                    </li>

                    {{-- Last --}}
                    <li class="page-item" :class="{ disabled: submitCurrentPage >= submitTotalPages }">
                        <button class="page-link" @click="changeSubmitPage(submitTotalPages)"
                            :disabled="submitCurrentPage >= submitTotalPages">
                            <i class="fa-solid fa-angles-right fa-xs"></i>
                        </button>
                    </li>

                </ul>
            </nav>

        </div>
    </div>

</div>{{-- /table-section --}}


{{-- ── Styles ────────────────────────────────────────────────────── --}}
<style>
    /* Submitted Report Table */
    .submit-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
        font-size: .775rem;
        background: #fff;
    }

    .submit-table thead {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .submit-table th {
        padding: 10px 10px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .3px;
        white-space: nowrap;
    }

    .submit-table tbody tr {
        border-bottom: 2px solid #e2e8f0;
        transition: background .12s;
    }

    .submit-table tbody tr:hover {
        background: #fafbfc;
    }

    .submit-table td {
        padding: 0 10px;
        color: #1e293b;
        vertical-align: middle;
    }

    .submit-table td.row-number {
        font-weight: 600;
        color: #94a3b8;
        font-size: 12px;
        text-align: center;
        padding: 12px 4px;
    }

    .submit-table td.td-submit-date {
        padding: 12px 10px;
    }

    .submit-table td.td-officer-name {
        padding: 12px 10px;
        font-weight: 500;
    }

    .submit-table td.td-station-name {
        padding: 12px 10px;
        font-weight: 500;
    }

    /* Fuel sub-rows */
    .submit-table .fuel-rows {
        display: flex;
        flex-direction: column;
    }

    .submit-table .fuel-row {
        display: flex;
        align-items: center;
        padding: 7px 0;
        min-height: 34px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .submit-table .fuel-row:last-child {
        border-bottom: none;
    }

    .submit-table .fuel-type {
        font-size: .72rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Status badges */
    .submit-status-badge {
        display: inline-block;
        font-size: .70rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }

    .submit-status-submitted {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .submit-status-pending {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* Export button */
    .btn-submit-export-pdf {
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background .2s;
    }

    .btn-submit-export-pdf:hover {
        background: #1d4ed8;
    }
</style>
