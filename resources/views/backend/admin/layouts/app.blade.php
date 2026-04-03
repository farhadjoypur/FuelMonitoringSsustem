<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FMS @hasSection('title')
            - @yield('title')
        @endif
    </title>

    <link rel="icon" href="{{ asset('backend/assets/images/logo.png') }}" type="image/x-icon">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/layout.css') }}">

    @stack('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <script>
        (function() {
            const sidebarState = localStorage.getItem("sidebar-state");
            if (sidebarState === "collapsed") {
                document.documentElement.classList.add('sidebar-is-collapsed');
            }
        })();
    </script>

    @include('backend.admin.layouts.navbar')
    <div class="main-wrapper">
        <nav class="site-nav">
            <button class="sidebar-toggle">
                <span class="material-symbols-rounded">menu</span>
            </button>
        </nav>
        <div class="container-custom">
            @include('backend.admin.layouts.sidebar')
            <div class="main-content">
                <div class="card-custom">
                    @yield('content')
                </div>
                @include('backend.admin.layouts.footer')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "toastClass": 'toast toast-full-opacity'
            };
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if (session('info'))
                toastr.info("{{ session('info') }}");
            @endif

            @if (session('warning'))
                toastr.warning("{{ session('warning') }}");
            @endif
        });
    </script>
    <script src="{{ asset('backend/assets/js/layout.js') }}"></script>
    @stack('scripts')
</body>

</html>
