@extends('layouts.main')

@section('title', 'Create User - E-TRANK')

@push('styles')
    <style>
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .form-card {
            background: white;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .form-section {
            padding: 2rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .section-title {
            color: var(--secondary-color);
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            background-color: #fafbfc;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background-color: white;
        }

        .form-control.is-invalid {
            border-color: var(--danger-color);
        }

        .invalid-feedback {
            display: block;
            font-size: 0.875rem;
            color: var(--danger-color);
            margin-top: 0.25rem;
        }

        .required::after {
            content: ' *';
            color: var(--danger-color);
        }

        .info-alert {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            border: 1px solid #93c5fd;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-alert .alert-icon {
            color: var(--primary-color);
            font-size: 1.25rem;
        }

        .bin-config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .bin-card {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .bin-card.active {
            border-color: var(--success-color);
            background: #f0fdf4;
        }

        .bin-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .bin-card.recycle .bin-icon {
            color: var(--success-color);
        }

        .bin-card.non-recycle .bin-icon {
            color: var(--warning-color);
        }

        .action-buttons {
            background: #f8fafc;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--secondary-color);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #475569;
            color: white;
            transform: translateY(-1px);
        }

        .input-group-text {
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            border-right: none;
            color: var(--secondary-color);
        }

        .form-control.with-icon {
            border-left: none;
            padding-left: 0.75rem;
        }

        @media (max-width: 768px) {
            .form-section {
                padding: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .action-buttons .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-2">
                        <i class="bi bi-person-plus me-2"></i>
                        Tambah User Baru
                    </h1>
                    <p class="mb-0 opacity-75">Daftarkan masyarakat baru ke dalam program bank sampah digital</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end">
                        <i class="bi bi-recycle" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Info Alert -->
        <div class="info-alert">
            <div class="d-flex align-items-start">
                <i class="bi bi-info-circle alert-icon me-3 mt-1"></i>
                <div>
                    <h6 class="mb-2">Informasi Penting</h6>
                    <p class="mb-0 small">
                        Setiap user akan otomatis mendapatkan 2 tong sampah:
                        <strong>1 tong recycle</strong> untuk sampah yang dapat didaur ulang dan
                        <strong>1 tong non-recycle</strong> untuk sampah organik dan lainnya.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="form-card">
            <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
                @csrf

                <!-- Personal Information -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-person"></i>
                        Informasi Personal
                    </h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label required">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" class="form-control with-icon @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}"
                                    placeholder="Masukkan nama lengkap" required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" class="form-control with-icon @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="contoh@email.com">
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label required">Nomor Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-telephone"></i>
                                </span>
                                <input type="text" class="form-control with-icon @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                                    required>
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-geo-alt"></i>
                        Informasi Alamat
                    </h5>

                    <div class="mb-3">
                        <label for="address" class="form-label required">Alamat Lengkap</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                            placeholder="Masukkan alamat lengkap termasuk RT/RW, Jalan, dll." required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="district" class="form-label required">Kelurahan/Desa</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-building"></i>
                                </span>
                                <input type="text"
                                    class="form-control with-icon @error('district') is-invalid @enderror" id="district"
                                    name="district" value="{{ old('district') }}" placeholder="Nama kelurahan/desa"
                                    required>
                            </div>
                            @error('district')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="postal_code" class="form-label">Kode Pos</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-mailbox"></i>
                                </span>
                                <input type="text"
                                    class="form-control with-icon @error('postal_code') is-invalid @enderror"
                                    id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
                                    placeholder="12345" maxlength="5">
                            </div>
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Bin Configuration -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-trash"></i>
                        Konfigurasi Tong Sampah
                    </h5>

                    <p class="text-muted mb-3">
                        Sistem akan otomatis membuat tong sampah untuk user baru dengan konfigurasi standar.
                    </p>

                    <div class="bin-config-grid">
                        <div class="bin-card recycle active">
                            <div class="bin-icon">
                                <i class="bi bi-recycle"></i>
                            </div>
                            <h6 class="mb-2">Tong Recycle</h6>
                            <p class="small text-muted mb-0">
                                Untuk sampah yang dapat didaur ulang seperti plastik, kertas, logam
                            </p>
                            <input type="hidden" name="bins[]" value="recycle">
                        </div>

                        <div class="bin-card non-recycle active">
                            <div class="bin-icon">
                                <i class="bi bi-trash"></i>
                            </div>
                            <h6 class="mb-2">Tong Non-Recycle</h6>
                            <p class="small text-muted mb-0">
                                Untuk sampah organik dan sampah yang tidak dapat didaur ulang
                            </p>
                            <input type="hidden" name="bins[]" value="non_recycle">
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Catatan:</strong>
                        Setiap tong akan diberikan kode unik dan kapasitas standar 50kg.
                        Konfigurasi dapat diubah setelah user dibuat.
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>
                        Kembali
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Phone number formatting
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value;
            });

            // Postal code formatting
            const postalInput = document.getElementById('postal_code');
            postalInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value;
            });

            // Form validation
            const form = document.getElementById('userForm');
            form.addEventListener('submit', function(e) {
                const requiredFields = ['name', 'phone', 'address', 'district'];
                let isValid = true;

                requiredFields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    const value = field.value.trim();

                    if (!value) {
                        isValid = false;
                        field.classList.add('is-invalid');

                        // Show custom error if not exists
                        if (!field.nextElementSibling || !field.nextElementSibling.classList
                            .contains('invalid-feedback')) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = 'Field ini wajib diisi';
                            field.parentNode.appendChild(errorDiv);
                        }
                    } else {
                        field.classList.remove('is-invalid');
                        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
                        if (errorDiv && !errorDiv.textContent.includes('{{')) {
                            errorDiv.remove();
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = document.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.focus();
                    }
                    return false;
                }

                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalContent = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                submitBtn.disabled = true;

                // Prevent double submission
                setTimeout(() => {
                    submitBtn.innerHTML = originalContent;
                    submitBtn.disabled = false;
                }, 3000);
            });

            // Real-time validation feedback
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endpush
