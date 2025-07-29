@extends('layouts.main')
@section('title', 'Riwayat Transaksi - E-TRANK')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <h2 class="mb-1 d-flex align-items-center">
                            <i class="bi bi-clock-history text-primary me-2"></i>
                            Riwayat Transaksi
                        </h2>
                        <p class="text-muted mb-0">Kelola dan pantau aktivitas poin Anda</p>
                    </div>

                    <!-- Filter Controls -->
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <select class="form-select form-select-sm" id="statusFilter" style="min-width: 140px;">
                            <option value="">Semua Status</option>
                            <option value="MENUNGGU_DIAMBIL">Menunggu Diambil</option>
                            <option value="SUDAH_DIAMBIL">Sudah Diambil</option>
                            <option value="GAGAL_DIAMBIL">Gagal Diambil</option>
                            <option value="MENUNGGU_KONFIRMASI">Menunggu Konfirmasi</option>
                            <option value="DIKONFIRMASI">Dikonfirmasi</option>
                            <option value="DITOLAK">Ditolak</option>
                        </select>
                        <select class="form-select form-select-sm" id="typeFilter" style="min-width: 140px;">
                            <option value="">Semua Transaksi</option>
                            <option value="deposit">Tukar Sampah</option>
                            <option value="withdrawal">Tarik Poin</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if ($transactions->count() > 0)
            <!-- Statistics Overview -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="card h-100 border-0" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <div class="card-body text-white p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-1 small opacity-75">Total Deposit</p>
                                    <h5 class="mb-0 fw-bold">
                                        +{{ number_format($transactions->where('transaction_type', 'deposit')->sum('points')) }}
                                    </h5>
                                    <small class="opacity-75">poin</small>
                                </div>
                                <i class="bi bi-recycle fs-1 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="card h-100 border-0" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <div class="card-body text-white p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-1 small opacity-75">Total Penarikan</p>
                                    <h5 class="mb-0 fw-bold">
                                        -{{ number_format($transactions->where('transaction_type', 'withdrawal')->sum('points')) }}
                                    </h5>
                                    <small class="opacity-75">poin</small>
                                </div>
                                <i class="bi bi-cash-coin fs-1 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="card h-100 border-0" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                        <div class="card-body text-white p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-1 small opacity-75">Saldo Poin</p>
                                    <h5 class="mb-0 fw-bold">{{ number_format(auth()->user()->balance) }}</h5>
                                    <small class="opacity-75">tersedia</small>
                                </div>
                                <i class="bi bi-wallet2 fs-1 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="card h-100 border-0" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <div class="card-body text-white p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="mb-1 small opacity-75">Total Transaksi</p>
                                    <h5 class="mb-0 fw-bold">{{ $transactions->total() }}</h5>
                                    <small class="opacity-75">aktivitas</small>
                                </div>
                                <i class="bi bi-graph-up fs-1 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions List -->
            <div class="card">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0 d-flex align-items-center">
                        <i class="bi bi-list-ul text-primary me-2"></i>
                        Daftar Transaksi
                    </h6>
                </div>
                <div class="card-body p-0">
                    <!-- Desktop Table View -->
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="transactionsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4 py-3">Waktu</th>
                                        <th class="border-0 py-3">Jenis</th>
                                        <th class="border-0 py-3">Deskripsi</th>
                                        <th class="border-0 py-3 text-center">Poin</th>
                                        <th class="border-0 py-3 text-center">Status</th>
                                        <th class="border-0 py-3 text-center">Diproses</th>
                                        <th class="border-0 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $transaction)
                                        <tr class="border-bottom">
                                            <td class="px-4 py-3">
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="fw-semibold">{{ $transaction->created_at->format('d M Y') }}</span>
                                                    <small class="text-muted">{{ $transaction->created_at->format('H:i') }}
                                                        WIB</small>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                @if ($transaction->transaction_type == 'deposit')
                                                    <div class="d-flex align-items-center">
                                                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-2">
                                                            <i class="bi bi-recycle text-success"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-semibold text-success">Tukar Sampah</span>
                                                            <br><small class="text-muted">Deposit Poin</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-2">
                                                            <i class="bi bi-cash-coin text-danger"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-semibold text-danger">Tarik Poin</span>
                                                            <br><small class="text-muted">Redemption</small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <div>
                                                    <span>{{ $transaction->description }}</span>
                                                    @if ($transaction->percentage_deposited)
                                                        <br><small class="text-muted">
                                                            <i
                                                                class="bi bi-percent me-1"></i>{{ $transaction->percentage_deposited }}%
                                                            dari kapasitas
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 text-center">
                                                <span
                                                    class="badge fs-6 px-3 py-2 {{ $transaction->transaction_type == 'deposit' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->points) }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-center">
                                                <span class="badge {{ $transaction->getStatusBadgeClass() }} px-3 py-2">
                                                    {{ $transaction->getStatusLabel() }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-center">
                                                @if ($transaction->processedBy)
                                                    <div>
                                                        <span
                                                            class="fw-medium">{{ $transaction->processedBy->name }}</span>
                                                        @if ($transaction->processed_at)
                                                            <br><small
                                                                class="text-muted">{{ $transaction->processed_at->format('d M Y H:i') }}</small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">Belum diproses</span>
                                                @endif
                                            </td>
                                            <td class="py-3 text-center">
                                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#detailModal{{ $transaction->id }}">
                                                    <i class="bi bi-eye me-1"></i>Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-lg-none">
                        <div id="transactionCards">
                            @foreach ($transactions as $transaction)
                                <div class="transaction-card border-bottom p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            @if ($transaction->transaction_type == 'deposit')
                                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                                    <i class="bi bi-recycle text-success"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-success">Tukar Sampah</h6>
                                                    <small class="text-muted">Deposit Poin</small>
                                                </div>
                                            @else
                                                <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3">
                                                    <i class="bi bi-cash-coin text-danger"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-danger">Tarik Poin</h6>
                                                    <small class="text-muted">Redemption</small>
                                                </div>
                                            @endif
                                        </div>
                                        <span
                                            class="badge {{ $transaction->transaction_type == 'deposit' ? 'bg-success' : 'bg-danger' }} fs-6">
                                            {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->points) }}
                                        </span>
                                    </div>

                                    <div class="mb-2">
                                        <p class="mb-1 small">{{ $transaction->description }}</p>
                                        @if ($transaction->percentage_deposited)
                                            <small class="text-muted">
                                                <i
                                                    class="bi bi-percent me-1"></i>{{ $transaction->percentage_deposited }}%
                                                dari kapasitas
                                            </small>
                                        @endif
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small
                                                class="text-muted d-block">{{ $transaction->created_at->format('d M Y H:i') }}
                                                WIB</small>
                                            <span class="badge {{ $transaction->getStatusBadgeClass() }} mt-1">
                                                {{ $transaction->getStatusLabel() }}
                                            </span>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#detailModal{{ $transaction->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                @if ($transactions->hasPages())
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-center">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Empty State -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body py-5">
                            <div class="mb-4">
                                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-muted mb-3">Belum Ada Transaksi</h4>
                            <p class="text-muted mb-4">
                                Anda belum memiliki riwayat transaksi poin. Mulai dengan menukar sampah atau menarik poin
                                Anda.
                            </p>
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                                    <i class="bi bi-recycle me-2"></i>Tukar Sampah
                                </a>
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="bi bi-cash-coin me-2"></i>Tarik Poin
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Detail Modals -->
    @foreach ($transactions as $transaction)
        <div class="modal fade" id="detailModal{{ $transaction->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-receipt text-primary me-2"></i>
                            Detail Transaksi #{{ $transaction->id }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <!-- Transaction Type Header -->
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center py-4">
                                        @if ($transaction->transaction_type == 'deposit')
                                            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                                                style="width: 80px; height: 80px;">
                                                <i class="bi bi-recycle text-success" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="text-success mb-2">Tukar Sampah ke Poin</h5>
                                            <p class="text-muted mb-0">Menukar sampah menjadi poin yang dapat digunakan</p>
                                        @else
                                            <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                                                style="width: 80px; height: 80px;">
                                                <i class="bi bi-cash-coin text-danger" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="text-danger mb-2">Tarik Poin</h5>
                                            <p class="text-muted mb-0">Menukar poin menjadi reward atau uang tunai</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Transaction Details -->
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">Informasi Transaksi</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">ID Transaksi:</span>
                                    <span class="fw-medium">#{{ $transaction->id }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Tanggal:</span>
                                    <span class="fw-medium">{{ $transaction->created_at->format('d M Y H:i') }} WIB</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Jumlah Poin:</span>
                                    <span
                                        class="fw-bold {{ $transaction->transaction_type == 'deposit' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->transaction_type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->points) }}
                                    </span>
                                </div>
                                @if ($transaction->percentage_deposited)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Persentase:</span>
                                        <span class="fw-medium">{{ $transaction->percentage_deposited }}%</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status:</span>
                                    <span class="badge {{ $transaction->getStatusBadgeClass() }}">
                                        {{ $transaction->getStatusLabel() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Processing Details -->
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">Detail Pemrosesan</h6>
                                @if ($transaction->processedBy)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Diproses Oleh:</span>
                                        <span class="fw-medium">{{ $transaction->processedBy->name }}</span>
                                    </div>
                                    @if ($transaction->processed_at)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Waktu Diproses:</span>
                                            <span class="fw-medium">{{ $transaction->processed_at->format('d M Y H:i') }}
                                                WIB</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-3">
                                        <i class="bi bi-clock text-warning mb-2" style="font-size: 2rem;"></i>
                                        <p class="text-muted mb-0">Transaksi belum diproses</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <h6 class="fw-semibold mb-2">Deskripsi</h6>
                                <div class="bg-light rounded p-3">
                                    <p class="mb-0">{{ $transaction->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const typeFilter = document.getElementById('typeFilter');
            const table = document.getElementById('transactionsTable');
            const mobileCards = document.getElementById('transactionCards');

            function filterTransactions() {
                const statusValue = statusFilter.value.toLowerCase();
                const typeValue = typeFilter.value.toLowerCase();

                // Filter desktop table
                if (table) {
                    const rows = table.querySelectorAll('tbody tr');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        if (row.classList.contains('no-results-row')) return;

                        const statusCell = row.cells[4].textContent.trim().toLowerCase();
                        const typeCell = row.cells[1].textContent.trim().toLowerCase();

                        const statusMatch = !statusValue || statusCell.includes(statusValue.replace('_',
                            ' '));
                        const typeMatch = !typeValue || typeCell.includes(typeValue === 'deposit' ?
                            'tukar sampah' : 'tarik poin');

                        if (statusMatch && typeMatch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Handle no results for table
                    handleNoResults(table.querySelector('tbody'), visibleCount, 7);
                }

                // Filter mobile cards
                if (mobileCards) {
                    const cards = mobileCards.querySelectorAll('.transaction-card');
                    let visibleCount = 0;

                    cards.forEach(card => {
                        if (card.classList.contains('no-results-card')) return;

                        const statusBadge = card.querySelector('.badge:last-of-type').textContent.trim()
                            .toLowerCase();
                        const typeText = card.querySelector('h6').textContent.trim().toLowerCase();

                        const statusMatch = !statusValue || statusBadge.includes(statusValue.replace('_',
                            ' '));
                        const typeMatch = !typeValue || typeText.includes(typeValue === 'deposit' ?
                            'tukar sampah' : 'tarik poin');

                        if (statusMatch && typeMatch) {
                            card.style.display = '';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // Handle no results for mobile cards
                    handleNoResults(mobileCards, visibleCount, 1);
                }
            }

            function handleNoResults(container, visibleCount, colspan) {
                let noResultsElement = container.querySelector('.no-results-row, .no-results-card');

                if (visibleCount === 0) {
                    if (!noResultsElement) {
                        if (container.tagName === 'TBODY') {
                            noResultsElement = document.createElement('tr');
                            noResultsElement.className = 'no-results-row';
                            noResultsElement.innerHTML = `
                        <td colspan="${colspan}" class="text-center py-5">
                            <i class="bi bi-search text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0">Tidak ada transaksi yang sesuai dengan filter</p>
                        </td>
                    `;
                        } else {
                            noResultsElement = document.createElement('div');
                            noResultsElement.className = 'no-results-card text-center py-5';
                            noResultsElement.innerHTML = `
                        <i class="bi bi-search text-muted mb-2" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0">Tidak ada transaksi yang sesuai dengan filter</p>
                    `;
                        }
                        container.appendChild(noResultsElement);
                    }
                    noResultsElement.style.display = '';
                } else if (noResultsElement) {
                    noResultsElement.style.display = 'none';
                }
            }

            statusFilter.addEventListener('change', filterTransactions);
            typeFilter.addEventListener('change', filterTransactions);
        });
    </script>
@endpush
