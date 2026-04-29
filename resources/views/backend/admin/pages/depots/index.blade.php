@extends('backend.admin.layouts.app')
@section('title', 'Depot Management')

{{-- ═══════════════════════════════════════════════════════════
     STYLES
═══════════════════════════════════════════════════════════════ --}}
@push('styles')
<style>
/* ── Design tokens ─────────────────────────────────────────── */
:root {
    --brand      : #006699;
    --brand-dark : #004d73;
    --brand-ring : rgba(0,102,153,.15);
    --ok         : #16a34a;
    --ok-bg      : #dcfce7;
    --err        : #dc2626;
    --err-bg     : #fee2e2;
    --warn       : #d97706;
    --border     : #e2e8f0;
    --surface    : #f8fafc;
    --text       : #0f172a;
    --muted      : #64748b;
    --radius     : 10px;
    --shadow     : 0 4px 20px rgba(0,0,0,.07);
}

/* ── Utilities ─────────────────────────────────────────────── */
[x-cloak] { display: none !important; }

/* ── Layout cards ──────────────────────────────────────────── */
.card-surface {
    background: #fff;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

/* ── Table ─────────────────────────────────────────────────── */
.data-table thead th {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--muted);
    border-bottom: 2px solid var(--border);
    padding: 11px 14px;
    white-space: nowrap;
}
.data-table tbody tr { transition: background .12s; }
.data-table tbody tr:hover { background: var(--surface); }
.data-table tbody td {
    vertical-align: middle;
    font-size: 13.5px;
    padding: 5px 7px;
    color: #374151;
    border-bottom: 1px solid #f1f5f9;
}

/* ── Status pills ──────────────────────────────────────────── */
.pill { display: inline-block; border-radius: 20px; padding: 3px 12px; font-size: 11.5px; font-weight: 600; }
.pill-active   { background: var(--ok-bg);  color: var(--ok);  }
.pill-inactive { background: var(--err-bg); color: var(--err); }

/* ── Code badge ────────────────────────────────────────────── */
.code-badge {
    background: #e0f2fe; color: #0369a1;
    border-radius: 6px; padding: 3px 10px;
    font-size: 11.5px; font-weight: 600;
}

