@extends('layouts.main')

@section('title', 'Menabung Sampah - E-TRANK')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="mb-0">Menabung Sampah</h3>
                    <p class="text-muted mb-0">Tukar sampah daur ulang Anda menjadi poin</p>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Success/Error Flash Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (!$recycleBin)
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Anda belum memiliki tempat sampah yang terdaftar. Silakan hubungi admin untuk registrasi kode tempat
                    sampah.
                </div>
            @elseif($availableRecyclePercentage == 0)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Tempat sampah daur ulang Anda masih kosong. Silakan masukkan sampah terlebih dahulu.
                </div>
            @else
                <!-- Deposit Form -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-recycle"></i>
                            Form Penukaran Sampah Daur Ulang
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="depositForm" action="{{ route('user.nabung.store') }}" method="POST" novalidate>
                            @csrf
                            <input type="hidden" name="waste_bin_type_id" value="{{ $recycleBin->id }}">

                            <div class="row g-4">
                                <!-- Current Waste Status -->
                                <div class="col-12">
                                    <div class="card bg-success bg-opacity-10 border-success">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success rounded-circle p-3 me-3">
                                                    <i class="bi bi-recycle text-white fs-4"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="text-success mb-1">Sampah Daur Ulang Tersedia</h6>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-muted">Persentase saat ini</span>
                                                        <span
                                                            class="badge bg-success fs-6">{{ number_format($availableRecyclePercentage, 1) }}%</span>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 8px;">
                                                        <div class="progress-bar bg-success"
                                                            style="width: {{ min($availableRecyclePercentage, 100) }}%">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted mt-1">
                                                        Estimasi poin yang akan didapat:
                                                        <strong>{{ floor($availableRecyclePercentage) }} poin</strong>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Waste Types Selection -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold required">
                                        <i class="bi bi-collection me-2"></i>
                                        Pilih Jenis Sampah yang Akan Ditukar
                                    </label>
                                    <div class="row g-3" id="wasteTypesContainer">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="card waste-type-option" data-waste-type="plastik">
                                                <div class="card-body text-center p-3">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 mx-auto mb-3"
                                                        style="width: 60px; height: 60px;">
                                                        <i class="bi bi-cup-straw text-warning fs-4"></i>
                                                    </div>
                                                    <h6 class="mb-2">Plastik</h6>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input waste-type-checkbox" type="checkbox"
                                                            name="waste_types[]" value="plastik" id="plastik"
                                                            {{ old('waste_types') && in_array('plastik', old('waste_types')) ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="card waste-type-option" data-waste-type="kertas">
                                                <div class="card-body text-center p-3">
                                                    <div class="bg-info bg-opacity-10 rounded-circle p-3 mx-auto mb-3"
                                                        style="width: 60px; height: 60px;">
                                                        <i class="bi bi-file-earmark text-info fs-4"></i>
                                                    </div>
                                                    <h6 class="mb-2">Kertas</h6>
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input waste-type-checkbox" type="checkbox"
                                                            name="waste_types[]" value="kertas" id="kertas"
                                                            {{ old('waste_types') && in_array('kertas', old('waste_types')) ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback d-block" id="wasteTypesError"
                                        style="display: none !important;">
                                        Silakan pilih minimal satu jenis sampah.
                                    </div>
                                </div>

                                <!-- Schedule Selection -->
                                <div class="col-12">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary bg-opacity-10">
                                            <h6 class="card-title mb-0 text-primary">
                                                <i class="bi bi-calendar-event me-2"></i>
                                                Jadwal Pengambilan Sampah
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="pickup_date" class="form-label fw-semibold required">
                                                        <i class="bi bi-calendar3 me-2"></i>
                                                        Tanggal Pengambilan
                                                    </label>
                                                    <input type="date"
                                                        class="form-control @error('pickup_date') is-invalid @enderror"
                                                        id="pickup_date" name="pickup_date"
                                                        value="{{ old('pickup_date') }}"
                                                        min="{{ date('Y-m-d', strtotime('+2 day')) }}"
                                                        max="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                                                    <div class="form-text">Pilih tanggal 1-7 hari ke depan</div>
                                                    @error('pickup_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="pickup_time" class="form-label fw-semibold required">
                                                        <i class="bi bi-clock me-2"></i>
                                                        Waktu Pengambilan
                                                    </label>
                                                    <select class="form-select @error('pickup_time') is-invalid @enderror"
                                                        id="pickup_time" name="pickup_time" required>
                                                        <option value="">Pilih waktu pengambilan</option>
                                                        <option value="08:00-10:00"
                                                            {{ old('pickup_time') == '08:00-10:00' ? 'selected' : '' }}>
                                                            08:00 - 10:00 WIB</option>
                                                        <option value="10:00-12:00"
                                                            {{ old('pickup_time') == '10:00-12:00' ? 'selected' : '' }}>
                                                            10:00 - 12:00 WIB</option>
                                                        <option value="13:00-15:00"
                                                            {{ old('pickup_time') == '13:00-15:00' ? 'selected' : '' }}>
                                                            13:00 - 15:00 WIB</option>
                                                        <option value="15:00-17:00"
                                                            {{ old('pickup_time') == '15:00-17:00' ? 'selected' : '' }}>
                                                            15:00 - 17:00 WIB</option>
                                                    </select>
                                                    <div class="form-text">Waktu operasional petugas</div>
                                                    @error('pickup_time')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Validation Summary -->
                            <div class="alert alert-warning mt-4" id="validationSummary" style="display: none;">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Mohon lengkapi data berikut:</strong>
                                <ul id="validationList" class="mb-0 mt-2"></ul>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-success btn-lg position-relative" id="submitBtn"
                                    disabled>
                                    <span id="submitBtnText">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Ajukan Penukaran Poin
                                    </span>
                                    <span id="submitBtnLoading" style="display: none;">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Memproses...
                                    </span>
                                </button>
                                <small class="text-muted text-center">
                                    Setelah mengajukan, admin akan menjadwalkan petugas untuk mengambil sampah Anda sesuai
                                    waktu yang dipilih.
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Information Card -->
            {{-- <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        Informasi Penting
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-0 small text-muted">
                                <li>1% sampah = 1 poin</li>
                                <li>Hanya sampah daur ulang (kardus, plastik, kertas) yang dapat ditukar menjadi poin</li>
                                <li>Pastikan sampah sudah bersih dan kering sebelum pengambilan</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-0 small text-muted">
                                <li>Petugas akan datang sesuai jadwal yang Anda pilih</li>
                                <li>Poin akan ditambahkan ke saldo Anda setelah petugas mengkonfirmasi pengambilan sampah
                                </li>
                                <li>Silakan pisahkan sampah sesuai jenisnya untuk memudahkan petugas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Form elements
                const form = document.getElementById('depositForm');
                const wasteTypeOptions = document.querySelectorAll('.waste-type-option');
                const wasteTypeCheckboxes = document.querySelectorAll('.waste-type-checkbox');
                const pickupDate = document.getElementById('pickup_date');
                const pickupTime = document.getElementById('pickup_time');
                const submitBtn = document.getElementById('submitBtn');
                const submitBtnText = document.getElementById('submitBtnText');
                const submitBtnLoading = document.getElementById('submitBtnLoading');
                const wasteTypesError = document.getElementById('wasteTypesError');
                const validationSummary = document.getElementById('validationSummary');
                const validationList = document.getElementById('validationList');

                // Form validation state
                let formState = {
                    wasteTypesSelected: false,
                    dateSelected: false,
                    timeSelected: false
                };

                // Initialize form state from old values
                initializeFormState();

                // Handle waste type selection
                wasteTypeOptions.forEach((option) => {
                    const checkbox = option.querySelector('.waste-type-checkbox');

                    // Handle click on card
                    option.addEventListener('click', function(e) {
                        if (e.target.type !== 'checkbox') {
                            e.preventDefault();
                            checkbox.checked = !checkbox.checked;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });

                    // Handle checkbox change
                    checkbox.addEventListener('change', function() {
                        updateWasteTypeSelection();
                        validateForm();
                    });
                });

                // Handle date and time changes
                pickupDate.addEventListener('change', function() {
                    formState.dateSelected = this.value !== '';
                    validateForm();
                });

                pickupTime.addEventListener('change', function() {
                    formState.timeSelected = this.value !== '';
                    validateForm();
                });

                // Form submission
                form.addEventListener('submit', function(e) {
                    if (!validateFormOnSubmit()) {
                        e.preventDefault();
                        return false;
                    }

                    // Show loading state
                    setSubmitButtonLoading(true);
                });

                // Functions
                function initializeFormState() {
                    // Check initial waste types selection
                    updateWasteTypeSelection();

                    // Check initial date and time
                    formState.dateSelected = pickupDate.value !== '';
                    formState.timeSelected = pickupTime.value !== '';

                    // Validate form
                    validateForm();
                }

                function updateWasteTypeSelection() {
                    const selectedTypes = [];

                    wasteTypeOptions.forEach((option) => {
                        const checkbox = option.querySelector('.waste-type-checkbox');

                        if (checkbox.checked) {
                            option.classList.add('border-primary', 'bg-primary', 'bg-opacity-10', 'shadow-sm');
                            selectedTypes.push(checkbox.value);
                        } else {
                            option.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10',
                                'shadow-sm');
                        }
                    });

                    formState.wasteTypesSelected = selectedTypes.length > 0;

                    // Hide/show error message
                    if (formState.wasteTypesSelected) {
                        wasteTypesError.style.display = 'none';
                    }
                }

                function validateForm() {
                    const isValid = formState.wasteTypesSelected &&
                        formState.dateSelected &&
                        formState.timeSelected;

                    // Enable/disable submit button
                    submitBtn.disabled = !isValid;

                    // Update button appearance
                    if (isValid) {
                        submitBtn.classList.remove('btn-secondary');
                        submitBtn.classList.add('btn-success');
                    } else {
                        submitBtn.classList.remove('btn-success');
                        submitBtn.classList.add('btn-secondary');
                    }

                    // Update validation summary
                    updateValidationSummary();

                    return isValid;
                }

                function updateValidationSummary() {
                    const issues = [];

                    if (!formState.wasteTypesSelected) {
                        issues.push('Pilih minimal satu jenis sampah');
                    }
                    if (!formState.dateSelected) {
                        issues.push('Pilih tanggal pengambilan');
                    }
                    if (!formState.timeSelected) {
                        issues.push('Pilih waktu pengambilan');
                    }

                    if (issues.length > 0) {
                        validationList.innerHTML = issues.map(issue => `<li>${issue}</li>`).join('');
                        validationSummary.style.display = 'block';
                    } else {
                        validationSummary.style.display = 'none';
                    }
                }

                function validateFormOnSubmit() {
                    let isValid = true;
                    const errors = [];

                    // Validate waste types
                    const selectedWasteTypes = document.querySelectorAll('.waste-type-checkbox:checked');
                    if (selectedWasteTypes.length === 0) {
                        isValid = false;
                        errors.push('Silakan pilih minimal satu jenis sampah');
                        wasteTypesError.style.display = 'block';
                    }

                    // Validate date
                    if (!pickupDate.value) {
                        isValid = false;
                        errors.push('Silakan pilih tanggal pengambilan');
                        pickupDate.classList.add('is-invalid');
                    } else {
                        pickupDate.classList.remove('is-invalid');
                    }

                    // Validate time
                    if (!pickupTime.value) {
                        isValid = false;
                        errors.push('Silakan pilih waktu pengambilan');
                        pickupTime.classList.add('is-invalid');
                    } else {
                        pickupTime.classList.remove('is-invalid');
                    }

                    // Show validation errors
                    if (!isValid) {
                        showValidationAlert(errors);

                        // Scroll to first error
                        const firstError = document.querySelector('.is-invalid, #wasteTypesError[style*="block"]');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    }

                    return isValid;
                }

                function showValidationAlert(errors) {
                    const alertHtml = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Mohon perbaiki kesalahan berikut:</strong>
                            <ul class="mb-0 mt-2">
                                ${errors.map(error => `<li>${error}</li>`).join('')}
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;

                    // Insert alert before the form
                    form.insertAdjacentHTML('beforebegin', alertHtml);

                    // Auto remove after 5 seconds
                    setTimeout(() => {
                        const alert = form.previousElementSibling;
                        if (alert && alert.classList.contains('alert')) {
                            alert.remove();
                        }
                    }, 5000);
                }

                function setSubmitButtonLoading(loading) {
                    if (loading) {
                        submitBtn.disabled = true;
                        submitBtnText.style.display = 'none';
                        submitBtnLoading.style.display = 'inline';
                    } else {
                        submitBtn.disabled = false;
                        submitBtnText.style.display = 'inline';
                        submitBtnLoading.style.display = 'none';
                    }
                }

                function formatDateToAsiaJakarta(date) {
                    // offset Asia/Jakarta = UTC+7
                    const jakartaOffset = 7 * 60; // menit
                    const localDate = new Date(date.getTime() + (jakartaOffset - date.getTimezoneOffset()) * 60000);

                    const year = localDate.getFullYear();
                    const month = String(localDate.getMonth() + 1).padStart(2, "0");
                    const day = String(localDate.getDate()).padStart(2, "0");

                    return `${year}-${month}-${day}`;
                }

                // Minimum: besok
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                pickupDate.min = formatDateToAsiaJakarta(tomorrow);

                // Maksimum: 7 hari ke depan
                const maxDate = new Date();
                maxDate.setDate(maxDate.getDate() + 7);
                pickupDate.max = formatDateToAsiaJakarta(maxDate);

                // Auto-dismiss alerts after 10 seconds
                setTimeout(() => {
                    const alerts = document.querySelectorAll('.alert-dismissible');
                    alerts.forEach(alert => {
                        const closeButton = alert.querySelector('.btn-close');
                        if (closeButton) {
                            closeButton.click();
                        }
                    });
                }, 10000);
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .required::after {
                content: ' *';
                color: #dc3545;
            }

            .waste-type-option {
                cursor: pointer;
                transition: all 0.3s ease;
                border: 2px solid #e9ecef;
                height: 100%;
            }

            .waste-type-option:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                border-color: #dee2e6;
            }

            .waste-type-option.border-primary {
                border-color: var(--bs-primary) !important;
                transform: translateY(-2px);
            }

            .waste-type-option .form-check-input {
                pointer-events: none;
            }

            .form-check-input:checked {
                background-color: var(--bs-success);
                border-color: var(--bs-success);
            }

            .btn:disabled {
                cursor: not-allowed;
            }

            .card {
                border-radius: 12px;
            }

            .card-header {
                border-radius: 12px 12px 0 0 !important;
            }

            .progress {
                border-radius: 10px;
            }

            .progress-bar {
                border-radius: 10px;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: var(--bs-primary);
                box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
            }

            .alert {
                border-radius: 12px;
            }

            @media (max-width: 768px) {
                .card-body {
                    padding: 1rem;
                }

                .waste-type-option .card-body {
                    padding: 1rem 0.5rem;
                }

                .waste-type-option h6 {
                    font-size: 0.9rem;
                }

                .row.g-4 {
                    --bs-gutter-x: 1rem;
                    --bs-gutter-y: 1rem;
                }

                .row.g-3 {
                    --bs-gutter-x: 0.75rem;
                    --bs-gutter-y: 0.75rem;
                }

                .d-flex.align-items-center .btn {
                    padding: 0.375rem 0.75rem;
                }

                .d-flex.align-items-center h3 {
                    font-size: 1.5rem;
                }
            }

            @media (max-width: 576px) {
                .col-md-4.col-sm-6 {
                    flex: 0 0 100%;
                    max-width: 100%;
                }

                .waste-type-option {
                    margin-bottom: 0.75rem;
                }

                .card-header h5 {
                    font-size: 1.1rem;
                }

                .btn-lg {
                    font-size: 1rem;
                    padding: 0.75rem 1rem;
                }
            }

            /* Loading animation */
            .spinner-border-sm {
                width: 1rem;
                height: 1rem;
            }

            /* Smooth transitions */
            .btn,
            .card,
            .alert {
                transition: all 0.3s ease;
            }

            /* Focus styles for accessibility */
            .waste-type-option:focus-within {
                outline: 2px solid var(--bs-primary);
                outline-offset: 2px;
            }

            /* Invalid state styles */
            .is-invalid {
                border-color: #dc3545;
            }

            .invalid-feedback {
                display: block;
                width: 100%;
                margin-top: 0.25rem;
                font-size: 0.875em;
                color: #dc3545;
            }
        </style>
    @endpush
@endsection
