@extends('layouts.main')

@section('title', 'Laporan Transaksi - E-TRANK')

@section('content')
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div
                class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                <div>
                    <h1 class="h2 mb-2 text-primary fw-bold d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-file-earmark-bar-graph-fill text-primary fs-4"></i>
                        </div>
                        Laporan Transaksi
                    </h1>
                    <p class="text-muted mb-0 fs-6 ms-5 ms-lg-0 ps-2 ps-lg-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Generate dan download laporan transaksi poin dalam format PDF
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Generation Form -->
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-gear-fill me-2 text-primary"></i>Pengaturan Laporan
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports') }}" method="GET" id="reportForm">
                        <div class="row g-3">
                            <!-- Report Type -->
                            <div class="col-12">
                                <label for="report_type" class="form-label fw-semibold">
                                    <i class="bi bi-file-earmark-text me-1"></i>Jenis Laporan
                                </label>
                                <select name="report_type" id="report_type" class="form-select" required>
                                    <option value="">Pilih Jenis Laporan</option>
                                    <option value="transactions">Transaksi Poin (Menabung)</option>
                                    <option value="redemptions">Penukaran Poin</option>
                                    <option value="combined">Laporan Lengkap</option>
                                </select>
                            </div>

                            <!-- Date Range -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event me-1"></i>Tanggal Mulai
                                </label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check me-1"></i>Tanggal Selesai
                                </label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            <!-- User Filter -->
                            <div class="col-12">
                                <label for="user_id" class="form-label fw-semibold">
                                    <i class="bi bi-person me-1"></i>Filter User (Opsional)
                                </label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="">Semua User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-12" id="statusFilter" style="display: none;">
                                <label for="status" class="form-label fw-semibold">
                                    <i class="bi bi-funnel me-1"></i>Filter Status (Opsional)
                                </label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12">
                                <hr class="my-4">
                                <div class="d-flex flex-column flex-sm-row gap-2">
                                    {{-- <a href="{{ route('admin.reports.preview') }}" type="button" class="btn btn-outline-primary" onclick="previewReport()">
                                        <i class="bi bi-eye me-2"></i>Preview Laporan
                                    </a> --}}
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-download me-2"></i>Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-lightning-fill me-2 text-warning"></i>Laporan Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports', ['report_type' => 'combined', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}"
                            class="btn btn-outline-success">
                            <i class="bi bi-calendar-day me-2"></i>Laporan Hari Ini
                        </a>
                        <a href="{{ route('admin.reports', ['report_type' => 'combined', 'start_date' => date('Y-m-d', strtotime('monday this week')), 'end_date' => date('Y-m-d')]) }}"
                            class="btn btn-outline-info">
                            <i class="bi bi-calendar-week me-2"></i>Laporan Minggu Ini
                        </a>
                        <a href="{{ route('admin.reports', ['report_type' => 'combined', 'start_date' => date('Y-m-01'), 'end_date' => date('Y-m-d')]) }}"
                            class="btn btn-outline-primary">
                            <i class="bi bi-calendar-month me-2"></i>Laporan Bulan Ini
                        </a>
                        <a href="{{ route('admin.reports', ['report_type' => 'transactions', 'status' => 'MENUNGGU_DIAMBIL']) }}"
                            class="btn btn-outline-warning">
                            <i class="bi bi-hourglass-split me-2"></i>Transaksi Pending
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-graph-up me-2 text-success"></i>Statistik Singkat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="bg-primary bg-opacity-10 rounded p-2 mb-2">
                                <i class="bi bi-arrow-up-circle text-primary fs-4"></i>
                            </div>
                            <div class="fw-bold text-primary">{{ number_format(\App\Models\PointTransactions::count()) }}
                            </div>
                            <small class="text-muted">Total Transaksi</small>
                        </div>
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 rounded p-2 mb-2">
                                <i class="bi bi-arrow-down-circle text-success fs-4"></i>
                            </div>
                            <div class="fw-bold text-success">{{ number_format(\App\Models\PointRedemptions::count()) }}
                            </div>
                            <small class="text-muted">Total Penukaran</small>
                        </div>
                        <div class="col-6">
                            <div class="bg-warning bg-opacity-10 rounded p-2 mb-2">
                                <i class="bi bi-hourglass-split text-warning fs-4"></i>
                            </div>
                            <div class="fw-bold text-warning">
                                {{ number_format(\App\Models\PointTransactions::where('status', 'MENUNGGU_DIAMBIL')->count()) }}
                            </div>
                            <small class="text-muted">Pending</small>
                        </div>
                        <div class="col-6">
                            <div class="bg-info bg-opacity-10 rounded p-2 mb-2">
                                <i class="bi bi-people text-info fs-4"></i>
                            </div>
                            <div class="fw-bold text-info">{{ number_format(\App\Models\User::count()) }}</div>
                            <small class="text-muted">Total User</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reportTypeSelect = document.getElementById('report_type');
            const statusFilter = document.getElementById('statusFilter');
            const statusSelect = document.getElementById('status');

            // Status options for different report types
            const statusOptions = {
                transactions: [{
                        value: 'MENUNGGU_DIAMBIL',
                        text: 'Menunggu Diambil'
                    },
                    {
                        value: 'SUDAH_DIAMBIL',
                        text: 'Sudah Diambil'
                    },
                    {
                        value: 'GAGAL_DIAMBIL',
                        text: 'Gagal Diambil'
                    }
                ],
                redemptions: [{
                        value: 'pending',
                        text: 'Menunggu'
                    },
                    {
                        value: 'approved',
                        text: 'Disetujui'
                    },
                    {
                        value: 'completed',
                        text: 'Selesai'
                    },
                    {
                        value: 'cancelled',
                        text: 'Dibatalkan'
                    }
                ]
            };

            reportTypeSelect.addEventListener('change', function() {
                const reportType = this.value;

                if (reportType === 'transactions' || reportType === 'redemptions') {
                    statusFilter.style.display = 'block';
                    updateStatusOptions(reportType);
                } else {
                    statusFilter.style.display = 'none';
                    statusSelect.value = '';
                }
            });

            function updateStatusOptions(reportType) {
                // Clear existing options except the first one
                statusSelect.innerHTML = '<option value="">Semua Status</option>';

                if (statusOptions[reportType]) {
                    statusOptions[reportType].forEach(option => {
                        const optionElement = document.createElement('option');
                        optionElement.value = option.value;
                        optionElement.textContent = option.text;
                        statusSelect.appendChild(optionElement);
                    });
                }
            }

            // Date validation
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            startDateInput.addEventListener('change', function() {
                endDateInput.min = this.value;
            });

            endDateInput.addEventListener('change', function() {
                startDateInput.max = this.value;
            });
        });

        function previewReport() {
            const form = document.getElementById('reportForm');
            const formData = new FormData(form);
            formData.set('format', 'view');

            const params = new URLSearchParams(formData);
            const previewUrl = form.action + '?' + params.toString();

            window.open(previewUrl, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
        }

        // Form validation
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            const reportType = document.getElementById('report_type').value;

            if (!reportType) {
                e.preventDefault();
                alert('Silakan pilih jenis laporan terlebih dahulu.');
                return false;
            }

            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Generating PDF...';
            submitButton.disabled = true;

            // Re-enable button after 5 seconds (in case of slow download)
            setTimeout(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }, 5000);
        });
    </script>
@endpush
