@extends('backend.admin.layouts.app')

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

        <form id="profileUpdateForm" action="{{ route('admin.profile.update', auth()->user()->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card profile-card h-100 p-5 text-center shadow-sm">
                        <div class="avatar-circle">
                            @php
                                $nameParts = explode(' ', auth()->user()->profile->name ?? 'User');
                                $initials =
                                    count($nameParts) > 1
                                        ? substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1)
                                        : substr($nameParts[0], 0, 2);
                            @endphp
                            {{ strtoupper($initials) }}
                        </div>
                        <h4 class="fw-bold mb-1">{{ auth()->user()->profile->name ?? '' }}</h4>
                        <p class="text-muted mb-3 text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                            {{ \App\Enums\UserRole::list()[auth()->user()->role] ?? 'Unknown Role' }}
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
                                    <a href="{{ route('admin.dashboard.index') }}"
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
