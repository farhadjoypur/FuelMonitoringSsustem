@extends('backend.admin.layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    *, *::before, *::after { box-sizing: border-box; }

    body, .main-content { background-color: #f0f4f8 !important; font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── Wrapper ── */
    .fs-wrapper { padding: 32px 36px; }

    /* ── Header ── */
    .fs-header {
        display: flex; align-items: flex-start;
        justify-content: space-between; margin-bottom: 28px;
    }
    .fs-header-left h2 {
        font-size: 1.65rem; font-weight: 800;
        color: #0f172a; margin: 0 0 4px; letter-spacing: -0.4px;
    }
    .fs-header-left p { font-size: 0.85rem; color: #64748b; margin: 0; }

    .btn-add {
        display: inline-flex; align-items: center; gap: 8px;
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        color: #fff; border: none; border-radius: 12px;
        padding: 12px 22px; font-size: 0.875rem; font-weight: 700;
        text-decoration: none; white-space: nowrap; letter-spacing: 0.01em;
        box-shadow: 0 4px 14px rgba(37,99,235,0.35);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .btn-add:hover {
        color: #fff; text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(37,99,235,0.45);
    }
    .btn-add svg { width: 15px; height: 15px; }

    /* ── Stat Cards ── */
    .fs-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 24px; }

    .stat-card {
        border-radius: 16px; padding: 20px 22px;
        position: relative; overflow: hidden; cursor: default;
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.15); }
    .stat-card::after {
        content: ''; position: absolute; right: -16px; bottom: -16px;
        width: 80px; height: 80px; border-radius: 50%;
        background: rgba(255,255,255,0.12);
    }
    .stat-card::before {
        content: ''; position: absolute; right: 18px; top: -20px;
        width: 60px; height: 60px; border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }
    .stat-card-label {
        font-size: 0.75rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: rgba(255,255,255,0.82); margin-bottom: 8px;
    }
    .stat-card-value {
        font-size: 2.4rem; font-weight: 800;
        color: #fff; line-height: 1; letter-spacing: -1px;
    }
    .stat-card-icon {
        position: absolute; right: 18px; top: 50%;
        transform: translateY(-50%); opacity: 0.22;
    }
    .stat-card-icon svg { width: 44px; height: 44px; }

    .stat-total   { background: linear-gradient(135deg, #0ea5e9, #06b6d4); box-shadow: 0 4px 16px rgba(14,165,233,0.3); }
    .stat-active  { background: linear-gradient(135deg, #22c55e, #16a34a); box-shadow: 0 4px 16px rgba(34,197,94,0.3); }
    .stat-inactive{ background: linear-gradient(135deg, #f43f5e, #e11d48); box-shadow: 0 4px 16px rgba(244,63,94,0.3); }
    .stat-govt    { background: linear-gradient(135deg, #a855f7, #7c3aed); box-shadow: 0 4px 16px rgba(168,85,247,0.3); }
    .stat-private { background: linear-gradient(135deg, #f97316, #ea580c); box-shadow: 0 4px 16px rgba(249,115,22,0.3); }

    /* ── Filter Card ── */
    .fs-filter-card {
        background: #fff; border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 16px 20px; margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .fs-filter-row {
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    }
    .fs-filter-label {
        display: flex; align-items: center; gap: 6px;
        font-size: 0.8rem; font-weight: 700; color: #475569;
        letter-spacing: 0.03em; text-transform: uppercase; white-space: nowrap;
    }
    .fs-filter-label svg { width: 14px; height: 14px; color: #64748b; }

    .fs-input {
        border: 1.5px solid #e2e8f0; border-radius: 9px;
        padding: 9px 13px; font-size: 0.875rem; color: #1e293b;
        background: #f8fafc; outline: none;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: border-color 0.15s, box-shadow 0.15s;
        flex: 1; min-width: 160px;
    }
    .fs-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        background: #fff;
    }
    .fs-input::placeholder { color: #94a3b8; }

    .fs-search-wrap { position: relative; flex: 2; min-width: 200px; }
    .fs-search-wrap svg {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%); width: 15px; height: 15px; color: #94a3b8;
    }
    .fs-search-wrap input { padding-left: 36px; }

    .btn-clear {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f1f5f9; color: #475569; border: 1.5px solid #e2e8f0;
        border-radius: 9px; padding: 9px 16px;
        font-size: 0.82rem; font-weight: 600; cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: background 0.15s, color 0.15s;
        white-space: nowrap;
    }
    .btn-clear:hover { background: #e2e8f0; color: #1e293b; }
    .btn-clear svg { width: 13px; height: 13px; }

    /* ── Table Card ── */
    .fs-table-card {
        background: #fff; border-radius: 16px;
        border: 1px solid #e2e8f0; overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .fs-table { width: 100%; border-collapse: collapse; }
    .fs-table thead tr { background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
    .fs-table thead th {
        font-size: 0.70rem; font-weight: 800;
        color: #64748b; letter-spacing: 0.08em;
        text-transform: uppercase; padding: 13px 16px; white-space: nowrap;
    }
    .fs-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.12s;
    }
    .fs-table tbody tr:last-child { border-bottom: none; }
    .fs-table tbody tr:hover { background: #f8fafc; }
    .fs-table tbody td { padding: 14px 16px; font-size: 0.875rem; color: #334155; vertical-align: middle; }

    /* Row index circle */
    .row-index {
        width: 28px; height: 28px; border-radius: 8px;
        background: #f1f5f9; color: #64748b;
        font-size: 0.75rem; font-weight: 700;
        display: inline-flex; align-items: center; justify-content: center;
    }

    .station-name { font-weight: 700; color: #0f172a; display: block; margin-bottom: 2px; font-size: 0.9rem; }
    .station-sub { font-size: 0.76rem; color: #94a3b8; }

    .badge-code {
        display: inline-block; padding: 4px 10px; border-radius: 7px;
        border: 1.5px solid #bfdbfe; background: #eff6ff;
        color: #1d4ed8; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.04em;
    }

    .badge-status {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 20px;
        font-size: 0.76rem; font-weight: 700; white-space: nowrap;
    }
    .badge-status::before {
        content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
    }
    .badge-active   { background: #f0fdf4; color: #16a34a; border: 1.5px solid #86efac; }
    .badge-active::before { background: #22c55e; }
    .badge-inactive { background: #fff1f2; color: #e11d48; border: 1.5px solid #fda4af; }
    .badge-inactive::before { background: #f43f5e; }

    .badge-fuel {
        display: inline-block; padding: 3px 8px; border-radius: 5px;
        background: #f1f5f9; color: #475569;
        font-size: 0.70rem; font-weight: 700; margin: 1px 2px;
        border: 1px solid #e2e8f0;
    }

    /* Actions */
    .actions-cell { display: flex; align-items: center; gap: 4px; }
    .action-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 34px; height: 34px; border-radius: 9px;
        border: none; cursor: pointer; background: transparent;
        transition: background 0.15s; text-decoration: none;
    }
    .action-btn svg { width: 16px; height: 16px; }
    .action-btn-view  { color: #2563eb; }
    .action-btn-view:hover  { background: #eff6ff; color: #1d4ed8; }
    .action-btn-edit  { color: #64748b; }
    .action-btn-edit:hover  { background: #f1f5f9; color: #334155; }
    .action-btn-delete{ color: #ef4444; }
    .action-btn-delete:hover{ background: #fff1f2; color: #dc2626; }

    /* Alert */
    .fs-alert {
        border-radius: 11px; padding: 12px 18px; margin-bottom: 18px;
        font-size: 0.875rem; display: flex; align-items: center; gap: 10px;
    }
    .fs-alert-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
    .fs-alert-danger  { background: #fff1f2; border: 1px solid #fda4af; color: #be123c; }
    .fs-alert svg { width: 16px; height: 16px; flex-shrink: 0; }

    /* Pagination */
    .fs-pagination { padding: 16px 20px; border-top: 1px solid #f1f5f9; }
    .fs-pagination .pagination { margin: 0; }

    /* Empty */
    .empty-row td {
        text-align: center; color: #94a3b8;
        padding: 56px 20px; font-size: 0.875rem;
    }
    .empty-icon { font-size: 2.5rem; margin-bottom: 8px; display: block; opacity: 0.35; }

    /* Responsive */
    @media (max-width: 1100px) {
        .fs-stats { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 700px) {
        .fs-wrapper { padding: 20px 16px; }
        .fs-stats { grid-template-columns: repeat(2, 1fr); }
        .fs-header { flex-direction: column; gap: 14px; }
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
        <a href="{{ route('stations.create') }}" class="btn-add">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add Filling Station
        </a>
    </div>

    {{-- STAT CARDS --}}
    <div class="fs-stats">
        <div class="stat-card stat-total">
            <div class="stat-card-label">Total Stations</div>
            <div class="stat-card-value">{{ $totalStations }}</div>
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M19.5 3h-15A2.5 2.5 0 0 0 2 5.5v13A2.5 2.5 0 0 0 4.5 21h15A2.5 2.5 0 0 0 22 18.5v-13A2.5 2.5 0 0 0 19.5 3zM12 17a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/></svg>
            </div>
        </div>
        <div class="stat-card stat-active">
            <div class="stat-card-label">Active</div>
            <div class="stat-card-value">{{ $activeStations }}</div>
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14l-4-4 1.41-1.41L11 13.17l6.59-6.59L19 8l-8 8z"/></svg>
            </div>
        </div>
        <div class="stat-card stat-inactive">
            <div class="stat-card-label">Inactive</div>
            <div class="stat-card-value">{{ $inactiveStations }}</div>
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
            </div>
        </div>
        <div class="stat-card stat-govt">
            <div class="stat-card-label">Government</div>
            <div class="stat-card-value">{{ $govtStations }}</div>
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7v2h20V7L12 2zM4 11v7H2v2h20v-2h-2v-7h-2v7h-3v-7h-2v7H9v-7H7v7H5v-7H4z"/></svg>
            </div>
        </div>
        <div class="stat-card stat-private">
            <div class="stat-card-label">Private</div>
            <div class="stat-card-value">{{ $privateStations }}</div>
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
    <div class="fs-alert fs-alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="fs-alert fs-alert-danger">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- FILTERS --}}
    <div class="fs-filter-card">
        <div class="fs-filter-row">
            <span class="fs-filter-label">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                Filters
            </span>
            <div class="fs-search-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="fs-input" id="searchInput"
                       placeholder="Search by name, owner, code...">
            </div>
            <select class="fs-input" id="divisionFilter" style="min-width:140px; cursor:pointer;">
                <option value="">All Divisions</option>
                @foreach($divisions as $div)
                    <option value="{{ $div }}">{{ $div }}</option>
                @endforeach
            </select>
            <select class="fs-input" id="statusFilter" style="min-width:130px; cursor:pointer;">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <select class="fs-input" id="companyFilter" style="min-width:140px; cursor:pointer;">
                <option value="">All Companies</option>
                @foreach($companies as $company)
                    <option value="{{ $company->name }}">{{ $company->name }}</option>
                @endforeach
            </select>
            <button class="btn-clear" onclick="clearFilters()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
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
                        @if($station->upazila)
                            <br><span class="station-sub">{{ $station->upazila }}</span>
                        @endif
                    </td>

                    <td>
                        <span class="station-name" style="font-size:.85rem;">{{ $station->owner_name ?? '—' }}</span>
                        <span class="station-sub">{{ $station->owner_phone ?? '' }}</span>
                    </td>

                    <td>{{ $station->company->name ?? '—' }}</td>

                    <td>{{ $station->tank_capacity ? number_format($station->tank_capacity).' L' : '—' }}</td>

                    <td>
                        @if($station->fuel_types)
                        @foreach($station->fuel_types as $fuel)
                            <span class="badge-fuel">{{ $fuel }}</span>
                        @endforeach
                        @else
                            <span class="station-sub">—</span>
                        @endif
                    </td>

                    <td>
                        @php $status = strtolower($station->status ?? 'active'); @endphp
                        <span class="badge-status {{ $status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>

                    <td>
                        <div class="actions-cell">
                            {{-- View --}}
                            <a href="{{ route('stations.show', $station->id) }}"
                               class="action-btn action-btn-view" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                            {{-- Edit --}}
                            <a href="{{ route('stations.edit', $station->id) }}"
                               class="action-btn action-btn-edit" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L7.5 19.79l-4 1 1-4 12.362-12.303z"/>
                                </svg>
                            </a>
                            {{-- Delete --}}
                            <form action="{{ route('stations.destroy', $station->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn action-btn-delete" title="Delete"
                                        onclick="return confirm('Are you sure you want to delete this station?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                                    </svg>
                                </button>
                            </form>
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

        <div id="noResultsRow" style="display:none; text-align:center; padding:48px 20px; color:#94a3b8; font-size:0.875rem;">
            <span style="font-size:2rem; display:block; margin-bottom:8px; opacity:0.35;">🔍</span>
            No matching stations found.
        </div>

        @if($stations->hasPages())
        <div class="fs-pagination">{{ $stations->links() }}</div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
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

        let visible = 0;
        tableBody.querySelectorAll('tr').forEach(row => {
            if (row.classList.contains('empty-row')) return;

            const text    = row.innerText.toLowerCase();
            const rowDiv  = (row.dataset.division  || '').toLowerCase();
            const rowSt   = (row.dataset.status    || '').toLowerCase();
            const rowCo   = (row.dataset.company   || '').toLowerCase();

            const matchQ  = !q        || text.includes(q);
            const matchD  = !division || rowDiv.includes(division);
            const matchS  = !status   || rowSt === status;
            const matchC  = !company  || rowCo.includes(company);

            const show = matchQ && matchD && matchS && matchC;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        noResults.style.display = visible === 0 ? 'block' : 'none';
    }

    function clearFilters() {
        searchInput.value = '';
        divisionFilter.value = '';
        statusFilter.value = '';
        companyFilter.value = '';
        applyFilters();
    }

    searchInput.addEventListener('input', applyFilters);
    divisionFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    companyFilter.addEventListener('change', applyFilters);
</script>
@endsection