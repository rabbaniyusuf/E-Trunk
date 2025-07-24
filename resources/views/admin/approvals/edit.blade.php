@extends('layouts.main')

@section('title', 'Detail Approval Poin')

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Detail Approval Poin</h1>
                <p class="text-muted mb-0">Detail permintaan penukaran poin</p>
            </div>
            <div>
                <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Transaction Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID Transaksi</label>
                                    <div class="form-control-plaintext">{{ $approval->id }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tipe Transaksi</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge bg-{{ $approval->getTypeColor() }}">
                                            {{ $approval->getTypeLabel() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jumlah Poin</label>
                                    <div class="form-control-plaintext">
                                        <span class="fw-bold text-primary fs-5">{{ number_format($approval->points) }}
                                            Poin</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge {{ $approval->getStatusBadgeClass() }} fs-6">
                                            {{ $approval->getStatusLabel() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <div class="form-control-plaintext">{{ $approval->description ?: 'Tidak ada deskripsi' }}</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Dibuat</label>
                                    <div class="form-control-plaintext">
                                        {{ $approval->created_at->format('d/m/Y H:i:s') }}
                                        <small
                                            class="text-muted d-block">{{ $approval->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            @if ($approval->processed_at)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tanggal Diproses</label>
                                        <div class="form-control-plaintext">
                                            {{ $approval->processed_at->format('d/m/Y H:i:s') }}
                                            <small
                                                class="text-muted d-block">{{ $approval->processed_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Collection Information -->
                @if ($approval->collectionRequest)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Collection</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Jenis Tempat Sampah</label>
                                        <div class="form-control-plaintext">
                                            <span class="badge bg-light text-dark">
                                                {{ $approval->collectionRequest->wasteBinType->name ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tipe Sampah</label>
                                        <div class="form-control-plaintext">
                                            {{ $approval->collectionRequest->getWasteTypesLabel() }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Tanggal Pickup</label>
                                        <div class="form-control-plaintext">
                                            {{ $approval->collectionRequest->pickup_date?->format('d/m/Y') ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Status Collection</label>
                                        <div class="form-control-plaintext">
                                            <span class="badge {{ $approval->collectionRequest->getStatusBadgeClass() }}">
                                                {{ $approval->collectionRequest->getStatusLabel() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- User Information & Actions -->
            <div class="col-lg-4">
                <!-- User Info -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi User</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div
                                class="avatar-lg bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-person fs-1"></i>
                            </div>
                            <h6 class="mb-1">{{ $approval->user->name }}</h6>
                            <small class="text-muted">{{ $approval->user->email }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Saldo Poin Saat Ini</label>
                            <div class="form-control-plaintext">
                                <span class="fw-bold text-success">{{ number_format($approval->user->balance) }}
                                    Poin</span>
                            </div>
                        </div>

                        @if ($approval->user->phone)
                            <div class="mb-3">
                                <label class="form-label fw-bold">No. Telepon</label>
                                <div class="form-control-plaintext">{{ $approval->user->phone }}</div>
                            </div>
                        @endif

                        @if ($approval->user->address)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Alamat</label>
                                <div class="form-control-plaintext">{{ $approval->user->address }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Processing Information -->
                @if ($approval->processedBy)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Pemroses</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Diproses Oleh</label>
                                <div class="form-control-plaintext">{{ $approval->processedBy->name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Diproses</label>
                                <div class="form-control-plaintext">
                                    {{ $approval->processed_at->format('d/m/Y H:i:s') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Actions -->
                @if ($approval->status === \App\Models\PointTransactions::STATUS_PENDING)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Aksi</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.approvals.update', $approval) }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="status" class="form-label fw-bold">Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="">Pilih Status</option>
                                        <option value="approved">Setujui</option>
                                        <option value="rejected">Tolak</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="admin_notes" class="form-label fw-bold">Catatan Admin</label>
                                    <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3"
                                        placeholder="Tambahkan catatan (opsional)..."></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Proses Approval
                                    </button>
                                </div>
                            </form>

                            <hr>

                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-success" onclick="quickAction('approved')"
                                    title="Quick Approve">
                                    <i class="bi bi-check"></i> Quick Approve
                                </button>
                                <button type="button" class="btn btn-danger" onclick="quickAction('rejected')"
                                    title="Quick Reject">
                                    <i class="bi bi-x"></i> Quick Reject
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Action Forms -->
    @if ($approval->status === \App\Models\PointTransactions::STATUS_PENDING)
        <form id="quickApprovalForm" method="POST" action="{{ route('admin.approvals.update', $approval) }}"
            style="display: none;">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="quickStatus">
        </form>
    @endif

@endsection

@push('scripts')
    <script>
        function quickAction(status) {
            const action = status === 'approved' ? 'menyetujui' : 'menolak';

            if (confirm(`Apakah Anda yakin ingin ${action} permintaan penukaran poin ini?`)) {
                document.getElementById('quickStatus').value = status;
                document.getElementById('quickApprovalForm').submit();
            }
        }

        // Show confirmation when form is submitted
        document.querySelector('form:not(#quickApprovalForm)')?.addEventListener('submit', function(e) {
            const status = document.getElementById('status').value;
            if (!status) {
                e.preventDefault();
                alert('Pilih status terlebih dahulu!');
                return;
            }

            const action = status === 'approved' ? 'menyetujui' : 'menolak';
            if (!confirm(`Apakah Anda yakin ingin ${action} permintaan penukaran poin ini?`)) {
                e.preventDefault();
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .avatar-lg {
            width: 64px;
            height: 64px;
        }

        .form-control-plaintext {
            padding: 0.375rem 0;
        }

        .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
    </style>
@endpush
