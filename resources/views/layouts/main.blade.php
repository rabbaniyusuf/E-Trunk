<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-TRANK - Bank Sampah Digital')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

    @stack('styles')
</head>

<body>
    <!-- Fixed Navigation with Mobile-First Design -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center"
                href="{{ auth()->user()->hasRole('petugas_pusat') ? route('admin.dashboard') : (auth()->user()->hasRole('petugas_kebersihan') ? route('petugas.dashboard') : route('user.dashboard')) }}">
                <i class="bi bi-recycle me-2 fs-4"></i>
                E-<strong>TRANK</strong>
            </a>

            <!-- Mobile Controls -->
            <div class="d-flex align-items-center order-lg-3">
                @auth
                <div class="d-lg-none">
                    {{-- Mobile Notification Button --}}
                    @role('petugas_pusat|petugas_kebersihan')
                        <a href="{{ route('admin.notifications.index') }}"
                            class="btn btn-outline-secondary btn-sm me-2 position-relative">
                            <i class="bi bi-bell"></i>
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                                    {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                    @endrole
                </div>

                    {{-- Mobile User Menu Button --}}
                    <div class="dropdown d-lg-none">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span class="d-none d-sm-inline">{{ Str::limit(Auth::user()->name, 10) }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="bi bi-shield-check"></i>
                                    @if (Auth::user()->hasRole('petugas_pusat'))
                                        Petugas Pusat
                                    @elseif(Auth::user()->hasRole('petugas_kebersihan'))
                                        Petugas Kebersihan
                                    @else
                                        Masyarakat
                                    @endif
                                </h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>

                            {{-- Role-based menu items --}}
                            @role('masyarakat')
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-bank"></i> Info Rekening
                                    </a>
                                </li>
                            @endrole

                            @role('petugas_pusat')
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-archive"></i> Backup Data
                                    </a>
                                </li>
                            @endrole

                            @role('petugas_kebersihan')
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-calendar-check"></i> Jadwal Saya
                                    </a>
                                </li>
                            @endrole

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="d-flex gap-1">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span class="d-none d-sm-inline ms-1">Masuk</span>
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-person-plus"></i>
                            <span class="d-none d-sm-inline ms-1">Daftar</span>
                        </a>
                    </div>
                @endauth

                <!-- Hamburger Menu Button -->
                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Collapsible Navigation -->
            <div class="collapse navbar-collapse order-lg-2" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Navigation items can be added here -->
                </ul>

                <!-- Desktop User Menu -->
                @auth
                    <div class="d-none d-lg-flex align-items-center">
                        {{-- Desktop Notification --}}
                        @role('petugas_pusat|petugas_kebersihan')
                            <div class="me-3">
                                <a href="{{ route('admin.notifications.index') }}"
                                    class="btn btn-outline-secondary position-relative">
                                    <i class="bi bi-bell"></i>
                                    @if (auth()->user()->unreadNotifications->count() > 0)
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        @endrole

                        {{-- Desktop User Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ Auth::user()->name }}
                                <small class="text-muted ms-1">
                                    @if (Auth::user()->hasRole('petugas_pusat'))
                                        (Petugas Pusat)
                                    @elseif(Auth::user()->hasRole('petugas_kebersihan'))
                                        (Petugas Kebersihan)
                                    @else
                                        (Masyarakat)
                                    @endif
                                </small>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <h6 class="dropdown-header">
                                        <i class="bi bi-shield-check"></i>
                                        @if (Auth::user()->hasRole('petugas_pusat'))
                                            Petugas Pusat
                                        @elseif(Auth::user()->hasRole('petugas_kebersihan'))
                                            Petugas Kebersihan
                                        @else
                                            Masyarakat
                                        @endif
                                    </h6>
                                </li>
                                <li><hr class="dropdown-divider"></li>

                                {{-- Role-based menu items --}}
                                @role('masyarakat')
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bi bi-bank"></i> Info Rekening
                                        </a>
                                    </li>
                                @endrole

                                @role('petugas_pusat')
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bi bi-archive"></i> Backup Data
                                        </a>
                                    </li>
                                @endrole

                                @role('petugas_kebersihan')
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bi bi-calendar-check"></i> Jadwal Saya
                                        </a>
                                    </li>
                                @endrole

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content with proper spacing for fixed navbar -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="text-primary mb-1">E-TRANK</h5>
                    <p class="text-muted small mb-0">Platform digital untuk mengelola bank sampah dan meningkatkan kesadaran lingkungan.</p>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <p class="text-muted small mb-0">
                        &copy; {{ date('Y') }} E-TRANK. Semua hak dilindungi.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS for mobile interactions -->
    <script>
        // Auto-close mobile navbar when clicking outside
        document.addEventListener('click', function(event) {
            const navbar = document.querySelector('.navbar-collapse');
            const toggleButton = document.querySelector('.navbar-toggler');

            if (!navbar.contains(event.target) && !toggleButton.contains(event.target)) {
                const bsCollapse = new bootstrap.Collapse(navbar, {
                    toggle: false
                });
                bsCollapse.hide();
            }
        });

        // Close navbar when clicking on a link (mobile)
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbar = document.querySelector('.navbar-collapse');
                const bsCollapse = new bootstrap.Collapse(navbar, {
                    toggle: false
                });
                bsCollapse.hide();
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
