@extends('layouts.main')

@section('title', 'Kelola Akun User - E-TRANK')

@section('content')
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div
                class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 mb-2 text-primary fw-bold d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-people-fill text-primary fs-4"></i>
                        </div>
                        Kelola Akun User
                    </h1>
                    <p class="text-muted mb-0 fs-6 ms-5 ms-lg-0 ps-2 ps-lg-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Daftar dan kelola akun masyarakat yang terdaftar di sistem E-TRANK
                    </p>
                </div>
                <div class="align-self-stretch align-self-lg-auto">
                    <a href="{{ route('admin.users.create') }}"
                        class="btn btn-primary d-flex align-items-center justify-content-center w-100 shadow-sm">
                        <i class="bi bi-person-plus me-2"></i>
                        <span class="d-none d-sm-inline">Tambah User Baru</span>
                        <span class="d-sm-none">Tambah User</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body p-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-people text-primary fs-5"></i>
                    </div>
                    <h5 class="card-title text-primary fw-bold mb-1 fs-4">{{ $users->total() }}</h5>
                    <p class="card-text text-muted mb-0 small">Total User</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body p-3">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-check-circle text-success fs-5"></i>
                    </div>
                    <h5 class="card-title text-success fw-bold mb-1 fs-4">
                        {{ $users->where('waste_bin_code', '!=', null)->count() }}
                    </h5>
                    <p class="card-text text-muted mb-0 small">User Aktif</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body p-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-exclamation-triangle text-warning fs-5"></i>
                    </div>
                    <h5 class="card-title text-warning fw-bold mb-1 fs-4">
                        {{ $users->where('waste_bin_code', null)->count() }}
                    </h5>
                    <p class="card-text text-muted mb-0 small">Belum Lengkap</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 text-center">
                <div class="card-body p-3">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 48px; height: 48px;">
                        <i class="bi bi-calendar-plus text-info fs-5"></i>
                    </div>
                    <h5 class="card-title text-info fw-bold mb-1 fs-4">
                        {{ $users->where('created_at', today())->count() }}
                    </h5>
                    <p class="card-text text-muted mb-0 small">Hari Ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div
                        class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
                        <h5 class="card-title mb-0 fw-semibold d-flex align-items-center">
                            <i class="bi bi-table me-2 text-primary"></i>Daftar User Terdaftar
                        </h5>
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-list-ul me-2"></i>
                            <span>{{ $users->count() }} dari {{ $users->total() }} user</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if ($users->count() > 0)
                        <!-- Mobile Cards View (visible on small screens) -->
                        <div class="d-md-none">
                            @foreach ($users as $index => $user)
                                <div class="border-bottom p-3">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <h6 class="fw-semibold text-dark mb-1">{{ $user->name }}</h6>
                                            <div class="small text-muted mb-2">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $user->created_at->format('d M Y') }}
                                            </div>
                                            <div class="small text-muted mb-1">
                                                <i class="bi bi-envelope me-1"></i>
                                                <span class="text-break">{{ $user->email }}</span>
                                            </div>
                                            <div class="small text-muted">
                                                <i class="bi bi-telephone me-1"></i>
                                                {{ $user->phone }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="small text-muted mb-1">Lokasi:</div>
                                            <div class="fw-medium small">{{ $user->district }}</div>
                                            <div class="small text-muted">{{ $user->postal_code }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="small text-muted mb-1">Saldo:</div>
                                            <div class="fw-bold text-success">{{ number_format($user->balance, 0) }} poin
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            @if ($user->wasteBin)
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 small">
                                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1 small">
                                                    <i class="bi bi-clock me-1"></i>Belum Lengkap
                                                </span>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $user->id }}"
                                        action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table View (hidden on small screens) -->
                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 fw-semibold">#</th>
                                            <th scope="col" class="px-4 py-3 fw-semibold">User</th>
                                            <th scope="col" class="px-4 py-3 fw-semibold">Kontak</th>
                                            <th scope="col" class="px-4 py-3 fw-semibold">Alamat</th>
                                            <th scope="col" class="px-4 py-3 fw-semibold">Tempat Sampah</th>
                                            <th scope="col" class="px-4 py-3 fw-semibold text-center">Saldo Poin</th>
                                            <th scope="col" class="px-4 py-3 fw-semibold text-center">Status</th>
                                            <th scope="col" class="px-4 py-3 fw-semibold text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $index => $user)
                                            <tr class="border-bottom">
                                                <td class="px-4 py-3">
                                                    <span
                                                        class="fw-medium text-muted">{{ $users->firstItem() + $index }}</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3"
                                                            style="width: 45px; height: 45px;">
                                                            <i class="bi bi-person text-primary fs-5"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold text-dark mb-1">{{ $user->name }}
                                                            </div>
                                                            <small class="text-muted">
                                                                <i class="bi bi-calendar3 me-1"></i>
                                                                Bergabung {{ $user->created_at->format('d M Y') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div>
                                                        <div class="fw-medium text-dark mb-1">
                                                            <i class="bi bi-envelope me-1 text-primary"></i>
                                                            {{ $user->email }}
                                                        </div>
                                                        <small class="text-muted">
                                                            <i class="bi bi-telephone me-1"></i>
                                                            {{ $user->phone }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div>
                                                        <div class="fw-medium text-dark mb-1">
                                                            <i class="bi bi-geo-alt me-1 text-danger"></i>
                                                            {{ $user->district }}
                                                        </div>
                                                        <small
                                                            class="text-muted d-block mb-1">{{ Str::limit($user->address, 35) }}</small>
                                                        <small class="text-muted">
                                                            <i class="bi bi-mailbox me-1"></i>
                                                            {{ $user->postal_code }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if ($user->wasteBin)
                                                        <div class="text-center">
                                                            <span
                                                                class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                                                                <i class="bi bi-trash3 me-1"></i>
                                                                {{ $user->waste_bin_code }}
                                                            </span>
                                                            <div class="mt-1">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-geo-alt me-1"></i>
                                                                    {{ $user->wasteBin->location }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-center">
                                                            <span
                                                                class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill">
                                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                                Belum Terdaftar
                                                            </span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="bg-success bg-opacity-10 rounded-circle p-2 mb-1">
                                                            <i class="bi bi-coin text-success"></i>
                                                        </div>
                                                        <div class="fw-bold text-success fs-6">
                                                            {{ number_format($user->balance, 0) }}
                                                        </div>
                                                        <small class="text-muted">poin</small>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if ($user->wasteBin)
                                                        <span class="badge bg-success px-3 py-2 rounded-pill">
                                                            <i class="bi bi-check-circle me-1"></i>
                                                            Aktif
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning px-3 py-2 rounded-pill">
                                                            <i class="bi bi-clock me-1"></i>
                                                            Belum Lengkap
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        title="Hapus User"
                                                        onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                    <form id="delete-form-{{ $user->id }}"
                                                        action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                        class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if ($users->hasPages())
                            <div class="card-footer bg-white border-0 py-3">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                                    <div class="text-muted small order-2 order-md-1">
                                        Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }}
                                        dari {{ $users->total() }} user
                                    </div>
                                    <div class="order-1 order-md-2">
                                        {{ $users->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px;">
                                    <i class="bi bi-people display-6 text-muted"></i>
                                </div>
                            </div>
                            <h4 class="text-muted mb-2">Belum Ada User Terdaftar</h4>
                            <p class="text-muted mb-4 px-3">Mulai dengan menambahkan user pertama untuk sistem E-TRANK</p>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus me-2"></i>Tambah User Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(userId, userName) {
            const result = confirm(
                `⚠️ Konfirmasi Penghapusan\n\nApakah Anda yakin ingin menghapus user "${userName}"?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data yang terkait dengan user tersebut.`
            );

            if (result) {
                // Show loading state
                const button = event.target.closest('button');
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                button.disabled = true;

                // Submit form
                document.getElementById('delete-form-' + userId).submit();
            }
        }

        // Add smooth hover effects for desktop table
        document.addEventListener('DOMContentLoaded', function() {
            const tableRows = document.querySelectorAll('.d-none.d-md-block tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>
@endpush
