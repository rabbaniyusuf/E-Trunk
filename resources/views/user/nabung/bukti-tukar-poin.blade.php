@extends('layouts.main')

@section('title', 'Bukti Penukaran Poin - E-TRANK')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <!-- Success Alert -->
            <div class="alert alert-success text-center mb-4">
                <i class="bi bi-check-circle-fill" style="font-size: 2rem;"></i>
                <h4 class="mt-2 mb-0">Pengajuan Berhasil!</h4>
                <p class="mb-0">Penukaran poin Anda telah diajukan</p>
            </div>

            <!-- Bukti Penukaran Card -->
            <div class="card" id="redemptionProof">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i>
                        BUKTI PENUKARAN POIN
                    </h5>
                </div>
                <div class="card-body">
                    <!-- QR Code or Barcode could be added here -->
                    <div class="text-center mb-4">
                        <div class="bg-light rounded p-3 d-inline-block">
                            <div class="fw-bold h4 mb-0 text-primary">{{ $redemption->redemption_code }}</div>
                            <small class="text-muted">Kode Penukaran</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="text-muted small">Nama</label>
                                <div class="fw-bold">{{ $redemption->user->name }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="text-muted small">Tanggal Pengajuan</label>
                                <div class="fw-bold">{{ $redemption->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="text-muted small">Poin Ditukar</label>
                                <div class="fw-bold text-primary">{{ number_format($redemption->points_redeemed) }} poin
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="text-muted small">Nilai Tukar</label>
                                <div class="fw-bold text-success">Rp
                                    {{ number_format($redemption->cash_value, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <div>
                            <span class="badge {{ $redemption->status_badge }} fs-6">
                                {{ $redemption->status_text }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Alamat Pengambilan</label>
                        <div class="fw-bold">Jl. Teluk Cendrawasih No. 20</div>
                    </div>

                    @if ($redemption->notes)
                        <div class="mb-3">
                            <label class="text-muted small">Catatan</label>
                            <div class="fw-bold">{{ $redemption->notes }}</div>
                        </div>
                    @endif

                    <!-- Status Timeline -->
                    <div class="border-top pt-3 mt-4">
                        <h6 class="mb-3">Status Penukaran:</h6>
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-success rounded-circle p-1 me-3" style="width: 12px; height: 12px;"></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">Pengajuan Diterima</div>
                                <small class="text-muted">{{ $redemption->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-{{ $redemption->status === 'completed' ? 'success' : 'light' }} rounded-circle p-1 me-3"
                                style="width: 12px; height: 12px;"></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold {{ $redemption->status !== 'completed' ? 'text-muted' : '' }}">
                                    Penukaran Selesai
                                </div>
                                <small class="text-muted">
                                    @if ($redemption->completed_at)
                                        {{ $redemption->completed_at->format('d/m/Y H:i') }}
                                    @else
                                        Belum selesai
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <button onclick="printProof()" class="btn btn-outline-primary me-2">
                    <i class="bi bi-printer"></i>
                    Cetak Bukti
                </button>
                <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-house"></i>
                    Kembali ke Dashboard
                </a>
            </div>

            <!-- Important Notes -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title text-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Penting!
                    </h6>
                    <ul class="mb-0 small">
                        <li>Simpan bukti penukaran ini dengan baik</li>
                        <li>Tunjukkan bukti ini kepada admin saat melakukan penukaran</li>
                        <li>Pastikan alamat pengambilan sudah benar</li>
                        <li>Petugas akan menghubungi Anda untuk konfirmasi jadwal pengambilan</li>
                        <li>Penukaran hanya dapat dilakukan saat status sudah "Disetujui"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function printProof() {
                const content = document.getElementById('redemptionProof').innerHTML;

                const printWindow = window.open('', '_blank', 'width=800,height=600');
                printWindow.document.write(`
            <html>
            <head>
                <title>Bukti Penukaran - E-TRANK</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body {
                        font-family: 'Segoe UI', sans-serif;
                        background: #fff;
                        padding: 30px;
                    }

                    .brand-header {
                        text-align: center;
                        border-bottom: 2px dashed #333;
                        margin-bottom: 30px;
                        padding-bottom: 10px;
                    }

                    .brand-header h2 {
                        margin: 0;
                        font-size: 24px;
                        color: #007bff;
                    }

                    .print-card {
                        border: 2px solid #333;
                        padding: 25px;
                        border-radius: 8px;
                    }

                    .print-title {
                        text-align: center;
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 25px;
                    }

                    .row + .row {
                        margin-top: 1rem;
                    }

                    .qr-placeholder {
                        width: 100px;
                        height: 100px;
                        border: 1px dashed #ccc;
                        display: inline-block;
                        margin: auto;
                        margin-bottom: 20px;
                        line-height: 100px;
                        text-align: center;
                        color: #999;
                        font-size: 12px;
                    }

                    .label {
                        font-size: 13px;
                        color: #888;
                    }

                    .value {
                        font-weight: 600;
                        font-size: 15px;
                        color: #000;
                    }

                    .footer-note {
                        font-size: 12px;
                        margin-top: 30px;
                        text-align: center;
                        color: #555;
                    }
                </style>
            </head>
            <body>
                <div class="brand-header">
                    <h2>E-TRANK</h2>
                    <small>Bukti Penukaran Poin</small>
                </div>

                <div class="print-card">
                    <div class="print-title">Informasi Penukaran</div>
                    ${content}
                </div>

                <div class="footer-note">
                    Simpan dan tunjukkan bukti ini saat melakukan penukaran. Terima kasih telah menggunakan E-TRANK.
                </div>

                <script>
                    window.onload = function() {
                        window.print();
                        window.onafterprint = function () {
                            window.close();
                        };
                    };
                <\/script>
            </body>
            </html>
        `);
                printWindow.document.close();
            }
        </script>
    @endpush


    @push('styles')
        <style>
            @media print {

                .btn,
                .alert,
                .card:last-child {
                    display: none !important;
                }

                .card {
                    border: 2px solid #000 !important;
                    box-shadow: none !important;
                }

                .card-header {
                    background-color: #000 !important;
                    color: #fff !important;
                }
            }
        </style>
    @endpush
@endsection