/* ── Icon action buttons ───────────────────────────────────── */
.btn-icon {
    border: none; background: transparent;
    border-radius: 6px; padding: 5px 8px;
    font-size: 1rem; cursor: pointer;
    transition: background .12s, color .12s;
    line-height: 1;
}
.btn-icon-edit   { color: #0ea5e9; } .btn-icon-edit:hover   { background: #e0f2fe; }
.btn-icon-delete { color: #ef4444; } .btn-icon-delete:hover { background: var(--err-bg); }

/* ── Form controls ─────────────────────────────────────────── */
.form-control, .form-select {
    border: 1.5px solid var(--border);
    border-radius: 8px;
    height: 40px;
    font-size: .875rem;
    transition: border-color .18s, box-shadow .18s;
}
.form-control:focus, .form-select:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px var(--brand-ring);
    outline: none;
}
textarea.form-control { height: auto; resize: vertical; }
.form-control.is-invalid, .form-select.is-invalid { border-color: var(--err) !important; box-shadow: 0 0 0 3px rgba(220,38,38,.1); }
.form-control.is-valid,   .form-select.is-valid   { border-color: var(--ok) !important; }
.field-error { font-size: .78rem; color: var(--err); margin-top: 4px; }

/* ── Modal polish ──────────────────────────────────────────── */
.modal-content  { border-radius: 14px; border: none; box-shadow: 0 24px 64px rgba(0,0,0,.18); }
.modal-header   { padding: 18px 24px; border-bottom: 1px solid var(--border); }
.modal-body     { padding: 24px; }
.modal-footer   { padding: 14px 24px; border-top: 1px solid var(--border); }
.field-label    { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
.field-label.req::after { content: ' *'; color: var(--err); }

/* ── Server error banner ───────────────────────────────────── */
.error-banner {
    background: #fef2f2;
    border: 1.5px solid #fecaca;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 18px;
}
.error-banner ul { margin: 6px 0 0; padding-left: 18px; font-size: 12.5px; color: #b91c1c; }

/* ── Spinner overlay ───────────────────────────────────────── */
.fetch-loader { display: flex; flex-direction: column; align-items: center; padding: 56px 0; gap: 12px; }

/* ── Toast stack ───────────────────────────────────────────── */
.toast-stack {
    position: fixed; bottom: 24px; right: 24px;
    z-index: 9999;
    display: flex; flex-direction: column-reverse; gap: 10px;
    pointer-events: none;
}
.toast-item {
    pointer-events: all;
    min-width: 280px; max-width: 360px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13.5px; font-weight: 500;
    display: flex; align-items: center; gap: 10px;
    box-shadow: 0 8px 32px rgba(0,0,0,.16);
    animation: toastIn .25s ease both;
}
.toast-item.success { background: var(--ok);  color: #fff; }
.toast-item.error   { background: var(--err); color: #fff; }
.toast-item.warning { background: var(--warn); color: #fff; }
.toast-item.out     { animation: toastOut .3s ease forwards; }
@keyframes toastIn  { from { transform: translateY(16px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
@keyframes toastOut { to   { transform: translateX(110%); opacity: 0; } }

/* ── Page-level button ─────────────────────────────────────── */
.btn-brand {
    background: var(--brand); color: #fff;
    border: none; border-radius: 8px;
    padding: 9px 20px; font-size: .875rem; font-weight: 600;
    transition: background .18s;
    cursor: pointer;
}
.btn-brand:hover { background: var(--brand-dark); }
.btn-brand:disabled { opacity: .65; cursor: not-allowed; }
</style>
@endpush


{{-- ═══════════════════════════════════════════════════════════
     CONTENT
═══════════════════════════════════════════════════════════════ --}}
@section('content')

{{-- ── Toast Stack ─────────────────────────────────────────── --}}
<div x-data="ToastManager()" x-cloak
     class="toast-stack"
     @toast.window="push($event.detail)">
    <template x-for="t in list" :key="t.id">
        <div class="toast-item" :class="[t.type, t.out ? 'out' : '']" :id="'t'+t.id">
            <i class="bi flex-shrink-0"
               :class="{ 'bi-check-circle-fill': t.type==='success',
                         'bi-x-circle-fill'    : t.type==='error',
                         'bi-exclamation-circle-fill': t.type==='warning' }"></i>
            <span class="flex-grow-1" x-text="t.msg"></span>
            <button @click="dismiss(t.id)"
                    style="background:none;border:none;color:inherit;opacity:.8;cursor:pointer;padding:0;font-size:.9rem;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </template>
</div>


<div class="container-fluid p-4">

    {{-- ── Page header ─────────────────────────────────────── --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:var(--text);">Depot Management</h4>
            <p class="text-muted small mb-0">Manage all fuel depots across Bangladesh</p>
        </div>
        <button class="btn-brand" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-2"></i>Add New Depot
        </button>
    </div>

    {{-- ── Filter bar ──────────────────────────────────────── --}}
    <form action="{{ route('admin.depots.index') }}" method="GET"
          class="card-surface p-3 mb-4">
        <div class="row g-2 align-items-end">

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Search</label>
                <div class="input-group">
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="form-control border-0 bg-light"
                           style="border-radius:8px 0 0 8px;"
                           placeholder="Name, code or district…">
                    <button type="submit" class="btn px-3 border-0"
                            style="background:var(--brand);color:#fff;border-radius:0 8px 8px 0;">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">District</label>
                <select name="district" class="form-select border-0 bg-light" onchange="this.form.submit()">
                    <option value="">All Districts</option>
                    @foreach ($locations['divisions'] as $div)
                        <optgroup label="{{ $div['name_en'] }}">
                            @foreach ($div['districts'] as $d)
                                <option value="{{ $d['name_en'] }}"
                                    {{ request('district') == $d['name_en'] ? 'selected' : '' }}>
                                    {{ $d['name_en'] }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                <select name="status" class="form-select border-0 bg-light" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn-brand flex-grow-1 d-flex align-items-center justify-content-center gap-1"
                        style="height:40px;">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="{{ route('admin.depots.index') }}"
                   class="btn btn-outline-secondary d-flex align-items-center justify-content-center"
                   style="border-radius:8px;height:40px;width:42px;" title="Reset filters">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>

        </div>
    </form>

    {{-- ── Data table ───────────────────────────────────────── --}}
    <div class="card-surface p-3">
        <div class="table-responsive">
            <table class="table data-table mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Depot Name</th>
                        <th>Code</th>
                        <th>District</th>
                        <th>Contact</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th class="text-center" style="width:90px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($depots as $i => $depot)
                    <tr>
                        <td class="text-muted">{{ $depots->firstItem() + $i }}</td>
                        <td class="fw-semibold" style="color:var(--text);">{{ $depot->depot_name }}</td>
                        <td><span class="code-badge">{{ $depot->depot_code }}</span></td>
                        <td>{{ $depot->district }}</td>
                        <td>{{ $depot->contact_number }}</td>
                        <td>{{ number_format($depot->capacity) }}
                            <span class="text-muted" style="font-size:11px;">L</span>
                        </td>
                        <td>
                            <span class="pill {{ strtolower($depot->status) === 'active' ? 'pill-active' : 'pill-inactive' }}">
                                {{ ucfirst($depot->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{-- Edit --}}
                            <button class="btn-icon btn-icon-edit"
                                    title="Edit depot"
                                    onclick="DepotEdit.open({{ $depot->id }})">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            {{-- Delete --}}
                            <button class="btn-icon btn-icon-delete"
                                    title="Delete depot"
                                    onclick="confirmDelete({{ $depot->id }}, '{{ addslashes($depot->depot_name) }}')">
                                <i class="bi bi-trash3"></i>
                            </button>

                            {{-- Hidden delete form --}}
                            <form id="del-{{ $depot->id }}"
                                  action="{{ route('admin.depots.destroy', $depot->id) }}"
                                  method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-building-slash d-block mb-2" style="font-size:2.5rem;opacity:.2;"></i>
                            No depots found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $depots->links('pagination::bootstrap-5') }}</div>

</div>{{-- /container --}}


{{-- ═══════════════════════════════════════════════════════════
     CREATE MODAL
═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true"
     x-data="DepotForm({ url: '{{ route('admin.depots.store') }}', method: 'POST' })"
     @hidden.bs.modal="reset()">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="font-size:1rem;">
                    <i class="bi bi-building-add me-2" style="color:var(--brand);"></i>Add New Depot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                @include('backend.admin.pages.depots._form')
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-brand px-5" style="min-width:140px;"
                        :disabled="busy" @click="submit()">
                    <span x-show="!busy"><i class="bi bi-floppy me-1"></i> Save Depot</span>
                    <span x-show="busy" x-cloak>
                        <span class="spinner-border spinner-border-sm me-1"></span> Saving…
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════
     EDIT MODAL
═══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true"
     x-data="DepotForm({ url: '', method: 'PUT' })"
     @hidden.bs.modal="reset()"
     id="editModal">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="font-size:1rem;">
                    <i class="bi bi-pencil-square me-2" style="color:var(--brand);"></i>Edit Depot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- Fetch loader --}}
                <div x-show="fetching" class="fetch-loader">
                    <div class="spinner-border text-primary" style="width:2.2rem;height:2.2rem;"></div>
                    <p class="text-muted small mb-0">Loading depot data…</p>
                </div>

                {{-- Fetch error --}}
                {{-- <div x-show="fetchFailed && !fetching" x-cloak
                     class="alert alert-danger d-flex align-items-center gap-2 mb-0">
                    <i class="bi bi-wifi-off"></i>
                    Failed to load depot. Please close and try again.
                </div> --}}

                {{-- The shared form partial --}}
                <div x-show="!fetching && !fetchFailed">
                    @include('backend.admin.pages.depots._form')
                </div>

            </div>

            <div class="modal-footer" x-show="!fetching && !fetchFailed">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-brand px-5" style="min-width:155px;"
                        :disabled="busy" @click="submit()">
                    <span x-show="!busy"><i class="bi bi-floppy me-1"></i> Save Changes</span>
                    <span x-show="busy" x-cloak>
                        <span class="spinner-border spinner-border-sm me-1"></span> Saving…
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>

@endsection


{{-- ═══════════════════════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
'use strict';

/* ── helpers ──────────────────────────────────────────────── */
const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

function toast(msg, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast', { detail: { msg, type } }));
}

/* ── Validation rules ─────────────────────────────────────── */
const RULES = {
    depot_name:     v => !v?.trim()            ? 'Depot name is required.'
                       : v.trim().length > 255 ? 'Max 255 characters.'
                       : null,

    depot_code:     v => !v?.trim()           ? 'Depot code is required.'
                       : v.trim().length > 50 ? 'Max 50 characters.'
                       : null,

    district:       v => !v ? 'Please select a district.' : null,

    contact_number: v => !v?.trim()                              ? 'Contact number is required.'
                       : !/^[+]?[\d\s\-]{7,20}$/.test(v.trim()) ? 'Enter a valid contact number.'
                       : null,

    email:          v => v && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())
                           ? 'Enter a valid email address.' : null,

    capacity:       v => (v === '' || v == null) ? 'Capacity is required.'
                       : isNaN(v) || Number(v) < 1 ? 'Capacity must be at least 1 litre.'
                       : null,
};

/* ── Blank form skeleton ──────────────────────────────────── */
const blankForm = () => ({
    depot_name: '', depot_code: '', district: '',
    contact_number: '', email: '', capacity: '',
    number_of_tanks: '', status: 'active',
    full_address: '', remarks: '',
});

/* ═══════════════════════════════════════════════════════════
   DepotForm — shared Alpine component for create & edit
══════════════════════════════════════════════════════════════ */
function DepotForm({ url, method }) {
    return {
        /* config */
        url, method,

        /* state */
        form        : blankForm(),
        errors      : {},       // field-level errors (frontend + backend mapped)
        serverErrors: [],       // flat list for the banner
        touched     : {},       // which fields have been interacted with
        busy        : false,    // submit spinner

        /* edit-only state */
        fetching    : method === 'PUT',  // show loader by default for edit modal
        fetchFailed : false,

        /* ── Validation helpers ────────────────────────────── */
        validate(field) {
            const rule = RULES[field];
            const msg  = rule ? rule(this.form[field]) : null;
            if (msg) this.errors[field] = msg;
            else     delete this.errors[field];
        },

        touch(field) {
            this.touched[field] = true;
            this.validate(field);
        },

        validateAll() {
            Object.keys(RULES).forEach(f => {
                this.touched[f] = true;
                this.validate(f);
            });
            return Object.keys(this.errors).length === 0;
        },

        fieldClass(field) {
            if (!this.touched[field]) return '';
            return this.errors[field] ? 'is-invalid' : 'is-valid';
        },

        /* ── Load data for edit ────────────────────────────── */
        async load(id) {
            this.url        = `/admin/depots/${id}`;   // update PUT target
            this.fetching   = true;
            this.fetchFailed = false;
            this.reset(false);                          // clear form but keep fetching=true

            try {
                const res = await fetch(`/admin/depots/${id}/get`);
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const d = await res.json();

                this.form = {
                    depot_name:      d.depot_name      ?? '',
                    depot_code:      d.depot_code      ?? '',
                    district:        d.district        ?? '',
                    contact_number:  d.contact_number  ?? '',
                    email:           d.email           ?? '',
                    capacity:        d.capacity        ?? '',
                    number_of_tanks: d.number_of_tanks ?? '',
                    status:          d.status          ?? 'active',
                    full_address:    d.full_address    ?? '',
                    remarks:         d.remarks         ?? '',
                };
            } catch {
                this.fetchFailed = true;
            } finally {
                this.fetching = false;
            }
        },

        /* ── Submit ────────────────────────────────────────── */
        async submit() {
            if (!this.validateAll()) {
                toast('Please fix the highlighted errors.', 'error');
                return;
            }

            this.busy         = true;
            this.serverErrors = [];

            const fd = new FormData();
            if (this.method === 'PUT') fd.append('_method', 'PUT');
            Object.entries(this.form).forEach(([k, v]) => fd.append(k, v ?? ''));

            try {
                const res  = await fetch(this.url, {
                    method : 'POST',           // always POST; _method spoofing for PUT
                    headers: { 'X-CSRF-TOKEN': csrf() },
                    body   : fd,
                });
                const data = await res.json();

                if (res.status === 422) {
                    /* Map Laravel errors → inline field errors + banner list */
                    this.serverErrors = Object.values(data.errors ?? {}).flat();
                    Object.entries(data.errors ?? {}).forEach(([field, msgs]) => {
                        this.errors[field]  = msgs[0];
                        this.touched[field] = true;
                    });
                    toast('Validation failed — please check the form.', 'error');
                    return;
                }

                if (data.success) {
                    toast(data.message, 'success');
                    bootstrap.Modal.getInstance(
                        document.getElementById(this.method === 'POST' ? 'createModal' : 'editModal')
                    )?.hide();
                    setTimeout(() => location.reload(), 950);
                } else {
                    toast(data.message || 'Something went wrong.', 'error');
                }
            } catch {
                toast('Network error — please check your connection.', 'error');
            } finally {
                this.busy = false;
            }
        },

        /* ── Reset (called on modal close) ────────────────── */
        reset(resetFetching = true) {
            this.form         = blankForm();
            this.errors       = {};
            this.touched      = {};
            this.serverErrors = [];
            this.busy         = false;
            if (resetFetching) {
                this.fetching    = this.method === 'PUT';
                this.fetchFailed = false;
            }
        },
    };
}

/* ═══════════════════════════════════════════════════════════
   ToastManager Alpine component
══════════════════════════════════════════════════════════════ */
function ToastManager() {
    return {
        list: [],
        push({ msg, type = 'success' }) {
            const id = Date.now();
            this.list.push({ id, msg, type, out: false });
            setTimeout(() => this.dismiss(id), 4200);
        },
        dismiss(id) {
            const t = this.list.find(x => x.id === id);
            if (!t) return;
            t.out = true;
            setTimeout(() => this.list = this.list.filter(x => x.id !== id), 320);
        },
    };
}

/* ═══════════════════════════════════════════════════════════
   Global bridge — lets onclick attributes reach Alpine data
══════════════════════════════════════════════════════════════ */
const DepotEdit = {
    open(id) {
        const el   = document.getElementById('editModal');
        const comp = Alpine.$data(el);
        bootstrap.Modal.getOrCreateInstance(el).show();
        // slight delay so modal is visible before fetch spinner shows
        setTimeout(() => comp.load(id), 60);
    }
};

/* ═══════════════════════════════════════════════════════════
   Delete with SweetAlert2
══════════════════════════════════════════════════════════════ */
function confirmDelete(id, name) {
    Swal.fire({
        title          : `Delete "${name}"?`,
        text           : 'This action cannot be undone.',
        icon           : 'warning',
        showCancelButton: true,
        reverseButtons : true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor : '#64748b',
        confirmButtonText : '<i class="bi bi-trash3 me-1"></i> Yes, Delete',
        cancelButtonText  : 'Cancel',
        customClass    : { popup: 'rounded-3', confirmButton: 'fw-semibold', cancelButton: 'fw-semibold' }
    }).then(({ isConfirmed }) => {
        if (isConfirmed) document.getElementById(`del-${id}`).submit();
    });
}
</script>
@endpush