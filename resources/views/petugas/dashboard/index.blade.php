@extends('layouts.main')

@section('title', 'Dashboard Petugas Kebersihan - E-TRANK')

@section('content')
    <div class="container-fluid px-3 py-4">
        <!-- Modern Header Section -->
        <div class="dashboard-header mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7 col-12">
                    <div class="header-content">
                        <h1 class="dashboard-title mb-2">Dashboard Petugas Kebersihan</h1>
                        <p class="dashboard-subtitle mb-0">
                            <i class="bi bi-calendar3 me-2"></i>
                            {{ now()->isoFormat('dddd, D MMMM Y') }}
                        </p>
                        <div class="greeting-text mt-2">
                            Selamat
                            {{ now()->format('H') < 12 ? 'pagi' : (now()->format('H') < 15 ? 'siang' : (now()->format('H') < 18 ? 'sore' : 'malam')) }},
                            {{ auth()->user()->name }}!
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 col-12 text-md-end">
                    <div class="header-actions d-flex gap-2 justify-content-md-end justify-content-start mt-3 mt-md-0">
                        <button class="btn btn-outline-light" onclick="refreshData()" data-bs-toggle="tooltip"
                            title="Refresh Data">
                            <i class="bi bi-arrow-clockwise"></i>
                            <span class="d-none d-sm-inline ms-1">Refresh</span>
                        </button>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="bi bi-funnel"></i>
                            <span class="d-none d-sm-inline ms-1">Filter</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Statistics Cards -->
        <div class="stats-grid mb-4">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="bi bi-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['today_total'] }}</div>
                    <div class="stat-label">Jadwal Hari Ini</div>
                </div>
                <div class="stat-trend">
                    <i class="bi bi-graph-up text-success"></i>
                </div>
            </div>

            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['today_completed'] }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
                <div class="stat-progress">
                    <div class="progress">
                        <div class="progress-bar bg-success"
                            style="width: {{ $stats['today_total'] > 0 ? ($stats['today_completed'] / $stats['today_total']) * 100 : 0 }}%">
                        </div>
                    </div>
                    <small
                        class="text-muted">{{ $stats['today_total'] > 0 ? round(($stats['today_completed'] / $stats['today_total']) * 100) : 0 }}%</small>
                </div>
            </div>

            <div class="stat-card stat-card-warning">
                <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['today_pending'] }}</div>
                    <div class="stat-label">Menunggu</div>
                </div>
                <div class="stat-badge">
                    @if ($stats['today_pending'] > 0)
                        <span class="badge bg-warning text-dark">Perlu Perhatian</span>
                    @else
                        <span class="badge bg-success">Semua Selesai</span>
                    @endif
                </div>
            </div>

            <div class="stat-card stat-card-danger">
                <div class="stat-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['high_priority'] }}</div>
                    <div class="stat-label">Prioritas Tinggi</div>
                </div>
                <div class="stat-pulse">
                    @if ($stats['high_priority'] > 0)
                        <div class="pulse-dot"></div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modern Today's Schedule -->
        <div class="content-card mb-4">
            <div class="card-header-modern">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="card-title-group">
                        <h5 class="card-title-modern mb-1">
                            <i class="bi bi-calendar-day text-primary me-2"></i>
                            Jadwal Hari Ini
                        </h5>
                        <p class="card-subtitle-modern mb-0">Kelola jadwal pengambilan sampah hari ini</p>
                    </div>
                    <div class="card-actions">
                        <div class="dropdown">
                            <button class="btn btn-ghost dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" onclick="markAllCompleted()">
                                        <i class="bi bi-check-all me-2"></i>Tandai Semua Selesai
                                    </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportSchedule()">
                                        <i class="bi bi-download me-2"></i>Export Data
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="refreshData()">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body-modern">
                @if ($todaySchedules->count() > 0)
                    <div class="schedule-list">
                        @foreach ($todaySchedules as $index => $schedule)
                            <div class="schedule-item {{ $schedule->status === 'completed' ? 'completed' : '' }}"
                                data-schedule-id="{{ $schedule->id }}">
                                <div class="schedule-time">
                                    <div
                                        class="time-badge {{ $schedule->priority === 'high' ? 'time-badge-danger' : ($schedule->priority === 'medium' ? 'time-badge-warning' : 'time-badge-primary') }}">
                                        {{ $schedule->scheduled_time->format('H:i') }}
                                    </div>
                                    <div class="schedule-index">#{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                                </div>

                                <div class="schedule-content">
                                    <div class="customer-info">
                                        <div class="customer-avatar">
                                            {{ strtoupper(substr($schedule->user->name, 0, 2)) }}
                                        </div>
                                        <div class="customer-details">
                                            <h6 class="customer-name">{{ $schedule->user->name }}</h6>
                                            <p class="customer-contact">
                                                <i
                                                    class="bi bi-telephone me-1"></i>{{ $schedule->user->phone ?? 'Tidak ada nomor' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="schedule-details">
                                        <div class="detail-row">
                                            <div class="detail-item">
                                                <i class="bi bi-geo-alt text-muted me-1"></i>
                                                <span
                                                    class="detail-text">{{ $schedule->user->address ?? 'Alamat tidak tersedia' }}</span>
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-item">
                                                <i class="bi bi-building text-muted me-1"></i>
                                                <span
                                                    class="detail-text text-muted">{{ $schedule->user->district ?? 'Kecamatan tidak diketahui' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="schedule-meta">
                                    <div class="schedule-badges mb-2">
                                        @if ($schedule->schedule_type === 'waste_collection')
                                            <span class="badge badge-success">
                                                <i class="bi bi-trash me-1"></i>Pengambilan Sampah
                                            </span>
                                        @else
                                            <span class="badge badge-info">
                                                <i class="bi bi-coin me-1"></i>Penukaran Poin
                                            </span>
                                        @endif

                                        @if ($schedule->priority === 'high')
                                            <span class="badge badge-danger ms-1">Prioritas Tinggi</span>
                                        @elseif($schedule->priority === 'medium')
                                            <span class="badge badge-warning ms-1">Prioritas Sedang</span>
                                        @endif
                                    </div>

                                    <div class="schedule-status">
                                        @if ($schedule->status === 'completed')
                                            <span class="status-badge status-completed">
                                                <i class="bi bi-check-circle me-1"></i>Selesai
                                            </span>
                                        @elseif($schedule->status === 'in_progress')
                                            <span class="status-badge status-progress">
                                                <i class="bi bi-clock me-1"></i>Sedang Proses
                                            </span>
                                        @elseif($schedule->status === 'cancelled')
                                            <span class="status-badge status-cancelled">
                                                <i class="bi bi-x-circle me-1"></i>Dibatalkan
                                            </span>
                                        @else
                                            <span class="status-badge status-scheduled">
                                                <i class="bi bi-calendar me-1"></i>Terjadwal
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="schedule-actions">
                                    @if ($schedule->status !== 'completed')
                                        @if ($schedule->status !== 'in_progress')
                                            <button class="btn btn-action btn-primary"
                                                onclick="updateStatus({{ $schedule->id }}, 'in_progress')"
                                                title="Mulai">
                                                <i class="bi bi-play-fill"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-action btn-success"
                                            onclick="updateStatus({{ $schedule->id }}, 'completed')" title="Selesai">
                                            <i class="bi bi-check2"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-action btn-info" onclick="viewDetails({{ $schedule->id }})"
                                        title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="https://maps.google.com?q={{ urlencode($schedule->user->address ?? '') }}"
                                        target="_blank" class="btn btn-action btn-warning" title="Maps">
                                        <i class="bi bi-geo-alt"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h5 class="empty-title">Tidak ada jadwal hari ini</h5>
                        <p class="empty-description">Anda tidak memiliki jadwal pengambilan sampah untuk hari ini. Nikmati
                            waktu istirahat Anda!</p>
                        <a href="#" class="btn btn-primary mt-2">
                            <i class="bi bi-calendar-plus me-2"></i>Lihat Jadwal Mendatang
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modern Upcoming Schedule -->
        <div class="content-card">
            <div class="card-header-modern">
                <div class="card-title-group">
                    <h5 class="card-title-modern mb-1">
                        <i class="bi bi-calendar-week text-primary me-2"></i>
                        Jadwal Mendatang
                    </h5>
                    <p class="card-subtitle-modern mb-0">7 hari ke depan ({{ $stats['upcoming_total'] }} jadwal)</p>
                </div>
            </div>

            <div class="card-body-modern">
                @if ($upcomingSchedules->count() > 0)
                    <div class="upcoming-grid">
                        @foreach ($upcomingSchedules->groupBy('scheduled_date') as $date => $schedules)
                            <div class="upcoming-day-group">
                                <div class="day-header">
                                    <div class="day-date">
                                        <div class="day-number">{{ \Carbon\Carbon::parse($date)->format('d') }}</div>
                                        <div class="day-month">{{ \Carbon\Carbon::parse($date)->format('M') }}</div>
                                    </div>
                                    <div class="day-info">
                                        <h6 class="day-name">{{ \Carbon\Carbon::parse($date)->isoFormat('dddd') }}</h6>
                                        <p class="day-count">{{ $schedules->count() }} jadwal</p>
                                    </div>
                                </div>

                                <div class="day-schedules">
                                    @foreach ($schedules->take(3) as $schedule)
                                        <div class="upcoming-item">
                                            <div class="upcoming-time">{{ $schedule->scheduled_time->format('H:i') }}
                                            </div>
                                            <div class="upcoming-customer">{{ Str::limit($schedule->user->name, 20) }}
                                            </div>
                                            <div class="upcoming-type">
                                                @if ($schedule->schedule_type === 'waste_collection')
                                                    <span class="type-badge type-waste">Sampah</span>
                                                @else
                                                    <span class="type-badge type-points">Poin</span>
                                                @endif
                                                @if ($schedule->priority === 'high')
                                                    <i class="bi bi-exclamation-triangle text-danger ms-1"
                                                        title="Prioritas Tinggi"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    @if ($schedules->count() > 3)
                                        <div class="more-schedules">
                                            <i class="bi bi-plus-circle me-1"></i>{{ $schedules->count() - 3 }} jadwal
                                            lainnya
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <h5 class="empty-title">Tidak ada jadwal mendatang</h5>
                        <p class="empty-description">Belum ada jadwal untuk 7 hari ke depan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle text-primary me-2"></i>Detail Jadwal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailModalContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-funnel text-primary me-2"></i>Filter Jadwal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="scheduled">Terjadwal</option>
                                <option value="in_progress">Sedang Proses</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prioritas</label>
                            <select class="form-select" name="priority">
                                <option value="">Semua Prioritas</option>
                                <option value="low">Rendah</option>
                                <option value="medium">Sedang</option>
                                <option value="high">Tinggi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis</label>
                            <select class="form-select" name="type">
                                <option value="">Semua Jenis</option>
                                <option value="waste_collection">Pengambilan Sampah</option>
                                <option value="point_exchange">Penukaran Poin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="date">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-outline-primary" onclick="resetFilter()">Reset</button>
                    <button type="button" class="btn btn-primary" onclick="applyFilter()">Terapkan Filter</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* Petugas Dashboard Styles */

        /* Dashboard Header */
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            padding: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }

        .dashboard-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .dashboard-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .greeting-text {
            font-size: 0.95rem;
            opacity: 0.85;
            position: relative;
            z-index: 1;
        }

        .header-actions .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }

        .header-actions .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .header-actions .btn-light {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .header-actions .btn-light:hover {
            background-color: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
        }

        /* Modern Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-color);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-card-primary {
            --accent-color: #3b82f6;
        }

        .stat-card-success {
            --accent-color: #10b981;
        }

        .stat-card-warning {
            --accent-color: #f59e0b;
        }

        .stat-card-danger {
            --accent-color: #ef4444;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 1rem;
            background: linear-gradient(135deg, var(--accent-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin-right: 1.25rem;
            opacity: 0.9;
            flex-shrink: 0;
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .stat-progress {
            margin-top: 0.75rem;
        }

        .stat-progress .progress {
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            margin-bottom: 0.25rem;
        }

        .stat-progress small {
            font-size: 0.75rem;
        }

        .stat-badge .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }

        .stat-pulse {
            position: relative;
            display: flex;
            justify-content: flex-end;
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            background: #ef4444;
            border-radius: 50%;
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* Modern Content Cards */
        .content-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .content-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .card-header-modern {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(135deg, #f8fafc, #ffffff);
        }

        .card-title-modern {
            font-size: 1.35rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .card-subtitle-modern {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 400;
        }

        .card-actions .btn-ghost {
            border: none;
            background: none;
            color: #6b7280;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            font-size: 1.1rem;
        }

        .card-actions .btn-ghost:hover {
            background: #f1f5f9;
            color: #374151;
        }

        .card-body-modern {
            padding: 0;
        }

        /* Schedule List */
        .schedule-list {
            padding: 1.25rem;
        }

        .schedule-item {
            display: flex;
            align-items: flex-start;
            padding: 1.75rem;
            border-radius: 1rem;
            background: #fafbfc;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .schedule-item:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .schedule-item.completed {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border-color: #bbf7d0;
        }

        .schedule-item.completed::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #10b981;
            border-radius: 0 4px 4px 0;
        }

        .schedule-time {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 1.5rem;
            min-width: 80px;
            flex-shrink: 0;
        }

        .time-badge {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
            min-width: 70px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .time-badge-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .time-badge-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .time-badge-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
    </style>
@endpush
