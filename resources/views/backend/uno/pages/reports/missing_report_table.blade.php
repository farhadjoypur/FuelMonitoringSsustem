{{-- ════════════════════════════════════════════════════════════════
     missing_report_table.blade.php  (UNO)
     missingFilter keys: from_date, to_date, district (locked),
                         thana_upazila (locked), company_id, depot_id, station_id
     NOTE: division filter hidden — UNO jurisdiction is locked to
           their own district + upazila, no cascade needed.
════════════════════════════════════════════════════════════════ --}}

<div class="filter-section">
    <div class="filter-title">
        <i class="fa-solid fa-sliders"></i> Filter Options
    </div>

    <div class="filter-grid">

        <div class="form-group">
            <label>From Date</label>
            <input type="date" x-model="missingFilter.from_date">
        </div>

        <div class="form-group">
            <label>To Date</label>
            <input type="date" x-model="missingFilter.to_date">
        </div>

        {{-- District — UNO এর নিজের district, locked --}}
        <div class="form-group">
            <label>District</label>
            <input type="text" :value="unoDistrict" readonly
                style="background:#f0f6ff; border:1.5px solid #bfdbfe; color:#1d4ed8;
                       border-radius:7px; padding:8px 10px; font-size:.845rem;
                       cursor:not-allowed; width:100%;">
        </div>

        {{-- Upazila — UNO এর নিজের upazila, locked --}}
        <div class="form-group">
            <label>Upazila</label>
            <input type="text" :value="unoUpazila" readonly
                style="background:#f0f6ff; border:1.5px solid #bfdbfe; color:#1d4ed8;
                       border-radius:7px; padding:8px 10px; font-size:.845rem;
                       cursor:not-allowed; width:100%;">
        </div>

        <div class="form-group">
            <label>Company</label>
            <select x-model="missingFilter.company_id">
                <option value="">All Companies</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Depot</label>
            <select x-model="missingFilter.depot_id">
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
            <button class="btn-apply" @click="applyMissingFilter()" :disabled="isMissingLoading">
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

    </div>
</div>


<div class="table-section">

 <div class="table-header-row">

    <div class="table-title">
        Tag Officer Pending Reports
    </div>

    <div class="header-right">

        <span class="record-count"
            x-show="missingTotalRecords > 0"
            x-text="missingTotalRecords + ' records found'">
        </span>

        <div class="export-row">
            <button class="btn-export btn-export-pdf" @click="exportMissingPdf()">
                <i class="fa-regular fa-file-pdf"></i> Export PDF
            </button>
        </div>

    </div>

</div>
    

    {{-- Empty --}}
    <div x-show="!isMissingLoading && missingTotalRecords === 0"
        style="text-align:center; padding:60px 24px; color:#94a3b8; font-size:13px;">
        <i class="fa-solid fa-inbox fa-2x"
            style="display:block; margin-bottom:12px; opacity:.4;"></i>
        No pending submissions found.
    </div>

    {{-- Loading --}}
    <div x-show="isMissingLoading"
        style="text-align:center; padding:60px; color:#64748b; font-size:13px;">
        <div class="loading-spinner" style="margin:0 auto 14px;"></div>
        Loading data...
    </div>

    {{-- Table --}}
    <div x-show="!isMissingLoading && missingTotalRecords > 0"
        style="overflow-x:auto;">

        <table class="diff-table">
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
                    <th style="width:80px;">COMPANY</th>
                    <th style="width:80px;">DEPOT</th>
                    <th style="width:80px;">FUEL TYPE</th>
                    <th style="width:80px;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, rowIndex) in missingReportRows" :key="row.id + '-' + rowIndex">
                    <tr>
                        <td class="row-number"
                            x-text="(missingCurrentPage - 1) * missingPerPage + rowIndex + 1">
                        </td>
                        <td class="td-date" x-text="row.missingDate"></td>
                        <td class="td-officer" x-text="row.officerName"></td>
                        <td x-text="row.officerPhone || '—'"></td>
                        <td x-text="row.division || '—'"></td>
                        <td x-text="row.district || '—'"></td>
                        <td x-text="row.thanaUpazila || '—'"></td>
                        <td class="td-station" x-text="row.stationName"></td>
                        <td x-text="row.companyName || '—'"></td>
                        <td x-text="row.depotName || '—'"></td>

                        {{-- Fuel sub-rows --}}
                        <td>
                            <div class="fuel-rows">
                                <div class="fuel-row"><span class="fuel-type">Octane</span></div>
                                <div class="fuel-row"><span class="fuel-type">Petrol</span></div>
                                <div class="fuel-row"><span class="fuel-type">Diesel</span></div>
                                <div class="fuel-row"><span class="fuel-type">Others</span></div>
                            </div>
                        </td>

                        {{-- Status badge --}}
                        <td>
                            <div class="fuel-rows">
                                <div class="fuel-row">
                                    <span class="diff-status-badge diff-status-high"
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
    </div>

    {{-- Pagination --}}
    <div x-show="missingTotalPages > 1">
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:14px 20px; border-top:1px solid #e2e8f0;
                    font-size:12px; color:#64748b; flex-wrap:wrap; gap:10px;">
            <span x-text="`Showing page ${missingCurrentPage} of ${missingTotalPages} — ${missingTotalRecords} records`"></span>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: missingCurrentPage <= 1 }">
                        <button class="page-link" @click="changeMissingPage(1)">
                            <i class="fa-solid fa-angles-left fa-xs"></i>
                        </button>
                    </li>
                    <li class="page-item" :class="{ disabled: missingCurrentPage <= 1 }">
                        <button class="page-link" @click="changeMissingPage(missingCurrentPage - 1)">
                            <i class="fa-solid fa-chevron-left fa-xs"></i>
                        </button>
                    </li>
                    <template x-for="page in (() => {
                        let pages = [], start = Math.max(1, missingCurrentPage - 2);
                        let end = Math.min(missingTotalPages, start + 4);
                        start = Math.max(1, end - 4);
                        for (let i = start; i <= end; i++) pages.push(i);
                        return pages;
                    })()" :key="page">
                        <li class="page-item" :class="{ active: page === missingCurrentPage }">
                            <button class="page-link" @click="changeMissingPage(page)" x-text="page"></button>
                        </li>
                    </template>
                    <li class="page-item" :class="{ disabled: missingCurrentPage >= missingTotalPages }">
                        <button class="page-link" @click="changeMissingPage(missingCurrentPage + 1)">
                            <i class="fa-solid fa-chevron-right fa-xs"></i>
                        </button>
                    </li>
                    <li class="page-item" :class="{ disabled: missingCurrentPage >= missingTotalPages }">
                        <button class="page-link" @click="changeMissingPage(missingTotalPages)">
                            <i class="fa-solid fa-angles-right fa-xs"></i>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

</div>{{-- /table-section --}}