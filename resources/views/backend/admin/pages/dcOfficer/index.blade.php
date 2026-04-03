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

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold">DC Officer Management</h4>
                <p class="text-muted small">Manage DC Officers Information and Assignments</p>
            </div>

            <button class="btn btn-primary px-4 py-2 w-sm-100 w-auto" data-bs-toggle="modal" data-bs-target="#addOfficerModal"
                style="background-color: #006699; border-radius: 8px; border: none;">
                <i class="bi bi-plus-lg me-2"></i> Add DC Officer
            </button>
        </div>

        <form action="{{ route('admin.dc-officer.index') }}" method="GET"
            class="bg-white p-3 rounded shadow-sm border mb-4">
            <div class="row g-2 align-items-end">

                {{-- Search Field with Button --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control border-0 bg-light"
                            style="border-radius: 0; font-size: 0.9rem;" value="{{ request('search') }}"
                            placeholder="Name, Email or Phone...">
                        <button class="btn btn-dark border-0 px-3" type="submit"
                            style="border-radius: 0 8px 8px 0; background-color: #006699;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                {{-- Division Field --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Division</label>
                    <select name="division" id="division" class="form-select border-0 bg-light"
                        style="border-radius: 8px;">
                        <option value="">All Division</option>
                        @foreach ($locationData['divisions'] as $division)
                            <option value="{{ $division['name_en'] }}"
                                {{ request('division') == $division['name_en'] ? 'selected' : '' }}>
                                {{ $division['name_en'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- District Field --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">District</label>
                    <select name="district" id="district" class="form-select border-0 bg-light"
                        style="border-radius: 8px;">
                        <option value="">All District</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm"
                        style="background-color: #006699; border: none; border-radius: 8px; height: 38px;">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>

                    <a href="{{ route('admin.dc-officer.index') }}"
                        class="btn btn-outline-secondary shadow-sm d-flex align-items-center justify-content-center"
                        style="border-radius: 8px; height: 38px; border-color: #dee2e6; min-width: 45px;">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>

            </div>
        </form>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="text-muted">
                        <tr style="font-size: 0.85rem; text-transform: uppercase;">
                            <th>SL</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Department</th>
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
                                <td class="fw-bold text-muted" style="font-size: 0.85rem;">
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    {{ $officer->profile->name ?? 'N/A' }}
                                </td>

                                <td>
                                    <span class="text-muted small">
                                        {{ $officer->profile->designation ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $officer->profile->department ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>{{ $officer->profile->division ?? 'N/A' }}</td>

                                <td>{{ $officer->profile->district ?? 'N/A' }}</td>
                                {{-- <td>{{ $officer->profile->upazila ?? 'N/A' }}</td> --}}

                                <td>{{ $officer->phone ?? 'N/A' }}</td>

                                <td>
                                    <span class="status-active">Active</span>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button class="btn-action btn-edit me-2 edit-btn" data-id="{{ $officer->id }}"
                                            data-name="{{ $officer->profile->name ?? 'N/A' }}"
                                            data-designation="{{ $officer->profile->designation ?? '' }}"
                                            data-department="{{ $officer->profile->department ?? '' }}"
                                            data-phone="{{ $officer->phone }}" data-email="{{ $officer->email }}"
                                            data-division="{{ $officer->profile->division ?? '' }}"
                                            data-district="{{ $officer->profile->district ?? '' }}"
                                            data-upazila="{{ $officer->profile->upazila ?? '' }}"
                                            data-url="{{ route('admin.dc-officer.update', $officer->id) }}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('admin.dc-officer.destroy', $officer->id) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-action btn-delete delete-confirm"
                                                title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No DC Officers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="my-4">
            {{ $dcOfficers->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <div class="modal fade" id="addOfficerModal" tabindex="-1" aria-labelledby="addOfficerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="addOfficerModalLabel">Add DC Officer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form action="{{ route('admin.dc-officer.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Officer Name *</label>
                            <input type="text" name="name"
                                class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror"
                                placeholder="Enter name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Designation *</label>
                            <input type="text" name="designation"
                                class="form-control bg-light border-0 py-2 @error('designation') is-invalid @enderror"
                                placeholder="Enter Designation" value="{{ old('designation') }}">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Department/ Agency*</label>
                            <input type="text" name="department"
                                class="form-control bg-light border-0 py-2 @error('department') is-invalid @enderror"
                                placeholder="Enter department" value="{{ old('department') }}">
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone Number *</label>
                            <input type="text" name="phone"
                                class="form-control bg-light border-0 py-2 @error('phone') is-invalid @enderror"
                                placeholder="Enter Phone Number" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email"
                                class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror"
                                placeholder="Enter email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password *</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" value="123456"
                                    class="form-control bg-light border-0 py-2 @error('password') is-invalid @enderror"
                                    placeholder="Enter password"
                                    style="border-top-right-radius: 0; border-bottom-right-radius: 0;">

                                <span class="input-group-text bg-light border-0" id="togglePassword"
                                    style="cursor: pointer; border-top-left-radius: 0; border-bottom-left-radius: 0; border: 1px solid transparent;">
                                    <i class="bi bi-eye-slash text-muted" id="eyeIcon"></i>
                                </span>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Division *</label>
                            <select name="division" id="add_division"
                                class="form-select bg-light border-0 py-2 @error('division') is-invalid @enderror">
                                <option value="">Select Division</option>
                            </select>
                            @error('division')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">District *</label>
                            <select name="district" id="add_district"
                                class="form-select bg-light border-0 py-2 @error('district') is-invalid @enderror"
                                disabled>
                                <option value="">Select District</option>
                            </select>
                            @error('district')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- <div class="mb-3">
                            <label class="form-label small fw-bold">upazila *</label>
                            <select name="upazila" id="add_upazila"
                                class="form-select bg-light border-0 py-2 @error('upazila') is-invalid @enderror" disabled>
                                <option value="">Select upazila</option>
                            </select>
                            @error('upazila')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <div class="mb-4 text-center">
                            <label class="form-label d-block text-start small fw-bold">Upload Photo</label>
                            <div class="upload-area border border-2 border-dashed rounded p-4 bg-light @error('photo') border-danger @enderror"
                                style="cursor: pointer;">
                                <input type="file" name="photo" class="d-none" id="photoInput">
                                <label for="photoInput" class="mb-0" style="cursor: pointer;">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                                    <p class="mb-0 text-muted small">Upload</p>
                                </label>
                            </div>
                            @error('photo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="modal-footer border-0 px-0 pb-4">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 py-2"
                                style="background-color: #006699; border: none;">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editOfficerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Edit DC Officer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">
                    <input type="hidden" id="old_division" value="{{ old('division') }}">
                    <input type="hidden" id="old_district" value="{{ old('district') }}">
                    <input type="hidden" id="old_upazila" value="{{ old('upazila') }}">

                    <form id="editOfficerForm" method="POST" action="{{ old('edit_url_handler') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="edit_url_handler" id="edit_url_handler"
                            value="{{ old('edit_url_handler') }}">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Officer Name *</label>
                            <input type="text" name="name" id="edit_name"
                                class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror"
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Designation *</label>
                            <input type="text" name="designation" id="edit_designation"
                                class="form-control bg-light border-0 py-2 @error('designation') is-invalid @enderror"
                                value="{{ old('designation') }}">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Department *</label>
                            <input type="text" name="department" id="edit_department"
                                class="form-control bg-light border-0 py-2 @error('department') is-invalid @enderror"
                                value="{{ old('department') }}">
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone Number *</label>
                            <input type="text" name="phone" id="edit_phone"
                                class="form-control bg-light border-0 py-2 @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email Address *</label>
                            <input type="email" name="email" id="edit_email"
                                class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror"
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password (Leave blank to keep current)</label>
                            <div class="input-group">
                                <input type="password" name="password" id="edit_password"
                                    class="form-control bg-light border-0 py-2 @error('password') is-invalid @enderror"
                                    placeholder="Enter new password">
                                <span class="input-group-text bg-light border-0" id="toggleEditPassword"
                                    style="cursor: pointer;">
                                    <i class="bi bi-eye-slash text-muted" id="editEyeIcon"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Division *</label>
                            <select name="division" id="edit_division"
                                class="form-select bg-light border-0 py-2 @error('division') is-invalid @enderror"></select>
                            @error('division')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">District *</label>
                            <select name="district" id="edit_district"
                                class="form-select bg-light border-0 py-2 @error('district') is-invalid @enderror"></select>
                            @error('district')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- <div class="mb-3">
                            <label class="form-label small fw-bold">Upazila *</label>
                            <select name="upazila" id="edit_upazila"
                                class="form-select bg-light border-0 py-2 @error('upazila') is-invalid @enderror"></select>
                            @error('upazila')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <div class="mb-4 text-center">
                            <label class="form-label d-block text-start small fw-bold">Change Photo (Optional)</label>
                            <div
                                class="upload-area border border-2 border-dashed rounded p-3 bg-light @error('photo') border-danger @enderror">
                                <input type="file" name="photo" class="form-control form-control-sm">
                            </div>
                            @error('photo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="modal-footer border-0 px-0 pb-4">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 py-2"
                                style="background-color: #006699; border: none;">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- @push('scripts')
    <script>
        $(document).on('click', '.delete-confirm', function(e) {
            let form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>

    <script>
        let timer;
        $('#searchInput').on('keyup', function() {
            clearTimeout(timer);
            timer = setTimeout(function() {
                $(this).closest('form').submit();
            }.bind(this), 500);
        });
    </script>

    <script>
        $(document).ready(function() {

            function togglePasswordVisibility(inputSelector, iconSelector) {
                const passInput = $(inputSelector);
                const icon = $(iconSelector);

                if (passInput.attr('type') === 'password') {
                    passInput.attr('type', 'text');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                } else {
                    passInput.attr('type', 'password');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                }
            }

            $(document).on('click', '#togglePassword', function() {
                togglePasswordVisibility('#password', '#eyeIcon');
            });

            $(document).on('click', '#toggleEditPassword', function() {
                togglePasswordVisibility('#edit_password', '#editEyeIcon');
            });
        });
    </script>

    <script>
        const locationData = @json($locationData);
        const divisions = locationData.divisions || [];

        $(document).ready(function() {
            function loadDistricts(divName, selectedDist, target) {
                const div = divisions.find(d => d.name_en === divName);
                let options = '<option value="">Select District</option>';
                if (div && div.districts) {
                    div.districts.forEach(dist => {
                        const sel = (dist.name_en === selectedDist) ? 'selected' : '';
                        options += `<option value="${dist.name_en}" ${sel}>${dist.name_en}</option>`;
                    });
                    $(target).html(options).prop('disabled', false);
                } else {
                    $(target).html(options).prop('disabled', true);
                }
            }

            function loadupazilas(divName, distName, selectedUpz, target) {
                const div = divisions.find(d => d.name_en === divName);
                const dist = div?.districts?.find(d => d.name_en === distName);
                let options = '<option value="">Select upazila</option>';
                if (dist && dist.police_stations) {
                    dist.police_stations.forEach(ps => {
                        const isSelected = (String(ps.name_en).trim() === String(selectedUpz).trim()) ?
                            'selected' : '';
                        options += `<option value="${ps.name_en}" ${isSelected}>${ps.name_en}</option>`;
                    });
                    $(target).html(options).prop('disabled', false);
                } else {
                    $(target).html(options).prop('disabled', true);
                }
            }

            function populateDivisions(selectedDiv, target) {
                let divOptions = '<option value="">Select Division</option>';
                divisions.forEach(div => {
                    const isSelected = (div.name_en === selectedDiv) ? 'selected' : '';
                    divOptions += `<option value="${div.name_en}" ${isSelected}>${div.name_en}</option>`;
                });
                $(target).html(divOptions);
            }

            populateDivisions('', '#add_division');

            $('#add_division').on('change', function() {
                loadDistricts($(this).val(), '', '#add_district');
                $('#add_upazila').html('<option value="">Select upazila</option>').prop('disabled', true);
            });

            $('#add_district').on('change', function() {
                loadupazilas($('#add_division').val(), $(this).val(), '', '#add_upazila');
            });

            $(document).on('click', '.edit-btn', function() {
                const data = $(this).data();
                $('#editOfficerForm').attr('action', data.url);
                $('#edit_url_handler').val(data.url);
                $('#edit_name').val(data.name);
                $('#edit_designation').val(data.designation);
                $('#edit_department').val(data.department);
                $('#edit_phone').val(data.phone);
                $('#edit_email').val(data.email);

                populateDivisions(data.division, '#edit_division');
                loadDistricts(data.division, data.district, '#edit_district');
                loadupazilas(data.division, data.district, data.upazila, '#edit_upazila');

                $('#editOfficerModal').modal('show');
            });

            $('#edit_division').on('change', function() {
                loadDistricts($(this).val(), '', '#edit_district');
                $('#edit_upazila').html('<option value="">Select upazila</option>').prop('disabled', true);
            });

            $('#edit_district').on('change', function() {
                loadupazilas($('#edit_division').val(), $(this).val(), '', '#edit_upazila');
            });

            @if ($errors->any())
                var oldAction = "{{ old('edit_url_handler') }}";
                const oldDiv = "{{ old('division') }}";
                const oldDist = "{{ old('district') }}";
                const oldUpz = "{{ old('upazila') }}";

                if (oldAction) {
                    $('#editOfficerForm').attr('action', oldAction);
                    populateDivisions(oldDiv, '#edit_division');
                    if (oldDiv) {
                        loadDistricts(oldDiv, oldDist, '#edit_district');
                        if (oldDist) {
                            loadupazilas(oldDiv, oldDist, oldUpz, '#edit_upazila');
                        }
                    }
                    $('#editOfficerModal').modal('show');
                } else {
                    populateDivisions(oldDiv, '#add_division');
                    if (oldDiv) {
                        loadDistricts(oldDiv, oldDist, '#add_district');
                        if (oldDist) {
                            loadupazilas(oldDiv, oldDist, oldUpz, '#add_upazila');
                        }
                    }
                    $('#addOfficerModal').modal('show');
                }
            @endif
        });
    </script>
@endpush --}}

@push('scripts')
    <script>
        const locationData = @json($locationData);
        const divisions = locationData.divisions || [];

        $(document).ready(function() {

            // ===============================
            // 🔹 COMMON FUNCTIONS
            // ===============================

            function populateDivisions(selectedDiv, target) {
                let options = '<option value="">Select Division</option>';
                divisions.forEach(div => {
                    const selected = div.name_en === selectedDiv ? 'selected' : '';
                    options += `<option value="${div.name_en}" ${selected}>${div.name_en}</option>`;
                });
                $(target).html(options);
            }

            function loadDistricts(divName, selectedDist, target) {
                const div = divisions.find(d => d.name_en === divName);
                let options = '<option value="">Select District</option>';

                if (div?.districts) {
                    div.districts.forEach(dist => {
                        const selected = dist.name_en === selectedDist ? 'selected' : '';
                        options += `<option value="${dist.name_en}" ${selected}>${dist.name_en}</option>`;
                    });
                    $(target).html(options).prop('disabled', false);
                } else {
                    $(target).html(options).prop('disabled', true);
                }
            }

            function loadUpazilas(divName, distName, selectedUpz, target) {
                const div = divisions.find(d => d.name_en === divName);
                const dist = div?.districts?.find(d => d.name_en === distName);

                let options = '<option value="">Select Upazila</option>';

                if (dist?.police_stations) {
                    dist.police_stations.forEach(ps => {
                        const selected = ps.name_en === selectedUpz ? 'selected' : '';
                        options += `<option value="${ps.name_en}" ${selected}>${ps.name_en}</option>`;
                    });
                    $(target).html(options).prop('disabled', false);
                } else {
                    $(target).html(options).prop('disabled', true);
                }
            }

            // ===============================
            // 🔹 FILTER (TOP SEARCH AREA)
            // ===============================

            $('#division').on('change', function() {
                loadDistricts($(this).val(), '', '#district');
                $('#upazila').html('<option value="">Select Upazila</option>').prop('disabled', true);
            });

            $('#district').on('change', function() {
                loadUpazilas($('#division').val(), $(this).val(), '', '#upazila');
            });

            // ===============================
            // 🔹 ADD MODAL
            // ===============================

            populateDivisions('', '#add_division');

            $('#add_division').on('change', function() {
                loadDistricts($(this).val(), '', '#add_district');
                $('#add_upazila').html('<option value="">Select Upazila</option>').prop('disabled', true);
            });

            $('#add_district').on('change', function() {
                loadUpazilas($('#add_division').val(), $(this).val(), '', '#add_upazila');
            });

            // ===============================
            // 🔹 EDIT MODAL
            // ===============================

            $(document).on('click', '.edit-btn', function() {
                const data = $(this).data();

                $('#editOfficerForm').attr('action', data.url);
                $('#edit_url_handler').val(data.url);
                $('#edit_name').val(data.name);
                $('#edit_designation').val(data.designation);
                $('#edit_department').val(data.department);
                $('#edit_phone').val(data.phone);
                $('#edit_email').val(data.email);
                $('#edit_status').val(data.status.toLowerCase());

                populateDivisions(data.division, '#edit_division');
                loadDistricts(data.division, data.district, '#edit_district');
                loadUpazilas(data.division, data.district, data.upazila, '#edit_upazila');

                $('#editOfficerModal').modal('show');
            });

            $('#edit_division').on('change', function() {
                loadDistricts($(this).val(), '', '#edit_district');
                $('#edit_upazila').html('<option value="">Select Upazila</option>').prop('disabled', true);
            });

            $('#edit_district').on('change', function() {
                loadUpazilas($('#edit_division').val(), $(this).val(), '', '#edit_upazila');
            });

            // ===============================
            // 🔹 DELETE CONFIRM
            // ===============================

            $(document).on('click', '.delete-confirm', function() {
                let form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // ===============================
            // 🔹 PASSWORD TOGGLE
            // ===============================

            function togglePassword(input, icon) {
                const field = $(input);
                const ic = $(icon);

                if (field.attr('type') === 'password') {
                    field.attr('type', 'text');
                    ic.removeClass('bi-eye-slash').addClass('bi-eye');
                } else {
                    field.attr('type', 'password');
                    ic.removeClass('bi-eye').addClass('bi-eye-slash');
                }
            }

            $(document).on('click', '#togglePassword', function() {
                togglePassword('#password', '#eyeIcon');
            });

            $(document).on('click', '#toggleEditPassword', function() {
                togglePassword('#edit_password', '#editEyeIcon');
            });

            // ===============================
            // 🔹 VALIDATION ERROR HANDLE
            // ===============================

            @if ($errors->any())
                let oldAction = "{{ old('edit_url_handler') }}";
                let oldDiv = "{{ old('division') }}";
                let oldDist = "{{ old('district') }}";
                let oldUpz = "{{ old('upazila') }}";

                if (oldAction) {
                    $('#editOfficerForm').attr('action', oldAction);
                    populateDivisions(oldDiv, '#edit_division');

                    if (oldDiv) {
                        loadDistricts(oldDiv, oldDist, '#edit_district');
                        if (oldDist) {
                            loadUpazilas(oldDiv, oldDist, oldUpz, '#edit_upazila');
                        }
                    }

                    $('#editOfficerModal').modal('show');
                } else {
                    populateDivisions(oldDiv, '#add_division');

                    if (oldDiv) {
                        loadDistricts(oldDiv, oldDist, '#add_district');
                        if (oldDist) {
                            loadUpazilas(oldDiv, oldDist, oldUpz, '#add_upazila');
                        }
                    }

                    $('#addOfficerModal').modal('show');
                }
            @endif

        });
    </script>
@endpush
