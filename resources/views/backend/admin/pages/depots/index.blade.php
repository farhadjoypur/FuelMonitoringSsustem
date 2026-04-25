@extends('backend.admin.layouts.app')

@section('title', 'Depot Management')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* ── Select2 ── */
        .select2-container--default .select2-selection--single {
            background-color: #f8f9fa !important;
            border: none !important;
            border-radius: 8px !important;
            height: 38px !important;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important; right: 8px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529 !important; padding-left: 12px !important;
            font-size: 0.9rem !important; line-height: 38px !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            outline: none !important; box-shadow: none !important;
        }
        .select2-dropdown {
            border: 1px solid #eee !important; border-radius: 8px !important;
            box-shadow: 0 4px 6px rgba(0,0,0,.1);
        }

        /* ── Layout ── */
        .table-container {
            background: white; border-radius: 12px;
            padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }
        .form-control, .form-select { border-radius: 6px; border: 1px solid #ddd; height: 38px; }
        textarea.form-control { height: auto; }

        /* ── Table actions ── */
        .btn-action { border: none; background: transparent; font-size: 1.1rem; cursor: pointer; }
        .btn-edit   { color: #03a9f4; }
        .btn-delete { color: #f44336; }
        .btn-action:hover { opacity: .75; }
        .table tbody td { vertical-align: middle; font-size: 14px; }
        .table thead tr { font-size: .85rem; text-transform: uppercase; }

        /* ── Modal ── */
        .modal-label { font-weight: 500; color: #333; margin-bottom: 5px; font-size: 14px; display: block; }
        .required::after { content: " *"; color: red; }

        /* ── Error box ── */
        #createErrorBox, #editErrorBox { font-size: 0.88rem; }
    </style>
@endpush

@section('content')
<div class="container-fluid p-4">

    {{-- ── PAGE HEADER ── --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold">Depot Management</h4>
            <p class="text-muted small mb-0">Manage all fuel depots across Bangladesh</p>
        </div>
        <button class="btn btn-primary px-4 py-2"
                data-bs-toggle="modal" data-bs-target="#createDepotModal"
                style="background-color:#006699;border-radius:8px;border:none;">
            <i class="bi bi-plus-lg me-2"></i> Add New Depot
        </button>
    </div>

    {{-- ── FILTERS ── --}}
    <form action="{{ route('admin.depots.index') }}" method="GET"
          class="bg-white p-3 rounded shadow-sm border mb-4">
        <div class="row g-2 align-items-end">

            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Search</label>
                <div class="input-group">
                    <input type="text" name="q" class="form-control border-0 bg-light"
                           style="border-radius:8px 0 0 8px;font-size:.9rem;"
                           value="{{ request('q') }}" placeholder="Name, code or district...">
                    <button class="btn border-0 px-3" type="submit"
                            style="border-radius:0 8px 8px 0;background-color:#006699;color:#fff;">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">District</label>
                <select name="district" class="form-select border-0 bg-light"
                        style="border-radius:8px;" onchange="this.form.submit()">
                    <option value="">All Districts</option>
                    @foreach ($locations['divisions'] as $division)
                        <optgroup label="{{ $division['name_en'] }}">
                            @foreach ($division['districts'] as $district)
                                <option value="{{ $district['name_en'] }}"
                                    {{ request('district') == $district['name_en'] ? 'selected' : '' }}>
                                    {{ $district['name_en'] }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">Status</label>
                <select name="status" class="form-select border-0 bg-light"
                        style="border-radius:8px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm"
                        style="background-color:#006699;border:none;border-radius:8px;height:38px;">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                <a href="{{ route('admin.depots.index') }}"
                   class="btn btn-outline-secondary shadow-sm d-flex align-items-center justify-content-center"
                   style="border-radius:8px;height:38px;" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>

        </div>
    </form>

    {{-- ── TABLE ── --}}
    <div class="table-container">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="text-muted">
                    <tr>
                        <th>SL</th>
                        <th>Depot Name</th>
                        <th>Code</th>
                        <th>District</th>
                        <th>Contact</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($depots as $key => $depot)
                        <tr>
                            <td>{{ $depots->firstItem() + $key }}</td>
                            <td class="fw-semibold">{{ $depot->depot_name }}</td>
                            <td><span class="badge bg-primary">{{ $depot->depot_code }}</span></td>
                            <td>{{ $depot->district }}</td>
                            <td>{{ $depot->contact_number }}</td>
                            <td>{{ number_format($depot->capacity) }} L</td>
                            <td>
                                @if(strtolower($depot->status) == 'active')
                                    <span style="background-color:#e6fffa;color:#38a169;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;display:inline-block;">
                                        Active
                                    </span>
                                @else
                                    <span style="background-color:#fff5f5;color:#e53e3e;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;display:inline-block;">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <button class="btn-action btn-edit me-2"
                                            onclick="openEditModal({{ $depot->id }})" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('admin.depots.destroy', $depot->id) }}"
                                          method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="btn-action btn-delete delete-confirm" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-archive" style="font-size:2rem;display:block;opacity:.25;margin-bottom:.5rem;"></i>
                                No depots found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="my-4">
        {{ $depots->links('pagination::bootstrap-5') }}
    </div>

</div>


{{-- ══════════════ CREATE MODAL ══════════════ --}}
<div class="modal fade" id="createDepotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:12px;border:none;">

            <div class="modal-header border-bottom px-4">
                <h5 class="modal-title fw-bold" style="font-size:1.1rem;">
                    <i class="bi bi-building me-2"></i> Add New Depot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div id="createErrorBox" class="alert alert-danger d-none mb-3">
                    <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Please fix the following:</strong>
                    <ul id="createErrorList" class="mb-0 mt-2"></ul>
                </div>

                <form id="createDepotForm" novalidate>
                    @csrf
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="modal-label required">Depot Name</label>
                            <input type="text" name="depot_name" class="form-control" placeholder="Enter depot name">
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label required">Depot Code</label>
                            <input type="text" name="depot_code" class="form-control" placeholder="e.g. DEP-001">
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label required">District</label>
                            <select name="district" class="form-select">
                                <option value="">-- Select District --</option>
                                @foreach ($locations['divisions'] as $division)
                                    <optgroup label="{{ $division['name_en'] }}">
                                        @foreach ($division['districts'] as $district)
                                            <option value="{{ $district['name_en'] }}">{{ $district['name_en'] }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label required">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" placeholder="+8801XXXXXXXXX">
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="depot@example.com">
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label required">Capacity (Litres)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="1" name="capacity"
                                       class="form-control" placeholder="50000">
                                <span class="input-group-text">L</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label">Number of Tanks</label>
                            <input type="number" min="0" name="number_of_tanks" class="form-control" placeholder="e.g. 8">
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="modal-label">Full Address</label>
                            <textarea name="full_address" class="form-control" rows="2"
                                      placeholder="Full address of the depot..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="modal-label">Remarks / Notes</label>
                            <textarea name="remarks" class="form-control" rows="2"
                                      placeholder="Any additional notes..."></textarea>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4 py-2"
                        data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="createSubmitBtn"
                        class="btn btn-primary px-5 py-2"
                        style="background-color:#006699;border:none;"
                        onclick="submitCreateForm()">
                    <i class="bi bi-floppy me-1"></i> Save Depot
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ══════════════ EDIT MODAL ══════════════ --}}
<div class="modal fade" id="editDepotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:12px;border:none;">

            <div class="modal-header border-bottom px-4">
                <h5 class="modal-title fw-bold" style="font-size:1.1rem;">
                    <i class="bi bi-pencil-square me-2"></i> Edit Depot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4" id="editModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 text-muted small">Loading depot data...</p>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 pb-4" id="editModalFooter" style="display:none;">
                <button type="button" class="btn btn-outline-secondary px-4 py-2"
                        data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="editSubmitBtn"
                        class="btn btn-primary px-5 py-2"
                        style="background-color:#006699;border:none;"
                        onclick="submitEditForm()">
                    <i class="bi bi-floppy me-1"></i> Save Changes
                </button>
            </div>

        </div>
    </div>
</div>

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

// ── District helper (server-built JS array) ──────────────────────────────
const allDistricts = [
    @foreach ($locations['divisions'] as $division)
        @foreach ($division['districts'] as $district)
            { division: "{{ addslashes($division['name_en']) }}", name: "{{ addslashes($district['name_en']) }}" },
        @endforeach
    @endforeach
];

function buildDistrictOptions(selected) {
    const grouped = {};
    allDistricts.forEach(d => {
        if (!grouped[d.division]) grouped[d.division] = [];
        grouped[d.division].push(d.name);
    });
    let html = '<option value="">-- Select District --</option>';
    Object.keys(grouped).forEach(div => {
        html += `<optgroup label="${div}">`;
        grouped[div].forEach(name => {
            html += `<option value="${name}" ${name === selected ? 'selected' : ''}>${name}</option>`;
        });
        html += '</optgroup>';
    });
    return html;
}

// ── Utilities ────────────────────────────────────────────────────────────
function escHtml(str) {
    return String(str ?? '')
        .replace(/&/g,'&amp;').replace(/"/g,'&quot;')
        .replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function showErrors(boxId, listId, errors) {
    const box = document.getElementById(boxId);
    const list = document.getElementById(listId);
    list.innerHTML = '';
    Object.values(errors).flat().forEach(msg => {
        const li = document.createElement('li');
        li.textContent = msg;
        list.appendChild(li);
    });
    box.classList.remove('d-none');
    box.scrollIntoView({ behavior:'smooth', block:'nearest' });
}

function hideErrors(id) { document.getElementById(id)?.classList.add('d-none'); }

function showToast(message, type = 'success') {
    const id    = 'toast_' + Date.now();
    const color = type === 'success' ? 'bg-success' : 'bg-danger';
    document.body.insertAdjacentHTML('beforeend', `
        <div id="${id}" class="toast align-items-center text-white ${color} border-0 position-fixed bottom-0 end-0 m-3"
             role="alert" style="z-index:9999">
            <div class="d-flex">
                <div class="toast-body fw-semibold">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`);
    const el = document.getElementById(id);
    new bootstrap.Toast(el, { delay: 3000 }).show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

function setBtnLoading(id, loading, label) {
    const btn = document.getElementById(id);
    if (!btn) return;
    btn.disabled = loading;
    btn.innerHTML = loading
        ? '<span class="spinner-border spinner-border-sm me-1"></span> Saving...'
        : label;
}

// ── CREATE ───────────────────────────────────────────────────────────────
function submitCreateForm() {
    hideErrors('createErrorBox');
    setBtnLoading('createSubmitBtn', true, '');

    const formData = new FormData(document.getElementById('createDepotForm'));

    fetch('{{ route('admin.depots.store') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData
    })
    .then(async res => {
        const data = await res.json();
        if (res.status === 422) {
            showErrors('createErrorBox', 'createErrorList', data.errors);
            setBtnLoading('createSubmitBtn', false, '<i class="bi bi-floppy me-1"></i> Save Depot');
            return;
        }
        if (data.success) {
            showToast(data.message);
            setTimeout(() => location.reload(), 800);
        } else {
            alert(data.message || 'Something went wrong!');
            setBtnLoading('createSubmitBtn', false, '<i class="bi bi-floppy me-1"></i> Save Depot');
        }
    })
    .catch(() => {
        alert('Network error. Please try again.');
        setBtnLoading('createSubmitBtn', false, '<i class="bi bi-floppy me-1"></i> Save Depot');
    });
}

// ── EDIT ─────────────────────────────────────────────────────────────────
let currentEditId = null;

function openEditModal(id) {
    currentEditId = id;
    document.getElementById('editModalBody').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2 text-muted small">Loading depot data...</p>
        </div>`;
    document.getElementById('editModalFooter').style.display = 'none';
    new bootstrap.Modal(document.getElementById('editDepotModal')).show();

    fetch(`/admin/depots/${id}/get`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editModalBody').innerHTML = `
                <div id="editErrorBox" class="alert alert-danger d-none mb-3">
                    <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Please fix the following:</strong>
                    <ul id="editErrorList" class="mb-0 mt-2"></ul>
                </div>
                <form id="editDepotForm">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="modal-label required">Depot Name</label>
                            <input type="text" name="depot_name" class="form-control"
                                   value="${escHtml(d.depot_name)}">
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label required">Depot Code</label>
                            <input type="text" name="depot_code" class="form-control"
                                   value="${escHtml(d.depot_code)}">
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label required">District</label>
                            <select name="district" class="form-select">
                                ${buildDistrictOptions(d.district)}
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label required">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control"
                                   value="${escHtml(d.contact_number)}">
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="${escHtml(d.email)}">
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label required">Capacity (Litres)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="1" name="capacity"
                                       class="form-control" value="${d.capacity}">
                                <span class="input-group-text">L</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label">Number of Tanks</label>
                            <input type="number" min="0" name="number_of_tanks" class="form-control"
                                   value="${escHtml(d.number_of_tanks)}">
                        </div>

                        <div class="col-md-6">
                            <label class="modal-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active"   ${d.status === 'active'   ? 'selected' : ''}>Active</option>
                                <option value="inactive" ${d.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="modal-label">Full Address</label>
                            <textarea name="full_address" class="form-control" rows="2">${escHtml(d.full_address)}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="modal-label">Remarks / Notes</label>
                            <textarea name="remarks" class="form-control" rows="2">${escHtml(d.remarks)}</textarea>
                        </div>

                    </div>
                </form>`;

            document.getElementById('editModalFooter').style.display = 'flex';
            setBtnLoading('editSubmitBtn', false, '<i class="bi bi-floppy me-1"></i> Save Changes');
        })
        .catch(() => {
            document.getElementById('editModalBody').innerHTML =
                `<div class="alert alert-danger">Failed to load depot. Please try again.</div>`;
        });
}

function submitEditForm() {
    hideErrors('editErrorBox');
    setBtnLoading('editSubmitBtn', true, '');

    const formData = new FormData(document.getElementById('editDepotForm'));
    formData.append('_method', 'PUT');

    fetch(`/admin/depots/${currentEditId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData
    })
    .then(async res => {
        const data = await res.json();
        if (res.status === 422) {
            showErrors('editErrorBox', 'editErrorList', data.errors);
            setBtnLoading('editSubmitBtn', false, '<i class="bi bi-floppy me-1"></i> Save Changes');
            return;
        }
        if (data.success) {
            showToast(data.message);
            setTimeout(() => location.reload(), 800);
        } else {
            alert(data.message || 'Update failed!');
            setBtnLoading('editSubmitBtn', false, '<i class="bi bi-floppy me-1"></i> Save Changes');
        }
    })
    .catch(() => {
        alert('Network error. Please try again.');
        setBtnLoading('editSubmitBtn', false, '<i class="bi bi-floppy me-1"></i> Save Changes');
    });
}

// ── DELETE with SweetAlert ───────────────────────────────────────────────
$(document).on('click', '.delete-confirm', function () {
    const form = $(this).closest('form');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then(result => {
        if (result.isConfirmed) form.submit();
    });
});

</script>
@endpush