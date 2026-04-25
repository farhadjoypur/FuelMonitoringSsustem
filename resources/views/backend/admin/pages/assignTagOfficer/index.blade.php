@extends('backend.admin.layouts.app')

@section('title', 'Assign Tag Officer')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 40px;
            background-color: #f8f9fa;
            border: none;
            border-radius: 5px;
            padding: 5px;
        }

        .card-assignment {
            border-radius: 12px;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-create {
            background-color: #006699;
            color: white;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
        }

        .btn-create:hover {
            background-color: #005580;
            color: white;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .bg-active {
            background-color: #e6f7ef;
            color: #1aae6f;
        }

        .bg-inactive {
            background-color: #fdeeee;
            color: #f25961;
        }

        .action-btn {
            color: #006699;
            font-size: 1.1rem;
            margin: 0 5px;
            cursor: pointer;
        }

        .delete-btn {
            color: #777;
        }

        .officer-info {
            display: flex;
            flex-direction: column;
        }

        .officer-name {
            font-weight: bold;
            color: #333;
        }

        .officer-subtext {
            font-size: 0.75rem;
            color: #888;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .search-box {
            border-radius: 25px;
            padding-left: 45px;
            height: 45px;
            border: 1px solid #ddd;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold">Assign Tag Officer</h4>
                <p class="text-muted small">Manage tag officer assignments to filling stations</p>
            </div>

            <button class="btn btn-primary px-4 py-2 w-sm-100 w-auto" data-bs-toggle="modal"
                data-bs-target="#assignOfficerModal" style="background-color: #006699; border-radius: 8px; border: none;">
                <i class="bi bi-plus-lg me-2"></i> Assign Tag Officer
            </button>
        </div>

        {{-- <form action="{{ route('admin.assign-tag-officer.index') }}" method="GET">
            <div class="position-relative mb-4">
                <i class="bi bi-search search-icon"
                    style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" name="search" id="searchInput" class="form-control search-box py-2"
                    style="padding-left: 40px;" value="{{ request('search') }}" placeholder="Search...">
            </div>
        </form> --}}

        <form action="{{ route('admin.assign-tag-officer.index') }}" method="GET"
            class="bg-white p-3 rounded shadow-sm border mb-4">
            <div class="row g-2 align-items-end">

                {{-- Search Field --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control border-0 bg-light"
                            style="border-radius: 8px 0 0 8px; font-size: 0.9rem;" value="{{ request('search') }}"
                            placeholder="Officer or Station Name...">
                        <button class="btn btn-dark border-0 px-3" type="submit"
                            style="border-radius: 0 8px 8px 0; background-color: #006699;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                {{-- Division Field --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Division</label>
                    <select name="division" id="division" class="form-select border-0 bg-light"
                        style="border-radius: 8px;">
                        <option value="">All Division</option>
                        @foreach ($locationData['divisions'] as $div)
                            <option value="{{ $div['name_en'] }}"
                                {{ request('division') == $div['name_en'] ? 'selected' : '' }}>
                                {{ $div['name_en'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- District Field --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">District</label>
                    <select name="district" id="district" class="form-select border-0 bg-light"
                        style="border-radius: 8px;">
                        <option value="">All District</option>
                    </select>
                </div>

                {{-- Upazila Field --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Upazila</label>
                    <select name="upazila" id="upazila" class="form-select border-0 bg-light" style="border-radius: 8px;">
                        <option value="">All Upazila</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm"
                        style="background-color: #006699; border: none; border-radius: 8px; height: 38px;">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>

                    <a href="{{ route('admin.assign-tag-officer.index') }}"
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
                            <th>Station Name</th>
                            <th>Officer</th>
                            <th>Assign Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $key => $assignment)
                            <tr>
                                <td>{{ $assignments->firstItem() + $key }}</td>
                                <td>
                                    <div class="officer-info">
                                        <span
                                            class="officer-name">{{ $assignment->fillingStation->station_name ?? '-' }}</span>
                                        <span class="officer-subtext">
                                            {{ $assignment->fillingStation->district ?? '' }},
                                            {{ $assignment->fillingStation->upazila ?? '' }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <div class="officer-info">
                                        <span
                                            class="officer-name">{{ $assignment->officer->profile->name ?? 'N/A' }}</span>
                                        <span class="officer-subtext">
                                            {{ $assignment->officer->profile->district ?? 'Officer' }},
                                            {{ $assignment->officer->profile->upazila ?? '' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-muted">
                                    {{ \Carbon\Carbon::parse($assignment->assign_date)->format('d M Y') }}
                                </td>
                                <td>
                                    @if ($assignment->status == 'active')
                                        <span class="status-badge bg-active">Active</span>
                                    @else
                                        <span class="status-badge bg-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn-action edit-btn border-0 bg-transparent me-2"
                                        data-id="{{ $assignment->id }}" data-officer_id="{{ $assignment->officer_id }}"
                                        data-station_id="{{ $assignment->filling_station_id }}"
                                        data-date="{{ $assignment->assign_date }}" data-status="{{ $assignment->status }}"
                                        data-url="{{ route('admin.assign-tag-officer.update', $assignment->id) }}">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </button>

                                    {{-- <form action="{{ route('admin.assign-tag-officer.destroy', $assignment->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action border-0 bg-transparent">
                                                <i class="bi bi-trash text-muted"></i>
                                            </button>
                                        </form> --}}

                                    <form action="{{ route('admin.assign-tag-officer.destroy', $assignment->id) }}"
                                        method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="btn-action border-0 bg-transparent text-danger delete-confirm"
                                            title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-5 text-muted">
                                    <i class="bi bi-info-circle d-block mb-2 fs-3"></i>
                                    No assignments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="my-4">
            {{ $assignments->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <div class="modal fade" id="assignOfficerModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" style="color: #1a202c;">Tag Officer Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form action="{{ route('admin.assign-tag-officer.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="form_type" value="create">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Select Officer *</label>
                            <select name="officer_id" id="officerSelect" class="form-select searchable-select">
                                <option value="">Select Officer</option>
                                @foreach ($officers as $officer)
                                    <option value="{{ $officer->id }}"
                                        data-upazila="{{ $officer->profile->upazila ?? '' }}">
                                        {{ $officer->profile->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('officer_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Station Select -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Select Filling Station*</label>
                            <select name="filling_station_id" id="stationSelect" class="form-select searchable-select">
                                <option value="">Select Station</option>
                                @foreach ($stations as $station)
                                    <option value="{{ $station->id }}" data-upazila="{{ $station->upazila }}">
                                        {{ $station->station_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('filling_station_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Assign Date *</label>
                            <input type="date" name="assign_date"
                                class="form-control bg-light border-0 py-2 @error('assign_date') is-invalid @enderror"
                                value="{{ old('assign_date', date('Y-m-d')) }}">
                            @error('assign_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Status *</label>
                            <select name="status"
                                class="form-select bg-light border-0 py-2 @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Remarks</label>
                            <input type="text" name="remarks"
                                class="form-control bg-light border-0 py-2 @error('remarks') is-invalid @enderror"
                                value="{{ old('remarks') }}" placeholder="Enter any notes">
                            @error('remarks')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-outline-secondary w-50 py-2 border-light text-muted"
                                data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                            <button type="submit" class="btn btn-primary w-50 py-2"
                                style="background-color: #006699; border: none; border-radius: 10px;">
                                Create Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAssignOfficerModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Update Tag Officer Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form id="editAssignForm" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="form_type" value="edit">
                        <input type="hidden" name="edit_url_handler" id="edit_url_handler"
                            value="{{ old('edit_url_handler') }}">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Select Officer *</label>
                            <select name="officer_id" id="edit_officer_id"
                                class="form-select searchable-select @error('officer_id') is-invalid @enderror">
                                @foreach ($officers as $officer)
                                    <option value="{{ $officer->id }}"
                                        data-upazila="{{ $officer->profile->upazila ?? '' }}"
                                        {{ old('officer_id') == $officer->id ? 'selected' : '' }}>
                                        {{ $officer->profile->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('officer_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Select Filling Station*</label>
                            <select name="filling_station_id" id="edit_station_id"
                                class="form-select searchable-select @error('filling_station_id') is-invalid @enderror">
                                @foreach ($stations as $station)
                                    <option value="{{ $station->id }}" data-upazila="{{ $station->upazila }}"
                                        {{ old('filling_station_id') == $station->id ? 'selected' : '' }}>
                                        {{ $station->station_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('filling_station_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Assign Date *</label>
                            <input type="date" name="assign_date" id="edit_assign_date"
                                class="form-control bg-light border-0 py-2 @error('assign_date') is-invalid @enderror"
                                value="{{ old('assign_date') }}">
                            @error('assign_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Status *</label>
                            <select name="status" id="edit_status"
                                class="form-select bg-light border-0 py-2 @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mb-4 mt-2">
                            <button type="button" class="btn btn-outline-secondary w-50 py-2 border-light text-muted"
                                data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
                            <button type="submit" class="btn btn-primary w-50 py-2"
                                style="background-color: #006699; border: none; border-radius: 10px;">
                                Update Assignment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const locationData = @json($locationData);
        const divisions = locationData.divisions || [];

        function loadDistricts(divName, selectedDist, target) {
            const div = divisions.find(d => d.name_en === divName);
            let options = '<option value="">All District</option>';
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
            let options = '<option value="">All Upazila</option>';
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

        $(document).ready(function() {
            // ==========================================
            // 🔹 TOP SEARCH FILTER LOGIC
            // ==========================================
            const oldDivision = "{{ request('division') }}";
            const oldDistrict = "{{ request('district') }}";
            const oldUpazila = "{{ request('upazila') }}";

            if (oldDivision) {
                loadDistricts(oldDivision, oldDistrict, '#district');
                if (oldDistrict) {
                    loadUpazilas(oldDivision, oldDistrict, oldUpazila, '#upazila');
                }
            }

            $('#division').on('change', function() {
                loadDistricts($(this).val(), '', '#district');
                $('#upazila').html('<option value="">All Upazila</option>');
            });

            $('#district').on('change', function() {
                loadUpazilas($('#division').val(), $(this).val(), '', '#upazila');
            });

            // ==========================================
            // 🔹 MODAL SELECT2 & STATION FILTERING
            // ==========================================
            const createStationBackup = $('#stationSelect').html();
            const editStationBackup = $('#edit_station_id').html();

            function initSelect2(modalId) {
                $(modalId + ' .searchable-select').each(function() {
                    $(this).select2({
                        width: '100%',
                        placeholder: "Select an option",
                        allowClear: true,
                        dropdownParent: $(this).closest('.modal')
                    });
                });
            }

            initSelect2('#assignOfficerModal');
            initSelect2('#editAssignOfficerModal');

            function filterStationOptions(officerSelectId, stationSelectId, backupHtml) {
                const officerSelect = $(officerSelectId);
                const stationSelect = $(stationSelectId);
                const selectedUpazila = officerSelect.find(':selected').data('upazila') || '';

                stationSelect.empty().append('<option value="">Select Station</option>');

                $(backupHtml).each(function() {
                    if (!$(this).val()) return;
                    if ($(this).data('upazila') == selectedUpazila) {
                        stationSelect.append($(this).clone());
                    }
                });

                stationSelect.trigger('change');
            }

            $('#officerSelect').on('change', function() {
                filterStationOptions('#officerSelect', '#stationSelect', createStationBackup);
            });

            // Edit Event
            $('#edit_officer_id').on('change', function() {
                filterStationOptions('#edit_officer_id', '#edit_station_id', editStationBackup);
            });

            // ==========================================
            // 🔹 EDIT BUTTON CLICK
            // ==========================================
            $(document).on('click', '.edit-btn', function() {
                const data = $(this).data();

                $('#editAssignForm').attr('action', data.url);
                $('#edit_url_handler').val(data.url);

                // অফিসার সেট করা এবং ফিল্টার ট্রিগার করা
                $('#edit_officer_id').val(data.officer_id).trigger('change');
                filterStationOptions('#edit_officer_id', '#edit_station_id', editStationBackup);

                // স্টেশন সেট করার জন্য সামান্য ডিলে
                setTimeout(() => {
                    $('#edit_station_id').val(data.station_id).trigger('change');
                }, 150);

                $('#edit_assign_date').val(data.date);
                $('#edit_status').val(data.status);
                $('#editAssignForm input[name="remarks"]').val(data.remarks);

                $('#editAssignOfficerModal').modal('show');
            });

            // ==========================================
            // 🔹 OTHERS (SEARCH, DELETE, VALIDATION)
            // ==========================================
            let timer;
            $('#searchInput').on('keyup', function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    $(this).closest('form').submit();
                }, 500);
            });

            $(document).on('click', '.delete-confirm', function() {
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

            @if ($errors->any())
                @if (old('form_type') == 'edit')
                    const oldUrl = "{{ old('edit_url_handler') }}";
                    if (oldUrl) $('#editAssignForm').attr('action', oldUrl);
                    $('#editAssignOfficerModal').modal('show');
                @else
                    $('#assignOfficerModal').modal('show');
                @endif
            @endif
        });
    </script>
@endpush
