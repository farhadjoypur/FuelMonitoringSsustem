{{-- ════════════════════════════════════════════════════════════════
     missing_report_table.blade.php
     @include('backend.admin.pages.reports.missing_report_table', [
         'divisions' => $divisions,
         'companies' => $companies,
         'depots'    => $depots,
         'stations'  => $stations,
     ])
     Alpine state lives in parent reportApp()
     Columns: Missing Date, Officer Name, Phone, Division,
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
            <input type="date" x-model="missingFilter.fromDate">
        </div>

        <div class="form-group">
            <label>To Date</label>
            <input type="date" x-model="missingFilter.toDate">
        </div>

        <div class="form-group">
            <label>Division</label>
            <select x-model="missingFilter.division" @change="onMissingDivisionChange()">
                <option value="">All Divisions</option>
                @foreach ($divisions as $division)
                    <option value="{{ $division['name_en'] }}">{{ $division['name_en'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>District</label>
            <select x-model="missingFilter.district"
                @change="onMissingDistrictChange()"
                :disabled="!missingFilter.division">
                <option value="">All Districts</option>
                <template x-for="district in missingAvailableDistricts" :key="district.name_en">
                    <option :value="district.name_en" x-text="district.name_en"></option>
                </template>
            </select>
        </div>

        <div class="form-group">
            <label>Upazila</label>
            <select x-model="missingFilter.thanaUpazila" :disabled="!missingFilter.district">
                <option value="">All Upazilas</option>
                <template x-for="upazila in missingAvailableUpazilas" :key="upazila.name_en">
                    <option :value="upazila.name_en" x-text="upazila.name_en"></option>
                </template>
            </select>
        </div>

        <div class="form-group">
            <label>Company</label>
            <select x-model="missingFilter.companyId">
                <option value="">All Companies</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Depot</label>
            <select x-model="missingFilter.depotId">
                <option value="">All Depots</option>
                @foreach ($depots as $depot)
                    <option value="{{ $depot->id }}">{{ $depot->depot_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Filling Station</label>
            <select x-model="missingFilter.stationId">
                <option value="">All Stations</option>
                @foreach ($stations as $station)
                    <option value="{{ $station->id }}">{{ $station->station_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Apply / Reset — span full row --}}
        <div class="filter-actions" style="grid-column: 1 / -1;">
            <button class="btn-apply"
                @click="applyMissingFilter()"
                :disabled="isMissingLoading">
                <template x-if="isMissingLoading">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </template>
                <template x-if="!isMissingLoading">
                    <i class="fa-solid fa-filter"></i>
                </template>
                <span x-text="isMissingLoading ? 'Loading...' : 'Apply Filters'"></span>
            </button>
            <button class="btn-reset" @click="resetMissingFilter()">
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
            Tag Officer Missing Reports
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <span class="record-count"
                x-show="missingTotalRecords > 0"
                x-text="missingTotalRecords + ' records found'">
            </span>
           <button class="btn-missing-export-pdf" @click="exportMissingPdf()">
    <i class="fa-solid fa-file-pdf"></i> Export to PDF
</button>
        </div>
    </div>

    {{-- Empty state --}}
    <div x-show="!isMissingLoading && missingTotalRecords === 0"
        style="text-align:center; padding:60px 24px; color:#94a3b8; font-size:13px;">
        <i class="fa-solid fa-inbox fa-2x"
            style="display:block; margin-bottom:12px; opacity:.4;"></i>
        No pending submissions found. Apply filters to search.
    </div>

    {{-- Loading state --}}
    <div x-show="isMissingLoading"
        style="text-align:center; padding:60px; color:#64748b; font-size:13px;">
        <div class="loading-spinner" style="margin:0 auto 14px;"></div>
        Loading data...
    </div>

    {{-- ── Data Table ── --}}
    <div x-show="!isMissingLoading && missingTotalRecords > 0"
        style="overflow-x:auto; overflow-y:auto;">

        <table class="missing-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th style="width:100px;">MISSING DATE</th>
                    <th style="width:130px;">OFFICER NAME</th>
                    <th style="width:110px;">PHONE</th>
                    <th style="width:90px;">DIVISION</th>
                    <th style="width:90px;">DISTRICT</th>
                    <th style="width:90px;">UPAZILA</th>
                    <th style="width:150px;">FILLING STATION</th>
                    {{-- Company --}}
                    <th style="width:80px;">COMPANY</th>
                    {{-- depot --}}
                    <th style="width:80px;">DEPOT</th>
                    <th style="width:80px;">FUEL</th>
                    <th style="width:80px;">STATUS</th>
                </tr>
            </thead>

            <tbody>
                <template x-for="(row, rowIndex) in missingReportRows" :key="row.id + '-' + rowIndex">
                    <tr>

                        {{-- # --}}
                        <td class="row-number"
                            x-text="(missingCurrentPage - 1) * missingPerPage + rowIndex + 1">
                        </td>

                        {{-- Missing Date --}}
                        <td class="td-missing-date">
                            <span x-text="row.missingDate"></span>
                        </td>

                        {{-- Officer Name --}}
                        <td class="td-officer-name" x-text="row.officerName"></td>

                        {{-- Phone --}}
                        <td class="td-phone" x-text="row.officerPhone || '—'"></td>

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
                                <div class="fuel-row">
                                    <span class="fuel-type">Octane</span>
                                </div>
                                <div class="fuel-row">
                                    <span class="fuel-type">Petrol</span>
                                </div>
                                <div class="fuel-row">
                                    <span class="fuel-type">Diesel</span>
                                </div>
                                <div class="fuel-row">
                                    <span class="fuel-type">Others</span>
                                </div>
                            </div>
                        </td>

                        {{-- Status — single badge for the whole row --}}
                        <td>
                            <div class="fuel-rows">
                                <div class="fuel-row">
                                    <span class="missing-status-badge missing-status-pending"
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
<div x-show="missingTotalPages > 1" style="display:none;">
    <div style="display:flex; align-items:center; justify-content:space-between;
                padding:14px 20px; border-top:1px solid #e2e8f0;
                font-size:12px; color:#64748b; flex-wrap:wrap; gap:10px;">

        {{-- Records info --}}
        <span x-text="`Showing page ${missingCurrentPage} of ${missingTotalPages} — ${missingTotalRecords} records`"></span>

        {{-- Pagination numbers --}}
        <nav>
            <ul class="pagination pagination-sm mb-0">

                {{-- First --}}
                <li class="page-item" :class="{ disabled: missingCurrentPage <= 1 }">
                    <button class="page-link" @click="changeMissingPage(1)" :disabled="missingCurrentPage <= 1">
                        <i class="fa-solid fa-angles-left fa-xs"></i>
                    </button>
                </li>

                {{-- Prev --}}
                <li class="page-item" :class="{ disabled: missingCurrentPage <= 1 }">
                    <button class="page-link" @click="changeMissingPage(missingCurrentPage - 1)" :disabled="missingCurrentPage <= 1">
                        <i class="fa-solid fa-chevron-left fa-xs"></i>
                    </button>
                </li>

                {{-- Page Numbers (window of 5) --}}
                <template x-for="page in (() => {
                    let pages = [];
                    let start = Math.max(1, missingCurrentPage - 2);
                    let end   = Math.min(missingTotalPages, start + 4);
                    start     = Math.max(1, end - 4);
                    for (let i = start; i <= end; i++) pages.push(i);
                    return pages;
                })()" :key="page">
                    <li class="page-item" :class="{ active: page === missingCurrentPage }">
                        <button class="page-link" @click="changeMissingPage(page)" x-text="page"></button>
                    </li>
                </template>

                {{-- Next --}}
                <li class="page-item" :class="{ disabled: missingCurrentPage >= missingTotalPages }">
                    <button class="page-link" @click="changeMissingPage(missingCurrentPage + 1)" :disabled="missingCurrentPage >= missingTotalPages">
                        <i class="fa-solid fa-chevron-right fa-xs"></i>
                    </button>
                </li>

                {{-- Last --}}
                <li class="page-item" :class="{ disabled: missingCurrentPage >= missingTotalPages }">
                    <button class="page-link" @click="changeMissingPage(missingTotalPages)" :disabled="missingCurrentPage >= missingTotalPages">
                        <i class="fa-solid fa-angles-right fa-xs"></i>
                    </button>
                </li>

            </ul>
        </nav>

    </div>
</div>

</div>{{-- /table-section --}}


{{-- ── Styles (add to index.blade.php <style> block) ─────────── --}}
<style>
    /* Missing Report Table */
    .missing-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
        font-size: .775rem;
        background: #fff;
    }
    .missing-table thead {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .missing-table th {
        padding: 10px 10px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .3px;
        white-space: nowrap;
    }
    .missing-table tbody tr {
        border-bottom: 2px solid #e2e8f0;
        transition: background .12s;
    }
    .missing-table tbody tr:hover { background: #fafbfc; }
    .missing-table td {
        padding: 0 10px;
        color: #1e293b;
        vertical-align: middle;
    }
    .missing-table td.row-number {
        font-weight: 600;
        color: #94a3b8;
        font-size: 12px;
        text-align: center;
        padding: 12px 4px;
    }
    .missing-table td.td-missing-date {
        padding: 12px 10px;
        font-weight: 600;
        font-size: .78rem;
        color: #334155;
        white-space: nowrap;
    }
    .missing-table td.td-officer-name {
        padding: 12px 10px;
        font-weight: 500;
    }
    .missing-table td.td-phone {
        padding: 12px 10px;
        white-space: nowrap;
    }
    .missing-table td.td-station-name {
        padding: 12px 10px;
        font-weight: 500;
    }

    /* Fuel sub-rows inside missing table */
    .missing-table .fuel-rows { display: flex; flex-direction: column; }
    .missing-table .fuel-row {
        display: flex;
        align-items: center;
        padding: 7px 0;
        min-height: 34px;
        border-bottom: 1px dashed #e2e8f0;
    }
    .missing-table .fuel-row:last-child { border-bottom: none; }
    .missing-table .fuel-type {
        font-size: .72rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Pending status badge */
    .missing-status-badge {
        display: inline-block;
        font-size: .70rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .missing-status-pending {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .missing-status-submitted {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    /* Export button */
    .btn-missing-export-pdf {
        background: #16a34a;
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
    .btn-missing-export-pdf:hover { background: #15803d; }
</style>