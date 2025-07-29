@extends('layouts.main')

@section('title', 'Riwayat Penukaran Poin - E-TRANK')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h3 class="mb-0">Riwayat Penukaran Poin</h3>
                        <p class="text-muted mb-0">Lihat semua riwayat penukaran poin Anda</p>
                    </div>
                </div>
                <a href="{{ route('user.tukar-poin') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Tukar Poin Baru
                </a>
            </div>

            @if ($redemptions->count() > 0)
                <!-- Redemptions List -->
                <div class="row g-4">
                    @foreach ($redemptions as $redemption)
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-2"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="bi bi-cash text-primary" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div class="small fw-bold">{{ $redemption->redemption_code }}</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <small class="text-muted">Poin Ditukar</small>
                                                        <div class="fw-bold text-primary">
                                                            {{ number_format($redemption->points_redeemed) }} poin</div>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">Tanggal Pengajuan</small>
                                                        <div class="fw-bold">
                                                            {{ $redemption->created_at->format('d/m/Y H:i') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-2">
                                                        <small class="text-muted">Nilai Tukar</small>
                                                        <div class="fw-bold text-success">Rp
                                                            {{ number_format($redemption->cash_value, 0, ',', '.') }}</div>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">Status</small>
                                                        <div>
                                                            <span class="badge {{ $redemption->status_badge }}">
                                                                {{ $redemption->status_text }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 text-md-end">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('user.tukar-poin.bukti', $redemption->id) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-receipt"></i>
                                                    Lihat Bukti
                                                </a>

                                                @if ($redemption->status === 'pending')
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="cancelRedemption({{ $redemption->id }})">
                                                        <i class="bi bi-x-circle"></i>
                                                        Batal
                                                    </button>
                                                @endif
                                            </div>

                                            @if ($redemption->status === 'approved')
                                                <div class="mt-2">
                                                    <small class="text-info">
                                                        <i class="bi bi-info-circle"></i>
                                                        Siap untuk diambil
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $redemptions->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted">Belum Ada Riwayat Penukaran</h4>
                    <p class="text-muted mb-4">Anda belum pernah melakukan penukaran poin</p>
                    <a href="{{ route('user.tukar-poin') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i>
                        Mulai Tukar Poin
                    </a>
                </div>
            @endif

            <!-- Summary Card -->
            @if ($redemptions->count() > 0)
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-graph-up"></i>
                            Ringkasan Penukaran
                        </h6>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <div class="h4 mb-0 text-primary">
                                        {{ $redemptions->where('status', 'completed')->count() }}</div>
                                    <small class="text-muted">Selesai</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <div class="h4 mb-0 text-info">{{ $redemptions->where('status', 'approved')->count() }}
                                    </div>
                                    <small class="text-muted">Disetujui</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <div class="h4 mb-0 text-warning">
                                        {{ $redemptions->where('status', 'pending')->count() }}</div>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-2">
                                    <div class="h4 mb-0 text-success">
                                        Rp
                                        {{ number_format($redemptions->where('status', 'completed')->sum('cash_value'), 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">Total Diterima</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function cancelRedemption(redemptionId) {
                if (confirm('Apakah Anda yakin ingin membatalkan penukaran ini? Poin akan dikembalikan ke saldo Anda.')) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/user/tukar-poin/${redemptionId}/cancel`;

                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfToken);

                    // Add method spoofing for DELETE
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
    @endpush

    @push('styles')
        <style>
            .card:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                transition: box-shadow 0.3s ease;
            }

            @media (max-width: 768px) {
                .btn-group {
                    width: 100%;
                }

                .btn-group .btn {
                    flex: 1;
                }
            }
        </style>
    @endpush
@endsection

