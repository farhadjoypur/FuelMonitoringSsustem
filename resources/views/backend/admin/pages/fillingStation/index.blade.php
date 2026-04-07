@extends('backend.admin.layouts.app')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body,
        .main-content {
            background-color: #f0f4f8 !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .fs-wrapper {
            padding: 32px 36px;
        }

        /* ── Header ── */
        .fs-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .fs-header-left h2 {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px;
            letter-spacing: -0.4px;
        }

        .fs-header-left p {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #006796;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 22px;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(0, 103, 150, .35);
            transition: transform .15s, box-shadow .15s;
        }

        .btn-add:hover {
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 103, 150, .45);
        }

        .btn-add svg {
            width: 15px;
            height: 15px;
        }

        /* ── Filter Card ── */
        .fs-filter-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
        }

        .fs-input {
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            padding: 9px 13px;
            font-size: 0.875rem;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color .15s, box-shadow .15s;
            min-width: 140px;
        }

        .fs-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
            background: #fff;
        }

        .fs-input::placeholder {
            color: #94a3b8;
        }

        .btn-filter {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #006796;
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 9px 18px;
            font-size: 0.82rem;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            white-space: nowrap;
            transition: background .15s;
        }

        .btn-filter:hover {
            background: #005580;
        }

        .btn-filter:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        .btn-clear {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            color: #475569;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            padding: 9px 16px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background .15s;
            white-space: nowrap;
        }

        .btn-clear:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        /* ── Result count badge ── */
        .result-badge {
            display: inline-flex;
            align-items: center;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1.5px solid #bfdbfe;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* ── Table Card ── */
        .fs-table-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .05);
        }

        .fs-table {
            width: 100%;
            border-collapse: collapse;
        }

        .fs-table thead tr {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .fs-table thead th {
            font-size: 0.70rem;
            font-weight: 800;
            color: #64748b;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 13px 16px;
            white-space: nowrap;
        }

        .fs-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background .12s;
        }

        .fs-table tbody tr:last-child {
            border-bottom: none;
        }

        .fs-table tbody tr:hover {
            background: #f8fafc;
        }

        .fs-table tbody td {
            padding: 14px 16px;
            font-size: 0.875rem;
            color: #334155;
            vertical-align: middle;
        }

        /* ── Skeleton loader ── */
        .skeleton-row td {
            padding: 14px 16px;
        }

        .skeleton-cell {
            height: 16px;
            border-radius: 6px;
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* ── Shared badge / name styles ── */
        .row-index {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #64748b;
            font-size: .75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .station-name {
            font-weight: 700;
            color: #0f172a;
            display: block;
            margin-bottom: 2px;
            font-size: .9rem;
        }

        .station-sub {
            font-size: .76rem;
            color: #94a3b8;
        }

        .badge-code {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 7px;
            border: 1.5px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .04em;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: .76rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-status::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .badge-active {
            background: #f0fdf4;
            color: #16a34a;
            border: 1.5px solid #86efac;
        }

        .badge-active::before {
            background: #22c55e;
        }

        .badge-inactive {
            background: #fff1f2;
            color: #e11d48;
            border: 1.5px solid #fda4af;
        }

        .badge-inactive::before {
            background: #f43f5e;
        }

        .badge-fuel {
            display: inline-block;
            width: 50px;
            padding: 5px 0;
            text-align: center;
            border-radius: 4px;
            background-color: #e9ecef;
            font-size: 10px;
            font-weight: 500;
            border: 1px solid #dee2e6;
        }

        /* ── Actions ── */
        .actions-cell {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 9px;
            border: none;
            cursor: pointer;
            background: transparent;
            transition: background .15s;
            text-decoration: none;
        }

        .action-btn svg {
            width: 16px;
            height: 16px;
        }

        .action-btn-edit {
            color: #64748b;
        }

        .action-btn-edit:hover {
            background: #f1f5f9;
            color: #334155;
        }

        .action-btn-delete {
            color: #ef4444;
        }

        .action-btn-delete:hover {
            background: #fff1f2;
            color: #dc2626;
        }

        /* ── Alerts ── */
        .fs-alert {
            border-radius: 11px;
            padding: 12px 18px;
            margin-bottom: 18px;
            font-size: .875rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .fs-alert-success {
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
        }

        .fs-alert-danger {
            background: #fff1f2;
            border: 1px solid #fda4af;
            color: #be123c;
        }

        .fs-alert svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        /* ── Modals ── */
        .fs-modal .modal-content {
            border-radius: 18px;
            border: none;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .18);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .fs-modal .modal-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            border-radius: 18px 18px 0 0;
            padding: 20px 28px;
            border-bottom: none;
        }

        .fs-modal .modal-title {
            color: #fff;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: -.2px;
        }

        .fs-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: .8;
        }

        .fs-modal .modal-body {
            padding: 28px;
        }

        .fs-modal .modal-footer {
            padding: 16px 28px 24px;
            border-top: 1px solid #f1f5f9;
        }

        .fs-modal .section-title {
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #94a3b8;
            padding-bottom: 10px;
            margin-bottom: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .fs-modal .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .fs-modal .form-label .req {
            color: #ef4444;
            margin-left: 2px;
        }

        .fs-modal .form-control,
        .fs-modal .form-select {
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            padding: 9px 13px;
            font-size: .875rem;
            color: #1e293b;
            background: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color .15s, box-shadow .15s;
        }

        .fs-modal .form-control:focus,
        .fs-modal .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
            background: #fff;
        }

        .fuel-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .fuel-check {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 14px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            cursor: pointer;
            font-size: .83rem;
            font-weight: 600;
            color: #374151;
            transition: border-color .15s, background .15s;
            user-select: none;
        }

        .fuel-check input {
            display: none;
        }

        .fuel-check.checked {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #1e3a5f, #2563eb);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-size: .875rem;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: 0 4px 14px rgba(37, 99, 235, .3);
            transition: opacity .15s;
        }

        .btn-save:hover {
            opacity: .9;
        }

        .btn-cancel-modal {
            background: #f1f5f9;
            color: #475569;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: .875rem;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background .15s;
        }

        .btn-cancel-modal:hover {
            background: #e2e8f0;
        }

        .modal-loading {
            text-align: center;
            padding: 48px 0;
            color: #94a3b8;
            font-size: .9rem;
        }

        /* ── Select2 override ── */
        .select2-container--default .select2-selection--single {
            height: 40px !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 9px !important;
            display: flex;
            align-items: center;
            padding: 0 10px;
            background-color: #f8fafc !important;
        }

        .select2-selection__rendered {
            line-height: normal !important;
            padding-left: 0 !important;
            font-size: 14px;
            color: #495057;
        }

        .select2-selection__arrow {
            height: 100% !important;
            right: 8px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        .select2-dropdown {
            border-radius: 9px !important;
        }

        @media (max-width: 700px) {
            .fs-wrapper {
                padding: 20px 16px;
            }

            .fs-header {
                flex-direction: column;
                gap: 14px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4" x-data="reportApp()">

        {{-- ── Header ── --}}
        <div class="fs-header">
            <div class="fs-header-left">
                <h2>Filling Station Management</h2>
                <p>Manage and organize all filling station information</p>
            </div>
            <a href="javascript:void(0)" class="btn-add" onclick="openCreateModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Filling Station
            </a>
        </div>

        {{-- ── Flash Alerts ── --}}
        @if (session('success'))
            <div class="fs-alert fs-alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="fs-alert fs-alert-danger">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ══════════════════════════════════════════════════════
         FILTER CARD  (Alpine.js bound)
    ══════════════════════════════════════════════════════ --}}
        <div class="fs-filter-card">
            <div class="d-flex align-items-center gap-2 flex-wrap">

                {{-- Free-text search --}}
                <input type="text" class="fs-input" placeholder="Search name, code, owner…" x-model="filters.search"
                    @keyup.enter="applyFilter()" style="min-width:200px;">

                {{-- Station Name (Select2 — wired via $watch) --}}
                <select id="stationNameFilter" style="min-width:180px;">
                    <option value="">All Stations</option>
                    @foreach ($allStationNames as $s)
                        <option value="{{ $s->station_name }}">{{ $s->station_name }}</option>
                    @endforeach
                </select>

                {{-- Division --}}
                <select class="fs-input" x-model="filters.division" @change="onDivisionChange()">
                    <option value="">Select Division</option>
                    @foreach ($locations['divisions'] as $div)
                        <option value="{{ $div['name_en'] }}">{{ $div['name_en'] }}</option>
                    @endforeach
                </select>

                {{-- District (populated by Alpine) --}}
                <select class="fs-input" x-model="filters.district" @change="onDistrictChange()">
                    <option value="">Select District</option>
                    <template x-for="d in districts" :key="d.name_en">
                        <option :value="d.name_en" x-text="d.name_en"></option>
                    </template>
                </select>

                {{-- Upazila (populated by Alpine) --}}
                <select class="fs-input" x-model="filters.upazila">
                    <option value="">Select Upazila</option>
                    <template x-for="u in upazilas" :key="u.name_en">
                        <option :value="u.name_en" x-text="u.name_en"></option>
                    </template>
                </select>

                {{-- Company --}}
                <select class="fs-input" x-model="filters.company_id">
                    <option value="">All Companies</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ explode(' ', trim($company->name))[0] }}</option>
                    @endforeach
                </select>

                {{-- Status --}}
                {{-- <select class="fs-input" x-model="filters.status">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select> --}}

                {{-- Apply button --}}
                <button class="btn-filter" @click="applyFilter()" :disabled="loading">
                    <template x-if="loading">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                    </template>
                    <template x-if="!loading">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.2" style="width:14px;height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                    </template>
                    <span x-text="loading ? 'Filtering…' : 'Apply Filter'"></span>
                </button>

                {{-- Clear --}}
                <button class="btn-clear" @click="resetFilter()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.2" style="width:14px;height:14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear
                </button>

                {{-- Result count --}}
                <span class="result-badge ms-auto" x-show="totalResults !== null">
                    <span x-text="totalResults"></span>&nbsp;result<span x-text="totalResults === 1 ? '' : 's'"></span>
                </span>

            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
         TABLE CARD
    ══════════════════════════════════════════════════ --}}
        <div class="fs-table-card">
            <div class="table-responsive">
                <table class="fs-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Station Name</th>
                            <th>Code</th>
                            <th>Division</th>
                            <th>District</th>
                            <th>Upazila</th>
                            <th>Owner</th>
                            <th>Company</th>
                            <th>Capacity</th>
                            <th>Fuel Types</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    {{-- tbody: Alpine x-html controls this. Initial HTML set via init() --}}
                    <tbody id="tableContainer" x-html="tableHtml">
                        @include('backend.admin.pages.fillingStation.table', [
                            'filteredReports' => $filteredReports,
                        ])
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════
     CREATE MODAL
