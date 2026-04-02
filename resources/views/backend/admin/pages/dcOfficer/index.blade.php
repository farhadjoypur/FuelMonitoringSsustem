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

        {{-- <div class="position-relative mb-4">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" class="form-control search-box"
                placeholder="Search by name, DC code, district, or division...">
        </div> --}}

        <div class="table-container">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="text-muted">
                        <tr style="font-size: 0.85rem; text-transform: uppercase;">
                            <th>Officer</th>
                            <th>Designation</th>
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
                                    {{ $officer->profile->name ?? 'N/A' }}
                                </td>

                                <td>
                                    <span class="text-muted small">
                                        {{ $officer->profile->designation ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>{{ $officer->profile->division ?? 'N/A' }}</td>

                                <td class="fw-bold">{{ $officer->profile->district ?? 'N/A' }}</td>

                                <td>{{ $officer->phone ?? 'N/A' }}</td>

                                <td>
                                    <span class="status-active">Active</span>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button class="btn-action btn-edit me-2 edit-btn" data-id="{{ $officer->id }}"
                                            data-name="{{ $officer->profile->name ?? 'N/A' }}"
                                            data-designation="{{ $officer->profile->designation ?? '' }}"
                                            data-phone="{{ $officer->phone }}" data-email="{{ $officer->email }}"
                                            data-division="{{ $officer->profile->division ?? '' }}"
                                            data-district="{{ $officer->profile->district ?? '' }}"
                                            data-upazila="{{ $officer->profile->upazila ?? '' }}"
                                            data-url="{{ route('admin.dc-officer.update', $officer->id) }}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('admin.dc-officer.destroy', $officer->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete"
                                                onclick="return confirm('Are you sure you want to delete this officer?')"
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
    </div>

    <div class="modal fade" id="addOfficerModal" tabindex="-1" aria-labelledby="addOfficerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="addOfficerModalLabel">Add Tag Officer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="container">
                    @if ($errors->any())
                        <div class="alert alert-danger pb-0" style="border-radius: 10px;">
                            <ul class="list-unstyled small">
                                @foreach ($errors->all() as $error)
                                    <li><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="modal-body px-4">
                    <form action="{{ route('admin.dc-officer.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Officer Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror"
                                placeholder="Enter name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Designation *</label>
                            <input type="text" name="designation" value="{{ old('designation') }}"
                                placeholder="Enter Designation"
                                class="form-control bg-light border-0 py-2 @error('designation') is-invalid @enderror">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Department/ Agency*</label>
                            <input type="text" name="department" value="{{ old('department') }}"
                                class="form-control bg-light border-0 py-2 @error('department') is-invalid @enderror"
                                placeholder="Enter department">
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone Number *</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="form-control bg-light border-0 py-2 @error('phone') is-invalid @enderror"
                                placeholder="Enter Phone">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror"
                                placeholder="Enter email">
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
                            <select name="division"
                                class="form-select bg-light border-0 py-2 @error('division') is-invalid @enderror">
                                <option value="">Select Division</option>
                            </select>
                            @error('division')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">District *</label>
                            <select name="district"
                                class="form-select bg-light border-0 py-2 @error('district') is-invalid @enderror"
                                disabled>
                                <option value="">Select District</option>
                            </select>
                            @error('district')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Upazila *</label>
                            <select name="upazila"
                                class="form-select bg-light border-0 py-2 @error('upazila') is-invalid @enderror" disabled>
                                <option value="">Select upazila</option>
                            </select>
                            @error('upazila')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label d-block text-start small fw-bold">Upload Photo</label>
                            <div class="upload-area border border-2 border-dashed rounded p-4 bg-light text-center @error('photo') border-danger @enderror"
                                style="border-style: dashed !important; cursor: pointer;">
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


    <div class="modal fade" id="editOfficerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Edit DC Officer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form id="editOfficerForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <input type="hidden" id="edit_url_handler" value="{{ old('edit_url_handler') }}">

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
                            <label class="form-label small fw-bold">Email Address *</label>
                            <input type="email" name="email" id="edit_email"
                                class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror"
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Designation *</label>
                            <input type="text" name="designation" id="edit_designation"
                                class="form-control bg-light border-0 py-2 @error('designation') is-invalid @enderror">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone Number *</label>
                            <input type="text" name="phone" id="edit_phone"
                                class="form-control bg-light border-0 py-2 @error('phone') is-invalid @enderror">
                            @error('phone')
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

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Upazila *</label>
                            <select name="upazila" id="edit_upazila"
                                class="form-select bg-light border-0 py-2 @error('upazila') is-invalid @enderror"></select>
                            @error('upazila')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 text-center">
                            <label class="form-label d-block text-start small fw-bold">Change Photo (Optional)</label>
                            <div class="upload-area border border-2 border-dashed rounded p-3 bg-light">
                                <input type="file" name="photo"
                                    class="form-control form-control-sm @error('photo') is-invalid @enderror">
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
        $(document).ready(function() {
            @if ($errors->any())
                @if (old('_method') == 'PUT')
                    var editModalElement = document.getElementById('editOfficerModal');
                    if (editModalElement) {
                        var editModal = new bootstrap.Modal(editModalElement);

                        var oldUrl = "{{ old('edit_url_handler') }}";
                        if (oldUrl) {
                            $('#editOfficerForm').attr('action', oldUrl);
                        }

                        editModal.show();

                        const oldDiv = "{{ old('division') }}";
                        const oldDist = "{{ old('district') }}";
                        const oldUpz = "{{ old('upazila') }}";

                        if (oldDiv) {
                            loadDistricts(oldDiv, oldDist, '#edit_district');
                            if (oldDist) {
                                loadupazilas(oldDiv, oldDist, oldUpz, '#edit_upazila');
                            }
                        }
                    }
                @else
                    var addModalElement = document.getElementById('addOfficerModal');
                    if (addModalElement) {
                        var addModal = new bootstrap.Modal(addModalElement);
                        addModal.show();
                    }
                @endif
            @endif
        });
    </script>

    <script>
        const locationData = @json($locationData);
        const divisions = locationData.divisions || [];

        const oldDivision = "{{ old('division') }}";
        const oldDistrict = "{{ old('district') }}";
        const oldupazila = "{{ old('upazila') }}";

        $(document).ready(function() {
            const $divSelect = $('select[name="division"]');
            const $distSelect = $('select[name="district"]');
            const $upazilaSelect = $('select[name="upazila"]');

            divisions.forEach(div => {
                const selected = (div.name_en === oldDivision) ? 'selected' : '';
                $divSelect.append(`<option value="${div.name_en}" ${selected}>${div.name_en}</option>`);
            });

            if (oldDivision) {
                loadDistricts(oldDivision, oldDistrict);
            }

            if (oldDistrict) {
                loadupazilas(oldDivision, oldDistrict, oldupazila);
            }

            $divSelect.on('change', function() {
                loadDistricts($(this).val());
            });

            $distSelect.on('change', function() {
                loadupazilas($divSelect.val(), $(this).val());
            });

            function loadDistricts(divName, selectedDist = '') {
                $distSelect.html('<option value="">Select District</option>').prop('disabled', !divName);
                $upazilaSelect.html('<option value="">Select upazila</option>').prop('disabled', true);

                if (divName) {
                    const selectedDiv = divisions.find(d => d.name_en === divName);
                    if (selectedDiv && selectedDiv.districts) {
                        selectedDiv.districts.forEach(dist => {
                            const isSelected = (dist.name_en === selectedDist) ? 'selected' : '';
                            $distSelect.append(
                                `<option value="${dist.name_en}" ${isSelected}>${dist.name_en}</option>`
                            );
                        });
                        $distSelect.prop('disabled', false);
                    }
                }
            }

            function loadupazilas(divName, distName, selectedUpz = '') {
                $upazilaSelect.html('<option value="">Select upazila</option>').prop('disabled', !distName);

                if (distName) {
                    const selectedDiv = divisions.find(d => d.name_en === divName);
                    const selectedDist = selectedDiv.districts.find(d => d.name_en === distName);

                    if (selectedDist && selectedDist.police_stations) {
                        selectedDist.police_stations.forEach(ps => {
                            const isSelected = (ps.name_en === selectedUpz) ? 'selected' : '';
                            $upazilaSelect.append(
                                `<option value="${ps.name_en}" ${isSelected}>${ps.name_en}</option>`);
                        });
                        $upazilaSelect.prop('disabled', false);
                    }
                }
            }
        });
    </script>

    <script>
        $(document).ready(function() {

            $(document).on('click', '.edit-btn', function() {
                const data = $(this).data();
                const $modal = $('#editOfficerModal');


                $('#editOfficerForm').attr('action', data.url);
                $('#edit_url_handler').val(data.url);
                $('#edit_name').val(data.name);
                $('#edit_email').val(data.email);
                $('#edit_designation').val(data.designation);
                $('#edit_phone').val(data.phone);

                let divOptions = '<option value="">Select Division</option>';
                if (typeof divisions !== 'undefined') {
                    divisions.forEach(div => {
                        const isSelected = (div.name_en === data.division) ? 'selected' : '';
                        divOptions +=
                            `<option value="${div.name_en}" ${isSelected}>${div.name_en}</option>`;
                    });
                }
                $('#edit_division').html(divOptions);

                loadDistricts(data.division, data.district, '#edit_district');
                loadupazilas(data.division, data.district, data.upazila, '#edit_upazila');

                $modal.modal('show');
            });

            $('#edit_division').on('change', function() {
                const selectedDiv = $(this).val();
                loadDistricts(selectedDiv, '', '#edit_district');
                $('#edit_upazila').html('<option value="">Select upazila</option>').prop('disabled',
                    true);
            });

            $('#edit_district').on('change', function() {
                const selectedDiv = $('#edit_division').val();
                const selectedDist = $(this).val();
                loadupazilas(selectedDiv, selectedDist, '', '#edit_upazila');
            });

            function loadDistricts(divName, selectedDist, target) {
                const div = divisions.find(d => d.name_en === divName);
                let options = '<option value="">Select District</option>';
                if (div && div.districts) {
                    div.districts.forEach(dist => {
                        const sel = (dist.name_en === selectedDist) ? 'selected' : '';
                        options += `<option value="${dist.name_en}" ${sel}>${dist.name_en}</option>`;
                    });
                }
                $(target).html(options).prop('disabled', !div);
            }

            function loadupazilas(divName, distName, selectedUpz, target) {
                const div = divisions.find(d => d.name_en === divName);
                const dist = div?.districts?.find(d => d.name_en === distName);
                let options = '<option value="">Select upazila</option>';

                if (dist && dist.police_stations) {
                    dist.police_stations.forEach(ps => {
                        // trim() ব্যবহার করা হয়েছে যাতে কোনো স্পেস থাকলে সমস্যা না হয়
                        const isSelected = (String(ps.name_en).trim() === String(selectedUpz).trim()) ?
                            'selected' : '';
                        options += `<option value="${ps.name_en}" ${isSelected}>${ps.name_en}</option>`;
                    });
                    $(target).html(options).prop('disabled', false);
                } else {
                    $(target).html(options).prop('disabled', true);
                }
            }
        });
    </script>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            }
        });
    </script>

    <script>
        $(document).on('click', '#toggleEditPassword', function() {
            const passInput = $('#edit_password');
            const icon = $('#editEyeIcon');
            if (passInput.attr('type') === 'password') {
                passInput.attr('type', 'text');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            } else {
                passInput.attr('type', 'password');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            }
        });
    </script>
@endpush
