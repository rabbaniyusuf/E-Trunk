@extends('layouts.main')

@section('title', 'Dashboard User - E-TRANK')

@push('styles')
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            padding: 2rem 1.5rem;
            margin-bottom: 2rem;
            overflow: hidden;
            position: relative;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 1;
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid #f1f5f9;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
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
            position: relative;
            overflow: hidden;
        }

        .stat-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            opacity: 0.1;
            transition: opacity 0.3s ease;
        }

        .stat-icon.balance {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #2563eb;
        }

        .stat-icon.recycle {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #16a34a;
        }

        .stat-icon.non-recycle {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #d97706;
        }

        .stat-icon.volume {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
            color: #7c3aed;
        }

        .progress-circle {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
        }

        .progress-circle svg {
            transform: rotate(-90deg);
            width: 100%;
            height: 100%;
        }

        .progress-circle .progress-bg {
            fill: none;
            stroke: #f1f5f9;
            stroke-width: 8;
        }

        .progress-circle .progress-bar {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
            transition: stroke-dasharray 1.5s cubic-bezier(0.4, 0, 0.2, 1);
            filter: drop-shadow(0 0 4px rgba(0, 0, 0, 0.1));
        }

        .progress-circle .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: 700;
            font-size: 0.9rem;
        }

        .progress-bar.recycle {
            stroke: #16a34a;
        }

        .progress-bar.non-recycle {
            stroke: #d97706;
        }

        .action-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .action-btn {
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0.5rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 160px;
            justify-content: center;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
            color: white;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-success-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning-color) 0%, #d97706 100%);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .btn-warning-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
            color: white;
        }
        .btn-danger-custom {
            background: linear-gradient(135deg, var(--danger-color) 0%, #d97706 100%);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .btn-danger-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
            color: white;
        }

        .recent-activity {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f8fafc;
            transition: background-color 0.3s ease;
        }

        .activity-item:hover {
            background-color: #f8fafc;
            border-radius: 0.5rem;
            margin: 0 -0.5rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
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

        .activity-icon.deposit {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .activity-icon.withdrawal {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .metric-change {
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .metric-change.positive {
            color: var(--success-color);
        }

        .metric-change.negative {
            color: var(--danger-color);
        }

        .metric-change.neutral {
            color: var(--secondary-color);
        }

        /* Additional styles for waste collection activities */
        /* Additional styles for waste collection activities */
        .activity-icon.primary {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .activity-icon.info {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .status-badge.status-processing {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .status-badge.status-scheduled {
            background-color: #f1f5f9;
            color: #475569;
        }

        /* Activity type indicators styling */
        .activity-type-indicator {
            position: absolute;
            top: -3px;
            right: -3px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid white;
            z-index: 1;
        }

        .activity-type-indicator.transaction {
            background-color: #10b981;
        }

        .activity-type-indicator.waste-collection {
            background-color: #3b82f6;
        }

        .activity-type-indicator.redemption {
            background-color: #f59e0b;
        }

        /* Button styling */
        .btn-xs {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.25;
            border-radius: 0.375rem;
        }

        /* Activity time info styling */
        .activity-time-info small {
            line-height: 1.4;
        }

        /* Loading spinner positioning */
        #activities-loading {
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        /* Collection details modal */
        .collection-details h6 {
            color: var(--primary-color);
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        /* Improved activity item responsive design */
        @media (max-width: 576px) {
            .activity-item .activity-details {
                margin-top: 0.5rem;
            }

            .activity-item .activity-meta {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }

            .activity-item .activity-points {
                margin-bottom: 0.25rem;
            }

            .activity-item .status-badge {
                align-self: flex-start;
            }
        }

        /* Activity type indicators */
        .activity-type-indicator {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .activity-type-indicator.transaction {
            background-color: #10b981;
        }

        .activity-type-indicator.waste-collection {
            background-color: #3b82f6;
        }

        .activity-type-indicator.redemption {
            background-color: #f59e0b;
        }

        /* Enhanced activity item hover effects */
        .activity-item:hover .activity-icon {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .activity-item:hover {
            background-color: #f8fafc;
            border-radius: 0.75rem;
            margin: 0 -1rem;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }

        /* Status-specific styling */
        .text-completed {
            color: #16a34a !important;
        }

        .text-in-progress {
            color: #2563eb !important;
        }

        .text-scheduled {
            color: #64748b !important;
        }

        .text-cancelled {
            color: #dc2626 !important;
        }

        @media (max-width: 768px) {
            .welcome-card {
                padding: 1.5rem 1rem;
                text-align: center;
            }

            .stat-card {
                margin-bottom: 1rem;
            }

            .action-btn {
                width: 100%;
                margin: 0.25rem 0;
            }

            .activity-item {
                flex-direction: column;
                text-align: center;
                padding: 1.5rem 0;
            }

            .activity-icon {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .bin-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .bin-status.low {
            color: var(--success-color);
        }

        .bin-status.medium {
            color: var(--warning-color);
        }

        .bin-status.high {
            color: #f59e0b;
        }

        .bin-status.full {
            color: var(--danger-color);
        }

        .bin-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .bin-status.low .bin-status-dot {
            background-color: var(--success-color);
        }

        .bin-status.medium .bin-status-dot {
            background-color: var(--warning-color);
        }

        .bin-status.high .bin-status-dot {
            background-color: #f59e0b;
        }

        .bin-status.full .bin-status-dot {
            background-color: var(--danger-color);
        }

        .activity-icon.success {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .activity-icon.danger {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .activity-icon.warning {
            background-color: #fef3c7;
            color: #d97706;
        }

        .activity-icon.secondary {
            background-color: #f8fafc;
            color: #64748b;
        }

        .status-badge.status-menunggu-diambil,
        .status-badge.status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-badge.status-sudah-diambil,
        .status-badge.status-approved,
        .status-badge.status-completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-badge.status-gagal-diambil,
        .status-badge.status-rejected,
        .status-badge.status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-badge.status-processing {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .activity-item .activity-icon {
            transition: all 0.3s ease;
        }

        .activity-item:hover .activity-icon {
            transform: scale(1.1);
        }

        .text-success {
            color: #16a34a !important;
        }

        .text-danger {
            color: #dc2626 !important;
        }

        .text-warning {
            color: #d97706 !important;
        }

        .text-secondary {
            color: #64748b !important;
        }

        /* Mobile responsiveness improvements */
        @media (max-width: 576px) {
            .activity-item {
                padding: 1rem 0.5rem;
            }

            .activity-item .d-flex {
                flex-direction: column;
                gap: 0.5rem;
            }

            .activity-item .text-end {
                text-align: left !important;
            }

            .status-badge {
                align-self: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="welcome-content">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">Selamat datang, {{ Auth::user()->name }}! 👋</h2>
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

    <!-- Statistics Cards - Updated Section Only -->
    <div class="row g-4 mb-4">
        <!-- Balance Card -->
        <div class="col-md-4 col-lg-4">
            <div class="card stat-card">
                <div class="stat-icon balance">
                    <i class="bi bi-wallet2"></i>
                </div>
                <h6 class="text-muted mb-2">Poin Tersedia</h6>
                <h3 class="text-primary mb-1">{{ number_format($availablePoints) }}</h3>
                <div class="metric-change {{ $monthlyGrowth >= 0 ? 'positive' : 'negative' }}">
                    <i class="bi {{ $monthlyGrowth >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                    {{ abs($monthlyGrowth) }}% dari bulan lalu
                </div>
            </div>
        </div>

        <!-- Recycle Volume -->
        <div class="col-md-4 col-lg-4">
            <div class="card stat-card">
                <div class="progress-circle">
                    <svg viewBox="0 0 80 80">
                        <circle class="progress-bg" cx="40" cy="40" r="36"></circle>
                        <circle class="progress-bar recycle" cx="40" cy="40" r="36"
                            stroke-dasharray="0 226.19" data-percentage="{{ $recyclePercentage }}">
                        </circle>
                    </svg>
                    <div class="progress-text text-success">{{ number_format($recyclePercentage, 1) }}%</div>
                </div>
                <h6 class="text-muted mb-2">Sampah Recycle</h6>
                <div
                    class="bin-status {{ $recyclePercentage >= 80 ? 'full' : ($recyclePercentage >= 60 ? 'high' : ($recyclePercentage >= 30 ? 'medium' : 'low')) }}">
                    <div class="bin-status-dot"></div>
                    {{ $recyclePercentage >= 80 ? 'PENUH' : ($recyclePercentage >= 60 ? 'TINGGI' : ($recyclePercentage >= 30 ? 'SEDANG' : 'RENDAH')) }}
                </div>
            </div>
        </div>

        <!-- Non-Recycle Volume -->
        <div class="col-md-4 col-lg-4">
            <div class="card stat-card">
                <div class="progress-circle">
                    <svg viewBox="0 0 80 80">
                        <circle class="progress-bg" cx="40" cy="40" r="36"></circle>
                        <circle class="progress-bar non-recycle" cx="40" cy="40" r="36"
                            stroke-dasharray="0 226.19" data-percentage="{{ $nonRecyclePercentage }}">
                        </circle>
                    </svg>
                    <div class="progress-text text-warning">{{ number_format($nonRecyclePercentage, 1) }}%</div>
                </div>
                <h6 class="text-muted mb-2">Sampah Non-Recycle</h6>
                <div
                    class="bin-status {{ $nonRecyclePercentage >= 80 ? 'full' : ($nonRecyclePercentage >= 60 ? 'high' : ($nonRecyclePercentage >= 30 ? 'medium' : 'low')) }}">
                    <div class="bin-status-dot"></div>
                    {{ $nonRecyclePercentage >= 80 ? 'PENUH' : ($nonRecyclePercentage >= 60 ? 'TINGGI' : ($nonRecyclePercentage >= 30 ? 'SEDANG' : 'RENDAH')) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="action-card">
                <h5 class="mb-3">Aksi Cepat</h5>
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
                    <a href="{{ route('user.tukar-poin.daftar') }}" class="btn btn-danger-custom action-btn">
                        <i class="bi bi-coin"></i> Daftar Penukaran Saldo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="recent-activity">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                    <div class="d-flex gap-2">

                        <a href="{{ route('user.riwayat-transaksi') }}" class="btn btn-outline-primary btn-sm">
                            Lihat Semua
                        </a>
                    </div>
                </div>

                <div id="activities-container">
                    @if (isset($recentActivities) && $recentActivities->count() > 0)
                        @foreach ($recentActivities as $activity)
                            <div class="activity-item">
                                <div class="activity-icon {{ $activity['icon_class'] }} position-relative">
                                    <i class="bi {{ $activity['icon'] }}"></i>
                                    {{-- Activity type indicator --}}
                                    <div
                                        class="activity-type-indicator {{ $activity['type'] == 'transaction' ? 'transaction' : ($activity['type'] == 'waste_collection' ? 'waste-collection' : 'redemption') }}">
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start activity-meta">
                                        <div class="activity-details">
                                            <h6 class="mb-1">
                                                {{ $activity['title'] }}
                                                @if ($activity['subtitle'])
                                                    <span class="text-muted">- {{ $activity['subtitle'] }}</span>
                                                @endif
                                            </h6>

                                            <div class="activity-time-info">
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $activity['created_at']->format('d M Y, H:i') }}

                                                    {{-- Additional info for waste collection --}}
                                                    @if ($activity['type'] == 'waste_collection' && $activity['pickup_info'])
                                                        <br><i
                                                            class="bi bi-calendar-event me-1"></i>{{ $activity['pickup_info'] }}
                                                    @endif

                                                    {{-- Additional info for transactions --}}
                                                    @if (
                                                        $activity['type'] == 'transaction' &&
                                                            isset($activity['data']->percentage_deposited) &&
                                                            $activity['data']->percentage_deposited &&
                                                            $activity['data']->transaction_type == 'deposit')
                                                        <br><i
                                                            class="bi bi-archive me-1"></i>{{ number_format($activity['data']->percentage_deposited, 1) }}%
                                                        volume
                                                    @endif

                                                    {{-- Additional info for redemptions --}}
                                                    @if ($activity['type'] == 'redemption' && $activity['cash_value'])
                                                        <br><i class="bi bi-cash me-1"></i>{{ $activity['cash_value'] }}
                                                    @endif
                                                </small>
                                            </div>

                                            {{-- Assigned petugas for waste collection --}}
                                            @if ($activity['type'] == 'waste_collection' && $activity['assigned_petugas'])
                                                <small class="text-success d-block mt-1">
                                                    <i class="bi bi-person-check me-1"></i>Petugas:
                                                    {{ $activity['assigned_petugas'] }}
                                                </small>
                                            @endif

                                            {{-- Description --}}
                                            @if ($activity['description'])
                                                <div class="activity-description mt-2">
                                                    <small
                                                        class="text-muted">{{ Str::limit($activity['description'], 100) }}</small>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="text-end activity-summary">
                                            {{-- Points display --}}
                                            @if ($activity['points'])
                                                <div
                                                    class="fw-semibold text-{{ $activity['icon_class'] }} activity-points mb-1">
                                                    {{ $activity['points'] }} poin
                                                </div>
                                            @endif

                                            {{-- Status badge --}}
                                            <span class="status-badge {{ $activity['status_class'] }}">
                                                {{ $activity['status'] }}
                                            </span>

                                            {{-- Additional action button for waste collection --}}
                                            @if (
                                                $activity['type'] == 'waste_collection' &&
                                                    in_array($activity['data']->status, [
                                                        App\Models\WasteCollection::STATUS_PENDING,
                                                        App\Models\WasteCollection::STATUS_SCHEDULED,
                                                    ]))
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="bi bi-clock-history"></i>
                            <h6>Belum ada aktivitas</h6>
                            <p class="text-muted mb-3">Mulai nabung sampah untuk melihat aktivitas Anda.</p>
                        </div>
                    @endif
                </div>

                {{-- Loading spinner --}}
                <div id="activities-loading" class="text-center py-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="ms-2 text-muted">Memuat aktivitas...</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Collection Details Modal --}}
    <div class="modal fade" id="collectionDetailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pengambilan Sampah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="collectionDetailsContent">
                    {{-- Content will be loaded via AJAX --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate progress circles
            const progressBars = document.querySelectorAll('.progress-bar');

            progressBars.forEach(bar => {
                const percentage = parseFloat(bar.dataset.percentage) || 0;
                const circumference = 2 * Math.PI * 36; // radius = 36
                const strokeDasharray = (percentage / 100) * circumference;

                // Start with 0
                bar.style.strokeDasharray = `0 ${circumference}`;

                // Animate to actual value
                setTimeout(() => {
                    bar.style.strokeDasharray = `${strokeDasharray} ${circumference}`;
                }, 500);
            });

            // Auto-dismiss alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (bootstrap.Alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });

            // Add hover effect to stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Refresh data every 30 seconds for real-time updates
            setInterval(() => {
                // You can implement AJAX call here to refresh sensor data
                console.log('Refreshing sensor data...');
            }, 30000);
        });
    </script>
@endpush
