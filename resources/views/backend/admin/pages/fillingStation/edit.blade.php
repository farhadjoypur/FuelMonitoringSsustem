@extends('backend.admin.layouts.app')

@section('styles')
<style>
    body, .main-content { background-color: #f0f4f7 !important; }
    .cm-wrapper { padding: 32px 36px; font-family: 'Segoe UI', sans-serif; }

    .cm-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; }
    .cm-header h2 { font-size: 1.75rem; font-weight: 700; color: #111827; margin: 0 0 4px; }
    .cm-header p { font-size: 0.875rem; color: #6b7280; margin: 0; }
    .btn-back {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fff; color: #374151; border: 1.5px solid #e5e9ef;
        border-radius: 10px; padding: 10px 20px;
        font-size: 0.875rem; font-weight: 600; text-decoration: none;
        transition: background 0.15s;
    }
    .btn-back:hover { background: #f9fafb; text-decoration: none; color: #111827; }
    .btn-back svg { width: 15px; height: 15px; }

    .form-card {
        background: #fff; border-radius: 16px;
        border: 1px solid #e5e9ef; padding: 32px 36px;
        box-shadow: 0 1px 6px rgba(0,0,0,0.05);
    }
    .form-section-title {
        font-size: 0.72rem; font-weight: 700; letter-spacing: 0.08em;
        text-transform: uppercase; color: #9ca3af;
        padding-bottom: 10px; margin-bottom: 20px;
        border-bottom: 1px solid #f3f4f6;
    }
    .form-grid { display: grid; gap: 20px; margin-bottom: 28px; }
    .form-grid-2 { grid-template-columns: 1fr 1fr; }
    .form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
    .form-grid-1 { grid-template-columns: 1fr; }

    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 0.8rem; font-weight: 600; color: #374151; }
    .form-label .req { color: #ef4444; margin-left: 2px; }
    .form-control-cm {
        width: 100%; padding: 10px 14px;
        border: 1.5px solid #e5e9ef; border-radius: 10px;
        font-size: 0.875rem; color: #111827; background: #fff;
        outline: none; transition: border-color 0.15s, box-shadow 0.15s;
        font-family: 'Segoe UI', sans-serif;
    }
    .form-control-cm:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .form-control-cm.is-invalid { border-color: #ef4444; }
    textarea.form-control-cm { resize: vertical; min-height: 90px; }
    .error-msg { font-size: 0.75rem; color: #ef4444; margin-top: 2px; }

    .fuel-options { display: flex; gap: 12px; flex-wrap: wrap; }
    .fuel-check {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 16px; border-radius: 8px;
        border: 1.5px solid #e5e9ef; cursor: pointer;
        font-size: 0.85rem; font-weight: 500; color: #374151;
        transition: border-color 0.15s, background 0.15s; user-select: none;
    }
    .fuel-check input { display: none; }
    .fuel-check.checked { border-color: #3b82f6; background: #eff6ff; color: #1d4ed8; }

    /* Existing file badge */
    .file-badge {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 7px 12px; border-radius: 8px;
        background: #f0fdf4; border: 1px solid #86efac;
        font-size: 0.8rem; color: #166534; margin-bottom: 8px;
    }
    .file-badge svg { width: 14px; height: 14px; }
    .file-badge a { color: #166534; font-weight: 600; text-decoration: underline; }

    .file-upload-label {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px; border-radius: 10px;
        border: 1.5px dashed #cbd5e1; cursor: pointer;
        font-size: 0.85rem; color: #6b7280; transition: border-color 0.15s;
    }
    .file-upload-label:hover { border-color: #3b82f6; color: #3b82f6; }
    .file-upload-label svg { width: 18px; height: 18px; flex-shrink: 0; }
    #license_file { display: none; }

    .form-footer {
        display: flex; align-items: center; justify-content: flex-end;
        gap: 12px; padding-top: 24px; border-top: 1px solid #f3f4f6;
    }
    .btn-cancel {
        padding: 10px 22px; border-radius: 10px;
        border: 1.5px solid #e5e9ef; background: #fff;
        font-size: 0.875rem; font-weight: 600; color: #374151;
        cursor: pointer; text-decoration: none; transition: background 0.15s;
    }
    .btn-cancel:hover { background: #f9fafb; text-decoration: none; color: #111827; }
    .btn-submit {
        padding: 10px 28px; border-radius: 10px; border: none;
        background: #1e3a5f; font-size: 0.875rem; font-weight: 600;
        color: #fff; cursor: pointer; transition: background 0.18s, box-shadow 0.18s;
    }
    .btn-submit:hover { background: #16304f; box-shadow: 0 4px 14px rgba(30,58,95,0.25); }
</style>
@endsection

@section('content')
<div class="cm-wrapper">

    {{-- HEADER --}}
    <div class="cm-header">
        <div>
            <h2>Edit Station</h2>
            <p>Update information for <strong>{{ $station->station_name }}</strong></p>
        </div>
        <a href="{{ route('stations.index') }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to List
        </a>
    </div>

    <div class="form-card">
        <form action="{{ route('stations.update', $station->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ── SECTION 1: Basic Info ── --}}
            <div class="form-section-title">Basic Information</div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Company <span class="req">*</span></label>
                    <select name="company_id" class="form-control-cm @error('company_id') is-invalid @enderror">
                        <option value="">— Select Company —</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}"
                                {{ (old('company_id', $station->company_id) == $company->id) ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Station Name <span class="req">*</span></label>
                    <input type="text" name="station_name"
                           class="form-control-cm @error('station_name') is-invalid @enderror"
                           value="{{ old('station_name', $station->station_name) }}"
                           placeholder="e.g. Padma Filling Station">
                    @error('station_name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Station Code <span class="req">*</span></label>
                    <input type="text" name="station_code"
                           class="form-control-cm @error('station_code') is-invalid @enderror"
                           value="{{ old('station_code', $station->station_code) }}"
                           placeholder="e.g. PFS-001">
                    @error('station_code') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Linked Depot</label>
                    <input type="text" name="linked_depot"
                           class="form-control-cm @error('linked_depot') is-invalid @enderror"
                           value="{{ old('linked_depot', $station->linked_depot) }}"
                           placeholder="e.g. Chittagong Depot">
                    @error('linked_depot') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ── SECTION 2: Owner ── --}}
            <div class="form-section-title">Owner Information</div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Owner Name</label>
                    <input type="text" name="owner_name"
                           class="form-control-cm @error('owner_name') is-invalid @enderror"
                           value="{{ old('owner_name', $station->owner_name) }}"
                           placeholder="Full name">
                    @error('owner_name') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Owner Phone</label>
                    <input type="text" name="owner_phone"
                           class="form-control-cm @error('owner_phone') is-invalid @enderror"
                           value="{{ old('owner_phone', $station->owner_phone) }}"
                           placeholder="+880 1X XXXX XXXX">
                    @error('owner_phone') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ── SECTION 3: Location ── --}}
            <div class="form-section-title">Location</div>
            <div class="form-grid form-grid-3">
                <div class="form-group">
                    <label class="form-label">Division <span class="req">*</span></label>
                    <select name="division" class="form-control-cm @error('division') is-invalid @enderror">
                        <option value="">— Select Division —</option>
                        @foreach(['Dhaka','Chittagong','Rajshahi','Khulna','Barishal','Sylhet','Rangpur','Mymensingh'] as $div)
                            <option value="{{ $div }}"
                                {{ old('division', $station->division) == $div ? 'selected' : '' }}>
                                {{ $div }}
                            </option>
                        @endforeach
                    </select>
                    @error('division') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">District</label>
                    <input type="text" name="district"
                           class="form-control-cm @error('district') is-invalid @enderror"
                           value="{{ old('district', $station->district) }}"
                           placeholder="e.g. Dhaka">
                    @error('district') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Upazila</label>
                    <input type="text" name="upazila"
                           class="form-control-cm @error('upazila') is-invalid @enderror"
                           value="{{ old('upazila', $station->upazila) }}"
                           placeholder="e.g. Savar">
                    @error('upazila') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-grid form-grid-1" style="margin-bottom: 28px;">
                <div class="form-group">
                    <label class="form-label">Full Address</label>
                    <textarea name="address"
                              class="form-control-cm @error('address') is-invalid @enderror"
                              placeholder="Enter full address...">{{ old('address', $station->address) }}</textarea>
                    @error('address') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ── SECTION 4: Technical ── --}}
            <div class="form-section-title">Technical Details</div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Tank Capacity (Litres)</label>
                    <input type="number" name="tank_capacity"
                           class="form-control-cm @error('tank_capacity') is-invalid @enderror"
                           value="{{ old('tank_capacity', $station->tank_capacity) }}"
                           placeholder="e.g. 10000">
                    @error('tank_capacity') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">License File</label>

                    {{-- Show existing file --}}
                    @if($station->license_file)
                    <div class="file-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                        </svg>
                        Current:
                        <a href="{{ asset('storage/'.$station->license_file) }}" target="_blank">
                            View File
                        </a>
                    </div>
                    @endif

                    <label class="file-upload-label" for="license_file" id="fileLabel">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span id="fileLabelText">
                            {{ $station->license_file ? 'Replace file (optional)' : 'Click to upload license file (PDF/Image)' }}
                        </span>
                    </label>
                    <input type="file" name="license_file" id="license_file" accept=".pdf,.jpg,.jpeg,.png">
                    @error('license_file') <span class="error-msg">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Fuel Types --}}
            <div class="form-group" style="margin-bottom: 28px;">
                <label class="form-label">Fuel Types</label>
                <div class="fuel-options">
                    @php
                        $existingFuels = old('fuel_types', json_decode($station->fuel_types ?? '[]', true) ?? []);
                    @endphp
                    @foreach(['Petrol','Diesel','Octane','CNG','LPG'] as $fuel)
                    <label class="fuel-check {{ in_array($fuel, $existingFuels) ? 'checked' : '' }}"
                           data-fuel="{{ $fuel }}">
                        <input type="checkbox" name="fuel_types[]"
                               value="{{ $fuel }}"
                               {{ in_array($fuel, $existingFuels) ? 'checked' : '' }}>
                        {{ $fuel }}
                    </label>
                    @endforeach
                </div>
                @error('fuel_types') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            {{-- Footer --}}
            <div class="form-footer">
                <a href="{{ route('stations.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Update Station</button>
            </div>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.fuel-check').forEach(label => {
        label.addEventListener('click', function () {
            const cb = this.querySelector('input[type=checkbox]');
            cb.checked = !cb.checked;
            this.classList.toggle('checked', cb.checked);
        });
    });

    document.getElementById('license_file').addEventListener('change', function () {
        const name = this.files[0] ? this.files[0].name : 'Replace file (optional)';
        document.getElementById('fileLabelText').textContent = name;
    });
</script>
@endsection