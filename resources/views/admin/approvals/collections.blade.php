@extends('layouts.main')

@section('title', 'Collection Selesai')

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Collection Sampah Selesai</h1>
                <p class="text-muted mb-0">Daftar pengumpulan sampah yang sudah selesai dan siap untuk diberi poin</p>
            </div>
            <div>
                <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Approval
                </a>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.approvals.collections') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from" class="form-control"
                            value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date_to" class="form-control"
                            value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari User</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Nama atau email..." value="{{ request('search') }}">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.approvals.collections') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Collections Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Collection Selesai</h5>
            </div>

            <div class="card-body p-0">
                @if ($collections->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Jenis Sampah</th>
                                    <th>Tipe Sampah</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($collections as $collection)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $collection->user->name }}</div>
                                                    <small class="text-muted">{{ $collection->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $collection->wasteBinType->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $collection->getWasteTypesLabel() }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $collection->completed_at?->format('d/m/Y H:i') ?? 'N/A' }}</div>
                                            <small
                                                class="text-muted">{{ $collection->completed_at?->diffForHumans() ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $collection->getStatusBadgeClass() }}">
                                                {{ $collection->getStatusLabel() }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="showPointModal({{ $collection->id }}, '{{ $collection->user->name }}', '{{ $collection->wasteBinType->name ?? 'N/A' }}')"
                                                title="Buat Transaksi Poin">
                                                <i class="bi bi-coin"></i> Beri Poin
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div class="text-muted">
                            Menampilkan {{ $collections->firstItem() ?? 0 }} - {{ $collections->lastItem() ?? 0 }}
                            dari {{ $collections->total() }} data
                        </div>
                        {{ $collections->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-collection display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">Tidak ada collection selesai</h5>
                        <p class="text-muted">Belum ada pengumpulan sampah yang selesai dan siap untuk diberi poin.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Point Transaction Modal -->
    <div class="modal fade" id="pointModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="pointForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Transaksi Poin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <input type="text" id="userName" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Sampah</label>
                            <input type="text" id="wasteType" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="points" class="form-label">Jumlah Poin <span class="text-danger">*</span></label>
                            <input type="number" name="points" id="points" class="form-control" min="1"
                                max="1000" placeholder="Masukkan jumlah poin..." required>
                            <small class="text-muted">Maksimal 1000 poin per transaksi</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi (Opsional)</label>
                            <textarea name="description" id="description" class="form-control" rows="3"
                                placeholder="Tambahkan deskripsi transaksi..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Transaksi poin yang dibuat akan masuk ke daftar approval dan perlu disetujui terlebih dahulu
                            sebelum poin ditambahkan ke saldo user.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Buat Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function showPointModal(collectionId, userName, wasteType) {
            const modal = new bootstrap.Modal(document.getElementById('pointModal'));

            // Set form action
            document.getElementById('pointForm').action = `/admin/approvals/collections/${collectionId}/create-point`;

            // Fill readonly fields
            document.getElementById('userName').value = userName;
            document.getElementById('wasteType').value = wasteType;

            // Clear form fields
            document.getElementById('points').value = '';
            document.getElementById('description').value = '';

            modal.show();
        }

        // Auto-focus points field when modal is shown
        document.getElementById('pointModal').addEventListener('shown.bs.modal', function() {
            document.getElementById('points').focus();
        });
    </script>
@endpush

@push('styles')
    <style>
        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #374151;
        }
    </style>
@endpush
