@extends('layouts.main')

@section('title', 'Menabung Poin - E-TRANK')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="mb-0">Menabung Poin</h3>
                    <p class="text-muted mb-0">Tukar sampah Anda menjadi poin (1% = 1 poin)</p>
                </div>
            </div>

            @if (!$recycleBin && !$nonRecycleBin)
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Anda belum memiliki tempat sampah yang terdaftar. Silakan hubungi admin untuk registrasi kode tempat
                    sampah.
                </div>
            @elseif($availableRecyclePercentage == 0 && $availableNonRecyclePercentage == 0)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Tempat sampah Anda masih kosong. Silakan masukkan sampah terlebih dahulu.
                </div>
            @else
                <!-- Deposit Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-piggy-bank"></i>
                            Pilih Sampah untuk Ditukar
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="depositForm" action="{{ route('user.nabung.store') }}" method="POST">
                            @csrf

                            <!-- Waste Bin Selection -->
                            <div class="row g-3 mb-4">
                                @if ($recycleBin && $availableRecyclePercentage > 0)
                                    <div class="col-md-6">
                                        <div class="card waste-bin-option" data-bin-id="{{ $recycleBin->id }}">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                                        <i class="bi bi-recycle text-success"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">Sampah Daur Ulang</h6>
                                                        <small class="text-muted">Recycle</small>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="small">Tersedia</span>
                                                        <span
                                                            class="badge bg-success">{{ number_format($availableRecyclePercentage, 1) }}%</span>
                                                    </div>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-success"
                                                            style="width: {{ min($availableRecyclePercentage, 100) }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="waste_bin_type_id"
                                                        value="{{ $recycleBin->id }}" id="recycle_bin">
                                                    <label class="form-check-label" for="recycle_bin">
                                                        Pilih tempat sampah ini
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($nonRecycleBin && $availableNonRecyclePercentage > 0)
                                    <div class="col-md-6">
                                        <div class="card waste-bin-option" data-bin-id="{{ $nonRecycleBin->id }}">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                                        <i class="bi bi-trash text-warning"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">Sampah Non-Daur Ulang</h6>
                                                        <small class="text-muted">Non-Recycle</small>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="small">Tersedia</span>
                                                        <span
                                                            class="badge bg-warning">{{ number_format($availableNonRecyclePercentage, 1) }}%</span>
                                                    </div>
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-warning"
                                                            style="width: {{ min($availableNonRecyclePercentage, 100) }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="waste_bin_type_id"
                                                        value="{{ $nonRecycleBin->id }}" id="non_recycle_bin">
                                                    <label class="form-check-label" for="non_recycle_bin">
                                                        Pilih tempat sampah ini
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Percentage Input -->
                            <div id="percentageSection" class="d-none">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-percent"></i>
                                            Tentukan Persentase Sampah
                                        </h6>

                                        <div class="row align-items-end">
                                            <div class="col-md-6">
                                                <label for="percentage_to_deposit" class="form-label">
                                                    Persentase yang akan ditukar
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="percentage_to_deposit"
                                                        name="percentage_to_deposit" min="1" max="100"
                                                        step="0.1" placeholder="Masukkan persentase">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <div class="form-text">
                                                    Maksimal: <span id="maxPercentage">0</span>%
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="bg-white rounded p-3 border">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Poin yang didapat:</span>
                                                        <span class="fw-bold text-success" id="pointsEarned">0 poin</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Range Slider -->
                                        <div class="mt-3">
                                            <input type="range" class="form-range" id="percentageRange" min="0"
                                                max="100" step="0.1" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                                    <i class="bi bi-check-circle"></i>
                                    Ajukan Penukaran Poin
                                </button>
                                <small class="text-muted text-center">
                                    Setelah mengajukan, admin akan menjadwalkan petugas untuk mengambil sampah Anda.
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Information Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle text-info"></i>
                        Informasi Penting
                    </h6>
                    <ul class="mb-0 small text-muted">
                        <li>1% sampah = 1 poin</li>
                        <li>Setelah mengajukan penukaran, admin akan menjadwalkan petugas untuk mengambil sampah</li>
                        <li>Poin akan ditambahkan ke saldo Anda setelah petugas mengkonfirmasi pengambilan sampah</li>
                        <li>Pastikan sampah sudah dipisahkan sesuai jenisnya (daur ulang/non-daur ulang)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const wasteOptions = document.querySelectorAll('.waste-bin-option');
                const percentageSection = document.getElementById('percentageSection');
                const percentageInput = document.getElementById('percentage_to_deposit');
                const percentageRange = document.getElementById('percentageRange');
                const maxPercentageSpan = document.getElementById('maxPercentage');
                const pointsEarnedSpan = document.getElementById('pointsEarned');
                const submitBtn = document.getElementById('submitBtn');

                let selectedMaxPercentage = 0;

                // Handle waste bin selection
                wasteOptions.forEach(option => {
                    const radioInput = option.querySelector('input[type="radio"]');

                    option.addEventListener('click', function() {
                        radioInput.checked = true;
                        updateSelection();
                    });

                    radioInput.addEventListener('change', updateSelection);
                });

                function updateSelection() {
                    const selectedRadio = document.querySelector('input[name="waste_bin_type_id"]:checked');

                    if (selectedRadio) {
                        // Update visual selection
                        wasteOptions.forEach(opt => opt.classList.remove('border-primary'));
                        selectedRadio.closest('.waste-bin-option').classList.add('border-primary');

                        // Get max percentage from the selected option
                        const binId = selectedRadio.value;
                        @if ($recycleBin)
                            if (binId == '{{ $recycleBin->id }}') {
                                selectedMaxPercentage = {{ $availableRecyclePercentage }};
                            }
                        @endif
                        @if ($nonRecycleBin)
                            if (binId == '{{ $nonRecycleBin->id }}') {
                                selectedMaxPercentage = {{ $availableNonRecyclePercentage }};
                            }
                        @endif

                        // Update form constraints
                        percentageInput.max = selectedMaxPercentage;
                        percentageRange.max = selectedMaxPercentage;
                        maxPercentageSpan.textContent = selectedMaxPercentage.toFixed(1);

                        // Show percentage section
                        percentageSection.classList.remove('d-none');

                        // Reset values
                        percentageInput.value = '';
                        percentageRange.value = 0;
                        updatePoints();
                    }
                }

                // Sync input and range slider
                percentageInput.addEventListener('input', function() {
                    const value = parseFloat(this.value) || 0;
                    percentageRange.value = value;
                    updatePoints();
                });

                percentageRange.addEventListener('input', function() {
                    const value = parseFloat(this.value);
                    percentageInput.value = value.toFixed(1);
                    updatePoints();
                });

                function updatePoints() {
                    const percentage = parseFloat(percentageInput.value) || 0;
                    const points = Math.floor(percentage);

                    pointsEarnedSpan.textContent = points + ' poin';

                    // Enable/disable submit button
                    const selectedRadio = document.querySelector('input[name="waste_bin_type_id"]:checked');
                    submitBtn.disabled = !selectedRadio || percentage <= 0 || percentage > selectedMaxPercentage;
                }

                // Form validation
                document.getElementById('depositForm').addEventListener('submit', function(e) {
                    const selectedRadio = document.querySelector('input[name="waste_bin_type_id"]:checked');
                    const percentage = parseFloat(percentageInput.value) || 0;

                    if (!selectedRadio) {
                        e.preventDefault();
                        alert('Silakan pilih tempat sampah terlebih dahulu.');
                        return;
                    }

                    if (percentage <= 0) {
                        e.preventDefault();
                        alert('Persentase harus lebih dari 0.');
                        return;
                    }

                    if (percentage > selectedMaxPercentage) {
                        e.preventDefault();
                        alert(`Persentase tidak boleh melebihi ${selectedMaxPercentage.toFixed(1)}%.`);
                        return;
                    }

                    // Show confirmation
                    const binType = selectedRadio.closest('.waste-bin-option').querySelector('h6').textContent;
                    const points = Math.floor(percentage);

                    if (!confirm(
                            `Apakah Anda yakin ingin menukar ${percentage.toFixed(1)}% sampah ${binType} menjadi ${points} poin?`
                            )) {
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .waste-bin-option {
                cursor: pointer;
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }

            .waste-bin-option:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .waste-bin-option.border-primary {
                border-color: var(--primary-color) !important;
            }

            .form-range {
                height: 8px;
            }

            .form-range::-webkit-slider-thumb {
                height: 20px;
                width: 20px;
                background: var(--primary-color);
            }

            .form-range::-moz-range-thumb {
                height: 20px;
                width: 20px;
                background: var(--primary-color);
                border: none;
            }

            @media (max-width: 768px) {
                .card-body {
                    padding: 1rem;
                }

                .row.g-3 {
                    --bs-gutter-x: 1rem;
                    --bs-gutter-y: 1rem;
                }
            }
        </style>
    @endpush
@endsection
