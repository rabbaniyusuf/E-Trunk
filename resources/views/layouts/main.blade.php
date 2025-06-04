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
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand"
                href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : (auth()->user()->hasRole('petugas_kebersihan') ? route('petugas.dashboard') : route('user.dashboard')) }}">
                E-<strong>TRANK</strong>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    
                </ul>

                <div class="d-flex align-items-center">
                    @auth
                        {{-- Notifikasi (khusus untuk user dan petugas kebersihan) --}}
                        @role('user|petugas_kebersihan')
                            <div class="me-3">
                                <a href="{{ route('notifikasi.index') }}"
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

                        {{-- User Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                {{ Auth::user()->name }}
                                <small class="text-muted">
                                    @if (Auth::user()->hasRole('admin'))
                                        (Petugas Pusat)
                                    @elseif(Auth::user()->hasRole('petugas_kebersihan'))
                                        (Petugas Kebersihan)
                                    @else
                                        (Masyarakat)
                                    @endif
                                </small>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <h6 class="dropdown-header">
                                        <i class="bi bi-shield-check"></i>
                                        @if (Auth::user()->hasRole('admin'))
                                            Petugas Pusat
                                        @elseif(Auth::user()->hasRole('petugas_kebersihan'))
                                            Petugas Kebersihan
                                        @else
                                            Masyarakat
                                        @endif
                                    </h6>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                {{-- <li>
                                    <a class="dropdown-item" href="{{ route('profil.show') }}">
                                        <i class="bi bi-person"></i> Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profil.edit') }}">
                                        <i class="bi bi-gear"></i> Edit Profil
                                    </a>
                                </li> --}}

                                {{-- Menu khusus berdasarkan role --}}
                                @role('user')
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('user.rekening.index') }}">
                                            <i class="bi bi-bank"></i> Info Rekening
                                        </a>
                                    </li>
                                @endrole

                                @role('admin')
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.backup.index') }}">
                                            <i class="bi bi-archive"></i> Backup Data
                                        </a>
                                    </li>
                                @endrole

                                @role('petugas_kebersihan')
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('petugas.jadwal.saya') }}">
                                            <i class="bi bi-calendar-check"></i> Jadwal Saya
                                        </a>
                                    </li>
                                @endrole

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
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
                        <div class="d-flex gap-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Daftar Sebagai Masyarakat
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary">E-TRANK</h5>
                    <p class="text-muted">Platform digital untuk mengelola bank sampah dan meningkatkan kesadaran
                        lingkungan.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        &copy; {{ date('Y') }} E-TRANK. Semua hak dilindungi.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
