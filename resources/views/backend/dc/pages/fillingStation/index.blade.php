@extends('backend.dc.layouts.app')

@section('title', 'Filling Station Management')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            background-color: #f8f9fa !important;
            border: none !important;
            border-radius: 8px !important;
            height: 38px !important;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529 !important;
            padding-left: 12px !important;
            font-size: 0.9rem !important;
            line-height: 38px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            outline: none !important;
            box-shadow: none !important;
        }

        .select2-dropdown {
            border: 1px solid #eee !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        /* Search & Filters */
        .form-control,
        .form-select {
            border-radius: 6px;
            border: 1px solid #ddd;
            height: 38px;
        }

        .btn-filter {
            background-color: #006699;
            color: white;
            border: none;
            padding: 0 25px;
        }

        .btn-filter:hover {
            background-color: #004d73;
            color: white;
        }

        .btn-add {
            background-color: #006699;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 500;
            border: none;
        }

        /* Status & Tags */
        .badge-status {
            background-color: #e6f7ef;
            color: #28a745;
            border-radius: 20px;
            padding: 5px 15px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status::before {
            content: "";
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
        }

        .fuel-tag {
            background: #f1f3f5;
            color: #495057;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            display: inline-block;
            margin-bottom: 2px;
        }

        /* Modal Styles */
        .modal-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .required::after {
            content: " *";
            color: red;
        }

        .btn-save {
            background-color: #006699;
            color: white;
            border: none;
            padding: 8px 25px;
            border-radius: 6px;
        }

        .btn-cancel {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 8px 25px;
            border-radius: 6px;
            color: #333;
        }

        .action-btn {
            color: #adb5bd;
            transition: 0.3s;
            margin: 0 5px;
            text-decoration: none;
        }

        .action-btn:hover {
            color: #006699;
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

        .table tbody td {
            vertical-align: middle;
            font-size: 14px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold">Filling Station Management</h4>
                <p class="text-muted small">Manage and organize all filling station information</p>
            </div>

            <button class="btn btn-primary px-4 py-2 w-sm-100 w-auto" data-bs-toggle="modal" data-bs-target="#addStationModal"
                style="background-color: #006699; border-radius: 8px; border: none;">
                <i class="bi bi-plus-lg me-2"></i> Add Filling Station
            </button>
        </div>

        <form action="{{ route('dc.filling-station.index') }}" method="GET"
            class="bg-white p-3 rounded shadow-sm border mb-4">
            <div class="row g-2 align-items-end">
                {{-- Search Field --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" id="searchInput" class="form-control border-0 bg-light"
                            style="border-radius: 8px 0 0 8px; font-size: 0.9rem;" value="{{ request('search') }}"
                            placeholder="Search here...">
                        <button class="btn btn-dark border-0 px-3" type="submit"
                            style="border-radius: 0 8px 8px 0; background-color: #006699;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Stations</label>
                    <select name="station_name" id="stationNameFilter"
                        class="form-select border-0 bg-light searchable-select" style="border-radius: 8px;">
                        <option value="">All Stations</option>
                        @foreach ($allStationNames as $s)
                            <option value="{{ $s->station_name }}"
                                {{ request('station_name') == $s->station_name ? 'selected' : '' }}>
                                {{ $s->station_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Division Field --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Division</label>
                    <select name="division" id="filter_division" class="form-select border-0 bg-light"
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
                    <select name="district" id="filter_district" class="form-select border-0 bg-light"
                        style="border-radius: 8px;">
                        <option value="">All District</option>
                    </select>
                </div>

                {{-- Upazila Field --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Upazila</label>
                    <select name="upazila" id="filter_upazila" class="form-select border-0 bg-light"
                        style="border-radius: 8px;">
                        <option value="">All Upazila</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm"
                        style="background-color: #006699; border: none; border-radius: 8px; height: 38px;">
                        <i class="bi bi-funnel-fill me-1"></i> Filter
                    </button>
                    <a href="{{ route('dc.filling-station.index') }}"
                        class="btn btn-outline-secondary shadow-sm d-flex align-items-center justify-content-center"
                        style="border-radius: 8px; height: 38px;">
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
                            <th class="text-nowrap">Station Name</th>
                            {{-- <th>Code</th> --}}
                            <th>Division</th>
                            <th>District</th>
                            <th>Upazila</th>
                            {{-- <th>Owner</th> --}}
                            <th>Company</th>
                            {{-- <th>Capacity</th> --}}
                            <th class="text-nowrap">Fuel Types</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stations as $key => $station)
                            <tr>
                                <td>{{ $stations->firstItem() + $key }}</td>
                                <td>{{ $station->station_name }}</td>
                                {{-- <td class="text-info fw-bold">{{ $station->station_code }}</td> --}}
                                <td>{{ $station->division ?? '-' }}</td>
                                <td>{{ $station->district ?? '-' }}</td>
                                <td>{{ $station->upazila ?? '-' }}</td>
                                {{-- <td>{{ $station->owner_name ?? '—' }}</td> --}}
                                <td>{{ $station->company ? explode(' ', $station->company->name)[0] : '-' }}</td>
                                {{-- <td>{{ $station->tank_capacity ? number_format($station->tank_capacity) . ' L' : '—' }} --}}
                                </td>
                                <td>
                                    @if ($station->fuel_types)
                                        @foreach ($station->fuel_types as $fuel)
                                            <span class="fuel-tag">{{ $fuel }}</span>
                                        @endforeach
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if (strtolower($station->status) == 'active')
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
                                        <button class="btn-action btn-edit me-2 edit-btn" data-id="{{ $station->id }}"
                                            data-company_id="{{ $station->company_id }}"
                                            data-station_name="{{ $station->station_name }}"
                                            data-station_code="{{ $station->station_code }}"
                                            data-owner_name="{{ $station->owner_name }}"
                                            data-owner_phone="{{ $station->owner_phone }}"
                                            data-division="{{ $station->division }}"
                                            data-district="{{ $station->district }}"
                                            data-upazila="{{ $station->upazila }}" data-address="{{ $station->address }}"
                                            data-linked_depot="{{ $station->linked_depot }}"
                                            data-tank_capacity="{{ $station->tank_capacity }}"
                                            data-fuel_types="{{ json_encode($station->fuel_types ?? []) }}"
                                            data-status="{{ $station->status }}"
                                            data-url="{{ route('dc.filling-station.update', $station->id) }}"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('dc.filling-station.destroy', $station->id) }}"
                                            method="POST" class="d-inline delete-form">
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
                                <td colspan="12" class="text-center py-5 text-muted">No Filling Stations Found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="my-4">
            {{ $stations->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <div class="modal fade" id="addStationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content" style="border-radius: 12px; border: none;">
                <div class="modal-header border-bottom px-4">
                    <h5 class="modal-title fw-bold" style="font-size: 1.1rem;">Add Filling Station</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('dc.filling-station.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="form_type" value="create">

                        <div class="mb-3">
                            <label class="modal-label required">Company</label>
                            <select name="company_id" class="form-select @error('company_id') is-invalid @enderror">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name ? explode(' ', trim($company->name))[0] : 'Not Found' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label required">Station Name</label>
                            <input type="text" class="form-control @error('station_name') is-invalid @enderror"
                                name="station_name" value="{{ old('station_name') }}">
                            @error('station_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Station Code</label>
                            <input type="text" class="form-control @error('station_code') is-invalid @enderror"
                                name="station_code" value="{{ old('station_code') }}">
                            @error('station_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Owner Name</label>
                            <input type="text" class="form-control" name="owner_name"
                                value="{{ old('owner_name') }}">
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Owner Phone</label>
                            <input type="text" class="form-control @error('owner_phone') is-invalid @enderror"
                                name="owner_phone" value="{{ old('owner_phone') }}">
                            @error('owner_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label required">Division</label>
                            <select class="form-select @error('division') is-invalid @enderror" name="division"
                                id="add_division">
                                <option value="">Select Division</option>
                                @foreach ($locationData['divisions'] as $div)
                                    <option value="{{ $div['name_en'] }}"
                                        {{ old('division') == $div['name_en'] ? 'selected' : '' }}>
                                        {{ $div['name_en'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('division')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label required">District</label>
                            <select class="form-select @error('district') is-invalid @enderror" name="district"
                                id="add_district">
                                <option value="">Select District</option>
                            </select>
                            @error('district')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label required">Upazila</label>
                            <select class="form-select @error('upazila') is-invalid @enderror" name="upazila"
                                id="add_upazila">
                                <option value="">Select Upazila</option>
                            </select>
                            @error('upazila')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Full Address</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror"
                                name="address" value="{{ old('address') }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Linked Depot</label>
                            <select name="linked_depot" class="form-select @error('linked_depot') is-invalid @enderror">
                                <option value="">Select Linked Depot</option>
                                @foreach ($depots as $depot)
                                    <option value="{{ $depot->id }}"
                                        {{ old('linked_depot') == $depot->id ? 'selected' : '' }}>
                                        {{ $depot->depot_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('linked_depot')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Tank Capacity</label>
                            <input type="text" class="form-control @error('tank_capacity') is-invalid @enderror"
                                name="tank_capacity" placeholder="e.g., 10,000" value="{{ old('tank_capacity') }}">
                            @error('tank_capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Fuel Types</label>
                            <div class="d-flex flex-column gap-2 mt-1">
                                @php $oldFuels = old('fuel_types', []); @endphp
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fuel_types[]" value="Petrol"
                                        id="petrol" {{ in_array('Petrol', $oldFuels) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="petrol">Petrol</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fuel_types[]" value="Diesel"
                                        id="diesel" {{ in_array('Diesel', $oldFuels) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="diesel">Diesel</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fuel_types[]" value="Octane"
                                        id="octane" {{ in_array('Octane', $oldFuels) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="octane">Octane</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fuel_types[]" value="Others"
                                        id="others" {{ in_array('Others', $oldFuels) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="others">Others</label>
                                </div>
                            </div>
                            @error('fuel_types')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="modal-label">Upload License</label>
                            <input type="file" class="form-control @error('license_file') is-invalid @enderror"
                                name="license_file">
                            @error('license_file')
                                <div class="invalid-feedback">{{ $message }}</div>
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

    <div class="modal fade" id="editStationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content" style="border-radius: 12px; border: none;">
                <div class="modal-header border-bottom px-4">
                    <h5 class="modal-title fw-bold" style="font-size: 1.1rem;">Edit Filling Station</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="editStationForm" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="form_type" value="edit">
                        <input type="hidden" name="edit_url_handler" id="edit_url_handler"
                            value="{{ old('edit_url_handler') }}">

                        <div class="mb-3">
                            <label class="modal-label required">Company</label>
                            <select name="company_id" id="edit_company_id"
                                class="form-select @error('company_id') is-invalid @enderror">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">
                                        {{ $company->name ? explode(' ', trim($company->name))[0] : 'Not Found' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label required">Station Name</label>
                            <input type="text" class="form-control @error('station_name') is-invalid @enderror"
                                name="station_name" id="edit_station_name">
                            @error('station_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Station Code</label>
                            <input type="text" class="form-control @error('station_code') is-invalid @enderror"
                                name="station_code" id="edit_station_code">
                            @error('station_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Owner Name</label>
                            <input type="text" class="form-control" name="owner_name" id="edit_owner_name">
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Owner Phone</label>
                            <input type="text" class="form-control @error('owner_phone') is-invalid @enderror"
                                name="owner_phone" id="edit_owner_phone">
                            @error('owner_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label required">Division</label>
                            <select class="form-select @error('division') is-invalid @enderror" name="division"
                                id="edit_division">
                                <option value="">Select Division</option>
                            </select>
                            @error('division')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label required">District</label>
                            <select class="form-select @error('district') is-invalid @enderror" name="district"
                                id="edit_district">
                                <option value="">Select District</option>
                            </select>
                            @error('district')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label required">Upazila</label>
                            <select class="form-select @error('upazila') is-invalid @enderror" name="upazila"
                                id="edit_upazila">
                                <option value="">Select Upazila</option>
                            </select>
                            @error('upazila')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Full Address</label>
                            <input type="text" class="form-control" name="address" id="edit_address">
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Linked Depot</label>
                            <select name="linked_depot" id="edit_linked_depot"
                                class="form-select @error('linked_depot') is-invalid @enderror">
                                <option value="">Select Linked Depot</option>
                                @foreach ($depots as $depot)
                                    <option value="{{ $depot->id }}">{{ $depot->depot_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Tank Capacity</label>
                            <input type="text" class="form-control" name="tank_capacity" id="edit_tank_capacity">
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Fuel Types</label>
                            <div class="d-flex flex-column gap-2 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input edit-fuel-checkbox" type="checkbox"
                                        name="fuel_types[]" value="Petrol" id="edit_petrol">
                                    <label class="form-check-label" for="edit_petrol">Petrol</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input edit-fuel-checkbox" type="checkbox"
                                        name="fuel_types[]" value="Diesel" id="edit_diesel">
                                    <label class="form-check-label" for="edit_diesel">Diesel</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input edit-fuel-checkbox" type="checkbox"
                                        name="fuel_types[]" value="Octane" id="edit_octane">
                                    <label class="form-check-label" for="edit_octane">Octane</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input edit-fuel-checkbox" type="checkbox"
                                        name="fuel_types[]" value="Others" id="edit_others">
                                    <label class="form-check-label" for="edit_others">Others</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="modal-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="modal-label">Upload New License</label>
                            <input type="file" class="form-control @error('license_file') is-invalid @enderror"
                                name="license_file">
                            @error('license_file')
                                <div class="invalid-feedback">{{ $message }}</div>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const locationData = @json($locationData);
        const divisions = locationData.divisions || [];

        $(document).ready(function() {

            // ===================================
            // 🔹 INITIALIZE SELECT2 (সার্চযোগ্য করার জন্য)
            // ===================================
            function initSelect2(selector, placeholder) {
                $(selector).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%',

                });
            }

            initSelect2('#stationNameFilter', 'Select Station');

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
            // 🔹 FILTER (PERSISTENCE LOGIC)
            // ===============================

            // ফিল্টার করার পর পেজ রিলোড হলে সিলেক্টেড ভ্যালু অনুযায়ী জেলা/উপজেলা লোড করা
            const currentDivision = "{{ request('division') }}";
            const currentDistrict = "{{ request('district') }}";
            const currentUpazila = "{{ request('upazila') }}";

            if (currentDivision) {
                loadDistricts(currentDivision, currentDistrict, '#filter_district');
                if (currentDistrict) {
                    loadUpazilas(currentDivision, currentDistrict, currentUpazila, '#filter_upazila');
                }
            }

            $('#filter_division').on('change', function() {
                loadDistricts($(this).val(), '', '#filter_district');
                $('#filter_upazila').html('<option value="">All Upazila</option>').prop('disabled', true);
            });

            $('#filter_district').on('change', function() {
                loadUpazilas($('#filter_division').val(), $(this).val(), '', '#filter_upazila');
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

                $('#editStationForm').attr('action', data.url);
                $('#edit_url_handler').val(data.url);

                $('#edit_company_id').val(data.company_id);
                $('#edit_station_name').val(data.station_name);
                $('#edit_station_code').val(data.station_code);
                $('#edit_owner_name').val(data.owner_name);
                $('#edit_owner_phone').val(data.owner_phone);
                $('#edit_address').val(data.address);
                $('#edit_tank_capacity').val(data.tank_capacity);
                $('#edit_linked_depot').val(data.linked_depot);
                $('#edit_status').val(data.status);

                populateDivisions(data.division, '#edit_division');
                loadDistricts(data.division, data.district, '#edit_district');
                loadUpazilas(data.division, data.district, data.upazila, '#edit_upazila');

                // fuel
                $('.edit-fuel-checkbox').prop('checked', false);
                let fuels = typeof data.fuel_types === 'string' ? JSON.parse(data.fuel_types) : data
                    .fuel_types;

                if (Array.isArray(fuels)) {
                    fuels.forEach(f => {
                        $(`.edit-fuel-checkbox[value="${f}"]`).prop('checked', true);
                    });
                }

                $('#editStationModal').modal('show');
            });

            $('#edit_division').on('change', function() {
                loadDistricts($(this).val(), '', '#edit_district');
                $('#edit_upazila').html('<option value="">Select Upazila</option>').prop('disabled', true);
            });

            $('#edit_district').on('change', function() {
                loadUpazilas($('#edit_division').val(), $(this).val(), '', '#edit_upazila');
            });

            // ===============================
            // 🔹 DELETE
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
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // ===============================
            // 🔹 VALIDATION ERROR FIX (MAIN)
            // ===============================
            @if ($errors->any())
                let oldAction = "{{ old('edit_url_handler') }}";
                let oldDiv = "{{ old('division') }}";
                let oldDist = "{{ old('district') }}";
                let oldUpz = "{{ old('upazila') }}";

                if (oldAction) {
                    $('#editStationForm').attr('action', oldAction);
                    populateDivisions(oldDiv, '#edit_division');
                    if (oldDiv) {
                        loadDistricts(oldDiv, oldDist, '#edit_district');
                        if (oldDist) {
                            loadUpazilas(oldDiv, oldDist, oldUpz, '#edit_upazila');
                        }
                    }
                    $('#editStationModal').modal('show');
                } else {
                    populateDivisions(oldDiv, '#add_division');
                    if (oldDiv) {
                        loadDistricts(oldDiv, oldDist, '#add_district');
                        if (oldDist) {
                            loadUpazilas(oldDiv, oldDist, oldUpz, '#add_upazila');
                        }
                    }
                    $('#addStationModal').modal('show');
                }
            @endif

        });
    </script>
@endpush
