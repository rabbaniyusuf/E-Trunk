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
                    <p class="text-muted mb-0">Tukar poin Anda dengan uang tunai</p>
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
                                <div class="fw-bold">10 poin = Rp 1.000</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($currentPoints < 10)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Anda memerlukan minimal 10 poin untuk melakukan penukaran. Saldo poin Anda saat ini:
                    {{ number_format($currentPoints) }} poin.
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
                        <form id="redemptionForm" action="{{ route('user.tukar-poin.store') }}" method="POST">
                            @csrf

                            <!-- Points Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Pilih jumlah poin:</label>
                                <div class="row g-3">
                                    @foreach ($availableOptions as $points)
                                        @php
                                            $cashValue = ($points / 10) * 1000;
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

                                    <!-- Custom Points Option -->
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card point-option" data-points="custom">
                                            <div class="card-body text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="points_to_redeem"
                                                        value="custom" id="points_custom">
                                                    <label class="form-check-label w-100" for="points_custom">
                                                        <div class="fw-bold text-primary mb-1">
                                                            <i class="bi bi-pencil-square"></i> Pilih Sendiri
                                                        </div>
                                                        <div class="text-muted small">Tentukan jumlah</div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Points Input -->
                            <div id="customPointsSection" class="d-none mb-4">
                                <label class="form-label fw-bold">Masukkan jumlah poin:</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="custom_points"
                                                id="customPointsInput" min="10" max="{{ $currentPoints }}"
                                                step="10" placeholder="Minimum 10 poin">
                                            <span class="input-group-text">poin</span>
                                        </div>
                                        <div class="form-text">
                                            Minimal 10 poin, maksimal {{ number_format($currentPoints) }} poin.
                                            <br>Harus kelipatan 10.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3 mt-md-0">
                                        <div class="bg-light rounded p-3">
                                            <div class="small text-muted mb-1">Nilai yang akan diterima:</div>
                                            <div class="fw-bold text-success h5 mb-0" id="customCashValue">Rp 0</div>
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
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Poin yang ditukar:</span>
                                                    <span class="fw-bold" id="summaryPoints">0 poin</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Nilai tukar:</span>
                                                    <span class="fw-bold text-success" id="summaryCashValue">Rp 0</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="d-flex justify-content-between">
                                                    <span>Sisa poin setelah tukar:</span>
                                                    <span class="fw-bold text-primary"
                                                        id="summaryRemainingPoints">{{ number_format($currentPoints) }}
                                                        poin</span>
                                                </div>
                                            </div>
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
                                    Setelah mengajukan, tunjukkan bukti penukaran ke admin untuk diproses lebih lanjut.
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
                        <li>Nilai tukar: 10 poin = Rp 1.000</li>
                        <li>Minimum penukaran: 10 poin</li>
                        <li>Penukaran harus dalam kelipatan 10 poin</li>
                        <li>Petugas akan mengantarkan uang tunai ke alamat yang terdaftar</li>
                        <li>Proses penukaran memerlukan konfirmasi admin terlebih dahulu</li>
                        <li>Tunjukkan bukti penukaran kepada admin saat melakukan transaksi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const pointOptions = document.querySelectorAll('.point-option');
                const customPointsSection = document.getElementById('customPointsSection');
                const customPointsInput = document.getElementById('customPointsInput');
                const customCashValue = document.getElementById('customCashValue');
                const summarySection = document.getElementById('summarySection');
                const submitBtn = document.getElementById('submitBtn');

                const summaryPoints = document.getElementById('summaryPoints');
                const summaryCashValue = document.getElementById('summaryCashValue');
                const summaryRemainingPoints = document.getElementById('summaryRemainingPoints');

                const currentPoints = {{ $currentPoints }};
                let selectedPoints = 0;

                // Handle point selection
                pointOptions.forEach(option => {
                    const radioInput = option.querySelector('input[type="radio"]');

                    option.addEventListener('click', function() {
                        radioInput.checked = true;
                        updatePointSelection();
                    });

                    radioInput.addEventListener('change', updatePointSelection);
                });

                // Handle custom points input
                customPointsInput.addEventListener('input', function() {
                    const value = parseInt(this.value) || 0;
                    const cashValue = Math.floor(value / 10) * 1000;

                    customCashValue.textContent = 'Rp ' + cashValue.toLocaleString('id-ID');

                    if (document.getElementById('points_custom').checked) {
                        selectedPoints = value;
                        updateSummary();
                    }
                });

                function updatePointSelection() {
                    const selectedRadio = document.querySelector('input[name="points_to_redeem"]:checked');

                    if (selectedRadio) {
                        const value = selectedRadio.value;

                        // Update visual selection
                        pointOptions.forEach(opt => opt.classList.remove('border-primary'));
                        selectedRadio.closest('.point-option').classList.add('border-primary');

                        if (value === 'custom') {
                            customPointsSection.classList.remove('d-none');
                            selectedPoints = parseInt(customPointsInput.value) || 0;
                        } else {
                            customPointsSection.classList.add('d-none');
                            selectedPoints = parseInt(value);
                        }

                        updateSummary();
                    }
                }

                function updateSummary() {
                    if (selectedPoints >= 10) {
                        const cashValue = Math.floor(selectedPoints / 10) * 1000;
                        const remainingPoints = currentPoints - selectedPoints;

                        summaryPoints.textContent = selectedPoints + ' poin';
                        summaryCashValue.textContent = 'Rp ' + cashValue.toLocaleString('id-ID');
                        summaryRemainingPoints.textContent = remainingPoints.toLocaleString('id-ID') + ' poin';

                        summarySection.classList.remove('d-none');
                        submitBtn.disabled = false;
                    } else {
                        summarySection.classList.add('d-none');
                        submitBtn.disabled = true;
                    }
                }

                // Form validation
                document.getElementById('redemptionForm').addEventListener('submit', function(e) {
                    const selectedPointsRadio = document.querySelector(
                    'input[name="points_to_redeem"]:checked');

                    if (!selectedPointsRadio) {
                        e.preventDefault();
                        alert('Silakan pilih jumlah poin yang akan ditukar.');
                        return;
                    }

                    let points = 0;
                    if (selectedPointsRadio.value === 'custom') {
                        points = parseInt(customPointsInput.value) || 0;
                        if (points < 10 || points > currentPoints || points % 10 !== 0) {
                            e.preventDefault();
                            alert('Jumlah poin harus minimal 10, maksimal ' + currentPoints +
                                ', dan kelipatan 10.');
                            return;
                        }
                    } else {
                        points = parseInt(selectedPointsRadio.value);
                    }

                    const cashValue = Math.floor(points / 10) * 1000;
                    const confirmMessage =
                        `Apakah Anda yakin ingin menukar ${points} poin menjadi uang tunai Rp ${cashValue.toLocaleString('id-ID')}?`;

                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .point-option {
                cursor: pointer;
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }

            .point-option:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .point-option.border-primary {
                border-color: var(--bs-primary) !important;
            }

            .form-check-input:checked {
                background-color: var(--bs-primary);
                border-color: var(--bs-primary);
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
