@extends('backend.admin.layouts.app')

@section('title', 'DC Officer Management')

@push('styles')
    <style>
        .card-stats {
            border: none;
            border-radius: 12px;
            color: white;
            padding: 1.5rem;
            transition: transform 0.3s;
        }

        .card-stats:hover {
            transform: translateY(-5px);
        }

        .bg-cyan {
            background-color: #00bcd4;
        }

        .bg-green {
            background-color: #2ecc71;
        }

        .bg-magenta {
            background-color: #e91e63;
        }

        .bg-orange {
            background-color: #ff9800;
        }

        .search-box {
            border-radius: 25px;
            padding-left: 45px;
            height: 45px;
            border: 1px solid #ddd;
        }

        .search-icon {
            position: absolute;
            left: 20px;
            top: 12px;
            color: #888;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .dc-code-badge {
            background-color: #e1f5fe;
            color: #03a9f4;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-active {
            background-color: #e8f5e9;
            color: #4caf50;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-action {
            border: none;
            background: transparent;
            font-size: 1.1rem;
        }

        .btn-edit {
            color: #03a9f4;
        }

        .btn-delete {
            color: #f44336;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">DC Officer Management</h4>
                <p class="text-muted small mb-0">Manage Deputy Commissioner Officers Information</p>
            </div>
            <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#addOfficerModal"
                style="background-color: #006699; border-radius: 8px;">
                <i class="bi bi-plus-lg me-2"></i> Add DC Officer
            </button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-stats bg-cyan">
                    <i class="bi bi-shield-check fs-4 mb-2"></i>
                    <div class="small">Total DC Officers</div>
                    <h2 class="fw-bold mb-0">{{ $dcOfficers->count() }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-green">
                    <i class="bi bi-shield-check fs-4 mb-2"></i>
                    <div class="small">Active Officers</div>
                    <h2 class="fw-bold mb-0">{{ $dcOfficers->count() }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-magenta">
                    <i class="bi bi-shield-check fs-4 mb-2"></i>
                    <div class="small">Total Divisions</div>
                    <h2 class="fw-bold mb-0">{{ $dcOfficers->pluck('profile.division')->unique()->count() }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-orange">
                    <i class="bi bi-shield-check fs-4 mb-2"></i>
                    <div class="small">Total Districts</div>
                    <h2 class="fw-bold mb-0">{{ $dcOfficers->pluck('profile.district')->unique()->count() }}</h2>
                </div>
            </div>
        </div>

        <div class="position-relative mb-4">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" class="form-control search-box"
                placeholder="Search by name, DC code, district, or division...">
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="text-muted">
                        <tr style="font-size: 0.85rem; text-transform: uppercase;">
                            <th>Name & Designation</th>
                            <th>DC Code</th>
                            <th>Division</th>
                            <th>District</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dcTableBody">
                        @forelse($dcOfficers as $officer)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $officer->name }}</div>
                                    <div class="text-muted small">
                                        {{ $officer->profile->designation ?? 'Deputy Commissioner' }}</div>
                                </td>
                                <td>
                                    <span class="dc-code-badge">{{ $officer->profile->id_code ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $officer->profile->division ?? 'N/A' }}</td>
                                <td class="fw-bold">{{ $officer->profile->district ?? 'N/A' }}</td>
                                <td>{{ $officer->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="status-active">Active</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn-action btn-edit me-2" title="Edit"><i
                                            class="bi bi-pencil-square"></i></button>
                                    <form action="#" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete"
                                            onclick="return confirm('Are you sure?')" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No DC Officers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addOfficerModal" tabindex="-1" aria-labelledby="addOfficerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="addOfficerModalLabel">Add Tag Officer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form action="{{ route('admin.dc-officer.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Officer Name *</label>
                            <input type="text" name="name" class="form-control bg-light border-0 py-2"
                                placeholder="Enter name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Designation *</label>
                            <input type="text" name="designation" class="form-control bg-light border-0 py-2"
                                placeholder="e.g., TO-001" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Department/ Agency*</label>
                            <input type="text" name="department" class="form-control bg-light border-0 py-2"
                                placeholder="Enter department" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone Number *</label>
                            <input type="text" name="phone" class="form-control bg-light border-0 py-2"
                                placeholder="e.g., 01712345678" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control bg-light border-0 py-2"
                                placeholder="Enter email">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Division *</label>
                            <select name="division" class="form-select bg-light border-0 py-2" required>
                                <option value="">Select Division</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">District *</label>
                            <select name="district" class="form-select bg-light border-0 py-2" required disabled>
                                <option value="">Select District</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Upazilla *</label>
                            <select name="upazilla" class="form-select bg-light border-0 py-2" required disabled>
                                <option value="">Select Upazilla</option>
                            </select>
                        </div>

                        <div class="mb-4 text-center">
                            <label class="form-label d-block text-start small fw-bold">Upload Photo</label>
                            <div class="upload-area border border-2 border-dashed rounded p-4 bg-light"
                                style="border-style: dashed !important; cursor: pointer;">
                                <input type="file" name="photo" class="d-none" id="photoInput">
                                <label for="photoInput" class="mb-0" style="cursor: pointer;">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                                    <p class="mb-0 text-muted small">Upload</p>
                                </label>
                            </div>
                        </div>

                        <div class="modal-footer border-0 px-0 pb-4">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal"
                                style="border-radius: 8px;">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 py-2"
                                style="background-color: #006699; border-radius: 8px; border: none;">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const locationData = @json($locationData);
        const divisions = locationData.divisions || [];

        $(document).ready(function() {
            const $divSelect = $('select[name="division"]');
            const $distSelect = $('select[name="district"]');
            const $upazillaSelect = $('select[name="upazilla"]');

            divisions.forEach(div => {
                $divSelect.append(`<option value="${div.name_en}">${div.name_en}</option>`);
            });

            $divSelect.on('change', function() {
                const selectedDivName = $(this).val();

                $distSelect.html('<option value="">Select District</option>').prop('disabled', !
                    selectedDivName);
                $upazillaSelect.html('<option value="">Select Upazilla</option>').prop('disabled', true);

                if (selectedDivName) {
                    const selectedDiv = divisions.find(d => d.name_en === selectedDivName);
                    if (selectedDiv && selectedDiv.districts) {
                        selectedDiv.districts.forEach(dist => {
                            $distSelect.append(
                                `<option value="${dist.name_en}">${dist.name_en}</option>`);
                        });
                        $distSelect.prop('disabled', false);
                    }
                }
            });

            $distSelect.on('change', function() {
                const selectedDivName = $divSelect.val();
                const selectedDistName = $(this).val();

                $upazillaSelect.html('<option value="">Select Upazilla</option>').prop('disabled', !
                    selectedDistName);

                if (selectedDistName) {
                    const selectedDiv = divisions.find(d => d.name_en === selectedDivName);
                    const selectedDist = selectedDiv.districts.find(d => d.name_en === selectedDistName);

                    if (selectedDist && selectedDist.police_stations) {
                        selectedDist.police_stations.forEach(ps => {
                            $upazillaSelect.append(
                                `<option value="${ps.name_en}">${ps.name_en}</option>`);
                        });
                        $upazillaSelect.prop('disabled', false);
                    }
                }
            });
        });
    </script>
@endpush
