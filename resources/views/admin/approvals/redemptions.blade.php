{{-- resources/views/admin/approvals/redemptions.blade.php --}}
@extends('layouts.main')

@section('title', 'Approval Penukaran Saldo')

@push('styles')
    <style>
        :root {
            --primary-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --gradient-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .dashboard-header {
            background: var(--gradient-primary);
            border-radius: 1rem;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        .stats-card {
            border: none;
            border-radius: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .stats-card.pending {
            background: var(--gradient-warning);
            color: white;
        }

        .stats-card.approved {
            background: var(--gradient-info);
            color: white;
        }

        .stats-card.completed {
            background: var(--gradient-success);
            color: white;
        }

        .stats-card.cash {
            background: var(--gradient-primary);
            color: white;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .filter-section {
            background: white;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
        }

        .filter-section .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .table-container {
            background: white;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .table thead th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            font-weight: 600;
            color: #374151;
            padding: 1rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
            transform: scale(1.001);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.approved {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-badge.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-success {
            background: var(--gradient-success);
            border: none;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
        }

        .loading-spinner {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 350px;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: none;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .modal-content {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            border-bottom: 1px solid #e5e7eb;
            border-radius: 1rem 1rem 0 0;
        }

        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                padding: 1.5rem;
                text-align: center;
            }

            .filter-section {
                padding: 1rem;
            }

            .user-info {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            .action-buttons .btn {
                width: 100%;
            }

            .stats-card {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-3 px-md-4">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2 fw-bold">Approval Penukaran Saldo</h2>
                    <p class="mb-0 opacity-90">Kelola dan proses permintaan penukaran poin ke saldo cash dengan mudah</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light btn-lg" onclick="refreshData()">
                        <i class="bi bi-arrow-clockwise me-2"></i>
                        Refresh Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-6 col-md-3 mb-3">
                <div class="card stats-card pending h-100">
                    <div class="card-body text-center position-relative">
                        <i class="bi bi-clock-history fs-1 mb-3 opacity-75"></i>
                        <h2 class="mb-2 fw-bold">{{ $stats['pending'] }}</h2>
                        <p class="mb-0 opacity-90">Menunggu Approval</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card stats-card completed h-100">
                    <div class="card-body text-center position-relative">
                        <i class="bi bi-check-all fs-1 mb-3 opacity-75"></i>
                        <h2 class="mb-2 fw-bold">{{ $stats['completed'] }}</h2>
                        <p class="mb-0 opacity-90">Selesai</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card stats-card cash h-100">
                    <div class="card-body text-center position-relative">
                        <i class="bi bi-cash-coin fs-1 mb-3 opacity-75"></i>
                        <h6 class="mb-2 fw-bold">Rp {{ number_format($stats['total_cash_pending'], 0, ',', '.') }}</h6>
                        <p class="mb-0 opacity-90">Total Pending</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.approvals.redemptions') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-funnel me-1"></i>Status
                        </label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-calendar me-1"></i>Dari Tanggal
                        </label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-calendar-check me-1"></i>Sampai Tanggal
                        </label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-search me-1"></i>Pencarian
                        </label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Kode atau nama user..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-end">
                        <a href="{{ route('admin.approvals.redemptions') }}" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px">#</th>
                            <th>User</th>
                            <th class="text-center">Kode Transaksi</th>
                            <th class="text-center">Poin</th>
                            <th class="text-center">Nilai Cash</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center" style="width: 200px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($redemptions as $index => $redemption)
                            <tr>
                                <td class="text-center">{{ $redemptions->firstItem() + $index }}</td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($redemption->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $redemption->user->name }}</div>
                                            <small class="text-muted">{{ $redemption->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <code class="bg-light px-2 py-1 rounded">{{ $redemption->redemption_code }}</code>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6">{{ number_format($redemption->points_redeemed) }}
                                        Poin</span>
                                </td>
                                <td class="text-center">
                                    <strong class="text-success fs-6">Rp
                                        {{ number_format($redemption->cash_value, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="status-badge {{ $redemption->status }}">
                                        {{ ucfirst($redemption->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="fw-medium">{{ $redemption->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $redemption->created_at->format('H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        @if ($redemption->status === 'pending')
                                            <button class="btn btn-success btn-sm me-1"
                                                onclick="approveRedemption({{ $redemption->id }})">
                                                <i class="bi bi-check-lg"></i>
                                                <span class="d-none d-md-inline ms-1">Setujui</span>
                                            </button>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="rejectRedemption({{ $redemption->id }})">
                                                <i class="bi bi-x-lg"></i>
                                                <span class="d-none d-md-inline ms-1">Tolak</span>
                                            </button>
                                        @else
                                            <button class="btn btn-outline-primary btn-sm"
                                                onclick="viewDetails({{ $redemption->id }})">
                                                <i class="bi bi-eye"></i>
                                                <span class="d-none d-md-inline ms-1">Detail</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <h5 class="mb-2">Tidak Ada Data</h5>
                                        <p class="mb-0">Belum ada permintaan penukaran saldo yang perlu diproses</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($redemptions->hasPages())
                <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
                    <div class="text-muted">
                        <small>Menampilkan {{ $redemptions->firstItem() }} - {{ $redemptions->lastItem() }} dari
                            {{ $redemptions->total() }} data</small>
                    </div>
                    {{ $redemptions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="mb-2">Memproses Permintaan</h5>
            <p class="mb-0 text-muted">Mohon tunggu sebentar...</p>
        </div>
    </div>

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle me-2"></i>
                        Konfirmasi Approval
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-cash-coin text-success" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-4">Apakah Anda yakin ingin menyetujui penukaran saldo ini?</p>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Catatan (Opsional)</label>
                        <textarea class="form-control" id="approvalNotes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-success" id="confirmApproval">
                        <i class="bi bi-check-lg me-1"></i> Ya, Setujui
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-x-circle me-2"></i>
                        Tolak Penukaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center mb-4">Berikan alasan penolakan untuk penukaran saldo ini:</p>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="rejectionReason" rows="4"
                            placeholder="Jelaskan alasan penolakan secara detail..." required></textarea>
                        <div class="invalid-feedback">Alasan penolakan harus diisi</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmRejection">
                        <i class="bi bi-x-lg me-1"></i> Ya, Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Modern JavaScript (ES6+) without jQuery dependency
        class RedemptionManager {
            constructor() {
                this.currentRedemptionId = null;
                this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.setupFormAutoSubmit();
            }

            setupEventListeners() {
                // Approval confirmation
                document.getElementById('confirmApproval')?.addEventListener('click', () => {
                    this.processApproval();
                });

                // Rejection confirmation
                document.getElementById('confirmRejection')?.addEventListener('click', () => {
                    this.processRejection();
                });

                // Real-time validation for rejection reason
                document.getElementById('rejectionReason')?.addEventListener('input', (e) => {
                    const element = e.target;
                    if (element.value.trim()) {
                        element.classList.remove('is-invalid');
                    }
                });
            }

            setupFormAutoSubmit() {
                // Auto-submit filter form on change
                const filterElements = document.querySelectorAll('#filterForm select, #filterForm input[type="date"]');
                filterElements.forEach(element => {
                    element.addEventListener('change', () => {
                        document.getElementById('filterForm').submit();
                    });
                });
            }

            showToast(message, type = 'success') {
                // Remove existing toasts
                document.querySelectorAll('.toast-notification').forEach(toast => toast.remove());

                const iconMap = {
                    success: 'bi-check-circle-fill',
                    danger: 'bi-x-circle-fill',
                    warning: 'bi-exclamation-triangle-fill',
                    info: 'bi-info-circle-fill'
                };

                const toast = document.createElement('div');
                toast.className = `alert alert-${type} alert-dismissible fade show toast-notification`;
                toast.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="bi ${iconMap[type]} me-2 fs-5"></i>
                        <div class="flex-grow-1">${message}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                document.body.appendChild(toast);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 5000);
            }

            showLoading() {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }

            hideLoading() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }

            async makeRequest(url, data = {}) {
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        throw new Error(result.message || 'Terjadi kesalahan sistem');
                    }

                    return result;
                } catch (error) {
                    throw error;
                }
            }

            approveRedemption(id) {
                this.currentRedemptionId = id;
                document.getElementById('approvalNotes').value = '';
                const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                modal.show();
            }

            rejectRedemption(id) {
                this.currentRedemptionId = id;
                const reasonInput = document.getElementById('rejectionReason');
                reasonInput.value = '';
                reasonInput.classList.remove('is-invalid');
                const modal = new bootstrap.Modal(document.getElementById('rejectionModal'));
                modal.show();
            }

            async processApproval() {
                if (!this.currentRedemptionId) return;

                this.showLoading();
                const modal = bootstrap.Modal.getInstance(document.getElementById('approvalModal'));
                modal.hide();

                try {
                    const notes = document.getElementById('approvalNotes').value;
                    const result = await this.makeRequest(
                        `/admin/approvals/redemptions/${this.currentRedemptionId}/approve`, {
                            notes
                        });

                    this.hideLoading();
                    this.showToast(result.message, 'success');

                    // Reload page after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } catch (error) {
                    this.hideLoading();
                    this.showToast(error.message, 'danger');
                }
            }

            async processRejection() {
                if (!this.currentRedemptionId) return;

                const reasonInput = document.getElementById('rejectionReason');
                const reason = reasonInput.value.trim();

                if (!reason) {
                    reasonInput.classList.add('is-invalid');
                    reasonInput.focus();
                    return;
                }

                reasonInput.classList.remove('is-invalid');
                this.showLoading();
                const modal = bootstrap.Modal.getInstance(document.getElementById('rejectionModal'));
                modal.hide();

                try {
                    const result = await this.makeRequest(
                        `/admin/approvals/redemptions/${this.currentRedemptionId}/reject`, {
                            reason
                        });

                    this.hideLoading();
                    this.showToast(result.message, 'success');

                    // Reload page after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } catch (error) {
                    this.hideLoading();
                    this.showToast(error.message, 'danger');
                }
            }

            viewDetails(id) {
                this.showToast('Fitur detail akan segera tersedia', 'info');
            }

            refreshData() {
                // Add loading animation to refresh button
                const refreshBtn = document.querySelector('[onclick="refreshData()"]');
                const originalContent = refreshBtn.innerHTML;

                refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-2"></i>Refreshing...';
                refreshBtn.disabled = true;

                // Add spin animation
                const style = document.createElement('style');
                style.textContent =
                    '.spin { animation: spin 1s linear infinite; } @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
                document.head.appendChild(style);

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }
        }

        // Initialize the manager when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.redemptionManager = new RedemptionManager();
        });

        // Global functions for backward compatibility
        function approveRedemption(id) {
            window.redemptionManager?.approveRedemption(id);
        }

        function rejectRedemption(id) {
            window.redemptionManager?.rejectRedemption(id);
        }

        function viewDetails(id) {
            window.redemptionManager?.viewDetails(id);
        }

        function refreshData() {
            window.redemptionManager?.refreshData();
        }

        // Add some nice hover effects and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate statistics cards on load
            const statsCards = document.querySelectorAll('.stats-card');
            statsCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Add ripple effect to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    this.appendChild(ripple);

                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Enhanced table row hover effects
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.zIndex = '10';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                    this.style.zIndex = '1';
                });
            });
        });

        // Add ripple effect CSS
        const rippleStyle = document.createElement('style');
        rippleStyle.textContent = `
            .btn {
                position: relative;
                overflow: hidden;
            }

            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }

            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(rippleStyle);
    </script>
@endpush
