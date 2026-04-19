{{-- ════════════════════════════════════════════════════════════════
     difference_table.blade.php
     diffFilter keys: from_date, to_date, division, district,
                      thana_upazila, company_id, station_id,
                      tag_officer, diff_status, min_diff_l, min_diff_pct
════════════════════════════════════════════════════════════════ --}}

{{-- ── Filter Section ──────────────────────────────────────────── --}}
<div class="filter-section">
    <div class="filter-title">
        <i class="fa-solid fa-sliders"></i> Filter Options
    </div>

    <div class="filter-grid">

        <div class="form-group">
            <label>From Date</label>
            <input type="date" x-model="diffFilter.from_date">
        </div>

        <div class="form-group">
            <label>To Date</label>
            <input type="date" x-model="diffFilter.to_date">
        </div>

        <div class="form-group">
            <label>Difference (L)</label>
            <input type="number" min="0" placeholder="e.g. 100"
                x-model="diffFilter.min_diff_l">
        </div>

        <div class="form-group">
            <label>Difference (%)</label>
            <input type="number" min="0" max="100" step="0.1" placeholder="e.g. 5"
                x-model="diffFilter.min_diff_pct">
        </div>

        {{-- Division — DC panel এ hidden/disabled, admin panel এ open --}}
        <div class="form-group" x-show="!dcDistrict">
            <label>Division</label>
            <select x-model="diffFilter.division" @change="onDiffDivisionChange()">
                <option value="">All Divisions</option>
                @foreach ($divisions as $division)
                    <option value="{{ $division['name_en'] }}">{{ $division['name_en'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- District --}}
        {{-- DC panel: locked (read-only text), admin panel: cascade select --}}
        <div class="form-group">
            <label>District</label>
            <template x-if="dcDistrict">
                {{-- DC: district locked --}}
                <input type="text" :value="dcDistrict" readonly
                    style="background:#f0f6ff; border:1.5px solid #bfdbfe; color:#1d4ed8;
                           border-radius:7px; padding:8px 10px; font-size:.845rem;
                           cursor:not-allowed; width:100%;">
            </template>
            <template x-if="!dcDistrict">
                {{-- Admin: cascade select --}}
                <select x-model="diffFilter.district"
                    @change="onDiffDistrictChange()"
                    :disabled="!diffFilter.division">
                    <option value="">All Districts</option>
                    <template x-for="district in diffAvailableDistricts" :key="district.name_en">
                        <option :value="district.name_en" x-text="district.name_en"></option>
                    </template>
                </select>
            </template>
        </div>

        {{-- Upazila — সবার জন্য open, DC এর upazilas init এ load হয়ে থাকে --}}
        <div class="form-group">
            <label>Upazila</label>
            <select x-model="diffFilter.thana_upazila"
                :disabled="diffAvailableUpazilas.length === 0">
                <option value="">All Upazilas</option>
                <template x-for="upazila in diffAvailableUpazilas" :key="upazila.name_en">
                    <option :value="upazila.name_en" x-text="upazila.name_en"></option>
                </template>
            </select>
        </div>

        <div class="form-group">
            <label>Company</label>
            <select x-model="diffFilter.company_id">
                <option value="">All Companies</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
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

        <div class="form-group">
            <label>Tag Officer</label>
            <input type="text" placeholder="Officer name..."
                x-model="diffFilter.tag_officer">
        </div>

        <div class="form-group">
            <label>Difference Status</label>
            <select x-model="diffFilter.diff_status">
                <option value="">All Status</option>
                <option value="high">High</option>
                <option value="low">Low</option>
                <option value="normal">Normal</option>
            </select>
        </div>

        <div class="filter-actions">
            <button class="btn-apply" @click="applyDiffFilter()" :disabled="isDiffLoading">
                <template x-if="isDiffLoading">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </template>
                <template x-if="!isDiffLoading">
                    <i class="fa-solid fa-filter"></i>
                </template>
                <span x-text="isDiffLoading ? 'Loading...' : 'Apply Filters'"></span>
            </button>
            <button class="btn-reset" @click="resetDiffFilter()">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </button>
        </div>

    </div>{{-- /filter-grid --}}
</div>{{-- /filter-section --}}


{{-- ── Table Section ────────────────────────────────────────────── --}}
<div class="table-section">
     {{-- Header: title + record count + export button --}}
    <div class="table-header-row">
        <div class="table-title">Difference Report</div>
        <div style="display:flex; align-items:center; gap:12px;">
            <span class="record-count" x-show="diffTotalRecords > 0" x-text="diffTotalRecords + ' records found'">
            </span>
            <button class="btn-diff-export-pdf" @click="exportDiffPdf()">
    <i class="fa-solid fa-file-pdf"></i> Export to PDF
</button>
        </div>
    </div>

    

    {{-- Empty state --}}
    <div x-show="!isDiffLoading && diffTotalRecords === 0"
        style="text-align:center; padding:60px 24px; color:#94a3b8; font-size:13px;">
        <i class="fa-solid fa-inbox fa-2x"
            style="display:block; margin-bottom:12px; opacity:.4;"></i>
        Apply filters to see the difference report.
    </div>

    {{-- Loading state --}}
    <div x-show="isDiffLoading"
        style="text-align:center; padding:60px; color:#64748b; font-size:13px;">
        <div class="loading-spinner" style="margin:0 auto 14px;"></div>
        Loading data...
    </div>

    {{-- ── Data Table ── --}}
    <div x-show="!isDiffLoading && diffTotalRecords > 0" class="diff-table-wrapper">

        <table class="diff-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th style="width:80px;">DATE</th>
                    <th style="width:120px;">STATION</th>
                    <th style="width:90px;">COMPANY</th>
                    <th style="width:100px;">TAG OFFICER</th>
                    <th style="width:110px;">DESIGNATION</th>
                    <th style="width:100px;">PHONE</th>
                    <th style="width:75px;">DISTRICT</th>
                    <th style="width:75px;">UPAZILA</th>
                    <th style="width:65px;">FUEL</th>
                    <th style="width:95px;">DIFFERENCE (L)</th>
                    <th style="width:95px;">DIFFERENCE (%)</th>
                    <th style="width:100px;">DIFF STATUS</th>
                    <th style="width:110px;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, rowIndex) in diffReportRows" :key="row.reportId">
                    <tr>
                        <td class="row-number"
                            x-text="(diffCurrentPage - 1) * diffPerPage + rowIndex + 1">
                        </td>
                        <td class="td-date">
                            <div class="date-cell">
                                <span x-text="row.dateFormatted"></span>
                                <span class="date-day" x-text="row.dayName"></span>
                            </div>
                        </td>
                        <td class="td-station" x-text="row.stationName"></td>
                        <td x-text="row.companyName"></td>
                        <td class="td-officer" x-text="row.tagOfficerName"></td>
                        <td class="td-designation" x-text="row.officerDesignation || '—'"></td>
                        <td class="td-phone" x-text="row.officerPhone || '—'"></td>
                        <td x-text="row.district"></td>
                        <td x-text="row.thanaUpazila"></td>

                        {{-- Fuel type labels --}}
                        <td>
                            <div class="fuel-rows">
                                <template x-for="fuelRow in row.fuelBreakdown" :key="fuelRow.fuelType">
                                    <div class="fuel-row">
                                        <span class="fuel-type" x-text="fuelRow.fuelType"></span>
                                    </div>
                                </template>
                            </div>
                        </td>

                        {{-- Difference (L) --}}
                        <td class="diff-column">
                            <div class="fuel-rows">
                                <template x-for="fuelRow in row.fuelBreakdown" :key="fuelRow.fuelType">
                                    <div class="fuel-row">
                                        <span class="fuel-value"
                                            :class="fuelRow.differenceL != '0' ? 'diff-value-nonzero' : ''"
                                            x-text="fuelRow.differenceL">
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </td>

                        {{-- Difference (%) --}}
                        <td class="diff-column">
                            <div class="fuel-rows">
                                <template x-for="fuelRow in row.fuelBreakdown" :key="fuelRow.fuelType">
                                    <div class="fuel-row">
                                        <span class="fuel-percent"
                                            :class="{
                                                'diff-percent-negative': parseFloat(fuelRow.differencePercent) < 0,
                                                'diff-percent-positive': parseFloat(fuelRow.differencePercent) > 0
                                            }"
                                            x-text="fuelRow.differencePercent + '%'">
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </td>

                        {{-- Diff Status --}}
                        <td>
                            <div class="fuel-rows">
                                <template x-for="fuelRow in row.fuelBreakdown" :key="fuelRow.fuelType">
                                    <div class="fuel-row">
                                        <span class="diff-status-badge"
                                            :class="{
                                                'diff-status-high':   fuelRow.diffStatus === 'High',
                                                'diff-status-low':    fuelRow.diffStatus === 'Low',
                                                'diff-status-normal': fuelRow.diffStatus === 'Normal'
                                            }"
                                            x-text="fuelRow.diffStatus">
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="action-btns">
                                <a :href="`/dc/reports/${row.reportId}/edit`"
                                class="action-btn"
                                style="background:#2563eb; color:#fff; text-decoration:none; border:none;">
                                    <i class="fa-solid fa-pen-to-square fa-xs"></i> Edit
                                </a>
                                <button class="action-btn btn-delete"
                                    @click="openDeleteModal(row.reportId, row.stationName)">
                                    <i class="fa-solid fa-trash fa-xs"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

    </div>{{-- /diff-table-wrapper --}}

    {{-- Pagination --}}
    <div x-show="diffTotalPages > 1">
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:14px 20px; border-top:1px solid #e2e8f0;
                    font-size:12px; color:#64748b; flex-wrap:wrap; gap:10px;">
            <span x-text="`Showing page ${diffCurrentPage} of ${diffTotalPages} — ${diffTotalRecords} records`"></span>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: diffCurrentPage <= 1 }">
                        <button class="page-link" @click="changeDiffPage(1)">
                            <i class="fa-solid fa-angles-left fa-xs"></i>
                        </button>
                    </li>
                    <li class="page-item" :class="{ disabled: diffCurrentPage <= 1 }">
                        <button class="page-link" @click="changeDiffPage(diffCurrentPage - 1)">
                            <i class="fa-solid fa-chevron-left fa-xs"></i>
                        </button>
                    </li>
                    <template x-for="page in (() => {
                        let pages = [], start = Math.max(1, diffCurrentPage - 2);
                        let end = Math.min(diffTotalPages, start + 4);
                        start = Math.max(1, end - 4);
                        for (let i = start; i <= end; i++) pages.push(i);
                        return pages;
                    })()" :key="page">
                        <li class="page-item" :class="{ active: page === diffCurrentPage }">
                            <button class="page-link" @click="changeDiffPage(page)" x-text="page"></button>
                        </li>
                    </template>
                    <li class="page-item" :class="{ disabled: diffCurrentPage >= diffTotalPages }">
                        <button class="page-link" @click="changeDiffPage(diffCurrentPage + 1)">
                            <i class="fa-solid fa-chevron-right fa-xs"></i>
                        </button>
                    </li>
                    <li class="page-item" :class="{ disabled: diffCurrentPage >= diffTotalPages }">
                        <button class="page-link" @click="changeDiffPage(diffTotalPages)">
                            <i class="fa-solid fa-angles-right fa-xs"></i>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

