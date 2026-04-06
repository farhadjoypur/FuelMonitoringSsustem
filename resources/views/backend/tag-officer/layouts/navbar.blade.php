<nav class="navbar navbar-expand-lg navbar-top" style="background-color: #006699;">
    <div class="container-fluid px-5 d-flex justify-content-between align-items-center">

        <div class="d-flex align-items-center">
            <img src="{{ asset('backend/assets/images/logo.png') }}" alt="loog" style="width: 40px">
            <h2 class="navbar-brand text-white fw-bold mb-0 mx-2" style="font-size: 24px; padding: 10px 0">Tag Officer
            </h2>
            <p class="text-stat text-white mb-0 d-none d-md-block" style=" padding-left: 32px;">
                Bangladesh Government - Fuel Distribution Monitoring System
            </p>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav ms-auto align-items-center gap-4">

                <div class="nav-item d-flex align-items-center gap-3">
                    <div class="text-end me-2">
                        <h6 class="text-white mb-0 fw-bold" style="font-size: 14px; line-height: 1.2;">
                            {{ Auth::user()->profile->name ?? 'User Name' }}
                        </h6>
                        <small class="text-white" style="font-size: 10px; display: block; margin-top: 2px;">
                            Live Stock Officer,
                            {{ Auth::user()->profile->upazila ?? 'Upazila' }},
                            {{ Auth::user()->profile->district ?? 'District' }}
                        </small>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                        style="width: 42px; height: 42px; background-color: #005580; color: white; border: 2px solid rgba(255,255,255,0.2); font-size: 14px; overflow: hidden;">

                        @php
                            $profilePhoto = Auth::user()->profile->photo ?? null;
                            $name = Auth::user()->profile->name ?? 'User Name';
                        @endphp

                        @if ($profilePhoto && file_exists(public_path($profilePhoto)))
                            <img src="{{ asset($profilePhoto) }}" alt="Profile"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            @php
                                $words = explode(' ', $name);
                                $initials =
                                    count($words) >= 2
                                        ? strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1))
                                        : strtoupper(substr($name, 0, 2));
                            @endphp
                            {{ $initials }}
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</nav>
