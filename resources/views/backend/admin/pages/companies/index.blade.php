@extends('backend.admin.layouts.app')

@push('styles')
<style>
    /* ── Page Background ── */
    body, .main-content {
        background-color: #f0f4f7 !important;
    }

    /* ── Page Wrapper ── */
    .cm-wrapper {
        padding: 32px 36px;
        font-family: 'Segoe UI', sans-serif;
    }

    /* ── Header ── */
    .cm-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 28px;
    }
    .cm-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #111827;
        margin: 0 0 4px;
        letter-spacing: -0.3px;
    }
    .cm-header p {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }
    .btn-add-company {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #1e3a5f;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 11px 22px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.18s, box-shadow 0.18s;
        white-space: nowrap;
    }
    .btn-add-company:hover {
        background-color: #16304f;
        box-shadow: 0 4px 14px rgba(30,58,95,0.25);
        color: #fff;
        text-decoration: none;
    }
    .btn-add-company svg {
        width: 15px;
        height: 15px;
    }

    /* ── Search Card ── */
    .cm-search-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e5e9ef;
        padding: 16px 20px;
        margin-bottom: 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .cm-search-input {
        width: 100%;
        border: none;
        outline: none;
        font-size: 0.92rem;
        color: #374151;
        background: transparent;
        padding-left: 28px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0 50%;
        background-size: 16px 16px;
    }
    .cm-search-input::placeholder {
        color: #9ca3af;
    }

    /* ── Table Card ── */
    .cm-table-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e5e9ef;
        overflow: hidden;
        box-shadow: 0 1px 6px rgba(0,0,0,0.05);
    }

    /* ── Table ── */
    .cm-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }
    .cm-table thead tr {
        background: #fff;
        border-bottom: 1px solid #e5e9ef;
    }
    .cm-table thead th {
        font-size: 0.72rem;
        font-weight: 700;
        color: #6b7280;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 14px 20px;
        white-space: nowrap;
    }
    .cm-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.12s;
    }
    .cm-table tbody tr:last-child {
        border-bottom: none;
    }
    .cm-table tbody tr:hover {
        background: #f9fafb;
    }
    .cm-table tbody td {
        padding: 16px 20px;
        font-size: 0.875rem;
        color: #374151;
        vertical-align: middle;
    }

    /* ── Company Name Cell ── */
    .company-name {
        font-weight: 600;
        color: #111827;
        font-size: 0.9rem;
        display: block;
        margin-bottom: 2px;
    }
    .company-email {
        font-size: 0.78rem;
        color: #9ca3af;
    }

    /* ── Code Badge ── */
    .badge-code {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1.5px solid #93c5fd;
        background: #eff6ff;
        color: #2563eb;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    /* ── Status Badge ── */
    .badge-active {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        border: 1.5px solid #6ee7b7;
        background: #f0fdf4;
        color: #059669;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .badge-inactive {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        border: 1.5px solid #fca5a5;
        background: #fef2f2;
        color: #dc2626;
        font-size: 0.78rem;
        font-weight: 600;
    }

    /* ── Action Buttons ── */
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        background: transparent;
        transition: background 0.15s;
        text-decoration: none;
    }
    .action-btn-edit {
        color: #6b7280;
    }
    .action-btn-edit:hover {
        background: #f3f4f6;
        color: #374151;
    }
    .action-btn-delete {
        color: #ef4444;
    }
    .action-btn-delete:hover {
        background: #fef2f2;
        color: #dc2626;
    }
    .action-btn svg {
        width: 16px;
        height: 16px;
    }
    .actions-cell {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ── Empty State ── */
    .empty-row td {
        text-align: center;
        color: #9ca3af;
        padding: 48px 20px;
        font-size: 0.875rem;
    }

    /* ── Alert ── */
    .cm-alert {
        border-radius: 10px;
        padding: 12px 18px;
        margin-bottom: 18px;
        font-size: 0.875rem;
        background: #f0fdf4;
        border: 1px solid #86efac;
        color: #166534;
    }

    /* ── Pagination ── */
    .cm-pagination {
        padding: 18px 20px;
        border-top: 1px solid #f3f4f6;
    }
    .cm-pagination .pagination {
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="cm-wrapper">

    {{-- ── HEADER ── --}}
    <div class="cm-header">
        <div>
            <h2>Company Management</h2>
            <p>Manage and organize company information</p>
        </div>
        <a href="#" class="btn-add-company">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Company
        </a>
    </div>

    {{-- ── SUCCESS ALERT ── --}}
    @if(session('success'))
        <div class="cm-alert">{{ session('success') }}</div>
    @endif

    {{-- ── SEARCH BAR ── --}}
    <div class="cm-search-card">
        <input type="text"
               class="cm-search-input"
               id="companySearch"
               placeholder="Search by company name, code, or type..."
               onkeyup="filterTable(this.value)">
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="cm-table-card">
        <table class="cm-table" id="companyTable">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <!-- <th>Actions</th> -->
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                <tr>
                    {{-- Company Name + Email --}}
                    <td>
                        <span class="company-name">{{ $company->name }}</span>
                        <span class="company-email">{{ $company->email }}</span>
                    </td>

                    {{-- Code --}}
                    <td>
                        <span class="badge-code">{{ $company->code }}</span>
                    </td>

                    {{-- Type --}}
                    <td>{{ $company->type }}</td>

                    {{-- Contact Person --}}
                    <td>{{ $company->contact_person }}</td>

                    {{-- Phone --}}
                    <td>{{ $company->phone }}</td>

                    {{-- Status --}}
                    <td>
                        @if($company->status)
                            <span class="badge-active">Active</span>
                        @else
                            <span class="badge-inactive">Inactive</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <!-- <td>
                        <div class="actions-cell">
                            {{-- Edit --}}
                            <a href=""
                               class="action-btn action-btn-edit"
                               title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16.862 4.487a2.1 2.1 0 1 1 2.97 2.97L7.5 19.79l-4 1 1-4 12.362-12.303z"/>
                                </svg>
                            </a>

                            {{-- Delete --}}
                            <form action=""
                                  method="POST"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')   
                                <button type="submit"
                                        class="action-btn action-btn-delete"
                                        title="Delete"
                                        onclick="return confirm('Are you sure you want to delete this company?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td> -->
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="7">No companies found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── PAGINATION ── --}}
        @if($companies->hasPages())
        <div class="cm-pagination">
            {{ $companies->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    function filterTable(query) {
        const rows = document.querySelectorAll('#companyTable tbody tr');
        const q = query.toLowerCase();
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }
</script>
@endsection