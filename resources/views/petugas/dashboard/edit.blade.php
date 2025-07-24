@extends('layouts.main')

@section('title', 'Detail Jadwal Petugas Kebersihan - E-TRANK')

@section('content')
    <div class="container-fluid px-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline-secondary me-3">
                            <i class="bi bi-arrow-left"></i>
                            <span class="d-none d-sm-inline ms-1">Kembali</span>
                        </a>
                        <div>
                            <h1 class="h3 mb-0 text-primary">Detail Jadwal Pengambilan</h1>
                            <p class="text-muted mb-0 small">Catat sampah yang berhasil diambil</p>
                        </div>
                    </div>
                    <div
                        class="badge {{ $wasteCollection->status === 'in_progress' ? 'bg-warning text-dark' : 'bg-info text-white' }} px-3 py-2">
                        <i class="bi bi-clock"></i>
                        @if ($wasteCollection->status === 'scheduled')
                            Terjadwal
                        @elseif($wasteCollection->status === 'in_progress')
                            Dalam Proses
                        @elseif($wasteCollection->status === 'completed')
                            Selesai
                        @else
                            {{ ucfirst($wasteCollection->status) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

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

        <div class="row g-4">
            <!-- Informasi Jadwal -->
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Informasi Jadwal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary rounded-circle p-2">
                                            <i class="bi bi-person-circle text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Nama Pengguna</h6>
                                        <p class="text-muted mb-0">{{ $wasteCollection->user->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                            </div>

                            <div class="col-sm-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar3 text-primary me-2"></i>
                                    <small class="text-muted">Tanggal</small>
                                </div>
                                <p class="mb-0 fw-medium">
                                    {{ \Carbon\Carbon::parse($wasteCollection->pickup_date)->format('d F Y') }}</p>
                            </div>

                            <div class="col-sm-6">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clock text-primary me-2"></i>
                                    <small class="text-muted">Waktu</small>
                                </div>
                                <p class="mb-0 fw-medium">
                                    {{ \Carbon\Carbon::parse($wasteCollection->pickup_time)->format('H:i') }}</p>
                            </div>

                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    <small class="text-muted">Alamat</small>
                                </div>
                                <p class="mb-0">{{ $wasteCollection->user->address }}</p>
                                @if ($wasteCollection->user->district)
                                    <small class="text-muted">{{ $wasteCollection->user->district }}</small>
                                @endif
                                @if ($wasteCollection->user->postal_code)
                                    <small class="text-muted"> - {{ $wasteCollection->user->postal_code }}</small>
                                @endif
                            </div>

                            @if ($wasteCollection->user->phone)
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone text-primary me-2"></i>
                                        <small class="text-muted">No. Telepon</small>
                                    </div>
                                    <p class="mb-0">{{ $wasteCollection->user->phone }}</p>
                                </div>
                            @endif

                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-trash text-primary me-2"></i>
                                    <small class="text-muted">Jenis Sampah yang Diminta</small>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    @if ($requestedWasteTypes && count($requestedWasteTypes) > 0)
                                        @foreach ($requestedWasteTypes as $type)
                                            @if ($type === 'kertas')
                                                <span class="badge bg-success">Kertas</span>
                                            @elseif($type === 'plastik')
                                                <span class="badge bg-info">Plastik</span>
                                            @elseif($type === 'kardus')
                                                <span class="badge bg-warning">Kardus</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($type) }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary">Tidak ada data</span>
                                    @endif
                                </div>
                            </div>

                            @if ($wasteCollection->wasteBinType)
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-archive text-primary me-2"></i>
                                        <small class="text-muted">Jenis Tempat Sampah</small>
                                    </div>
                                    <p class="mb-0 small">{{ $wasteCollection->wasteBinType->name }}</p>
                                </div>
                            @endif

                            @if ($wasteCollection->notes)
                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-chat-left-text text-primary me-2"></i>
                                        <small class="text-muted">Catatan</small>
                                    </div>
                                    <p class="mb-0 small">{{ $wasteCollection->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Pencatatan Sampah -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clipboard-data me-2"></i>Pencatatan Sampah
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('petugas.tasks.update', $wasteCollection->id) }}" method="POST"
                            id="sampahForm">
                            @csrf
                            @method('PUT')

                            <!-- Alert Info -->
                            <div class="alert alert-info d-flex align-items-start mb-4">
                                <i class="bi bi-info-circle me-2 mt-1"></i>
                                <div>
                                    <strong>Informasi Poin:</strong><br>
                                    <small>
                                        • Kertas: 15 poin/kg<br>
                                        • Plastik: 10 poin/kg<br>
                                        • Kardus: 12 poin/kg<br>
                                        <span class="text-warning">* Poin akan menunggu approval admin</span>
                                    </small>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Input Kertas -->
                                <div class="col-md-4">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-success rounded-circle p-2 me-3">
                                                <i class="bi bi-file-earmark-text text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Sampah Kertas</h6>
                                                <small class="text-muted">15 poin per kg</small>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="berat_kertas" class="form-label">Berat (kg)</label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control form-control-lg text-center @error('berat_kertas') is-invalid @enderror"
                                                    id="berat_kertas" name="berat_kertas" min="0" step="0.1"
                                                    placeholder="0.0" value="{{ old('berat_kertas') }}"
                                                    oninput="hitungPoin()">
                                                <span class="input-group-text">kg</span>
                                                @error('berat_kertas')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <div class="bg-white rounded p-2 border">
                                                <small class="text-muted d-block">Poin yang didapat</small>
                                                <span class="h5 text-success mb-0" id="poin_kertas">0</span>
                                                <small class="text-muted ms-1">poin</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Input Plastik -->
                                <div class="col-md-4">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-info rounded-circle p-2 me-3">
                                                <i class="bi bi-cup-straw text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Sampah Plastik</h6>
                                                <small class="text-muted">10 poin per kg</small>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="berat_plastik" class="form-label">Berat (kg)</label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control form-control-lg text-center @error('berat_plastik') is-invalid @enderror"
                                                    id="berat_plastik" name="berat_plastik" min="0"
                                                    step="0.1" placeholder="0.0" value="{{ old('berat_plastik') }}"
                                                    oninput="hitungPoin()">
                                                <span class="input-group-text">kg</span>
                                                @error('berat_plastik')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <div class="bg-white rounded p-2 border">
                                                <small class="text-muted d-block">Poin yang didapat</small>
                                                <span class="h5 text-info mb-0" id="poin_plastik">0</span>
                                                <small class="text-muted ms-1">poin</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Input Kardus -->
                                <div class="col-md-4">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-warning rounded-circle p-2 me-3">
                                                <i class="bi bi-box text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Sampah Kardus</h6>
                                                <small class="text-muted">12 poin per kg</small>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="berat_kardus" class="form-label">Berat (kg)</label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control form-control-lg text-center @error('berat_kardus') is-invalid @enderror"
                                                    id="berat_kardus" name="berat_kardus" min="0" step="0.1"
                                                    placeholder="0.0" value="{{ old('berat_kardus') }}"
                                                    oninput="hitungPoin()">
                                                <span class="input-group-text">kg</span>
                                                @error('berat_kardus')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <div class="bg-white rounded p-2 border">
                                                <small class="text-muted d-block">Poin yang didapat</small>
                                                <span class="h5 text-warning mb-0" id="poin_kardus">0</span>
                                                <small class="text-muted ms-1">poin</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Poin -->
                                <div class="col-12">
                                    <div class="card border-primary">
                                        <div class="card-body text-center py-4">
                                            <h6 class="text-muted mb-2">Total Poin yang Akan Diberikan</h6>
                                            <div class="h2 text-primary mb-0">
                                                <span id="total_poin">0</span>
                                                <small class="h6 text-muted ms-1">poin</small>
                                            </div>
                                            <small class="text-warning">Menunggu Approval Admin</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Catatan Tambahan -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="catatan" class="form-label">
                                            <i class="bi bi-chat-left-text me-1"></i>
                                            Catatan Tambahan (Opsional)
                                        </label>
                                        <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3"
                                            placeholder="Tuliskan catatan jika ada kondisi khusus atau informasi tambahan...">{{ old('catatan') }}</textarea>
                                        @error('catatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                                        <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-1"></i>
                                            <span class="d-none d-sm-inline">Batal</span>
                                        </a>
                                        <button type="submit" class="btn btn-success" id="btnSubmit" disabled>
                                            <i class="bi bi-check-circle me-1"></i>
                                            <span class="d-none d-sm-inline">Selesaikan Pengambilan</span>
                                            <span class="d-sm-none">Selesai</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function hitungPoin() {
                // Ambil nilai input
                const beratKertas = parseFloat(document.getElementById('berat_kertas').value) || 0;
                const beratPlastik = parseFloat(document.getElementById('berat_plastik').value) || 0;
                const beratKardus = parseFloat(document.getElementById('berat_kardus').value) || 0;

                // Hitung poin sesuai dengan controller
                const poinKertas = beratKertas * 15;
                const poinPlastik = beratPlastik * 10;
                const poinKardus = beratKardus * 12;
                const totalPoin = poinKertas + poinPlastik + poinKardus;

                // Update tampilan
                document.getElementById('poin_kertas').textContent = poinKertas.toFixed(0);
                document.getElementById('poin_plastik').textContent = poinPlastik.toFixed(0);
                document.getElementById('poin_kardus').textContent = poinKardus.toFixed(0);
                document.getElementById('total_poin').textContent = totalPoin.toFixed(0);

                // Enable/disable submit button
                const btnSubmit = document.getElementById('btnSubmit');
                if (totalPoin > 0) {
                    btnSubmit.disabled = false;
                    btnSubmit.classList.remove('disabled');
                } else {
                    btnSubmit.disabled = true;
                    btnSubmit.classList.add('disabled');
                }
            }

            // Form validation
            document.getElementById('sampahForm').addEventListener('submit', function(e) {
                const beratKertas = parseFloat(document.getElementById('berat_kertas').value) || 0;
                const beratPlastik = parseFloat(document.getElementById('berat_plastik').value) || 0;
                const beratKardus = parseFloat(document.getElementById('berat_kardus').value) || 0;

                if (beratKertas === 0 && beratPlastik === 0 && beratKardus === 0) {
                    e.preventDefault();
                    alert('Harap masukkan minimal satu jenis sampah yang diambil!');
                    return false;
                }

                // Konfirmasi sebelum submit
                const totalPoin = (beratKertas * 15) + (beratPlastik * 10) + (beratKardus * 12);
                const konfirmasi = confirm(
                    `Apakah Anda yakin ingin menyelesaikan pengambilan ini?\n\nTotal poin yang akan diberikan: ${totalPoin} poin\n\nPoin akan menunggu approval admin sebelum ditambahkan ke akun pengguna.`
                );

                if (!konfirmasi) {
                    e.preventDefault();
                    return false;
                }

                // Disable submit button to prevent double submission
                const btnSubmit = document.getElementById('btnSubmit');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="bi bi-clock me-1"></i>Memproses...';
            });

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                hitungPoin();
            });
        </script>
    @endpush
@endsection
