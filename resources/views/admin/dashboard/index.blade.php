@extends('layouts.main')
@section('title', 'Dashboard - E-TRANK')

@push('styles')
    <style>
        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;a
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .welcome-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            transform: translate(-20px, 20px);
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        /* Statistics Cards */
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .stat-icon.balance {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .stat-icon.recycle {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .stat-icon.non-recycle {
            background-color: #fef3c7;
            color: #d97706;
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Action Buttons */
        .action-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.25rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 160px;
            justify-content: center;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: white;
            transform: translateY(-2px);
        }

        .btn-success-custom {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .btn-success-custom:hover {
            background-color: #059669;
            border-color: #059669;
            color: white;
            transform: translateY(-2px);
        }

        .btn-danger-custom {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }

        .btn-danger-custom:hover {
            background-color: #dc2626;
            border-color: #dc2626;
            color: white;
            transform: translateY(-2px);
        }

        .btn-warning-custom {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: white;
        }

        .btn-warning-custom:hover {
            background-color: #d97706;
            border-color: #d97706;
            color: white;
            transform: translateY(-2px);
        }

        /* Recent Activity */
        .recent-activity {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        .activity-time {
            color: var(--secondary-color);
            font-size: 0.8rem;
        }

        /* Quick Stats Grid */
        .quick-stats {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .quick-stat-item {
            text-align: center;
            padding: 1rem;
        }

        .quick-stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .quick-stat-label {
            font-size: 0.85rem;
            color: var(--secondary-color);
        }

        /* Mobile Responsive Styles */
        @media (max-width: 575.98px) {
            .welcome-card {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .welcome-card h2 {
                font-size: 1.5rem;
                margin-bottom: 0.75rem;
            }

            .welcome-card p {
                font-size: 0.9rem;
            }

            .stat-card {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
                margin-bottom: 0.75rem;
            }

            .stat-number {
                font-size: 1.5rem;
            }

            .stat-label {
                font-size: 0.8rem;
            }

            .action-btn {
                padding: 0.6rem 1rem;
                margin: 0.2rem;
                min-width: auto;
                width: 100%;
                font-size: 0.85rem;
            }

            .recent-activity {
                padding: 1rem;
            }

            .activity-icon {
                width: 35px;
                height: 35px;
                margin-right: 0.75rem;
            }

            .activity-title {
                font-size: 0.85rem;
            }

            .activity-time {
                font-size: 0.75rem;
            }

            .quick-stats {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .quick-stat-item {
                padding: 0.75rem;
            }

            .quick-stat-number {
                font-size: 1.25rem;
            }

            .quick-stat-label {
                font-size: 0.8rem;
            }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {
            .action-btn {
                min-width: 140px;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .action-btn {
                min-width: 150px;
            }
        }

        /* Animation for cards */
        .card-animate {
            animation: slideInUp 0.5s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading states */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-3">
        <!-- Welcome Section -->
        <div class="row">
            <div class="col-12">
                <div class="welcome-card card-animate">
                    <div class="welcome-content">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">Selamat datang, {{ Auth::user()->name ?? 'Pengguna' }}! 👋</h2>
                                <p class="mb-0 opacity-90">
                                    Mari bersama-sama menjaga lingkungan dengan mengelola sampah secara bijak.
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="h4 mb-0">{{ now()->format('d M Y') }}</div>
                                <small class="opacity-90">{{ now()->format('l') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-animate">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-lightning-charge me-2"></i>Aksi Cepat
                        </h5>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <a href="{{ route('admin.users.index') }}" class="action-btn btn-primary-custom">
                                    <i class="bi bi-person-plus"></i>
                                    <span>Buat Akun</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.approvals.index') }}" class="action-btn btn-danger-custom">
                                    <i class="bi bi-coin"></i>
                                    <span>Approval Penukaran Poin</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.approvals.redemptions') }}" class="action-btn btn-success-custom">
                                    <i class="bi bi-calendar-plus"></i>
                                    <span>Penukaran Saldo</span>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.reports.index') }}" class="action-btn btn-warning-custom">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <span>Cetak Report</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        {{-- <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="recent-activity card-animate">
                    <h5 class="mb-3">
                        <i class="bi bi-clock-history me-2"></i>Aktivitas Terbaru
                    </h5>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon bg-success text-white">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">User baru mendaftar</div>
                                <div class="activity-time">2 menit yang lalu</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-primary text-white">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Transaksi sampah berhasil</div>
                                <div class="activity-time">5 menit yang lalu</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-warning text-white">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Jadwal petugas diperbarui</div>
                                <div class="activity-time">10 menit yang lalu</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-info text-white">
                                <i class="bi bi-file-text"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Report bulanan dibuat</div>
                                <div class="activity-time">1 jam yang lalu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card card-animate">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Informasi Sistem
                        </h5>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Kapasitas Server</span>
                                <span class="small text-muted">75%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 75%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Database Usage</span>
                                <span class="small text-muted">60%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: 60%"></div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                Sistem berjalan normal
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all animated cards
            document.querySelectorAll('.card-animate').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease-out';
                observer.observe(card);
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Add loading state to action buttons
            document.querySelectorAll('.action-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Don't prevent default, just add loading state
                    this.style.opacity = '0.7';
                    this.style.pointerEvents = 'none';

                    // Remove loading state after 2 seconds (in case navigation fails)
                    setTimeout(() => {
                        this.style.opacity = '1';
                        this.style.pointerEvents = 'auto';
                    }, 2000);
                });
            });

            // Mobile touch improvements
            if ('ontouchstart' in window) {
                document.querySelectorAll('.stat-card, .action-btn').forEach(element => {
                    element.addEventListener('touchstart', function() {
                        this.style.transform = 'scale(0.98)';
                    });

                    element.addEventListener('touchend', function() {
                        this.style.transform = '';
                    });
                });
            }

            // Refresh data every 30 seconds (for real-time updates)
            setInterval(function() {
                // You can add AJAX calls here to update statistics
                console.log('Refreshing dashboard data...');
            }, 30000);
        });
    </script>
@endpush
