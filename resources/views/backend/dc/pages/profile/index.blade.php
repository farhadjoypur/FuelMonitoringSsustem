@extends('backend.dc.layouts.app')

@section('title', 'Profile')

@push('styles')
    <style>
        .profile-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            background-color: #fff;
        }

        .avatar-circle {
            width: 120px;
            height: 120px;
            background-color: #006699;
            color: white;
            font-size: 40px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 20px;
            text-transform: uppercase;
        }

        .edit-alert {
            background-color: #f0f7ff;
            border: 1px solid #d1e9ff;
            color: #006699;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-label {
            font-weight: 600;
            color: #4a5568;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .form-control-custom {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            border-radius: 10px;
        }

        .form-control-custom:focus {
            background-color: #fff;
            border-color: #006699;
            box-shadow: none;
        }

        .badge-active {
            background-color: #e6fffa;
            color: #38a169;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            text-transform: capitalize;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4">
        <div class="mb-4">
            <h4 class="fw-bold mb-0">My Profile</h4>
        </div>

        <div class="alert edit-alert mb-4 d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <span>You are in edit mode. Make changes and click "Save Changes" to update your profile.</span>
        </div>

        <form id="profileUpdateForm" action="{{ route('dc.profile.update', auth()->user()->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card profile-card h-100 p-5 text-center shadow-sm">

                        <div class="avatar-circle mb-3 mx-auto" style="position: relative; overflow: hidden;">
                            @php
                                $profilePhoto = auth()->user()->profile->photo ?? null;
                                $defaultPhoto = asset('backend/assets/images/default-avatar.png');
                            @endphp

                            @if ($profilePhoto && file_exists(public_path($profilePhoto)))
                                <img src="{{ asset($profilePhoto) }}" id="profilePreview" alt="Profile Photo"
                                    class="rounded-circle w-100 h-100" style="object-fit: cover;">
                            @else
                                <img src="{{ $defaultPhoto }}" id="profilePreview" alt="Default Profile Photo"
                                    class="rounded-circle w-100 h-100" style="object-fit: cover;">
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="photoInput" class="btn btn-sm btn-light shadow-sm"
                                style="border-radius: 20px; border: 1px solid #ddd; padding: 5px 15px; cursor: pointer;">
                                <i class="fas fa-camera me-1"></i> Change Photo
                            </label>
                            <input type="file" name="photo" id="photoInput" class="d-none" accept="image/*"
                                data-url="{{ route('dc.profile.update', auth()->user()->id) }}">

                            @error('photo')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <h4 class="fw-bold mb-1">{{ auth()->user()->profile->name ?? 'User Name' }}</h4>
                        <p class="text-dark mb-3" style="font-size: 13px; letter-spacing: 0.5px;">
                            Deputy Commissioner <br>
                            <span class="text-dark">
                                {{-- {{ auth()->user()->profile->upazila ?? 'Upazila' }}, --}}
                                {{ auth()->user()->profile->district ?? 'District' }}
                            </span>
                        </p>
                        <div>
                            <span class="badge-active">{{ auth()->user()->status ?? 'Active' }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 mb-4">
                    <div class="card profile-card p-4 shadow-sm">
                        <h5 class="fw-bold mb-4" style="color: #1a202c;">Personal Information</h5>

                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label"><i class="far fa-user me-2"></i> Full Name</label>
                                <input type="text" name="name"
                                    class="form-control form-control-custom @error('name') is-invalid @enderror"
                                    value="{{ old('name', auth()->user()->profile->name ?? '') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-phone-alt me-2"></i> Mobile Number</label>
                                <input type="text" name="phone"
                                    class="form-control form-control-custom @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="far fa-envelope me-2"></i> Email Address</label>
                                <input type="email" name="email"
                                    class="form-control form-control-custom @error('email') is-invalid @enderror"
                                    value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-lock me-2"></i> New Password</label>
                                <input type="password" name="password"
                                    class="form-control form-control-custom @error('password') is-invalid @enderror"
                                    placeholder="Enter new password">
                                <small class="text-muted mt-1 d-block">Leave blank to keep current password</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-shield-alt me-2"></i> Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-custom"
                                    placeholder="Confirm new password">
                            </div>

                            <div class="col-12 mt-4">
                                <div
                                    class="d-flex flex-column flex-sm-row justify-content-sm-end gap-2 gap-sm-3 border-top pt-4">
                                    <a href="{{ route('dc.dashboard.index') }}"
                                        class="btn btn-secondary px-sm-5 py-2 order-2 order-sm-1"
                                        style="border-radius: 10px; background-color: #64748b; border: none;">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary px-sm-5 py-2 order-1 order-sm-2"
                                        style="border-radius: 10px; background-color: #006699; border: none;">
                                        <i class="fas fa-save me-2"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $('#photoInput').on('change', function() {
            const file = this.files[0];
            if (file) {
                let preview = document.getElementById('profilePreview');
                if (preview) {
                    preview.src = URL.createObjectURL(file);
                }
                $('.avatar-circle').css('opacity', '0.5');

                let formData = new FormData();
                formData.append('photo', file);
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT');

                formData.append('name', '{{ auth()->user()->profile->name ?? '' }}');
                formData.append('phone', '{{ auth()->user()->phone ?? '' }}');
                formData.append('email', '{{ auth()->user()->email ?? '' }}');

                $.ajax({
                    url: "{{ route('dc.profile.update', auth()->user()->id) }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        $('.avatar-circle').css('opacity', '1');
                        alert('Error uploading photo: ' + (xhr.responseJSON.message ||
                            'Unknown error'));
                    }
                });
            }
        });
    </script>
@endpush
