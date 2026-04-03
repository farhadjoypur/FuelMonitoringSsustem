@extends('backend.admin.layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    body, .main-content { 
        background-color: #f0f4f8 !important; 
        font-family: 'Plus Jakarta Sans', sans-serif; 
    }
    .fs-wrapper { padding: 30px; }

    .fs-header { 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        margin-bottom: 25px; 
        flex-wrap: wrap; 
        gap: 15px; 
    }
    .fs-header h2 { 
        font-weight: 800; 
        color: #0f172a; 
        margin: 0; 
        font-size: 1.75rem; 
    }

    .btn-add {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: white; 
        border: none; 
        border-radius: 12px;
        padding: 12px 24px; 
        font-weight: 700;
        display: inline-flex; 
        align-items: center; 
        gap: 8px;
    }

    .stat-card {
        border-radius: 16px; 
        padding: 24px 20px; 
        color: white;
        position: relative; 
        overflow: hidden; 
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-card-value { font-size: 2.6rem; font-weight: 800; }
    .stat-card-icon { 
        position: absolute; 
        right: 20px; 
        top: 50%; 
        transform: translateY(-50%); 
        font-size: 3.2rem; 
        opacity: 0.25; 
    }

    .stat-total   { background: linear-gradient(135deg, #0ea5e9, #06b6d4); }
    .stat-active  { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .stat-inactive{ background: linear-gradient(135deg, #ef4444, #dc2626); }
</style>
@endpush

@section('content')
<div class="fs-wrapper">

    <div class="fs-header">
        <div>
            <h2>Depot Management</h2>
            <p class="text-muted mb-0">Manage all fuel depots across Bangladesh</p>
        </div>
        <button onclick="openCreateModal()" class="btn btn-add">
            <i class="fas fa-plus"></i> Add New Depot
        </button>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card stat-total">
                <div class="stat-card-label">Total Depots</div>
                <div class="stat-card-value">{{ $totalDepots }}</div>
                <div class="stat-card-icon"><i class="fas fa-warehouse"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-active">
                <div class="stat-card-label">Active Depots</div>
                <div class="stat-card-value">{{ $activeDepots }}</div>
                <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-inactive">
                <div class="stat-card-label">Inactive Depots</div>
                <div class="stat-card-value">{{ $inactiveDepots }}</div>
                <div class="stat-card-icon"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by name, code or district...">
                    </div>
                </div>
                <div class="col-md-3">
                    <button onclick="clearFilters()" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="depotTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Depot Name</th>
                        <th>Code</th>
                        <th>District</th>
                        <th>Contact</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($depots as $depot)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $depot->depot_name }}</strong></td>
                        <td><span class="badge bg-primary">{{ $depot->depot_code }}</span></td>
                        <td>{{ $depot->district }}</td>
                        <td>{{ $depot->contact_number }}</td>
                        <td>{{ number_format($depot->capacity) }} L</td>
                        <td>
                            <span class="badge {{ $depot->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($depot->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button onclick="openEditModal({{ $depot->id }})" class="btn btn-sm btn-outline-primary me-1">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.depots.destroy', $depot->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Are you sure you want to delete this depot?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">No depots found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- ===================== CREATE DEPOT MODAL (Modern) ===================== -->
<div class="modal fade" id="createDepotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            <!-- Header -->
            <div class="modal-header border-0 bg-white text-dark px-4 py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <i class="fas fa-warehouse fa-lg me-3"></i>
                    <h5 class="modal-title fw-bold mb-0">Add New Depot</h5>
                </div>
                <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">
                <form id="createDepotForm">
                    @csrf
                    
                    <div class="row g-4">

                        <!-- Row 1 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Depot Name <span class="text-danger">*</span></label>
                            <input type="text" name="depot_name" class="form-control form-control-lg" placeholder="Enter depot name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Depot Code <span class="text-danger">*</span></label>
                            <input type="text" name="depot_code" class="form-control form-control-lg" placeholder="e.g. DEP-001" required>
                        </div>

                        <!-- Row 2 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                District <span class="text-danger">*</span>
                            </label>

                            <select name="district" class="form-select form-select-lg" required>
                                <option value="">-- Select District --</option>

                                @foreach($locations['divisions'] as $division)
                                    @foreach($division['districts'] as $district)
                                        <option value="{{ $district['name_en'] }}">
                                            {{ $district['name_en'] }} ({{ $district['name_bn'] }})
                                        </option>
                                    @endforeach
                                @endforeach

                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control form-control-lg" placeholder="+8801XXXXXXXXX" required>
                        </div>

                        <!-- Row 3 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="depot@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Capacity (Litres) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="capacity" class="form-control form-control-lg" placeholder="50000" required>
                                <span class="input-group-text">L</span>
                            </div>
                        </div>

                        <!-- Row 4 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Number of Tanks</label>
                            <input type="number" name="number_of_tanks" class="form-control form-control-lg" placeholder="e.g. 8">
                        </div>

                        <!-- Full Address -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Address</label>
                            <textarea name="full_address" class="form-control" rows="3" placeholder="Full address of the depot..."></textarea>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" name="status" id="createStatus" value="active" checked style="width: 3rem; height: 1.6rem;">
                                <label class="form-check-label fw-bold fs-5 ms-2" for="createStatus">Active</label>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Remarks / Notes</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="Any additional notes..."></textarea>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 px-4 py-3 bg-light">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-5 fw-semibold" onclick="submitCreateForm()">
                    <i class="fas fa-save me-2"></i> Save Depot
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ===================== EDIT MODAL ===================== -->
<div class="modal fade" id="editDepotModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-white text-dark border-bottom">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Depot</h5>
                <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="editModalBody">
                <!-- AJAX loaded -->
            </div>
            <div class="modal-footer" id="editModalFooter" style="display:none;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitEditForm()">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function applyFilters() {
        const q = document.getElementById('searchInput').value.toLowerCase().trim();
        document.querySelectorAll('#tableBody tr').forEach(row => {
            if (row.cells.length < 2) return;
            const match = row.textContent.toLowerCase().includes(q);
            row.style.display = match ? '' : 'none';
        });
    }

    function clearFilters() {
        document.getElementById('searchInput').value = '';
        applyFilters();
    }

    function openCreateModal() {
        new bootstrap.Modal(document.getElementById('createDepotModal')).show();
    }

    function submitCreateForm() {
        const formData = new FormData(document.getElementById('createDepotForm'));
        // Checkbox to status
        formData.set('status', formData.has('status') ? 'active' : 'inactive');

        fetch('/admin/depots', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
            else alert(data.message || 'Failed');
        });
    }

    let currentEditId = null;

    function openEditModal(id) {
        currentEditId = id;
        const body = document.getElementById('editModalBody');
        body.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Loading depot data...</p></div>`;
        document.getElementById('editModalFooter').style.display = 'none';

        new bootstrap.Modal(document.getElementById('editDepotModal')).show();

        fetch(`/admin/depots/${id}/get`)
            .then(r => r.json())
            .then(d => {
                const isActive = d.status === 'active';
                body.innerHTML = `
                <form id="editDepotForm">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Depot Name <span class="text-danger">*</span></label>
                            <input type="text" name="depot_name" class="form-control" value="${d.depot_name}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Depot Code <span class="text-danger">*</span></label>
                            <input type="text" name="depot_code" class="form-control" value="${d.depot_code}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">District <span class="text-danger">*</span></label>
                            <select name="district" class="form-select" required>
                                <option value="">-- Select District --</option>
                                @foreach($locations['divisions'] as $dist)
                                    <option value="{{ $dist['name_en'] }}" ${d.district === '{{ $dist['name_en'] }}' ? 'selected' : ''}>
                                        {{ $dist['name_en'] }} ({{ $dist['name_bn'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" class="form-control" value="${d.contact_number}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="${d.email || ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Capacity (Litres) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="capacity" class="form-control" value="${d.capacity}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Number of Tanks</label>
                            <input type="number" name="number_of_tanks" class="form-control" value="${d.number_of_tanks || ''}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Full Address</label>
                            <textarea name="full_address" class="form-control" rows="2">${d.full_address || ''}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="status" value="active" ${isActive ? 'checked' : ''}>
                                <label class="form-check-label fw-bold">Active</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3">${d.remarks || ''}</textarea>
                        </div>
                    </div>
                </form>`;
                document.getElementById('editModalFooter').style.display = 'flex';
            });
    }

    function submitEditForm() {
        const formData = new FormData(document.getElementById('editDepotForm'));
        formData.set('status', formData.has('status') ? 'active' : 'inactive');

        fetch(`/admin/depots/${currentEditId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
            else alert(data.message || 'Update failed');
        });
    }

    // Live Search
    document.getElementById('searchInput').addEventListener('input', applyFilters);
</script>
@endpush