@extends('layouts.main')

@section('title', 'Manajemen Pengguna - E-TRANK')

@push('styles')
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .user-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .bin-indicator {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 0.75rem;
        }

        .bin-recycle {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .bin-non-recycle {
            background-color: #fef3c7;
            color: #d97706;
        }

        .bin-inactive {
            background-color: #f1f5f9;
            color: #64748b;
        }

        .progress-thin {
            height: 6px;
            border-radius: 3px;
        }

        .capacity-bar {
            height: 4px;
            background-color: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }

        .capacity-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.6s ease;
        }

        .capacity-fill.low {
            background-color: #10b981;
        }

        .capacity-fill.medium {
            background-color: #f59e0b;
        }

        .capacity-fill.high {
            background-color: #ef4444;
        }

        .stats-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.25rem;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .filter-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .badge-role {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .badge-masyarakat {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .badge-petugas {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .search-box input {
            padding-left: 2.5rem;
        }

        @media (max-width: 768px) {
            .user-card {
                padding: 1rem;
            }

            .action-buttons {
                justify-content: center;
                margin-top: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">
                    <i class="bi bi-people"></i> Manajemen Pengguna
                </h2>
                <p class="mb-0 opacity-90">
                    Kelola pengguna dan pantau status tong sampah mereka
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('admin.users.create') }}" class="btn btn-light">
                    <i class="bi bi-plus-circle"></i> Tambah Pengguna
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number text-primary">{{ $totalUsers ?? 0 }}</div>
                <div class="text-muted">Total Pengguna</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number text-success">{{ $activeBins ?? 0 }}</div>
                <div class="text-muted">Tong Aktif</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number text-warning">{{ $fullBins ?? 0 }}</div>
                <div class="text-muted">Tong Penuh</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number text-info">{{ $todayPickups ?? 0 }}</div>
                <div class="text-muted">Pickup Hari Ini</div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Cari Pengguna</label>
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                            placeholder="Nama, email, atau kode tong...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role">
                        <option value="">Semua Role</option>
                        <option value="masyarakat" {{ request('role') == 'masyarakat' ? 'selected' : '' }}>Masyarakat
                        </option>
                        <option value="petugas_kebersihan" {{ request('role') == 'petugas_kebersihan' ? 'selected' : '' }}>
                            Petugas Kebersihan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status Tong</label>
                    <select class="form-select" name="bin_status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('bin_status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('bin_status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif
                        </option>
                        <option value="full" {{ request('bin_status') == 'full' ? 'selected' : '' }}>Penuh</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kecamatan</label>
                    <select class="form-select" name="district">
                        <option value="">Semua Kecamatan</option>
                        @foreach ($districts ?? [] as $district)
                            <option value="{{ $district }}" {{ request('district') == $district ? 'selected' : '' }}>
                                {{ $district }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Users List -->
    @forelse($users ?? [] as $user)
        <div class="user-card">
            <div class="row align-items-center">
                <!-- User Info -->
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <div class="text-muted small">{{ $user->email }}</div>
                            <div class="text-muted small">
                                <i class="bi bi-phone"></i> {{ $user->phone }}
                            </div>
                            <span
                                class="badge badge-role {{ $user->hasRole('masyarakat') ? 'badge-masyarakat' : 'badge-petugas' }}">
                                {{ $user->hasRole('masyarakat') ? 'Masyarakat' : 'Petugas' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="col-md-2">
                    <div class="small">
                        <div class="text-muted">Alamat:</div>
                        <div>{{ Str::limit($user->address, 30) }}</div>
                        <div class="text-primary">{{ $user->district }}</div>
                    </div>
                </div>

                <!-- Bins Status -->
                <div class="col-md-4">
                    @if ($user->bins->count() > 0)
                        @foreach ($user->bins as $bin)
                            <div class="d-flex align-items-center mb-2">
                                <div
                                    class="bin-indicator {{ $bin->type == 'recycle' ? 'bin-recycle' : 'bin-non-recycle' }} {{ !$bin->isActive() ? 'bin-inactive' : '' }}">
                                    <i class="bi bi-{{ $bin->type == 'recycle' ? 'recycle' : 'trash' }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="fw-semibold">{{ $bin->bin_code }}</small>
                                        <small class="text-muted">{{ $bin->fill_percentage }}%</small>
                                    </div>
                                    <div class="capacity-bar mt-1">
                                        <div class="capacity-fill {{ $bin->fill_percentage <= 60 ? 'low' : ($bin->fill_percentage <= 85 ? 'medium' : 'high') }}"
                                            style="width: {{ $bin->fill_percentage }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted">{{ $bin->current_weight }}kg /
                                            {{ $bin->capacity }}kg</small>
                                        <small class="text-{{ $bin->isActive() ? 'success' : 'danger' }}">
                                            {{ $bin->isActive() ? 'Aktif' : 'Tidak Aktif' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted text-center py-2">
                            <i class="bi bi-inbox"></i>
                            <div class="small">Belum ada tong sampah</div>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="col-md-3">
                    <div class="action-buttons">
                        {{-- <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-primary btn-action">
                            <i class="bi bi-eye"></i> Detail
                        </a> --}}
                        <a href="{{ route('admin.users.edit', $user->id) }}"
                            class="btn btn-outline-secondary btn-action">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @if ($user->bins->count() == 0)
                            {{-- <a href="{{ route('admin.bins.create', ['user_id' => $user->id]) }}"
                                class="btn btn-outline-success btn-action">
                                <i class="bi bi-plus"></i> Tambah Tong
                            </a> --}}
                        @endif
                        @if (
                            $user->bins->where('status', 'active')->where('current_weight', '>=', function ($bin) {
                                    return $bin->capacity * 0.8;
                                })->count() > 0)
                            <button class="btn btn-warning btn-action" onclick="schedulePickup({{ $user->id }})">
                                <i class="bi bi-truck"></i> Jadwal Pickup
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <h5>Belum Ada Pengguna</h5>
            <p class="text-muted">Mulai tambahkan pengguna untuk mengelola sistem bank sampah</p>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Pengguna Pertama
            </a>
        </div>
    @endforelse

    <!-- Pagination -->
    @if (isset($users) && $users->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        // Auto-submit form when filter changes
        document.querySelectorAll('select[name="role"], select[name="bin_status"], select[name="district"]').forEach(
            select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });

        // Search with debounce
        let searchTimeout;
        document.querySelector('input[name="search"]').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });

        // Schedule pickup function
        function schedulePickup(userId) {
            if (confirm('Apakah Anda yakin ingin menjadwalkan pickup untuk pengguna ini?')) {
                // Implement pickup scheduling logic here
                fetch(`/admin/users/${userId}/schedule-pickup`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Pickup berhasil dijadwalkan!');
                            location.reload();
                        } else {
                            alert('Terjadi kesalahan saat menjadwalkan pickup.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menjadwalkan pickup.');
                    });
            }
        }

        // Initialize tooltips if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Add any initialization code here
            console.log('Users management page loaded');
        });
    </script>
@endpush
