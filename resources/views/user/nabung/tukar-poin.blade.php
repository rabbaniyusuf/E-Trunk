@extends('layouts.main')

@section('title', 'Tukar Poin - E-TRANK')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="mb-0">Tukar Poin</h3>
                    <p class="text-muted mb-0">Tukar poin Anda dengan uang tunai atau donasi</p>
                </div>
            </div>

            <!-- Current Points Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-wallet2 text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="text-start">
                                    <h5 class="mb-0">Saldo Poin Anda</h5>
                                    <h2 class="text-primary mb-0">{{ number_format($currentPoints) }} <small>poin</small>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <div class="bg-light rounded p-3">
                                <div class="small text-muted mb-1">Nilai Tukar</div>
                                <div class="fw-bold">20 poin = Rp 1.000</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($currentPoints < 20)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Anda memerlukan minimal 20 poin untuk melakukan penukaran. Saldo poin Anda saat ini:
                    {{ number_format($currentPoints) }} poin.
                </div>
            @elseif(empty($availableOptions))
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Poin Anda tidak mencukupi untuk melakukan penukaran dengan kelipatan yang tersedia.
                </div>
            @else
                <!-- Redemption Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-cash-coin"></i>
                            Pilih Jumlah Poin untuk Ditukar
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="redemptionForm" action="{{ route('user.points.redemption.process') }}" method="POST">
                            @csrf

                            <!-- Points Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Pilih jumlah poin:</label>
                                <div class="row g-3">
                                    @foreach ($availableOptions as $points)
                                        @php
                                            $cashValue = ($points / 20) * 1000;
                                        @endphp
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card point-option" data-points="{{ $points }}">
                                                <div class="card-body text-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="points_to_redeem" value="{{ $points }}"
                                                            id="points_{{ $points }}">
                                                        <label class="form-check-label w-100"
                                                            for="points_{{ $points }}">
                                                            <div class="fw-bold text-primary mb-1">{{ $points }} poin
                                                            </div>
                                                            <div class="text-success">Rp
                                                                {{ number_format($cashValue, 0, ',', '.') }}</div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Redemption Type -->
                            <div id="redemptionTypeSection" class="d-none mb-4">
                                <label class="form-label fw-bold">Pilih jenis penukaran:</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card redemption-type-option" data-type="cash">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="redemption_type"
                                                        value="cash" id="type_cash">
                                                    <label class="form-check-label w-100" for="type_cash">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                                                <i class="bi bi-cash text-success"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold">Uang Tunai</div>
                                                                <div class="small text-muted">Petugas akan mengantarkan
                                                                    tunai ke alamat Anda</div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card redemption-type-option" data-type="donation">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="redemption_type"
                                                        value="donation" id="type_donation">
                                                    <label class="form-check-label w-100" for="type_donation">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                                                <i class="bi bi-heart text-info"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold">Donasi</div>
                                                                <div class="small text-muted">Sumbangkan untuk program
                                                                    lingkungan</div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div id="summarySection" class="d-none mb-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-receipt"></i>
                                            Ringkasan Penukaran
                                        </h6>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="d-flex justify-content-between">
                                                    <span>Poin yang ditukar:</span>
                                                    <span class="fw-bold" id="summaryPoints">0 poin</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="d-flex justify-content-between">
                                                    <span>Nilai tukar:</span>
                                                    <span class="fw-bold text-success" id="summaryCashValue">Rp 0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <span>Jenis penukaran:</span>
                                            <span class="fw-bold" id="summaryType">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Sisa poin setelah tukar:</span>
                                            <span class="fw-bold text-primary"
                                                id="summaryRemainingPoints">{{ number_format($currentPoints) }}
                                                poin</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    <i class="bi bi-check-circle"></i>
                                    Ajukan Penukaran
                                </button>
                                <small class="text-muted text-center">
                                    Setelah mengajukan, admin akan memproses permintaan penukaran Anda.
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
                        Informasi Penukaran Poin
                    </h6>
                    <ul class="mb-0 small text-muted">
                        <li>Penukaran poin hanya tersedia dalam kelipatan: 20, 40, 60, 80, dan 100 poin</li>
                        <li>Nilai tukar: 20 poin = Rp 1.000</li>
                        <li>Untuk penukaran tunai, petugas akan mengantarkan ke alamat yang terdaftar</li>
                        <li>Untuk donasi, nilai akan disumbangkan untuk program lingkungan</li>
                        <li>Proses penukaran memerlukan konfirmasi admin terlebih dahulu</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const pointOptions = document.querySelectorAll('.point-option');
                const redemptionTypeOptions = document.querySelectorAll('.redemption-type-option');
                const redemptionTypeSection = document.getElementById('redemptionTypeSection');
                const summarySection = document.getElementById('summarySection');
                const submitBtn = document.getElementById('submitBtn');

                const summaryPoints = document.getElementById('summaryPoints');
                const summaryCashValue = document.getElementById('summaryCashValue');
                const summaryType = document.getElementById('summaryType');
                const summaryRemainingPoints = document.getElementById('summaryRemainingPoints');

                const currentPoints = {{ $currentPoints }};
                let selectedPoints = 0;
                let selectedType = '';

                // Handle point selection
                pointOptions.forEach(option => {
                    const radioInput = option.querySelector('input[type="radio"]');

                    option.addEventListener('click', function() {
                        radioInput.checked = true;
                        updatePointSelection();
                    });

                    radioInput.addEventListener('change', updatePointSelection);
                });

                // Handle redemption type selection
                redemptionTypeOptions.forEach(option => {
                    const radioInput = option.querySelector('input[type="radio"]');

                    option.addEventListener('click', function() {
                        radioInput.checked = true;
                        updateTypeSelection();
                    });

                    radioInput.addEventListener('change', updateTypeSelection);
                });

                function updatePointSelection() {
                    const selectedRadio = document.querySelector('input[name="points_to_redeem"]:checked');

                    if (selectedRadio) {
                        selectedPoints = parseInt(selectedRadio.value);

                        // Update visual selection
                        pointOptions.forEach(opt => opt.classList.remove('border-primary'));
                        selectedRadio.closest('.point-option').classList.add('border-primary');

                        // Show redemption type section
                        redemptionTypeSection.classList.remove('d-none');

                        updateSummary();
                    }
                }

                function updateTypeSelection() {
                    const selectedRadio = document.querySelector('input[name="redemption_type"]:checked');

                    if (selectedRadio) {
                        selectedType = selectedRadio.value;

                        // Update visual selection
                        redemptionTypeOptions.forEach(opt => opt.classList.remove('border-primary'));
                        selectedRadio.closest('.redemption-type-option').classList.add('border-primary');

                        // Show summary section
                        summarySection.classList.remove('d-none');

                        updateSummary();
                    }
                }

                function updateSummary() {
                    if (selectedPoints > 0) {
                        const cashValue = (selectedPoints / 20) * 1000;
                        const remainingPoints = currentPoints - selectedPoints;

                        summaryPoints.textContent = selectedPoints + ' poin';
                        summaryCashValue.textContent = 'Rp ' + cashValue.toLocaleString('id-ID');
                        summaryRemainingPoints.textContent = remainingPoints.toLocaleString('id-ID') + ' poin';

                        if (selectedType) {
                            summaryType.textContent = selectedType === 'cash' ? 'Uang Tunai' : 'Donasi';
                            submitBtn.disabled = false;
                        } else {
                            summaryType.textContent = '-';
                            submitBtn.disabled = true;
                        }
                    }
                }

                // Form validation
                document.getElementById('redemptionForm').addEventListener('submit', function(e) {
                    const selectedPointsRadio = document.querySelector(
                    'input[name="points_to_redeem"]:checked');
                    const selectedTypeRadio = document.querySelector('input[name="redemption_type"]:checked');

                    if (!selectedPointsRadio || !selectedTypeRadio) {
                        e.preventDefault();
                        alert('Silakan lengkapi pilihan Anda.');
                        return;
                    }

                    const points = parseInt(selectedPointsRadio.value);
                    const type = selectedTypeRadio.value;
                    const cashValue = (points / 20) * 1000;

                    let confirmMessage = `Apakah Anda yakin ingin menukar ${points} poin `;
                    confirmMessage += type === 'cash' ?
                        `menjadi uang tunai Rp ${cashValue.toLocaleString('id-ID')}?` :
                        `untuk donasi senilai Rp ${cashValue.toLocaleString('id-ID')}?`;

                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .point-option,
            .redemption-type-option {
                cursor: pointer;
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }

            .point-option:hover,
            .redemption-type-option:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .point-option.border-primary,
            .redemption-type-option.border-primary {
                border-color: var(--primary-color) !important;
            }

            .form-check-input:checked {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
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
