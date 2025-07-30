@extends('layouts.main')
@section('title', 'Riwayat Transaksi - E-TRANK')

@section('content')
    <div class="container-fluid">
        <!-- Modern Page Header with Animation -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="header-card bg-gradient-primary text-white rounded-4 p-4 mb-4 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 opacity-10">
                        <i class="bi bi-clock-history" style="font-size: 8rem;"></i>
                    </div>
                    <div class="position-relative">
                        <h2 class="mb-2 d-flex align-items-center animate-fade-in">
                            <i class="bi bi-clock-history me-3 fs-1"></i>
                            <div>
                                <span class="fw-bold">Riwayat Transaksi</span>
                                <div class="fs-6 opacity-75 fw-normal">Kelola dan pantau aktivitas poin Anda</div>
                            </div>
                        </h2>
                    </div>
                </div>

                <!-- Enhanced Filter Controls -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted small">FILTER STATUS</label>
                                        <select class="form-select form-select-lg border-0 bg-light rounded-3"
                                            id="statusFilter">
                                            <option value="">Semua Status</option>
                                            <option value="MENUNGGU_DIAMBIL">Menunggu Diambil</option>
                                            <option value="SUDAH_DIAMBIL">Sudah Diambil</option>
                                            <option value="GAGAL_DIAMBIL">Gagal Diambil</option>
                                            <option value="MENUNGGU_KONFIRMASI">Menunggu Konfirmasi</option>
                                            <option value="DIKONFIRMASI">Dikonfirmasi</option>
                                            <option value="DITOLAK">Ditolak</option>
                                            <option value="SELESAI">Selesai</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-muted small">JENIS TRANSAKSI</label>
                                        <select class="form-select form-select-lg border-0 bg-light rounded-3"
                                            id="typeFilter">
                                            <option value="">Semua Transaksi</option>
                                            <option value="deposit">Tukar Sampah</option>
                                            <option value="redemption">Tukar ke Saldo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-secondary rounded-3 w-100"
                                            id="resetFilters">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Reset Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($transactions->count() > 0)
            <!-- Enhanced Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stats-card position-relative overflow-hidden rounded-4 h-100">
                        <div class="card-gradient bg-success"></div>
                        <div class="card border-0 h-100 bg-transparent">
                            <div class="card-body text-white p-4 position-relative">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1 small opacity-75">Total Deposit</p>
                                        <h4 class="mb-0 fw-bold counter" data-count="{{ $stats['total_deposits'] }}">0</h4>
                                        <small class="opacity-75">poin diperoleh</small>
                                    </div>
                                    <div class="stats-icon">
                                        <i class="bi bi-recycle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stats-card position-relative overflow-hidden rounded-4 h-100">
                        <div class="card-gradient bg-danger"></div>
                        <div class="card border-0 h-100 bg-transparent">
                            <div class="card-body text-white p-4 position-relative">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1 small opacity-75">Total Penarikan</p>
                                        <h4 class="mb-0 fw-bold counter"
                                            data-count="{{ $stats['total_withdrawals'] + $stats['total_redemptions'] }}">0
                                        </h4>
                                        <small class="opacity-75">poin digunakan</small>
                                    </div>
                                    <div class="stats-icon">
                                        <i class="bi bi-cash-coin"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stats-card position-relative overflow-hidden rounded-4 h-100">
                        <div class="card-gradient bg-primary"></div>
                        <div class="card border-0 h-100 bg-transparent">
                            <div class="card-body text-white p-4 position-relative">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1 small opacity-75">Saldo Poin</p>
                                        <h4 class="mb-0 fw-bold counter" data-count="{{ $stats['current_balance'] }}">0</h4>
                                        <small class="opacity-75">tersedia</small>
                                    </div>
                                    <div class="stats-icon">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="stats-card position-relative overflow-hidden rounded-4 h-100">
                        <div class="card-gradient bg-warning"></div>
                        <div class="card border-0 h-100 bg-transparent">
                            <div class="card-body text-white p-4 position-relative">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1 small opacity-75">Total Transaksi</p>
                                        <h4 class="mb-0 fw-bold counter" data-count="{{ $stats['total_transactions'] }}">0
                                        </h4>
                                        <small class="opacity-75">aktivitas</small>
                                    </div>
                                    <div class="stats-icon">
                                        <i class="bi bi-graph-up"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Transactions List -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-list-ul text-primary me-2"></i>
                            Daftar Transaksi
                        </h5>
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                            {{ $transactions->total() }} transaksi
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop View -->
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="transactionsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4 py-3 fw-semibold">Waktu</th>
                                        <th class="border-0 py-3 fw-semibold">Jenis Transaksi</th>
                                        <th class="border-0 py-3 fw-semibold">Deskripsi</th>
                                        <th class="border-0 py-3 text-center fw-semibold">Poin/Nilai</th>
                                        <th class="border-0 py-3 text-center fw-semibold">Status</th>
                                        <th class="border-0 py-3 text-center fw-semibold">Diproses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr class="transaction-row" data-status="{{ $transaction->status }}"
                                            data-type="{{ $transaction->transaction_type }}">
                                            <td class="px-4 py-4">
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="fw-semibold">{{ $transaction->created_at->format('d M Y') }}</span>
                                                    <small class="text-muted">{{ $transaction->created_at->format('H:i') }}
                                                        WIB</small>
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                @if ($transaction->transaction_type == 'deposit')
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-wrapper bg-success rounded-3 p-2 me-3">
                                                            <i class="bi bi-recycle text-white"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-semibold text-success">Tukar Sampah</span>
                                                            <br><small class="text-muted">Deposit Poin</small>
                                                        </div>
                                                    </div>
                                                @elseif ($transaction->transaction_type == 'withdrawal')
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-wrapper bg-danger rounded-3 p-2 me-3">
                                                            <i class="bi bi-arrow-up-circle text-white"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-semibold text-danger">Tarik Poin</span>
                                                            <br><small class="text-muted">Penarikan Poin</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-wrapper bg-primary rounded-3 p-2 me-3">
                                                            <i class="bi bi-cash-coin text-white"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-semibold text-primary">Tukar ke Saldo</span>
                                                            <br><small class="text-muted">Penukaran Poin</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-4">
                                                <p class="mb-0">{{ $transaction->description }}</p>
                                            </td>
                                            <td class="py-4 text-center">
                                                <span
                                                    class="fw-bold {{ $transaction->transaction_type == 'deposit' ? 'text-success' : ($transaction->transaction_type == 'redemption' ? 'text-primary' : 'text-danger') }}">
                                                    {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->points) }}
                                                </span>
                                                @if (isset($transaction->cash_value) && $transaction->cash_value)
                                                    <div class="text-success small">Rp
                                                        {{ number_format($transaction->cash_value, 0, ',', '.') }}</div>
                                                @endif
                                            </td>
                                            <td class="py-4 text-center">
                                                <span
                                                    class="badge {{ $transaction->status_badge_class }} rounded-pill py-2 px-3">
                                                    {{ $transaction->status_label }}
                                                </span>
                                            </td>
                                            <td class="py-4 text-center">
                                                @if ($transaction->processed_by)
                                                    <div class="d-flex flex-column">
                                                        <span
                                                            class="fw-medium">{{ $transaction->processed_by->name ?? 'System' }}</span>
                                                        <small
                                                            class="text-muted">{{ $transaction->processed_at ? $transaction->processed_at->format('d M H:i') : '' }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            {{-- <td class="py-4 text-center">
                                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailModal{{ $transaction->id }}">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </button>
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile View -->
                    <div class="d-lg-none" id="transactionCards">
                        @foreach ($transactions as $transaction)
                            <div class="card transaction-card border-0 rounded-4 mb-3 hover-scale"
                                data-status="{{ $transaction->status }}"
                                data-type="{{ $transaction->transaction_type }}">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            @if ($transaction->transaction_type == 'deposit')
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-wrapper bg-success rounded-3 p-2 me-3">
                                                        <i class="bi bi-recycle text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-success">Tukar Sampah</h6>
                                                        <small class="text-muted">Deposit Poin</small>
                                                    </div>
                                                </div>
                                            @elseif ($transaction->transaction_type == 'withdrawal')
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-wrapper bg-danger rounded-3 p-2 me-3">
                                                        <i class="bi bi-arrow-up-circle text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-danger">Tarik Poin</h6>
                                                        <small class="text-muted">Penarikan Poin</small>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-wrapper bg-primary rounded-3 p-2 me-3">
                                                        <i class="bi bi-cash-coin text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-primary">Tukar ke Saldo</h6>
                                                        <small class="text-muted">Penukaran Poin</small>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="badge {{ $transaction->status_badge_class }} rounded-pill py-2 px-3">
                                            {{ $transaction->status_label }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <p class="small text-muted mb-1">Waktu</p>
                                            <p class="mb-0 fw-medium">{{ $transaction->created_at->format('d M Y H:i') }}
                                                WIB</p>
                                        </div>
                                        <div class="col-6 text-end">
                                            <p class="small text-muted mb-1">Poin</p>
                                            <p
                                                class="mb-0 fw-bold {{ $transaction->transaction_type == 'deposit' ? 'text-success' : ($transaction->transaction_type == 'redemption' ? 'text-primary' : 'text-danger') }}">
                                                {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->points) }}
                                            </p>
                                            @if (isset($transaction->cash_value) && $transaction->cash_value)
                                                <p class="mb-0 text-success small">Rp
                                                    {{ number_format($transaction->cash_value, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <p class="small text-muted mb-1">Deskripsi</p>
                                        <p class="mb-0">{{ $transaction->description }}</p>
                                    </div>

                                    <div class="mt-3 d-flex justify-content-end">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                            data-bs-toggle="modal" data-bs-target="#detailModal{{ $transaction->id }}">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if ($transactions->hasPages())
                    <div class="card-footer bg-transparent border-0 p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari
                                {{ $transactions->total() }} transaksi
                            </div>
                            <div>
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5">
                    <div class="empty-state-animation">
                        <i class="bi bi-inbox text-muted mb-3" style="font-size: 3.5rem;"></i>
                        <h4 class="text-muted mb-2">Belum Ada Transaksi</h4>
                        <p class="text-muted mb-4">Anda belum memiliki riwayat transaksi apapun</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="#" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-recycle me-1"></i> Tukar Sampah
                            </a>
                            <a href="#" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-cash-coin me-1"></i> Tukar Poin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modals for Transaction Details -->
    @foreach ($transactions as $transaction)
        <div class="modal fade" id="detailModal{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Detail Transaksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <!-- Transaction Type -->
                            <div class="col-12 mb-4">
                                <div class="d-flex align-items-center">
                                    @if ($transaction->transaction_type == 'deposit')
                                        <div class="icon-wrapper bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="bi bi-recycle text-white" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="ms-4">
                                            <h5 class="text-success mb-1">Tukar Sampah</h5>
                                            <p class="text-muted mb-0">Deposit poin dari penukaran sampah</p>
                                        </div>
                                    @elseif ($transaction->transaction_type == 'withdrawal')
                                        <div class="icon-wrapper bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="bi bi-arrow-up-circle text-white" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="ms-4">
                                            <h5 class="text-danger mb-1">Tarik Poin</h5>
                                            <p class="text-muted mb-0">Penarikan poin untuk keperluan tertentu</p>
                                        </div>
                                    @elseif($transaction->transaction_type == 'redemption')
                                        <div class="icon-wrapper bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="bi bi-cash-coin text-white" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="ms-4">
                                            <h5 class="text-primary mb-1">Tukar Poin ke Saldo</h5>
                                            <p class="text-muted mb-0">Menukar poin menjadi saldo uang tunai</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Transaction Details -->
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">Informasi Transaksi</h6>
                                <div class="info-list">
                                    <div class="info-item d-flex justify-content-between mb-2">
                                        <span class="text-muted">ID Transaksi:</span>
                                        <span class="fw-medium">#{{ $transaction->id }}</span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between mb-2">
                                        <span class="text-muted">Tanggal:</span>
                                        <span class="fw-medium">{{ $transaction->created_at->format('d M Y H:i') }}
                                            WIB</span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between mb-2">
                                        <span class="text-muted">Jumlah Poin:</span>
                                        <span
                                            class="fw-bold {{ $transaction->transaction_type == 'deposit' ? 'text-success' : ($transaction->transaction_type == 'redemption' ? 'text-primary' : 'text-danger') }}">
                                            {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->points) }}
                                        </span>
                                    </div>
                                    @if (isset($transaction->cash_value) && $transaction->cash_value)
                                        <div class="info-item d-flex justify-content-between mb-2">
                                            <span class="text-muted">Nilai Uang:</span>
                                            <span class="fw-medium text-success">Rp
                                                {{ number_format($transaction->cash_value, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    @if (isset($transaction->percentage_deposited) && $transaction->percentage_deposited)
                                        <div class="info-item d-flex justify-content-between mb-2">
                                            <span class="text-muted">Persentase:</span>
                                            <span class="fw-medium">{{ $transaction->percentage_deposited }}%</span>
                                        </div>
                                    @endif
                                    @if (isset($transaction->redemption_code))
                                        <div class="info-item d-flex justify-content-between mb-2">
                                            <span class="text-muted">Kode Penukaran:</span>
                                            <span
                                                class="fw-medium font-monospace">{{ $transaction->redemption_code }}</span>
                                        </div>
                                    @endif
                                    <div class="info-item d-flex justify-content-between">
                                        <span class="text-muted">Status:</span>
                                        <span class="badge {{ $transaction->status_badge_class }} rounded-pill">
                                            {{ $transaction->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Processing Details -->
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">Detail Pemrosesan</h6>
                                @if ($transaction->processed_by)
                                    <div class="info-list">
                                        <div class="info-item d-flex justify-content-between mb-2">
                                            <span class="text-muted">Diproses Oleh:</span>
                                            <span
                                                class="fw-medium">{{ $transaction->processed_by->name ?? 'System' }}</span>
                                        </div>
                                        @if ($transaction->processed_at)
                                            <div class="info-item d-flex justify-content-between mb-2">
                                                <span class="text-muted">Waktu Diproses:</span>
                                                <span
                                                    class="fw-medium">{{ $transaction->processed_at->format('d M Y H:i') }}
                                                    WIB</span>
                                            </div>
                                        @endif
                                        @if (isset($transaction->completed_at) && $transaction->completed_at)
                                            <div class="info-item d-flex justify-content-between">
                                                <span class="text-muted">Waktu Selesai:</span>
                                                <span
                                                    class="fw-medium">{{ $transaction->completed_at->format('d M Y H:i') }}
                                                    WIB</span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-clock text-warning mb-2" style="font-size: 2.5rem;"></i>
                                        <p class="text-muted mb-0">Transaksi belum diproses</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Description -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-semibold mb-2">Deskripsi</h6>
                                <div class="bg-light rounded-3 p-3">
                                    <p class="mb-0">{{ $transaction->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Tutup
                        </button>
                        @if ($transaction->transaction_type == 'redemption' && isset($transaction->redemption_code))
                            <button type="button" class="btn btn-primary rounded-pill px-4"
                                onclick="copyToClipboard('{{ $transaction->redemption_code }}')">
                                <i class="bi bi-clipboard me-1"></i>Salin Kode
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('styles')
    <style>
        /* Modern Animations and Effects */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes countUp {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .animate-slide-up {
            animation: slideInUp 0.4s ease-out;
        }

        /* Header Card Gradient */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }

        .bg-gradient-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }

        /* Stats Cards */
        .stats-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .card-gradient {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: inherit;
            opacity: 0.9;
        }

        .card-gradient.bg-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .card-gradient.bg-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .card-gradient.bg-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .card-gradient.bg-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .stats-card:hover .stats-icon {
            opacity: 1;
            transform: scale(1.1);
        }

        /* Icon Wrappers */
        .icon-wrapper {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        /* Hover Effects */
        .hover-scale {
            transition: all 0.2s ease;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }

        /* Card Enhancements */
        .transaction-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        .transaction-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: rgba(102, 126, 234, 0.3);
        }

        /* Filter Controls */
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Empty State Animation */
        .empty-state-animation {
            animation: pulse 2s infinite;
        }

        /* Modal Enhancements */
        .modal-content {
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .info-list .info-item {
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .info-list .info-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
            margin: 0 -12px;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .info-list .info-item:last-child {
            border-bottom: none;
        }

        /* Loading States */
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

        /* Counter Animation */
        .counter {
            transition: all 0.3s ease;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 15px;
            }

            .transaction-card {
                margin-bottom: 12px;
            }

            .header-card {
                text-align: center;
            }

            .header-card .position-absolute {
                opacity: 0.05;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .transaction-row:hover {
                background-color: rgba(255, 255, 255, 0.05);
            }

            .info-list .info-item:hover {
                background-color: rgba(255, 255, 255, 0.05);
            }
        }

        /* Smooth transitions for all interactive elements */
        * {
            transition-property: transform, box-shadow, background-color, border-color;
            transition-duration: 0.2s;
            transition-timing-function: ease;
        }

        /* Badge enhancements */
        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Button hover effects */
        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-light:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Counter animation
            function animateCounters() {
                const counters = document.querySelectorAll('.counter');

                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-count'));
                    const duration = 2000; // 2 seconds
                    const step = target / (duration / 16); // 60 FPS
                    let current = 0;

                    const updateCounter = () => {
                        current += step;
                        if (current < target) {
                            counter.textContent = Math.floor(current).toLocaleString();
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target.toLocaleString();
                        }
                    };

                    // Start animation with slight delay
                    setTimeout(updateCounter, Math.random() * 500);
                });
            }

            // Start counter animation
            animateCounters();

            // Filter functionality
            const statusFilter = document.getElementById('statusFilter');
            const typeFilter = document.getElementById('typeFilter');
            const resetFilters = document.getElementById('resetFilters');
            const table = document.getElementById('transactionsTable');
            const mobileCards = document.getElementById('transactionCards');

            function filterTransactions() {
                const statusValue = statusFilter.value.toLowerCase();
                const typeValue = typeFilter.value.toLowerCase();

                // Show loading state
                showLoadingState();

                setTimeout(() => {
                    // Filter desktop table
                    if (table) {
                        const rows = table.querySelectorAll('tbody tr:not(.no-results-row)');
                        let visibleCount = 0;

                        rows.forEach(row => {
                            const statusAttr = row.getAttribute('data-status')?.toLowerCase() || '';
                            const typeAttr = row.getAttribute('data-type')?.toLowerCase() || '';

                            const statusMatch = !statusValue || statusAttr.includes(statusValue
                                .replace('_', ' '));
                            const typeMatch = !typeValue || typeAttr === typeValue;

                            if (statusMatch && typeMatch) {
                                row.style.display = '';
                                row.style.animation = 'slideInUp 0.4s ease-out';
                                visibleCount++;
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        handleNoResults(table.querySelector('tbody'), visibleCount, 7, 'table');
                    }

                    // Filter mobile cards
                    if (mobileCards) {
                        const cards = mobileCards.querySelectorAll(
                            '.transaction-card:not(.no-results-card)');
                        let visibleCount = 0;

                        cards.forEach(card => {
                            const statusBadge = card.querySelector('.badge:last-of-type')
                                ?.textContent.trim().toLowerCase() || '';
                            const typeElement = card.querySelector('h6');
                            const typeText = typeElement ? typeElement.textContent.trim()
                                .toLowerCase() : '';

                            const statusMatch = !statusValue || statusBadge.includes(statusValue
                                .replace('_', ' '));
                            let typeMatch = false;

                            if (!typeValue) {
                                typeMatch = true;
                            } else if (typeValue === 'deposit') {
                                typeMatch = typeText.includes('tukar sampah');
                            } else if (typeValue === 'withdrawal') {
                                typeMatch = typeText.includes('tarik poin');
                            } else if (typeValue === 'redemption') {
                                typeMatch = typeText.includes('tukar ke saldo');
                            }

                            if (statusMatch && typeMatch) {
                                card.style.display = '';
                                card.style.animation = 'slideInUp 0.4s ease-out';
                                visibleCount++;
                            } else {
                                card.style.display = 'none';
                            }
                        });

                        handleNoResults(mobileCards, visibleCount, 1, 'mobile');
                    }

                    hideLoadingState();
                }, 300);
            }

            function showLoadingState() {
                // Add subtle loading indication
                document.body.style.cursor = 'wait';
            }

            function hideLoadingState() {
                document.body.style.cursor = 'default';
            }

            function handleNoResults(container, visibleCount, colspan, type) {
                let noResultsElement = container.querySelector('.no-results-row, .no-results-card');

                if (visibleCount === 0) {
                    if (!noResultsElement) {
                        if (type === 'table') {
                            noResultsElement = document.createElement('tr');
                            noResultsElement.className = 'no-results-row';
                            noResultsElement.innerHTML = `
                        <td colspan="${colspan}" class="text-center py-5">
                            <div class="empty-state-animation">
                                <i class="bi bi-search text-muted mb-3" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mb-2">Tidak Ada Hasil</h5>
                                <p class="text-muted mb-0">Tidak ada transaksi yang sesuai dengan filter yang dipilih</p>
                            </div>
                        </td>
                    `;
                        } else {
                            noResultsElement = document.createElement('div');
                            noResultsElement.className = 'no-results-card text-center py-5';
                            noResultsElement.innerHTML = `
                        <div class="empty-state-animation">
                            <i class="bi bi-search text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mb-2">Tidak Ada Hasil</h5>
                            <p class="text-muted mb-0">Tidak ada transaksi yang sesuai dengan filter</p>
                        </div>
                    `;
                        }
                        container.appendChild(noResultsElement);
                    }
                    noResultsElement.style.display = '';
                    noResultsElement.style.animation = 'fadeIn 0.5s ease-out';
                } else if (noResultsElement) {
                    noResultsElement.style.display = 'none';
                }
            }

            // Event listeners
            statusFilter.addEventListener('change', filterTransactions);
            typeFilter.addEventListener('change', filterTransactions);

            resetFilters.addEventListener('click', function() {
                statusFilter.value = '';
                typeFilter.value = '';
                filterTransactions();

                // Add visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });

            // Copy to clipboard function
            window.copyToClipboard = function(text) {
                navigator.clipboard.writeText(text).then(function() {
                    // Show success toast
                    showToast('Kode berhasil disalin!', 'success');
                }).catch(function() {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    showToast('Kode berhasil disalin!', 'success');
                });
            };

            // Toast notification function
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `toast-notification toast-${type}`;
                toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span>${message}</span>
            </div>
        `;

                // Add toast styles
                toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : '#3b82f6'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
        `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.animation = 'slideOutRight 0.3s ease-out';
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 300);
                }, 3000);
            }

            // Add slide animations for toast
            const style = document.createElement('style');
            style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
            document.head.appendChild(style);

            // Add loading states for buttons
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!this.classList.contains('no-loading')) {
                        const originalText = this.innerHTML;
                        this.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
                        this.disabled = true;

                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }, 1000);
                    }
                });
            });

            // Intersection Observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'slideInUp 0.6s ease-out';
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe elements for animation
            document.querySelectorAll('.transaction-card, .stats-card').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
@endpush
