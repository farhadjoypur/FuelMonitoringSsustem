@extends('backend.admin.layouts.app')

@section('title', 'Admin User Management')

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
    <div class="container-fluid p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold">Admin User Management</h4>
                <p class="text-muted small">Manage Admin User Information</p>
            </div>

            <button class="btn btn-primary px-4 py-2 w-sm-100 w-auto" data-bs-toggle="modal" data-bs-target="#addOfficerModal"
                style="background-color: #006699; border-radius: 8px; border: none;">
                <i class="bi bi-plus-lg me-2"></i> Add Admin User
            </button>
        </div>

        {{-- <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-stats bg-cyan">
                    <i class="bi bi-shield-check fs-4 mb-2"></i>
                    <div class="small">Total Admin</div>
                    <h2 class="fw-bold mb-0">{{ $stats['total'] }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-green">
                    <i class="bi bi-shield-check fs-4 mb-2"></i>
                    <div class="small">Active Admin</div>
                    <h2 class="fw-bold mb-0">{{ $stats['active'] }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-magenta">
                    <i class="bi bi-shield-check fs-4 mb-2"></i>
                    <div class="small">Total Divisions</div>
                    <h2 class="fw-bold mb-0">{{ $stats['divisions'] }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats bg-orange">
                    <i class="bi bi-shield-check fs-4 mb-2"></i>
                    <div class="small">Total Districts</div>
                    <h2 class="fw-bold mb-0">{{ $stats['districts'] }}</h2>
                </div>
            </div>
        </div> --}}

        {{-- <form action="{{ route('admin.admin-user.index') }}" method="GET">
            <div class="position-relative mb-4">
                <i class="bi bi-search search-icon"
                    style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" name="search" id="searchInput" class="form-control search-box"
                    style="padding-left: 40px;" value="{{ request('search') }}" placeholder="Search dc officer...">
            </div>
        </form> --}}

        <div class="table-container">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="text-muted">
                        <tr style="font-size: 0.85rem; text-transform: uppercase;">
                            <th>SL</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dcTableBody">
                        @forelse($admins as $key => $officer)
                            <tr>
                                <td>{{ $admins->firstItem() + $key }}</td>
                                <td>
                                    {{ $officer->profile->name ?? '-' }}
                                </td>

                                <td>
                                    <span class="text-muted small">
                                        {{ $officer->profile->designation ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $officer->profile->department ?? '-' }}
                                    </span>
                                </td>


                                <td>{{ $officer->email ?? '-' }}</td>
                                <td>{{ $officer->phone ?? '-' }}</td>

                                <td>
                                    @if (strtolower($officer->status) == 'active')
                                        <span
                                            style="background-color: #e6fffa; color: #38a169; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block;">
                                            Active
                                        </span>
                                    @else
                                        <span
                                            style="background-color: #fff5f5; color: #e53e3e; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block;">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button class="btn-action btn-edit me-2 edit-btn" data-id="{{ $officer->id }}"
                                            data-name="{{ $officer->profile->name ?? 'N/A' }}"
                                            data-designation="{{ $officer->profile->designation ?? '' }}"
                                            data-department="{{ $officer->profile->department ?? '' }}"
                                            data-phone="{{ $officer->phone }}" data-email="{{ $officer->email }}"
                                            data-status="{{ $officer->status }}"
                                            data-url="{{ route('admin.admin-user.update', $officer->id) }}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('admin.admin-user.destroy', $officer->id) }}" method="POST"
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
                                <td colspan="8" class="text-center py-4 text-muted">No Admin found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="my-4">
            {{ $admins->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <div class="modal fade" id="addOfficerModal" tabindex="-1" aria-labelledby="addOfficerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="addOfficerModalLabel">Add Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form action="{{ route('admin.admin-user.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Name *</label>
                            <input type="text" name="name"
                                class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror"
                                placeholder="Enter name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Designation</label>
                            <input type="text" name="designation"
                                class="form-control bg-light border-0 py-2 @error('designation') is-invalid @enderror"
                                placeholder="Enter Designation" value="{{ old('designation') }}">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Department</label>
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

                        <div class="mb-4 text-center">
                            <label class="form-label d-block text-start small fw-bold">Upload Photo</label>
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
                    <h5 class="modal-title fw-bold">Edit Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4">

                    <form id="editOfficerForm" method="POST" action="{{ old('edit_url_handler') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="edit_url_handler" id="edit_url_handler"
                            value="{{ old('edit_url_handler') }}">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Name *</label>
                            <input type="text" name="name" id="edit_name"
                                class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror"
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Designation</label>
                            <input type="text" name="designation" id="edit_designation"
                                placeholder="Enter Designation"
                                class="form-control bg-light border-0 py-2 @error('designation') is-invalid @enderror"
                                value="{{ old('designation') }}">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Department</label>
                            <input type="text" name="department" id="edit_department" placeholder="Enter Department"
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
                            <label class="form-label small fw-bold">Email</label>
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
                            <label class="form-label small fw-bold">Status *</label>
                            <select name="status" id="edit_status"
                                class="form-select bg-light border-0 py-2 @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

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

@push('scripts')
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
        $(document).ready(function() {

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

                $('#editOfficerModal').modal('show');
            });

            @if ($errors->any())
                var oldAction = "{{ old('edit_url_handler') }}";

                if (oldAction) {
                    $('#editOfficerForm').attr('action', oldAction);
                    $('#editOfficerModal').modal('show');
                } else {
                    $('#addOfficerModal').modal('show');
                }
            @endif
        });
    </script>
@endpush
