{{-- ════════════════════════════════════════════════════════════════
     submit_report_table.blade.php
     submitFilter keys: from_date, to_date, division, district,
                        thana_upazila, company_id, depot_id, station_id
════════════════════════════════════════════════════════════════ --}}

<div class="filter-section">
    <div class="filter-title">
        <i class="fa-solid fa-sliders"></i> Filter Options
    </div>

    <div class="filter-grid">

        <div class="form-group">
            <label>From Date</label>
            <input type="date" x-model="submitFilter.from_date">
        </div>

        <div class="form-group">
            <label>To Date</label>
            <input type="date" x-model="submitFilter.to_date">
        </div>

        {{-- Division — DC এ hidden --}}
        <div class="form-group" x-show="!dcDistrict">
            <label>Division</label>
            <select x-model="submitFilter.division" @change="onSubmitDivisionChange()">
                <option value="">All Divisions</option>
                @foreach ($divisions as $division)
                    <option value="{{ $division['name_en'] }}">{{ $division['name_en'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- District --}}
        <div class="form-group">
            <label>District</label>
            <template x-if="dcDistrict">
                <input type="text" :value="dcDistrict" readonly
                    style="background:#f0f6ff; border:1.5px solid #bfdbfe; color:#1d4ed8;
                           border-radius:7px; padding:8px 10px; font-size:.845rem;
                           cursor:not-allowed; width:100%;">
            </template>
            <template x-if="!dcDistrict">
                <select x-model="submitFilter.district"
                    @change="onSubmitDistrictChange()"
                    :disabled="!submitFilter.division">
                    <option value="">All Districts</option>
                    <template x-for="district in submitAvailableDistricts" :key="district.name_en">
                        <option :value="district.name_en" x-text="district.name_en"></option>
                    </template>
                </select>
            </template>
        </div>

        {{-- Upazila — সবসময় open --}}
        <div class="form-group">
            <label>Upazila</label>
            <select x-model="submitFilter.thana_upazila"
                :disabled="submitAvailableUpazilas.length === 0">
                <option value="">All Upazilas</option>
                <template x-for="upazila in submitAvailableUpazilas" :key="upazila.name_en">
                    <option :value="upazila.name_en" x-text="upazila.name_en"></option>
                </template>
            </select>
        </div>

        <div class="form-group">
            <label>Company</label>
            <select x-model="submitFilter.company_id">
                <option value="">All Companies</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Depot</label>
            <select x-model="submitFilter.depot_id">
                <option value="">All Depots</option>
                @foreach ($depots as $depot)
                    <option value="{{ $depot->id }}">{{ $depot->depot_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Filling Station</label>
            <select x-model="submitFilter.station_id">
                <option value="">All Stations</option>
                @foreach ($stations as $station)
                    <option value="{{ $station->id }}">{{ $station->station_name }}</option>
                @endforeach
            </select>
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

    </div>
</div>


<div class="table-section">

    <div class="table-header-row">
        <div class="table-title">Tag Officer Submitted Reports</div>
        <span class="record-count"
            x-show="submitTotalRecords > 0"
            x-text="submitTotalRecords + ' records found'">
        </span>
    </div>

    {{-- Empty --}}
    <div x-show="!isSubmitLoading && submitTotalRecords === 0"
        style="text-align:center; padding:60px 24px; color:#94a3b8; font-size:13px;">
        <i class="fa-solid fa-inbox fa-2x"
            style="display:block; margin-bottom:12px; opacity:.4;"></i>
        No submitted reports found.
    </div>

    {{-- Loading --}}
    <div x-show="isSubmitLoading"
        style="text-align:center; padding:60px; color:#64748b; font-size:13px;">
        <div class="loading-spinner" style="margin:0 auto 14px;"></div>
        Loading data...
    </div>

    {{-- Table --}}
    <div x-show="!isSubmitLoading && submitTotalRecords > 0"
        style="overflow-x:auto;">

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
                    <th style="width:80px;">COMPANY</th>
                    <th style="width:80px;">DEPOT</th>
                    <th style="width:80px;">FUEL TYPE</th>
                    <th style="width:90px;">CLOSING STOCK</th>
                    <th style="width:80px;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(row, rowIndex) in submitReportRows" :key="row.id + '-' + rowIndex">
                    <tr>
                        <td class="row-number"
                            x-text="(submitCurrentPage - 1) * submitPerPage + rowIndex + 1">
                        </td>
                        <td class="td-submit-date">
                            <div style="display:flex; flex-direction:column; line-height:1.4;">
                                <span x-text="row.submitDateFormatted" style="font-weight:600;"></span>
                                <span x-text="row.submitDayName"
                                    style="font-size:.65rem; color:#94a3b8; margin-top:2px;"></span>
                            </div>
                        </td>
                        <td class="td-officer-name" x-text="row.officerName"></td>
                        <td x-text="row.officerPhone || '—'"></td>
                        <td x-text="row.division || '—'"></td>
                        <td x-text="row.district || '—'"></td>
                        <td x-text="row.thanaUpazila || '—'"></td>
                        <td class="td-station-name" x-text="row.stationName"></td>
                        <td x-text="row.companyName || '—'"></td>
                        <td x-text="row.depotName || '—'"></td>

                        {{-- Fuel sub-rows --}}
                        <td>
                            <div class="fuel-rows">
                                <template x-for="fuelRow in row.fuelBreakdown" :key="fuelRow.fuelType">
                                    <div class="fuel-row">
                                        <span class="fuel-type" x-text="fuelRow.fuelType"></span>
                                    </div>
                                </template>
                            </div>
                        </td>

                        {{-- Closing stock per fuel --}}
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

                        {{-- Status --}}
                        <td>
                            <div class="fuel-rows">
                                <div class="fuel-row">
                                    <span class="submit-status-badge"
                                        :class="{
                                            'submit-status-submitted': row.status === 'Submitted',
                                            'submit-status-pending':   row.status === 'Pending'
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
    </div>

    {{-- Pagination --}}
    <div x-show="submitTotalPages > 1">
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:14px 20px; border-top:1px solid #e2e8f0;
                    font-size:12px; color:#64748b; flex-wrap:wrap; gap:10px;">
            <span x-text="`Showing page ${submitCurrentPage} of ${submitTotalPages} — ${submitTotalRecords} records`"></span>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" :class="{ disabled: submitCurrentPage <= 1 }">
                        <button class="page-link" @click="changeSubmitPage(1)">
                            <i class="fa-solid fa-angles-left fa-xs"></i>
                        </button>
                    </li>
                    <li class="page-item" :class="{ disabled: submitCurrentPage <= 1 }">
                        <button class="page-link" @click="changeSubmitPage(submitCurrentPage - 1)">
                            <i class="fa-solid fa-chevron-left fa-xs"></i>
                        </button>
                    </li>
                    <template x-for="page in (() => {
                        let pages = [], start = Math.max(1, submitCurrentPage - 2);
                        let end = Math.min(submitTotalPages, start + 4);
                        start = Math.max(1, end - 4);
                        for (let i = start; i <= end; i++) pages.push(i);
                        return pages;
                    })()" :key="page">
                        <li class="page-item" :class="{ active: page === submitCurrentPage }">
                            <button class="page-link" @click="changeSubmitPage(page)" x-text="page"></button>
                        </li>
                    </template>
                    <li class="page-item" :class="{ disabled: submitCurrentPage >= submitTotalPages }">
                        <button class="page-link" @click="changeSubmitPage(submitCurrentPage + 1)">
                            <i class="fa-solid fa-chevron-right fa-xs"></i>
                        </button>
                    </li>
                    <li class="page-item" :class="{ disabled: submitCurrentPage >= submitTotalPages }">
                        <button class="page-link" @click="changeSubmitPage(submitTotalPages)">
                            <i class="fa-solid fa-angles-right fa-xs"></i>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

</div>{{-- /table-section --}}