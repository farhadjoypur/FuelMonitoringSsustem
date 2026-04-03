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

        .table thead th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
            text-transform: capitalize;
            border-bottom: 1px solid #eee;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f1f1;
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
            font-weight: 700;
            color: #333;
        }

        .officer-subtext {
            font-size: 0.75rem;
            color: #888;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Assign Tag Officer</h3>
                <p class="text-muted small mb-0">Manage tag officer assignments to filling stations</p>
            </div>
            <button class="btn btn-create" data-bs-toggle="modal" data-bs-target="#assignOfficerModal">
                <i class="bi bi-plus-lg me-1"></i> Assign Tag Officer
            </button>
        </div>

        <form action="{{ route('admin.assign-tag-officer.index') }}" method="GET">
            <div class="position-relative mb-4">
                <i class="bi bi-search search-icon"
                    style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%);"></i>
                <input type="text" name="search" id="searchInput" class="form-control search-box"
                    style="padding-left: 40px;" value="{{ request('search') }}" placeholder="Search...">
            </div>
        </form>


        <div class="card card-assignment">
            <div class="card-body p-0">
                <div class="p-4">
                    <h5 class="fw-bold mb-0">Tag Officer Assignments</h5>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Station Name</th>
                                <th>Officer</th>
                                <th>Assign Date</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $assignment)
                                <tr>
                                    <td class="fw-bold">
                                        {{ $assignment->fillingStation->station_name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <div class="officer-info">
                                            <span
                                                class="officer-name">{{ $assignment->officer->profile->name ?? 'N/A' }}</span>
                                            <span class="officer-subtext">
                                                {{ $assignment->officer->profile->designation ?? 'Officer' }},
                                                {{ $assignment->officer->profile->upazilla ?? '' }}
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
                                            data-date="{{ $assignment->assign_date }}"
                                            data-status="{{ $assignment->status }}"
                                            data-url="{{ route('admin.assign-tag-officer.update', $assignment->id) }}">
                                            <i class="bi bi-pencil text-primary"></i>
                                        </button>

                                        <form action="{{ route('admin.assign-tag-officer.destroy', $assignment->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action border-0 bg-transparent">
                                                <i class="bi bi-trash text-muted"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-info-circle d-block mb-2 fs-3"></i>
                                        No assignments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
                            <select name="officer_id"
                                class="form-select searchable-select @error('officer_id') is-invalid @enderror">
                                <option value="">Select Officer</option>
                                @foreach ($officers as $officer)
                                    <option value="{{ $officer->id }}"
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
                            <select name="filling_station_id"
                                class="form-select searchable-select @error('filling_station_id') is-invalid @enderror">
                                <option value="">Select Station</option>
                                @foreach ($stations as $station)
                                    <option value="{{ $station->id }}"
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
                                    <option value="{{ $station->id }}"
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
        $(document).ready(function() {
            $('#assignOfficerModal .searchable-select').select2({
                dropdownParent: $('#assignOfficerModal'),
                width: '100%',
                placeholder: "Select an option"
            });

            $('#editAssignOfficerModal .searchable-select').select2({
                dropdownParent: $('#editAssignOfficerModal'),
                width: '100%',
                allowClear: true,
                placeholder: "Select an option"
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

            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                var officer_id = $(this).data('officer_id');
                var station_id = $(this).data('station_id');
                var date = $(this).data('date');
                var status = $(this).data('status');
                var url = $(this).data('url');

                $('#editAssignForm').attr('action', url);
                $('#edit_url_handler').val(url);

                $('#edit_officer_id').val(officer_id).trigger('change');
                $('#edit_station_id').val(station_id).trigger('change');

                $('#edit_assign_date').val(date);
                $('#edit_status').val(status);

                $('#editAssignOfficerModal').modal('show');
            });

            @if ($errors->any())
                @if (old('form_type') == 'edit')

                    var oldUrl = "{{ old('edit_url_handler') }}";
                    if (oldUrl) {
                        $('#editAssignForm').attr('action', oldUrl);
                    }
                    $('#editAssignOfficerModal').modal('show');
                @else

                    $('#assignOfficerModal').modal('show');
                @endif
            @endif
        });
    </script>
@endpush