══════════════════════════════════════════════════ --}}
    <div class="modal fade fs-modal" id="createStationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2" style="width:18px;height:18px;margin-right:8px;vertical-align:-3px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Filling Station
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="createStationForm" enctype="multipart/form-data">
                        @csrf

                        <div class="section-title">Basic Information</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Company <span class="req">*</span></label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">— Select Company —</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Station Name <span class="req">*</span></label>
                                <input type="text" name="station_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Station Code <span class="req">*</span></label>
                                <input type="text" name="station_code" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Linked Depot <span class="req">*</span></label>
                                <select name="linked_depot" id="depotSelect" class="form-select">
                                    <option value="">— Select Depot —</option>
                                    @foreach ($depots as $depot)
                                        <option value="{{ $depot->id }}">{{ $depot->depot_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="section-title">Owner Information</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Owner Name</label>
                                <input type="text" name="owner_name" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Owner Phone</label>
                                <input type="text" name="owner_phone" class="form-control">
                            </div>
                        </div>

                        <div class="section-title">Location</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Division <span class="req">*</span></label>
                                <select id="division" name="division" class="form-select" required>
                                    <option value="">— Select Division —</option>
                                    @foreach ($locations['divisions'] as $division)
                                        <option value="{{ $division['name_en'] }}">{{ $division['name_en'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">District</label>
                                <select id="district" name="district" class="form-select">
                                    <option value="">— Select District —</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Upazila</label>
                                <select id="upazila" name="upazila" class="form-select">
                                    <option value="">— Select Upazila —</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Full Address</label>
                                <textarea name="address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="section-title">Technical Details</div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tank Capacity (Litres)</label>
                                <input type="number" name="tank_capacity" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fuel Types</label>
                                <div class="fuel-options">
                                    @foreach (['Petrol', 'Diesel', 'Octane', 'Others'] as $fuel)
                                        <label>
                                            <input type="checkbox" name="fuel_types[]" value="{{ $fuel }}">
                                            {{ $fuel }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Upload License (Optional)</label>
                                <input type="file" name="license_file" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-save" onclick="submitCreateForm()">
                        Save Station
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
     EDIT MODAL
══════════════════════════════════════════════════ --}}
    <div class="modal fade fs-modal" id="editStationModal" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2" style="width:18px;height:18px;margin-right:8px;vertical-align:-3px;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L7.5 19.79l-4 1 1-4 12.362-12.303z" />
                        </svg>
                        Edit Filling Station
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" id="editModalBody">
                    <div class="modal-loading">
                        <div class="spinner-border text-primary mb-3" role="status" style="width:2rem;height:2rem;">
                        </div>
                        <div>Loading station data…</div>
                    </div>
                </div>

                <div class="modal-footer" id="editModalFooter" style="display:none;">
                    <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-save" onclick="submitEditForm()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.2" style="width:15px;height:15px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // ═══════════════════════════════════════════════════════════
        //  LOCATION DATA (injected once from server)
        // ═══════════════════════════════════════════════════════════
        const locationData = @json($locations['divisions']);
        window.depots = @json($depots);

        // ═══════════════════════════════════════════════════════════
        //  ALPINE.JS — reportApp()
        // ═══════════════════════════════════════════════════════════
        function reportApp() {
            return {
                // ── Filter state ────────────────────────────────────────
                filters: {
                    search: '',
                    station_name: '',
                    division: '',
                    district: '',
                    upazila: '',
                    company_id: '',
                    status: '',
                },

                // ── Dependent dropdowns ──────────────────────────────────
                districts: [],
                upazilas: [],

                // ── Table state ──────────────────────────────────────────
                tableHtml: document.getElementById('tableContainer').innerHTML, // server-rendered HTML সংরক্ষণ
                initialTableHtml: '', // init() এ set হবে
                loading: false,
                totalResults: null,

                init() {
                    // Page load এর server-rendered HTML টা save করে রাখি reset এর জন্য
                    this.initialTableHtml = document.getElementById('tableContainer').innerHTML;
                },

                // ── Division → populate districts ───────────────────────
                onDivisionChange() {
                    this.filters.district = '';
                    this.filters.upazila = '';
                    this.upazilas = [];

                    const division = locationData.find(d => d.name_en === this.filters.division);
                    this.districts = division ? division.districts : [];
                },

                // ── District → populate upazilas ────────────────────────
                onDistrictChange() {
                    this.filters.upazila = '';

                    const division = locationData.find(d => d.name_en === this.filters.division);
                    if (!division) {
                        this.upazilas = [];
                        return;
                    }

                    const district = division.districts.find(d => d.name_en === this.filters.district);
                    this.upazilas = district ? district.police_stations : [];
                },

                // ── Apply Filter (AJAX) ──────────────────────────────────
                async applyFilter() {
                    // Sync Select2 station_name value into Alpine filters
                    this.filters.station_name = document.getElementById('stationNameFilter').value;

                    this.loading = true;

                    // Build query string from non-empty filter values
                    const params = new URLSearchParams();
                    Object.entries(this.filters).forEach(([key, val]) => {
                        if (val && val.trim() !== '') params.append(key, val.trim());
                    });

                    try {
                        const response = await fetch(`{{ route('admin.stations.index') }}?${params.toString()}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });

                        if (!response.ok) throw new Error('Network error');

                        const data = await response.json();

                        if (data.success) {
                            this.tableHtml = data.html;
                            this.totalResults = data.total;
                        }
                    } catch (err) {
                        console.error('Filter error:', err);
                        alert('Failed to apply filters. Please try again.');
                    } finally {
                        this.loading = false;
                    }
                },

                // ── Reset Filter ─────────────────────────────────────────
                resetFilter() {
                    // সব filter clear করি
                    this.filters = {
                        search: '',
                        station_name: '',
                        division: '',
                        district: '',
                        upazila: '',
                        company_id: '',
                        status: '',
                    };
                    this.districts = [];
                    this.upazilas = [];
                    this.totalResults = null;

                    // Select2 reset
                    $('#stationNameFilter').val(null).trigger('change');

                    // Server-rendered initial HTML restore করি — কোনো AJAX দরকার নেই
                    this.tableHtml = this.initialTableHtml;
                },
            };
        }

        // ═══════════════════════════════════════════════════════════
        //  SELECT2 INIT
        // ═══════════════════════════════════════════════════════════
        document.addEventListener('DOMContentLoaded', function() {

            // Station name filter (Select2)
            $('#stationNameFilter').select2({
                placeholder: 'Search station…',
                allowClear: true,
                width: '200px',
            });

            // Depot select inside Create Modal (reinit on modal open)
            $('#createStationModal').on('shown.bs.modal', function() {
                const $depot = $('#depotSelect');
                if ($depot.hasClass('select2-hidden-accessible')) $depot.select2('destroy');
                $depot.select2({
                    placeholder: 'Search Depot…',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#createStationModal'),
                    minimumResultsForSearch: 0,
                });
            });
        });

        // ═══════════════════════════════════════════════════════════
        //  LOCATION HELPERS (used by Edit Modal)
        // ═══════════════════════════════════════════════════════════
        function populateDistricts(divisionName, districtEl, selected = null) {
            districtEl.innerHTML = '<option value="">— Select District —</option>';
            const division = locationData.find(d => d.name_en === divisionName);
            if (!division) return;
            division.districts.forEach(dist => {
                const opt = document.createElement('option');
                opt.value = dist.name_en;
                opt.textContent = dist.name_en;
                if (selected === dist.name_en) opt.selected = true;
                districtEl.appendChild(opt);
            });
        }

        function populateUpazilas(divisionName, districtName, upazilaEl, selected = null) {
            upazilaEl.innerHTML = '<option value="">— Select Upazila —</option>';
            const division = locationData.find(d => d.name_en === divisionName);
            if (!division) return;
            const district = division.districts.find(d => d.name_en === districtName);
            if (!district) return;
            district.police_stations.forEach(up => {
                const opt = document.createElement('option');
                opt.value = up.name_en;
                opt.textContent = up.name_en;
                if (selected === up.name_en) opt.selected = true;
                upazilaEl.appendChild(opt);
            });
        }

        // ═══════════════════════════════════════════════════════════
        //  CREATE MODAL
        // ═══════════════════════════════════════════════════════════
        function openCreateModal() {
            new bootstrap.Modal(document.getElementById('createStationModal')).show();
        }

        // Create modal — division → district chain
        document.getElementById('division')?.addEventListener('change', function() {
            populateDistricts(this.value, document.getElementById('district'));
            document.getElementById('upazila').innerHTML = '<option value="">— Select Upazila —</option>';
        });
        document.getElementById('district')?.addEventListener('change', function() {
            populateUpazilas(
                document.getElementById('division').value,
                this.value,
                document.getElementById('upazila')
            );
        });

        function submitCreateForm() {
            const form = document.getElementById('createStationForm');
            const formData = new FormData(form);
            const btn = document.querySelector('#createStationModal .btn-save');

            btn.disabled = true;
            btn.innerHTML = 'Saving…';

            fetch(`{{ route('admin.stations.store') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(async res => {
                    const data = await res.json().catch(() => null);
                    if (!res.ok) throw data || {
                        message: 'Server Error'
                    };
                    return data;
                })
                .then(data => {
                    if (data.success) location.reload();
                    else alert(data.message || 'Failed');
                })
                .catch(err => alert(err?.message || 'Something went wrong'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = 'Save Station';
                });
        }

        // ═══════════════════════════════════════════════════════════
        //  EDIT MODAL
        // ═══════════════════════════════════════════════════════════
        let currentEditId = null;

        function openEditModal(id) {
            currentEditId = id;

            const body = document.getElementById('editModalBody');
            const footer = document.getElementById('editModalFooter');

            body.innerHTML = `
        <div class="modal-loading">
            <div class="spinner-border text-primary mb-3" role="status" style="width:2rem;height:2rem;"></div>
            <div>Loading station data…</div>
        </div>`;
            footer.style.display = 'none';

            new bootstrap.Modal(document.getElementById('editStationModal')).show();

            fetch(`/admin/stations/${id}/get`)
                .then(r => r.json())
                .then(s => {
                    const fuels = s.fuel_types || [];
                    const companies = @json($companies);

                    const companyOptions = companies.map(c =>
                        `<option value="${c.id}" ${s.company_id == c.id ? 'selected' : ''}>${c.name}</option>`
                    ).join('');

                    const fuelHtml = ['Petrol', 'Diesel', 'Octane', 'Others'].map(f => `
                <label class="fuel-check ${fuels.includes(f) ? 'checked' : ''}" data-fuel="${f}">
                    <input type="checkbox" name="fuel_types[]" value="${f}" ${fuels.includes(f) ? 'checked' : ''}>
                    ${f}
                </label>`).join('');

                    const divisionOptions = locationData.map(d =>
                        `<option value="${d.name_en}" ${s.division === d.name_en ? 'selected' : ''}>${d.name_en}</option>`
                    ).join('');

                    body.innerHTML = `
                <form id="editStationForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="section-title">Basic Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Company <span class="req">*</span></label>
                            <select name="company_id" class="form-select" required>
                                <option value="">— Select Company —</option>${companyOptions}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Station Name <span class="req">*</span></label>
                            <input type="text" name="station_name" class="form-control" value="${s.station_name ?? ''}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Station Code <span class="req">*</span></label>
                            <input type="text" name="station_code" class="form-control" value="${s.station_code ?? ''}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Linked Depot</label>
                            <select name="linked_depot" class="form-select">
                                <option value="">— Select Depot —</option>
                                ${window.depots.map(d =>
                                    `<option value="${d.id}" ${d.id == s.linked_depot ? 'selected' : ''}>${d.depot_name}</option>`
                                ).join('')}
                            </select>
                        </div>
                    </div>

                    <div class="section-title">Owner Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Owner Name</label>
                            <input type="text" name="owner_name" class="form-control" value="${s.owner_name ?? ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Owner Phone</label>
                            <input type="text" name="owner_phone" class="form-control" value="${s.owner_phone ?? ''}">
                        </div>
                    </div>

                    <div class="section-title">Location</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Division <span class="req">*</span></label>
                            <select id="edit_division" name="division" class="form-select" required>
                                <option value="">— Select Division —</option>${divisionOptions}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">District</label>
                            <select id="edit_district" name="district" class="form-select">
                                <option value="">— Select District —</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Upazila</label>
                            <select id="edit_upazila" name="upazila" class="form-select">
                                <option value="">— Select Upazila —</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="${s.address ?? ''}">
                        </div>
                    </div>

                    <div class="section-title">Technical Details</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tank Capacity (Litres)</label>
                            <input type="number" name="tank_capacity" class="form-control" value="${s.tank_capacity ?? ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active"   ${(s.status ?? 'active') === 'active'   ? 'selected' : ''}>Active</option>
                                <option value="inactive" ${s.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fuel Types</label>
                            <div class="fuel-options" id="editFuelOptions">${fuelHtml}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Replace License File</label>
                            <input type="file" name="license_file" class="form-control"
                                   accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </form>`;

                    // Wire dropdown chain for edit form
                    const editDivisionEl = document.getElementById('edit_division');
                    const editDistrictEl = document.getElementById('edit_district');
                    const editUpazilaEl = document.getElementById('edit_upazila');

                    if (s.division) populateDistricts(s.division, editDistrictEl, s.district);
                    if (s.division && s.district) populateUpazilas(s.division, s.district, editUpazilaEl, s.upazila);

                    editDivisionEl.addEventListener('change', function() {
                        populateDistricts(this.value, editDistrictEl);
                        editUpazilaEl.innerHTML = '<option value="">— Select Upazila —</option>';
                    });
                    editDistrictEl.addEventListener('change', function() {
                        populateUpazilas(editDivisionEl.value, this.value, editUpazilaEl);
                    });

                    // Fuel toggle
                    document.querySelectorAll('#editFuelOptions .fuel-check').forEach(label => {
                        label.addEventListener('click', function() {
                            const cb = this.querySelector('input');
                            cb.checked = !cb.checked;
                            this.classList.toggle('checked', cb.checked);
                        });
                    });

                    footer.style.display = 'flex';
                })
                .catch(() => {
                    body.innerHTML =
                        `<div class="text-center text-danger py-4">Failed to load station data. Please try again.</div>`;
                });
        }

        function submitEditForm() {
            const form = document.getElementById('editStationForm');
            if (!form) return;

            const formData = new FormData(form);
            formData.append('_method', 'PUT');

            const saveBtn = document.querySelector('#editModalFooter .btn-save');
            saveBtn.disabled = true;
            saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Saving…`;

            fetch(`/admin/stations/${currentEditId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                })
                .then(r => {
                    if (r.redirected) {
                        window.location.href = r.url;
                        return;
                    }
                    return r.json().then(data => {
                        if (data.success) window.location.reload();
                        else alert(data.message || 'Update failed.');
                    });
                })
                .catch(() => alert('Something went wrong. Please try again.'))
                .finally(() => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2.2" style="width:15px;height:15px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg> Save Changes`;
                });
        }

        // ═══════════════════════════════════════════════════════════
        //  DELETE
        // ═══════════════════════════════════════════════════════════
        function deleteStation(id) {
            if (!confirm('Are you sure you want to delete this station?')) return;

            const btn = event.currentTarget;
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span>`;

            fetch(`/admin/stations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const row = btn.closest('tr');
                        if (row) {
                            row.style.transition = 'opacity 0.3s';
                            row.style.opacity = '0';
                            setTimeout(() => location.reload(), 300);
                        } else {
                            location.reload();
                        }
                    } else {
                        alert(data.message || 'Delete failed.');
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    }
                })
                .catch(() => {
                    alert('Something went wrong. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
        }
    </script>
@endpush