</div>{{-- /table-section --}}


{{-- ── Difference-tab-specific styles ─────────────────────────── --}}
{{-- Add these inside the existing <style> block in index.blade.php --}}
<style>
    /* Export PDF button — scoped name to avoid conflict with stock tab */
    .btn-diff-export-pdf {
        background: #307c32;
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

    .btn-diff-export-pdf:hover {
        background: #dc2626;
    }

    /* Difference value colours */
    .diff-value-nonzero {
        color: #dc2626;
        font-weight: 700;
    }

    .diff-percent-negative {
        color: #dc2626;
        font-weight: 700;
    }

    .diff-percent-positive {
        color: #f59e0b;
        font-weight: 700;
    }

    /* Status badges */
    .diff-status-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 20px;
        font-weight: 600;
        white-space: nowrap;
        display: inline-block;
    }

    .diff-status-high {
        background: #fef2f2;
        color: #991b1b;
    }

    .diff-status-low {
        background: #fff7ed;
        color: #c2410c;
    }

    .diff-status-normal {
        background: #f0fdf4;
        color: #15803d;
    }

    /* Pagination buttons (reused from stock tab via .btn-page) */
    .btn-page {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 4px;
        transition: background .15s;
    }

    .btn-page:hover:not(:disabled) {
        background: #f1f5f9;
    }

    .btn-page:disabled {
        opacity: .4;
        cursor: not-allowed;
    }
</style>
