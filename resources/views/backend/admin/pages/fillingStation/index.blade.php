@extends('backend.admin.layouts.app')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
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

        /* Header */
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
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .btn-add:hover {
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
        }

        .btn-add svg {
            width: 15px;
            height: 15px;
        }

        /* Stat Cards */
        .fs-stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            border-radius: 16px;
            padding: 20px 22px;
            position: relative;
            overflow: hidden;
            cursor: default;
            transition: transform 0.18s, box-shadow 0.18s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            right: -16px;
            bottom: -16px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            right: 18px;
            top: -20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .stat-card-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: rgba(255, 255, 255, 0.82);
            margin-bottom: 8px;
        }

        .stat-card-value {
            font-size: 2.4rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            letter-spacing: -1px;
        }

        .stat-card-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.22;
        }

        .stat-card-icon svg {
            width: 44px;
            height: 44px;
        }

        .stat-total {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            box-shadow: 0 4px 16px rgba(14, 165, 233, 0.3);
        }

        .stat-active {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            box-shadow: 0 4px 16px rgba(34, 197, 94, 0.3);
        }

        .stat-inactive {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            box-shadow: 0 4px 16px rgba(244, 63, 94, 0.3);
        }

        .stat-govt {
            background: linear-gradient(135deg, #a855f7, #7c3aed);
            box-shadow: 0 4px 16px rgba(168, 85, 247, 0.3);
        }

        .stat-private {
            background: linear-gradient(135deg, #f97316, #ea580c);
            box-shadow: 0 4px 16px rgba(249, 115, 22, 0.3);
        }

        /* Filter Card */
        .fs-filter-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        .fs-filter-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .fs-filter-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #475569;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .fs-filter-label svg {
            width: 14px;
            height: 14px;
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
            transition: border-color 0.15s, box-shadow 0.15s;
            flex: 1;
            min-width: 160px;
        }

        .fs-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: #fff;
        }

        .fs-input::placeholder {
            color: #94a3b8;
        }

        .fs-search-wrap {
            position: relative;
            flex: 2;
            min-width: 200px;
        }

        .fs-search-wrap svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: #94a3b8;
        }

        .fs-search-wrap input {
            padding-left: 36px;
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
            transition: background 0.15s;
            white-space: nowrap;
        }

        .btn-clear:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-clear svg {
            width: 13px;
            height: 13px;
        }

        /* Table Card */
        .fs-table-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 13px 16px;
            white-space: nowrap;
        }

        .fs-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.12s;
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

        .row-index {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.75rem;
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
            font-size: 0.9rem;
        }

        .station-sub {
            font-size: 0.76rem;
            color: #94a3b8;
        }

        .badge-code {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 7px;
            border: 1.5px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.76rem;
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
            padding: 3px 8px;
            border-radius: 5px;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.70rem;
            font-weight: 700;
            margin: 1px 2px;
            border: 1px solid #e2e8f0;
        }

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
            transition: background 0.15s;
            text-decoration: none;
        }

        .action-btn svg {
            width: 16px;
            height: 16px;
        }

        .action-btn-view:hover {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .action-btn-view {
            color: #2563eb;
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

        .fs-alert {
            border-radius: 11px;
            padding: 12px 18px;
            margin-bottom: 18px;
            font-size: 0.875rem;
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

        .fs-pagination {
            padding: 16px 20px;
            border-top: 1px solid #f1f5f9;
        }

        .fs-pagination .pagination {
            margin: 0;
        }

        .empty-row td {
            text-align: center;
            color: #94a3b8;
            padding: 56px 20px;
            font-size: 0.875rem;
        }

        .empty-icon {
            font-size: 2.5rem;
            margin-bottom: 8px;
            display: block;
            opacity: 0.35;
        }

        /* ── Edit Modal Overrides ── */
        .fs-modal .modal-content {
            border-radius: 18px;
            border: none;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18);
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
            letter-spacing: -0.2px;
        }

        .fs-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .fs-modal .modal-body {
            padding: 28px;
        }

        .fs-modal .modal-footer {
            padding: 16px 28px 24px;
            border-top: 1px solid #f1f5f9;
        }

        .fs-modal .section-title {
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #94a3b8;
            padding-bottom: 10px;
            margin-bottom: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .fs-modal .form-label {
            font-size: 0.8rem;
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
            font-size: 0.875rem;
            color: #1e293b;
            background: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .fs-modal .form-control:focus,
        .fs-modal .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: #fff;
        }

        .fs-modal .invalid-feedback {
            font-size: 0.75rem;
        }

        /* Fuel checkboxes in modal */
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
            font-size: 0.83rem;
            font-weight: 600;
            color: #374151;
            transition: border-color 0.15s, background 0.15s;
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

        /* File upload in modal */
        .file-upload-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 13px;
            border-radius: 9px;
            border: 1.5px dashed #cbd5e1;
            cursor: pointer;
            font-size: 0.84rem;
            color: #6b7280;
            transition: border-color 0.15s;
            background: #f8fafc;
        }

        .file-upload-label:hover {
            border-color: #2563eb;
            color: #2563eb;
        }

        .file-upload-label svg {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
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
            font-size: 0.875rem;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
            transition: opacity 0.15s;
        }

        .btn-save:hover {
            opacity: 0.9;
        }

        .btn-cancel-modal {
            background: #f1f5f9;
            color: #475569;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background 0.15s;
        }

        .btn-cancel-modal:hover {
            background: #e2e8f0;
        }

        /* Spinner */
        .modal-loading {
            text-align: center;
            padding: 48px 0;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        @media (max-width: 1100px) {
            .fs-stats {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 700px) {
            .fs-wrapper {
                padding: 20px 16px;
            }

            .fs-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .fs-header {
                flex-direction: column;
                gap: 14px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="fs-wrapper">

        {{-- HEADER --}}
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

        {{-- STAT CARDS --}}
        {{-- <div class="fs-stats">
            <div class="stat-card stat-total">
                <div class="stat-card-label">Total Stations</div>
                <div class="stat-card-value">{{ $totalStations }}</div>
                <div class="stat-card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M19.5 3h-15A2.5 2.5 0 0 0 2 5.5v13A2.5 2.5 0 0 0 4.5 21h15A2.5 2.5 0 0 0 22 18.5v-13A2.5 2.5 0 0 0 19.5 3zM12 17a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                    </svg></div>
            </div>
            <div class="stat-card stat-active">
                <div class="stat-card-label">Active</div>
                <div class="stat-card-value">{{ $activeStations }}</div>
                <div class="stat-card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14l-4-4 1.41-1.41L11 13.17l6.59-6.59L19 8l-8 8z" />
                    </svg></div>
            </div>
            <div class="stat-card stat-inactive">
                <div class="stat-card-label">Inactive</div>
                <div class="stat-card-value">{{ $inactiveStations }}</div>
                <div class="stat-card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z" />
                    </svg></div>
            </div>
            <div class="stat-card stat-govt">
                <div class="stat-card-label">Government</div>
                <div class="stat-card-value">{{ $govtStations }}</div>
                <div class="stat-card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v2h20V7L12 2zM4 11v7H2v2h20v-2h-2v-7h-2v7h-3v-7h-2v7H9v-7H7v7H5v-7H4z" />
                    </svg></div>
            </div>
            <div class="stat-card stat-private">
                <div class="stat-card-label">Private</div>
                <div class="stat-card-value">{{ $privateStations }}</div>
                <div class="stat-card-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                    </svg></div>
            </div>
        </div> --}}

        {{-- ALERTS --}}
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

        {{-- FILTERS --}}
        <div class="fs-filter-card">
            <div class="fs-filter-row">
                <span class="fs-filter-label">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                    Filters
                </span>
                <div class="fs-search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <input type="text" class="fs-input" id="searchInput" placeholder="Search by name, owner, code...">
                </div>
                <select class="fs-input" id="divisionFilter" style="min-width:140px;cursor:pointer;">
                    <option value="">All Divisions</option>
                    @foreach ($locations['divisions'] as $div)
                        <option value="{{ $div['name_en'] }}">{{ $div['name_en'] }}</option>
                    @endforeach
                </select>
                <select class="fs-input" id="statusFilter" style="min-width:130px;cursor:pointer;">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select class="fs-input" id="companyFilter" style="min-width:140px;cursor:pointer;">
                    <option value="">All Companies</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->name }}">{{ $company->name }}</option>
                    @endforeach
                </select>
                <button class="btn-clear" onclick="clearFilters()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear Filters
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="fs-table-card">
            <table class="fs-table" id="stationTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Station Name</th>
                        <th>Code</th>
                        <th>Division</th>
                        <th>District</th>
                        <th>Owner</th>
                        <th>Company</th>
                        <th>Capacity</th>
                        <th>Fuel Types</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($stations as $station)
                        <tr data-division="{{ strtolower($station->division ?? '') }}"
                            data-status="{{ strtolower($station->status ?? 'active') }}"
                            data-company="{{ strtolower($station->company->name ?? '') }}">
                            <td><span class="row-index">{{ $loop->iteration }}</span></td>

                            <td>
                                <span class="station-name">{{ $station->station_name }}</span>
                                <span class="station-sub">{{ $station->linked_depot ?? '—' }}</span>
                            </td>

                            <td><span class="badge-code">{{ $station->station_code }}</span></td>
                            <td>{{ $station->division ?? '—' }}</td>

                            <td>
                                {{ $station->district ?? '—' }}
                                @if ($station->upazila)
                                    <br><span class="station-sub">{{ $station->upazila }}</span>
                                @endif
                            </td>

                            <td>
                                <span class="station-name"
                                    style="font-size:.85rem;">{{ $station->owner_name ?? '—' }}</span>
                                <span class="station-sub">{{ $station->owner_phone ?? '' }}</span>
                            </td>

                            <td>{{ $station->company->name ?? '—' }}</td>
                            <td>{{ $station->tank_capacity ? number_format($station->tank_capacity) . ' L' : '—' }}</td>

                            <td>
                                @if ($station->fuel_types)
                                    @foreach ($station->fuel_types as $fuel)
                                        <span class="badge-fuel">{{ $fuel }}</span>
                                    @endforeach
                                @else
                                    <span class="station-sub">—</span>
                                @endif
                            </td>

                            <td>
                                @php $st = strtolower($station->status ?? 'active'); @endphp
                                <span class="badge-status {{ $st === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                    {{ ucfirst($st) }}
                                </span>
                            </td>

                            <td>
                                <div class="actions-cell">
                                    {{-- View --}}
                                    <!-- <a href="{{ route('admin.stations.show', $station->id) }}" class="action-btn action-btn-view" title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a> -->

                                    {{-- Edit → opens modal --}}
                                    <button type="button" class="action-btn action-btn-edit" title="Edit"
                                        onclick="openEditModal({{ $station->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L7.5 19.79l-4 1 1-4 12.362-12.303z" />
                                        </svg>
                                    </button>

                                    {{-- Delete --}}
                                    <!-- Delete Button - AJAX Version -->
                                    <button type="button" class="action-btn action-btn-delete" title="Delete"
                                        onclick="deleteStation({{ $station->id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="empty-row">
                            <td colspan="11">
                                <span class="empty-icon">⛽</span>
                                No filling stations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div id="noResultsRow"
                style="display:none;text-align:center;padding:48px 20px;color:#94a3b8;font-size:0.875rem;">
                <span style="font-size:2rem;display:block;margin-bottom:8px;opacity:0.35;">🔍</span>
                No matching stations found.
            </div>

            @if ($stations->hasPages())
                <div class="fs-pagination">{{ $stations->links() }}</div>
            @endif
        </div>

    </div>
    <!-- CREATE MODAL -->
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

                        {{-- Basic Info --}}
                        <div class="section-title">Basic Information</div>
                        <div class="row g-3 mb-4">

                            <div class="col-md-6">
                                <label class="form-label">Company <span class="req">*</span></label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">— Select Company —</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">
                                            {{ $company->name }}
                                        </option>
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
                                <label class="form-label">Linked Depot</label>
                                <input type="text" name="linked_depot" class="form-control">
                            </div>

                        </div>

                        {{-- Owner Info --}}
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

                        {{-- Location --}}
                        <div class="section-title">Location</div>
                        <div class="row g-3 mb-4">

                            <div class="col-md-4">
                                <label class="form-label">Division <span class="req">*</span></label>
                                <select id="division" name="division" class="form-select" required>
                                    <option value="">— Select Division —</option>
                                    @foreach ($locations['divisions'] as $division)
                                        <option value="{{ $division['name_en'] }}">
                                            {{ $division['name_en'] }}
                                        </option>
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

                        {{-- Technical --}}
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
                                    <label><input type="checkbox" name="fuel_types[]" value="Petrol"> Petrol</label>
                                    <label><input type="checkbox" name="fuel_types[]" value="Diesel"> Diesel</label>
                                    <label><input type="checkbox" name="fuel_types[]" value="Octane"> Octane</label>
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


    {{-- ══════════════════════════════════════════
     EDIT MODAL (Bootstrap 5)
══════════════════════════════════════════ --}}
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
                        <div>Loading station data...</div>
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
<script>
// ═══════════════════════════════════════════════════════════
//  LOCATIONS DATA (একবারই declare)
// ═══════════════════════════════════════════════════════════
const locations = @json($locations['divisions']);
 
// ═══════════════════════════════════════════════════════════
//  HELPER — Division → Districts populate
// ═══════════════════════════════════════════════════════════
function populateDistricts(divisionName, districtEl, selectedDistrict = null) {
    districtEl.innerHTML = '<option value="">— Select District —</option>';
 
    const division = locations.find(d => d.name_en === divisionName);
    if (!division) return;
 
    division.districts.forEach(dist => {
        const opt = document.createElement('option');
        opt.value       = dist.name_en;
        opt.textContent = dist.name_en;
        if (selectedDistrict === dist.name_en) opt.selected = true;
        districtEl.appendChild(opt);
    });
}
 
// ═══════════════════════════════════════════════════════════
//  HELPER — District → Upazilas populate
// ═══════════════════════════════════════════════════════════
function populateUpazilas(divisionName, districtName, upazilaEl, selectedUpazila = null) {
    upazilaEl.innerHTML = '<option value="">— Select Upazila —</option>';
 
    const division = locations.find(d => d.name_en === divisionName);
    if (!division) return;
 
    const district = division.districts.find(d => d.name_en === districtName);
    if (!district) return;
 
    district.police_stations.forEach(up => {
        const opt = document.createElement('option');
        opt.value       = up.name_en;
        opt.textContent = up.name_en;
        if (selectedUpazila === up.name_en) opt.selected = true;
        upazilaEl.appendChild(opt);
    });
}
 
// ═══════════════════════════════════════════════════════════
//  CREATE MODAL — Dropdown chain
// ═══════════════════════════════════════════════════════════
const createDivision = document.getElementById('division');
const createDistrict = document.getElementById('district');
const createUpazila  = document.getElementById('upazila');
 
createDivision?.addEventListener('change', function () {
    populateDistricts(this.value, createDistrict);
    createUpazila.innerHTML = '<option value="">— Select Upazila —</option>';
});
 
createDistrict?.addEventListener('change', function () {
    populateUpazilas(createDivision.value, this.value, createUpazila);
});
 
// ═══════════════════════════════════════════════════════════
//  TABLE FILTER
// ═══════════════════════════════════════════════════════════
const searchInput    = document.getElementById('searchInput');
const divisionFilter = document.getElementById('divisionFilter');
const statusFilter   = document.getElementById('statusFilter');
const companyFilter  = document.getElementById('companyFilter');
const tableBody      = document.getElementById('tableBody');
const noResults      = document.getElementById('noResultsRow');
 
function applyFilters() {
    const q        = searchInput.value.toLowerCase().trim();
    const division = divisionFilter.value.toLowerCase();
    const status   = statusFilter.value.toLowerCase();
    const company  = companyFilter.value.toLowerCase();
    let visible    = 0;
 
    tableBody.querySelectorAll('tr').forEach(row => {
        if (row.classList.contains('empty-row')) return;
        const matchQ = !q        || row.innerText.toLowerCase().includes(q);
        const matchD = !division || (row.dataset.division || '').toLowerCase().includes(division);
        const matchS = !status   || (row.dataset.status   || '').toLowerCase() === status;
        const matchC = !company  || (row.dataset.company  || '').toLowerCase().includes(company);
        const show   = matchQ && matchD && matchS && matchC;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
 
    noResults.style.display = visible === 0 ? 'block' : 'none';
}
 
function clearFilters() {
    searchInput.value = divisionFilter.value = statusFilter.value = companyFilter.value = '';
    applyFilters();
}
 
searchInput?.addEventListener('input', applyFilters);
divisionFilter?.addEventListener('change', applyFilters);
statusFilter?.addEventListener('change', applyFilters);
companyFilter?.addEventListener('change', applyFilters);
 
// ═══════════════════════════════════════════════════════════
//  CREATE MODAL — Open
// ═══════════════════════════════════════════════════════════
function openCreateModal() {
    const modal = new bootstrap.Modal(document.getElementById('createStationModal'));
    modal.show();
}
 
// ═══════════════════════════════════════════════════════════
//  CREATE MODAL — Submit
// ═══════════════════════════════════════════════════════════
function submitCreateForm() {
    const form    = document.getElementById('createStationForm');
    const formData = new FormData(form);
    const btn     = document.querySelector('#createStationModal .btn-save');
 
    if (btn) { btn.disabled = true; btn.innerHTML = 'Saving...'; }
 
    fetch(`/admin/stations`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(async res => {
        const data = await res.json().catch(() => null);
        if (!res.ok) throw data || { message: 'Server Error' };
        return data;
    })
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Failed');
    })
    .catch(err => alert(err?.message || 'Something went wrong'))
    .finally(() => {
        if (btn) { btn.disabled = false; btn.innerHTML = 'Save Station'; }
    });
}
 
// ═══════════════════════════════════════════════════════════
//  EDIT MODAL — Open (AJAX দিয়ে data load, তারপর dropdown chain)
// ═══════════════════════════════════════════════════════════
let currentEditId = null;
 
function openEditModal(id) {
    currentEditId = id;
 
    const body   = document.getElementById('editModalBody');
    const footer = document.getElementById('editModalFooter');
 
    // Loading state
    body.innerHTML = `
        <div class="modal-loading">
            <div class="spinner-border text-primary mb-3" role="status" style="width:2rem;height:2rem;"></div>
            <div>Loading station data...</div>
        </div>`;
    footer.style.display = 'none';
 
    const modal = new bootstrap.Modal(document.getElementById('editStationModal'));
    modal.show();
 
    // Fetch station data
    fetch(`/admin/stations/${id}/get`)
        .then(r => r.json())
        .then(s => {
            const fuels    = s.fuel_types || [];
            const companies = @json($companies);
 
            const companyOptions = companies.map(c =>
                `<option value="${c.id}" ${s.company_id == c.id ? 'selected' : ''}>${c.name}</option>`
            ).join('');
 
            const fuelHtml = ['Petrol', 'Diesel', 'Octane'].map(f => `
                <label class="fuel-check ${fuels.includes(f) ? 'checked' : ''}" data-fuel="${f}">
                    <input type="checkbox" name="fuel_types[]" value="${f}" ${fuels.includes(f) ? 'checked' : ''}>
                    ${f}
                </label>`).join('');
 
            // Division options build — সব division option add করি
            const divisionOptions = locations.map(d =>
                `<option value="${d.name_en}" ${s.division === d.name_en ? 'selected' : ''}>${d.name_en}</option>`
            ).join('');
 
            // Form HTML inject
            body.innerHTML = `
                <form id="editStationForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
 
                    {{-- Basic Info --}}
                    <div class="section-title">Basic Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Company <span class="req">*</span></label>
                            <select name="company_id" class="form-select" required>
                                <option value="">— Select Company —</option>
                                ${companyOptions}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Station Name <span class="req">*</span></label>
                            <input type="text" name="station_name" class="form-control"
                                   value="${s.station_name ?? ''}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Station Code <span class="req">*</span></label>
                            <input type="text" name="station_code" class="form-control"
                                   value="${s.station_code ?? ''}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Linked Depot</label>
                            <input type="text" name="linked_depot" class="form-control"
                                   value="${s.linked_depot ?? ''}">
                        </div>
                    </div>
 
                    {{-- Owner Info --}}
                    <div class="section-title">Owner Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Owner Name</label>
                            <input type="text" name="owner_name" class="form-control"
                                   value="${s.owner_name ?? ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Owner Phone</label>
                            <input type="text" name="owner_phone" class="form-control"
                                   value="${s.owner_phone ?? ''}">
                        </div>
                    </div>
 
                    {{-- Location --}}
                    <div class="section-title">Location</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Division <span class="req">*</span></label>
                            <select id="edit_division" name="division" class="form-select" required>
                                <option value="">— Select Division —</option>
                                ${divisionOptions}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">District <span class="req">*</span></label>
                            <select id="edit_district" name="district" class="form-select" required>
                                <option value="">— Select District —</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Upazila</label>
                            <select id="edit_upazila" name="upazila" class="form-select">
                                <option value="">— Select Upazila —</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control"
                                   value="${s.address ?? ''}">
                        </div>
                    </div>
 
                    {{-- Technical --}}
                    <div class="section-title">Technical Details</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tank Capacity (Litres)</label>
                            <input type="number" name="tank_capacity" class="form-control"
                                   value="${s.tank_capacity ?? ''}">
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
                            <label class="file-upload-label" for="edit_license_file">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span id="editFileLabelText">Click to upload (PDF/Image)</span>
                            </label>
                            <input type="file" name="license_file" id="edit_license_file"
                                   accept=".pdf,.jpg,.jpeg,.png" style="display:none;"
                                   onchange="document.getElementById('editFileLabelText').textContent = this.files[0]?.name || 'Click to upload'">
                        </div>
                    </div>
                </form>`;
 
            // ── এখানে DOM তৈরি হয়েছে, এখন dropdown chain setup করি ──
            const editDivisionEl = document.getElementById('edit_division');
            const editDistrictEl = document.getElementById('edit_district');
            const editUpazilaEl  = document.getElementById('edit_upazila');
 
            // ১. আগের data দিয়ে district ও upazila populate করি
            if (s.division) {
                populateDistricts(s.division, editDistrictEl, s.district);
            }
            if (s.division && s.district) {
                populateUpazilas(s.division, s.district, editUpazilaEl, s.upazila);
            }
 
            // ২. Division change → district reload
            editDivisionEl.addEventListener('change', function () {
                populateDistricts(this.value, editDistrictEl);
                editUpazilaEl.innerHTML = '<option value="">— Select Upazila —</option>';
            });
 
            // ৩. District change → upazila reload
            editDistrictEl.addEventListener('change', function () {
                populateUpazilas(editDivisionEl.value, this.value, editUpazilaEl);
            });
 
            // ── Fuel type toggle ──
            document.querySelectorAll('#editFuelOptions .fuel-check').forEach(label => {
                label.addEventListener('click', function () {
                    const cb = this.querySelector('input');
                    cb.checked = !cb.checked;
                    this.classList.toggle('checked', cb.checked);
                });
            });
 
            footer.style.display = 'flex';
        })
        .catch(() => {
            body.innerHTML = `
                <div class="text-center text-danger py-4">
                    Failed to load station data. Please try again.
                </div>`;
        });
}
 
// ═══════════════════════════════════════════════════════════
//  EDIT MODAL — Submit
// ═══════════════════════════════════════════════════════════
function submitEditForm() {
    const form = document.getElementById('editStationForm');
    if (!form) return;
 
    const formData = new FormData(form);
    formData.append('_method', 'PUT');
 
    const saveBtn = document.querySelector('#editModalFooter .btn-save');
    saveBtn.disabled = true;
    saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Saving...`;
 
    fetch(`/admin/stations/${currentEditId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData,
    })
    .then(r => {
        if (r.redirected) { window.location.href = r.url; return; }
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
//  DELETE — AJAX
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
                row.style.opacity    = '0';
                setTimeout(() => location.reload(), 300);
            } else {
                location.reload();
            }
        } else {
            alert(data.message || 'Delete failed.');
            btn.disabled  = false;
            btn.innerHTML = originalHTML;
        }
    })
    .catch(() => {
        alert('Something went wrong. Please try again.');
        btn.disabled  = false;
        btn.innerHTML = originalHTML;
    });
}
</script>
@endpush
 
