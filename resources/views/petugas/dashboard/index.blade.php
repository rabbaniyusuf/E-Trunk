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
                        <button class="btn btn-outline-light" onclick="window.location.reload()" data-bs-toggle="tooltip"
                            title="Refresh Data">
                            <i class="bi bi-arrow-clockwise"></i>
                            <span class="d-none d-sm-inline ms-1">Refresh</span>
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
                    <div class="stat-label">Pengambilan Hari Ini</div>
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
                    <div class="stat-label">Sedang Proses</div>
                </div>
                <div class="stat-pulse">
                    @if ($stats['high_priority'] > 0)
                        <div class="pulse-dot"></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="filter-section">
            <div class="filter-tabs">
                <form method="GET" action="{{ route('petugas.dashboard') }}" id="filterForm">
                    <input type="hidden" name="upcoming_filter" value="{{ $upcomingFilter }}">

                    <div class="filter-group">
                        <label class="filter-label">Filter Hari Ini:</label>
                        <div class="filter-buttons">
                            <button type="button" class="filter-btn {{ $todayFilter === 'all' ? 'active' : '' }}"
                                onclick="updateFilter('today_filter', 'all')">
                                <i class="bi bi-list-ul me-1"></i>
                                Semua <span class="filter-count">({{ $filterCounts['today']['all'] }})</span>
                            </button>
                            <button type="button" class="filter-btn {{ $todayFilter === 'pending' ? 'active' : '' }}"
                                onclick="updateFilter('today_filter', 'pending')">
                                <i class="bi bi-clock-history me-1"></i>
                                Belum Selesai <span class="filter-count">({{ $filterCounts['today']['pending'] }})</span>
                            </button>
                            <button type="button" class="filter-btn {{ $todayFilter === 'in_progress' ? 'active' : '' }}"
                                onclick="updateFilter('today_filter', 'in_progress')">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                Sedang Proses <span
                                    class="filter-count">({{ $filterCounts['today']['in_progress'] }})</span>
                            </button>
                            <button type="button" class="filter-btn {{ $todayFilter === 'completed' ? 'active' : '' }}"
                                onclick="updateFilter('today_filter', 'completed')">
                                <i class="bi bi-check-circle me-1"></i>
                                Selesai <span class="filter-count">({{ $filterCounts['today']['completed'] }})</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modern Today's Collections -->
        <div class="content-card mb-4">
            <div class="card-header-modern">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="card-title-group">
                        <h5 class="card-title-modern mb-1">
                            <i class="bi bi-calendar-day text-primary me-2"></i>
                            Pengambilan Sampah Hari Ini
                        </h5>
                        <p class="card-subtitle-modern mb-0">Kelola pengambilan sampah hari ini</p>
                    </div>
                </div>
            </div>

            <div class="card-body-modern">
                @if ($todayCollections->count() > 0)
                    <div class="schedule-list">
                        @foreach ($todayCollections as $index => $collection)
                            @php
                                $completedClass =
                                    $collection->status === App\Models\WasteCollection::STATUS_COMPLETED
                                        ? 'completed'
                                        : '';
                            @endphp

                            <div class="schedule-item {{ $completedClass }}" data-collection-id="{{ $collection->id }}">
                                <div class="schedule-time">
                                    @php
                                        $timeBadgeClass = match ($collection->status) {
                                            App\Models\WasteCollection::STATUS_IN_PROGRESS => 'time-badge-warning',
                                            App\Models\WasteCollection::STATUS_COMPLETED => 'time-badge-success',
                                            App\Models\WasteCollection::STATUS_CANCELLED => 'time-badge-danger',
                                            default => 'time-badge-primary',
                                        };
                                    @endphp

                                    <div class="time-badge {{ $timeBadgeClass }}">
                                        {{ \Carbon\Carbon::parse($collection->pickup_time)->format('H:i') }}
                                    </div>

                                    <div class="schedule-index">#{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                                </div>

                                <div class="schedule-content">
                                    <div class="customer-info">
                                        <div class="customer-avatar">
                                            {{ strtoupper(substr($collection->user->name, 0, 2)) }}
                                        </div>
                                        <div class="customer-details">
                                            <h6 class="customer-name">{{ $collection->user->name }}</h6>
                                            <p class="customer-contact">
                                                <i
                                                    class="bi bi-telephone me-1"></i>{{ $collection->user->phone ?? 'Tidak ada nomor' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="schedule-details">
                                        <div class="detail-row">
                                            <div class="detail-item">
                                                <i class="bi bi-geo-alt text-muted me-1"></i>
                                                <span
                                                    class="detail-text">{{ $collection->user->address ?? 'Alamat tidak tersedia' }}</span>
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-item">
                                                <i class="bi bi-building text-muted me-1"></i>
                                                <span
                                                    class="detail-text text-muted">{{ $collection->user->district ?? 'Kecamatan tidak diketahui' }}</span>
                                            </div>
                                        </div>
                                        @if ($collection->wasteBinType)
                                            <div class="detail-row">
                                                <div class="detail-item">
                                                    <i class="bi bi-trash text-muted me-1"></i>
                                                    <span
                                                        class="detail-text">{{ $collection->wasteBinType->name ?? 'Jenis sampah tidak diketahui' }}</span>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($collection->waste_types && is_array($collection->waste_types))
                                            <div class="detail-row">
                                                <div class="detail-item">
                                                    <i class="bi bi-list-ul text-muted me-1"></i>
                                                    <span class="detail-text">
                                                        @foreach ($collection->waste_types as $type)
                                                            <span
                                                                class="badge badge-outline-secondary me-1">{{ ucfirst($type) }}</span>
                                                        @endforeach
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="schedule-meta">
                                    <div class="schedule-badges mb-2">
                                        <span class="badge badge-success">
                                            <i class="bi bi-trash me-1"></i>Pengambilan Sampah
                                        </span>
                                        @if ($collection->pickup_date < now()->toDateString())
                                            <span class="badge badge-danger ms-1">Terlambat</span>
                                        @elseif($collection->pickup_date == now()->toDateString() && $collection->pickup_time < now()->toTimeString())
                                            <span class="badge badge-warning ms-1">Perlu Segera</span>
                                        @endif
                                    </div>

                                    <div class="schedule-status">
                                        @if ($collection->status === App\Models\WasteCollection::STATUS_COMPLETED)
                                            <span class="status-badge status-completed">
                                                <i class="bi bi-check-circle me-1"></i>Selesai
                                            </span>
                                        @elseif($collection->status === App\Models\WasteCollection::STATUS_IN_PROGRESS)
                                            <span class="status-badge status-progress">
                                                <i class="bi bi-clock me-1"></i>Sedang Proses
                                            </span>
                                        @elseif($collection->status === App\Models\WasteCollection::STATUS_CANCELLED)
                                            <span class="status-badge status-cancelled">
                                                <i class="bi bi-x-circle me-1"></i>Dibatalkan
                                            </span>
                                        @elseif($collection->status === App\Models\WasteCollection::STATUS_PENDING)
                                            <span class="status-badge status-scheduled">
                                                <i class="bi bi-calendar me-1"></i>Menunggu Jadwal
                                            </span>
                                        @else
                                            <span class="status-badge status-scheduled">
                                                <i class="bi bi-calendar me-1"></i>Terjadwal
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="schedule-actions">
                                    @if (in_array($collection->status, [
                                            App\Models\WasteCollection::STATUS_SCHEDULED,
                                            App\Models\WasteCollection::STATUS_IN_PROGRESS,
                                        ]))
                                        <a class="btn btn-action btn-success"
                                            href="{{ route('petugas.tasks.show', $collection->id) }}"
                                            title="Proses Pengambilan">
                                            <i class="bi bi-check2"></i>
                                        </a>
                                        <a class="btn btn-action btn-info"
                                            href="{{ route('petugas.tasks.show', $collection->id) }}" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                    <a href="https://maps.google.com?q={{ urlencode($collection->user->address ?? '') }}"
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
                        <h5 class="empty-title">Tidak ada pengambilan hari ini</h5>
                        <p class="empty-description">Anda tidak memiliki jadwal pengambilan sampah untuk hari ini. Nikmati
                            waktu istirahat Anda!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modern Upcoming Collections -->
        <div class="content-card">
            <div class="card-header-modern">
                <div class="card-title-group">
                    <h5 class="card-title-modern mb-1">
                        <i class="bi bi-calendar-week text-primary me-2"></i>
                        Pengambilan Mendatang
                    </h5>
                    <p class="card-subtitle-modern mb-0">7 hari ke depan ({{ $stats['upcoming_total'] }} pengambilan)</p>
                </div>
            </div>

            <div class="card-body-modern">
                @if ($upcomingCollections->count() > 0)
                    <div class="upcoming-grid">
                        @foreach ($upcomingCollections->groupBy('pickup_date') as $date => $collections)
                            <div class="upcoming-day-group">
                                <div class="day-header">
                                    <div class="day-date">
                                        <div class="day-number">{{ \Carbon\Carbon::parse($date)->format('d') }}</div>
                                        <div class="day-month">{{ \Carbon\Carbon::parse($date)->format('M') }}</div>
                                    </div>
                                    <div class="day-info">
                                        <h6 class="day-name">{{ \Carbon\Carbon::parse($date)->isoFormat('dddd') }}</h6>
                                        <p class="day-count">{{ $collections->count() }} pengambilan</p>
                                    </div>
                                </div>

                                <div class="day-schedules">
                                    @foreach ($collections->take(3) as $collection)
                                        <div class="upcoming-item">
                                            <div class="upcoming-time">
                                                {{ \Carbon\Carbon::parse($collection->pickup_time)->format('H:i') }}</div>
                                            <div class="upcoming-customer">{{ Str::limit($collection->user->name, 20) }}
                                            </div>
                                            <div class="upcoming-type">
                                                <span class="type-badge type-waste">Sampah</span>
                                                @if ($collection->wasteBinType)
                                                    <small
                                                        class="text-muted ms-1">{{ $collection->wasteBinType->name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    @if ($collections->count() > 3)
                                        <div class="more-schedules">
                                            <i class="bi bi-plus-circle me-1"></i>{{ $collections->count() - 3 }}
                                            pengambilan lainnya
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
                        <h5 class="empty-title">Tidak ada pengambilan mendatang</h5>
                        <p class="empty-description">Belum ada jadwal untuk 7 hari ke depan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Petugas Dashboard Styles - Complete */

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

        .stat-trend {
            margin-left: auto;
            font-size: 1.2rem;
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
            gap: 1rem;
        }

        .schedule-item:last-child {
            margin-bottom: 0;
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
            margin-bottom: 0.5rem;
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

        .time-badge-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .schedule-index {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Customer Info */
        .schedule-content {
            flex: 1;
            min-width: 0;
        }

        .customer-info {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .customer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .customer-details {
            flex: 1;
            min-width: 0;
        }

        .customer-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 0.25rem 0;
        }

        .customer-contact {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }

        /* Schedule Details */
        .schedule-details {
            margin-bottom: 1rem;
        }

        .detail-row {
            margin-bottom: 0.5rem;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            font-size: 0.875rem;
        }

        .detail-item i {
            width: 16px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .detail-text {
            color: #4b5563;
            line-height: 1.4;
        }

        /* Schedule Meta */
        .schedule-meta {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .schedule-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            display: inline-flex;
            align-items: center;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .badge-outline-secondary {
            background-color: transparent;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }

        /* Status Badges */
        .schedule-status {
            display: flex;
            align-items: center;
        }

        .status-badge {
            padding: 0.5rem 0.875rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            border: 1px solid;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .status-progress {
            background-color: #dbeafe;
            color: #1e40af;
            border-color: #93c5fd;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: #fca5a5;
        }

        .status-scheduled {
            background-color: #f3f4f6;
            color: #374151;
            border-color: #d1d5db;
        }

        /* Schedule Actions */
        .schedule-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-left: auto;
            flex-shrink: 0;
        }

        .btn-action {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.2s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-decoration: none;
        }

        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-action.btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-action.btn-primary:hover {
            background-color: #2563eb;
            color: white;
        }

        .btn-action.btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-action.btn-success:hover {
            background-color: #059669;
            color: white;
        }

        .btn-action.btn-info {
            background-color: #06b6d4;
            color: white;
        }

        .btn-action.btn-info:hover {
            background-color: #0891b2;
            color: white;
        }

        .btn-action.btn-warning {
            background-color: #f59e0b;
            color: white;
        }

        .btn-action.btn-warning:hover {
            background-color: #d97706;
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #6b7280;
        }

        .empty-icon {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1.5rem;
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .empty-description {
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }

        /* Upcoming Schedule */
        .upcoming-grid {
            padding: 1.25rem;
            display: grid;
            gap: 1.5rem;
        }

        .upcoming-day-group {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .upcoming-day-group:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .day-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .day-date {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 1.25rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 0.75rem;
            padding: 1rem;
            min-width: 70px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .day-number {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .day-month {
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            margin-top: 0.25rem;
            opacity: 0.9;
        }

        .day-info h6 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 0.25rem 0;
        }

        .day-count {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }

        .day-schedules {
            display: grid;
            gap: 0.75rem;
        }

        .upcoming-item {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .upcoming-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .upcoming-time {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            background: #f3f4f6;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            min-width: 50px;
            text-align: center;
        }

        .upcoming-customer {
            font-size: 0.9rem;
            font-weight: 500;
            color: #1f2937;
        }

        .upcoming-type {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.25rem;
        }

        .type-badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .type-waste {
            background-color: #d1fae5;
            color: #065f46;
        }

        .type-points {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .more-schedules {
            text-align: center;
            padding: 0.75rem;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: #667eea;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1.5rem;
            }

            .dashboard-title {
                font-size: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
                margin-right: 1rem;
            }

            .stat-number {
                font-size: 1.8rem;
            }

            .schedule-item {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
                padding: 1.25rem;
            }

            .schedule-time {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                min-width: auto;
            }

            .schedule-actions {
                flex-direction: row;
                justify-content: center;
                margin-left: 0;
            }

            .upcoming-grid {
                padding: 1rem;
                gap: 1rem;
            }

            .upcoming-day-group {
                padding: 1.25rem;
            }

            .upcoming-item {
                grid-template-columns: 1fr;
                gap: 0.5rem;
                text-align: center;
            }

            .day-date {
                min-width: 60px;
                padding: 0.75rem;
            }

            .upcoming-type {
                align-items: center;
            }
        }

        @media (max-width: 576px) {
            .header-actions {
                margin-top: 1rem;
            }

            .header-actions .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }

            .customer-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .customer-avatar {
                margin-right: 0;
            }

            .schedule-badges {
                justify-content: center;
            }

            .schedule-status {
                justify-content: center;
            }
        }

        .filter-section {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
            margin: 0;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .filter-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin: 0;
        }

        .filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .filter-btn {
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.5rem 0.875rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            white-space: nowrap;
        }

        .filter-btn:hover {
            background: #f9fafb;
            border-color: #9ca3af;
            color: #374151;
            transform: translateY(-1px);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-color: #667eea;
            color: white;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25);
        }

        .filter-btn.active:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
            transform: translateY(-1px);
        }

        .filter-count {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 0.375rem;
            padding: 0.125rem 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.375rem;
        }

        .filter-btn:not(.active) .filter-count {
            background: #e5e7eb;
            color: #6b7280;
        }

        /* Loading state */
        .filter-loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .filter-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid #e5e7eb;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .filter-section {
                padding: 1rem;
            }

            .filter-group {
                gap: 0.5rem;
            }

            .filter-buttons {
                gap: 0.375rem;
            }

            .filter-btn {
                padding: 0.375rem 0.625rem;
                font-size: 0.8rem;
            }

            .filter-count {
                padding: 0.0625rem 0.25rem;
                font-size: 0.7rem;
                margin-left: 0.25rem;
            }
        }

        @media (max-width: 576px) {
            .filter-btn {
                flex: 1;
                justify-content: center;
                min-width: 0;
            }

            .filter-btn i {
                display: none;
            }
        }
    </style>
@endpush


@push('scripts')
    <script>
        function updateFilter(filterType, filterValue) {
            // Tampilkan loading state
            const filterSection = document.querySelector('.filter-section');
            filterSection.classList.add('filter-loading');

            // Update active state pada button
            const filterButtons = document.querySelectorAll('.filter-btn');
            filterButtons.forEach(btn => {
                btn.classList.remove('active');
            });

            event.target.classList.add('active');

            // Buat URL dengan parameter filter
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set(filterType, filterValue);

            // Preserve filter lainnya
            if (filterType === 'today_filter') {
                const upcomingFilter = document.querySelector('input[name="upcoming_filter"]').value;
                currentUrl.searchParams.set('upcoming_filter', upcomingFilter);
            } else {
                const todayFilter = document.querySelector('input[name="today_filter"]').value;
                currentUrl.searchParams.set('today_filter', todayFilter);
            }

            // Redirect ke URL dengan filter baru
            window.location.href = currentUrl.toString();
        }

        // Auto-refresh setiap 30 detik jika ada filter aktif
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const hasActiveFilter = urlParams.get('today_filter') !== 'all' ||
                urlParams.get('upcoming_filter') !== 'all';

            if (hasActiveFilter) {
                setInterval(function() {
                    // Refresh halaman dengan mempertahankan filter
                    window.location.reload();
                }, 30000); // 30 detik
            }
        });

        // Handle form submission untuk kompatibilitas
        document.getElementById('filterForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
        });

        document.getElementById('upcomingFilterForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    </script>
@endpush
