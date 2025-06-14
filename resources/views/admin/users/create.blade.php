@extends('layouts.main')

@section('title', 'Tambah User Baru - E-TRANK')

@section('content')
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div
                class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                            <i class="bi bi-person-plus text-primary fs-5"></i>
                        </div>
                        <div>
                            <h1 class="h3 mb-0 fw-bold">Tambah User Baru</h1>
                            <p class="text-muted mb-0 small">Buat akun baru untuk masyarakat</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <form action="{{ route('admin.users.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <!-- Personal Information Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-person text-primary"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold">Informasi Pribadi</h5>
                                <small class="text-muted">Data identitas pengguna</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-medium">
                                    <i class="bi bi-person-badge me-1 text-muted"></i>
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-medium">
                                    <i class="bi bi-envelope me-1 text-muted"></i>
                                    Alamat Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-medium">
                                    <i class="bi bi-telephone me-1 text-muted"></i>
                                    Nomor Telepon <span class="text-danger">*</span>
                                </label>
                                <input type="tel"
                                    class="form-control form-control-lg @error('phone') is-invalid @enderror" id="phone"
                                    name="phone" value="{{ old('phone') }}" placeholder="0812-3456-7890" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- District -->
                            <div class="col-md-6">
                                <label for="district" class="form-label fw-medium">
                                    <i class="bi bi-geo-alt me-1 text-muted"></i>
                                    Kecamatan <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control form-control-lg @error('district') is-invalid @enderror"
                                    id="district" name="district" value="{{ old('district') }}"
                                    placeholder="Contoh: Lowokwaru" required>
                                @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <label for="address" class="form-label fw-medium">
                                    <i class="bi bi-house me-1 text-muted"></i>
                                    Alamat Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                    placeholder="Masukkan alamat lengkap termasuk RT/RW" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Postal Code -->
                            <div class="col-md-6">
                                <label for="postal_code" class="form-label fw-medium">
                                    <i class="bi bi-mailbox me-1 text-muted"></i>
                                    Kode Pos <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control form-control-lg @error('postal_code') is-invalid @enderror"
                                    id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
                                    placeholder="65141" maxlength="10" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waste Bin Assignment Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-trash text-success"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold">Penugasan Tempat Sampah</h5>
                                <small class="text-muted">Pilih tempat sampah untuk pengguna</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Waste Bin Selection -->
                            <div class="col-12">
                                <label for="waste_bin_code" class="form-label fw-medium">
                                    <i class="bi bi-qr-code me-1 text-muted"></i>
                                    Kode Tempat Sampah <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg @error('waste_bin_code') is-invalid @enderror"
                                    id="waste_bin_code" name="waste_bin_code" required>
                                    <option value="">🗂 Pilih Tempat Sampah</option>
                                    @foreach ($availableBins as $bin)
                                        <option value="{{ $bin->bin_code }}"
                                            {{ old('waste_bin_code') == $bin->bin_code ? 'selected' : '' }}>
                                            📍 {{ $bin->bin_code }} - {{ $bin->location }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('waste_bin_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle-fill text-primary me-1"></i>
                                    Setiap pengguna akan diberikan satu tempat sampah khusus
                                </div>
                            </div>

                            <!-- Bin Information Display -->
                            <div class="col-12" id="bin-info" style="display: none;">
                                <div class="alert alert-info border-0 shadow-sm">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3 mt-1">
                                            <i class="bi bi-info-circle text-info"></i>
                                        </div>
                                        <div>
                                            <h6 class="alert-heading mb-2 fw-semibold">Informasi Tempat Sampah</h6>
                                            <div id="bin-details" class="small"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Settings Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-shield-lock text-warning"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-semibold">Keamanan Akun</h5>
                                <small class="text-muted">Atur password untuk akun baru</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-medium">
                                    <i class="bi bi-key me-1 text-muted"></i>
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Minimal 8 karakter" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('password')" title="Tampilkan/Sembunyikan Password">
                                        <i class="bi bi-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-shield-check me-1 text-success"></i>
                                    Gunakan kombinasi huruf, angka, dan simbol
                                </div>
                            </div>

                            <!-- Password Confirmation -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-medium">
                                    <i class="bi bi-key-fill me-1 text-muted"></i>
                                    Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg"
                                        id="password_confirmation" name="password_confirmation"
                                        placeholder="Ulangi password" required>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePassword('password_confirmation')"
                                        title="Tampilkan/Sembunyikan Password">
                                        <i class="bi bi-eye" id="password_confirmation-icon"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-arrow-repeat me-1 text-muted"></i>
                                    Masukkan password yang sama seperti di atas
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-between">
                            <a href="{{ route('admin.users.index') }}"
                                class="btn btn-outline-secondary btn-lg order-2 order-sm-1">
                                <i class="bi bi-arrow-left me-2"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg order-1 order-sm-2">
                                <i class="bi bi-check-circle me-2"></i>
                                Simpan User Baru
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                field.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // Show bin information when selected
        document.getElementById('waste_bin_code').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const binInfo = document.getElementById('bin-info');
            const binDetails = document.getElementById('bin-details');

            if (this.value) {
                const binCode = this.value;
                const location = selectedOption.text.split(' - ')[1];

                binDetails.innerHTML = `
                    <div class="row g-2">
                        <div class="col-auto">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-qr-code me-1"></i>
                                Kode: ${binCode}
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="bi bi-geo-alt-fill me-1"></i>
                                Lokasi: ${location}
                            </span>
                        </div>
                    </div>
                `;
                binInfo.style.display = 'block';

                // Smooth scroll to show the info
                setTimeout(() => {
                    binInfo.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 100);
            } else {
                binInfo.style.display = 'none';
            }
        });

        // Auto-format phone number
        document.getElementById('phone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');

            if (value.startsWith('0')) {
                // Format: 0812-3456-7890
                if (value.length > 4) {
                    value = value.substring(0, 4) + '-' + value.substring(4);
                }
                if (value.length > 9) {
                    value = value.substring(0, 9) + '-' + value.substring(9, 13);
                }
            }

            this.value = value;
        });

        // Auto-format postal code (numbers only)
        document.getElementById('postal_code').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });

        // Real-time password confirmation validation
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        function validatePasswordMatch() {
            if (passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Password tidak cocok');
                passwordConfirmation.classList.add('is-invalid');
            } else {
                passwordConfirmation.setCustomValidity('');
                passwordConfirmation.classList.remove('is-invalid');
            }
        }

        password.addEventListener('input', validatePasswordMatch);
        passwordConfirmation.addEventListener('input', validatePasswordMatch);

        // Bootstrap form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                const forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();

                            // Scroll to first invalid field
                            const firstInvalid = form.querySelector(':invalid');
                            if (firstInvalid) {
                                firstInvalid.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                                firstInvalid.focus();
                            }
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Smooth loading animation
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
@endpush
