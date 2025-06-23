@extends('layouts.main')

@section('title', 'Approval Penukaran Poin')

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Approval Penukaran Poin</h1>
                <p class="text-muted mb-0">Kelola permintaan penukaran poin dari masyarakat</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card border-left-warning h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Menunggu Approval
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['pending']) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-clock-history fs-2 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card border-left-success h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Disetujui
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['approved']) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-check-circle fs-2 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card border-left-danger h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Ditolak
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['rejected']) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-x-circle fs-2 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="card border-left-info h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Poin Pending
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_points_pending']) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-coin fs-2 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.approvals.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui
                            </option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from" class="form-control"
                            value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date_to" class="form-control"
                            value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="search" class="form-label">Cari User</label>
                        <input type="text" name="search" id="search" class="form-control"
                            placeholder="Nama atau email..." value="{{ request('search') }}">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Approval Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Permintaan Penukaran Poin</h5>

                @if ($approvals->where('status', 'pending')->count() > 0)
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-check2-all"></i> Bulk Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <button type="button" class="dropdown-item" onclick="showBulkModal('approve')">
                                    <i class="bi bi-check-circle text-success"></i> Setujui Terpilih
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item" onclick="showBulkModal('reject')">
                                    <i class="bi bi-x-circle text-danger"></i> Tolak Terpilih
                                </button>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>

            <div class="card-body p-0">
                @if ($approvals->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    @if ($approvals->where('status', 'pending')->count() > 0)
                                        <th width="50">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                    @endif
                                    <th>User</th>
                                    <th>Jenis Sampah</th>
                                    <th>Poin</th>
                                    <th>Tanggal Request</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvals as $approval)
                                    <tr>
                                        @if ($approvals->where('status', 'pending')->count() > 0)
                                            <td>
                                                @if ($approval->status === 'pending')
                                                    <input type="checkbox" name="selected_approvals[]"
                                                        value="{{ $approval->id }}"
                                                        class="form-check-input approval-checkbox">
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $approval->user->name }}</div>
                                                    <small class="text-muted">{{ $approval->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $approval->wasteBinType->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">{{ number_format($approval->points) }}
                                                Poin</span>
                                        </td>
                                        <td>
                                            <div>{{ $approval->created_at->format('d/m/Y H:i') }}</div>
                                            <small class="text-muted">{{ $approval->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            @if ($approval->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($approval->status === 'approved')
                                                <span class="badge bg-success">Disetujui</span>
                                            @else
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.approvals.edit', $approval) }}"
                                                    class="btn btn-outline-primary" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                @if ($approval->status === 'pending')
                                                    <button type="button" class="btn btn-outline-success"
                                                        onclick="quickApprove({{ $approval->id }}, 'approved')"
                                                        title="Setujui">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger"
                                                        onclick="quickApprove({{ $approval->id }}, 'rejected')"
                                                        title="Tolak">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div class="text-muted">
                            Menampilkan {{ $approvals->firstItem() ?? 0 }} - {{ $approvals->lastItem() ?? 0 }}
                            dari {{ $approvals->total() }} data
                        </div>
                        {{ $approvals->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">Tidak ada data approval</h5>
                        <p class="text-muted">Belum ada permintaan penukaran poin yang perlu diproses.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    <div class="modal fade" id="bulkActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="bulkActionForm" method="POST" action="{{ route('admin.approvals.bulk-update') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkActionTitle">Bulk Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="bulk_action" id="bulkAction">
                        <div id="selectedItems"></div>

                        <div class="mb-3">
                            <label for="bulk_notes" class="form-label">Catatan Admin (Opsional)</label>
                            <textarea name="bulk_notes" id="bulk_notes" class="form-control" rows="3" placeholder="Tambahkan catatan..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <span id="bulkActionMessage"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn" id="bulkActionButton">Proses</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Approval Forms -->
    @foreach ($approvals->where('status', 'pending') as $approval)
        <form id="quickApprovalForm{{ $approval->id }}" method="POST"
            action="{{ route('admin.approvals.update', $approval) }}" style="display: none;">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="quickStatus{{ $approval->id }}">
        </form>
    @endforeach

@endsection

@push('scripts')
    <script>
        // Select All Functionality
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.approval-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Quick Approval
        function quickApprove(approvalId, status) {
            const action = status === 'approved' ? 'menyetujui' : 'menolak';

            if (confirm(`Apakah Anda yakin ingin ${action} permintaan penukaran poin ini?`)) {
                document.getElementById(`quickStatus${approvalId}`).value = status;
                document.getElementById(`quickApprovalForm${approvalId}`).submit();
            }
        }

        // Show Bulk Modal
        function showBulkModal(action) {
            const selectedCheckboxes = document.querySelectorAll('.approval-checkbox:checked');

            if (selectedCheckboxes.length === 0) {
                alert('Pilih minimal satu item untuk diproses.');
                return;
            }

            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
            const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));

            // Set action
            document.getElementById('bulkAction').value = action;

            // Set title and button
            const title = action === 'approve' ? 'Setujui Permintaan' : 'Tolak Permintaan';
            const buttonClass = action === 'approve' ? 'btn-success' : 'btn-danger';
            const message = action === 'approve' ?
                `Anda akan menyetujui ${selectedIds.length} permintaan penukaran poin.` :
                `Anda akan menolak ${selectedIds.length} permintaan penukaran poin.`;

            document.getElementById('bulkActionTitle').textContent = title;
            document.getElementById('bulkActionButton').className = `btn ${buttonClass}`;
            document.getElementById('bulkActionButton').textContent = action === 'approve' ? 'Setujui' : 'Tolak';
            document.getElementById('bulkActionMessage').textContent = message;

            // Add hidden inputs for selected IDs
            const selectedItemsDiv = document.getElementById('selectedItems');
            selectedItemsDiv.innerHTML = '';
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_approvals[]';
                input.value = id;
                selectedItemsDiv.appendChild(input);
            });

            modal.show();
        }

        // Auto-refresh every 30 seconds for pending approvals
        @if (request('status') === 'pending' || !request()->filled('status'))
            setInterval(function() {
                if (document.querySelector('.approval-checkbox:checked') === null) {
                    location.reload();
                }
            }, 30000);
        @endif
    </script>
@endpush

@push('styles')
    <style>
        .border-left-warning {
            border-left: 4px solid #f59e0b !important;
        }

        .border-left-success {
            border-left: 4px solid #10b981 !important;
        }

        .border-left-danger {
            border-left: 4px solid #ef4444 !important;
        }

        .border-left-info {
            border-left: 4px solid #3b82f6 !important;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #374151;
        }

        .btn-group-sm>.btn {
            --bs-btn-padding-y: 0.25rem;
            --bs-btn-padding-x: 0.5rem;
            --bs-btn-font-size: 0.75rem;
        }
    </style>
@endpush
