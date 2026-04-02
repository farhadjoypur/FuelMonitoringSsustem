<nav class="navbar navbar-expand-lg navbar-top">
    <div class="container">
        <a href="{{ route('admin.dashboard.index') }}" class="navbar-brand">
            <img src="{{ asset('backend/assets/images/logo.png') }}" alt="logo" class="navbar-logo">
            <span class="fw-bold text-white"></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
            aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav ms-auto gap-3">

                {{-- settings --}}
                <a class="top-navbar-icon-btn text-decoration-none text-reset" href="#">
                    <span class="material-symbols-rounded">settings</span>
                </a>
            </div>
        </div>
    </div>
</nav>
