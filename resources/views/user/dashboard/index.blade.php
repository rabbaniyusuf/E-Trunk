@extends('layouts.main')

@section('title', 'Dashboard User - E-TRANK')

@push('styles')
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
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

        .progress-circle {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
        }

        .progress-circle svg {
            transform: rotate(-90deg);
        }

        .progress-circle .progress-bg {
            fill: none;
            stroke: #e5e7eb;
            stroke-width: 8;
        }

        .progress-circle .progress-bar {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
            transition: stroke-dasharray 0.6s ease;
        }

        .progress-circle .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .action-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.25rem;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
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
            transform: translateY(-2px);
        }

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
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2">Selamat datang, {{ Auth::user()->name ?? 'Pengguna' }}! 👋</h2>
                        <p class="mb-0 opacity-90">
                            Mari bersama-sama menjaga lingkungan dengan mengelola sampah secara bijak.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="h4 mb-0">{{ now()->format('d M Y') }}</div>
                        <small class="opacity-90">{{ now()->format('l') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Saldo Card -->
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="stat-icon balance">
                    <i class="bi bi-wallet2"></i>
                </div>
                <h6 class="text-muted mb-2">Saldo Bank Sampah</h6>
                <h3 class="text-primary mb-0">{{ number_format($balance ?? 0) }} Poin</h3>
                <small class="text-success">
                    <i class="bi bi-arrow-up"></i> +15 dari bulan lalu
                </small>
            </div>
        </div>

        <!-- Volume Recycle -->
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="progress-circle">
                    <svg width="80" height="80" viewBox="0 0 80 80">
                        <circle class="progress-bg" cx="40" cy="40" r="36"></circle>
                        <circle class="progress-bar" cx="40" cy="40" r="36" stroke="#16a34a"
                            stroke-dasharray="{{ (60 / 100) * 226.19 }} 226.19">
                        </circle>
                    </svg>
                    <div class="progress-text text-success">0%</div>
                </div>
                <h6 class="text-muted mb-2">Sampah Recycle</h6>
                <div class="text-success fw-semibold">{{ number_format($recycleVolume ?? 0) }} kg</div>
            </div>
        </div>

        <!-- Volume Non-Recycle -->
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="progress-circle">
                    <svg width="80" height="80" viewBox="0 0 80 80">
                        <circle class="progress-bg" cx="40" cy="40" r="36"></circle>
                        <circle class="progress-bar" cx="40" cy="40" r="36" stroke="#d97706"
                            stroke-dasharray="{{ (40 / 100) * 226.19 }} 226.19">
                        </circle>
                    </svg>
                    <div class="progress-text text-warning">0%</div>
                </div>
                <h6 class="text-muted mb-2">Sampah Non-Recycle</h6>
                <div class="text-warning fw-semibold">{{ number_format($nonRecycleVolume ?? 0) }} kg</div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">Aksi Cepat</h5>
                    <div class="d-flex flex-wrap justify-content-center">
                        <a href="{{ route('user.nabung') }}" class="btn btn-primary-custom action-btn">
                            <i class="bi bi-plus-circle"></i> Nabung Sampah
                        </a>
                        <a href="{{ route('user.tukar-poin') }}" class="btn btn-success-custom action-btn">
                            <i class="bi bi-gift"></i> Tukar Poin
                        </a>
                        <a href="{{ route('user.riwayat-transaksi') }}" class="btn btn-warning-custom action-btn">
                            <i class="bi bi-clock-history"></i> Riwayat Transaksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Add some interactivity for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Animate progress circles
            const progressCircles = document.querySelectorAll('.progress-circle');
            progressCircles.forEach(circle => {
                const progressBar = circle.querySelector('.progress-bar');
                const currentStroke = progressBar.getAttribute('stroke-dasharray');
                progressBar.style.strokeDasharray = '0 226.19';

                setTimeout(() => {
                    progressBar.style.strokeDasharray = currentStroke;
                }, 500);
            });

            // Auto-dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
@endpush
