<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $report_title }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
        }

        .header-table td {
            vertical-align: top;
        }

        .company-info h1 {
            color: #007bff;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-info h2 {
            color: #666;
            font-size: 12px;
            font-weight: normal;
            margin-bottom: 8px;
        }

        .company-details {
            font-size: 10px;
            color: #666;
        }

        .report-info {
            text-align: right;
            font-size: 10px;
        }

        .report-title {
            background-color: #007bff;
            color: white;
            padding: 12px;
            text-align: center;
            margin: 15px 0;
        }

        .report-title h3 {
            font-size: 16px;
            margin-bottom: 3px;
        }

        .period-info {
            background-color: #f8f9fa;
            padding: 12px;
            border-left: 3px solid #007bff;
            margin-bottom: 15px;
        }

        .period-table {
            width: 100%;
            font-size: 10px;
        }

        .period-table td {
            padding: 2px 0;
        }

        .summary-section {
            margin-bottom: 20px;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: center;
            width: 22%;
            display: inline-block;
            margin: 1%;
            vertical-align: top;
        }

        .summary-card .value {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 2px;
        }

        .summary-card .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }

        .section-title {
            background-color: #e9ecef;
            padding: 8px 12px;
            font-weight: bold;
            color: #495057;
            border-left: 3px solid #007bff;
            margin: 15px 0 8px;
            font-size: 12px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }

        .data-table th {
            background-color: #007bff;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #0056b3;
        }

        .data-table td {
            padding: 4px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .type-deposit {
            color: #28a745;
            font-weight: bold;
        }

        .type-withdrawal {
            color: #dc3545;
            font-weight: bold;
        }

        .points-positive {
            color: #28a745;
            font-weight: bold;
        }

        .points-negative {
            color: #dc3545;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 25px;
            color: #666;
            font-style: italic;
        }

        .analysis-section {
            background-color: #f8f9fa;
            padding: 12px;
            margin: 15px 0;
        }

        .analysis-table {
            width: 100%;
        }

        .analysis-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }

        .analysis-list {
            margin-left: 15px;
            line-height: 1.5;
        }

        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            font-size: 9px;
            color: #666;
        }

        .footer-table {
            width: 100%;
        }

        .currency {
            font-family: 'DejaVu Sans Mono', monospace;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-warning {
            color: #ffc107;
        }

        .text-info {
            color: #17a2b8;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 70%;">
                    <div class="company-info">
                        <h1>{{ $company_info['name'] }}</h1>
                        <h2>{{ $company_info['subtitle'] }}</h2>
                        <div class="company-details">
                            <div>📍 {{ $company_info['address'] }}</div>
                            <div>📞 {{ $company_info['phone'] }}</div>
                            <div>✉️ {{ $company_info['email'] }}</div>
                        </div>
                    </div>
                </td>
                <td style="width: 30%;">
                    <div class="report-info">
                        <div><strong>Tanggal Generate:</strong><br>{{ $generated_at->format('d/m/Y H:i') }} WIB</div>
                        <div style="margin-top: 5px;"><strong>Dibuat oleh:</strong><br>{{ $generated_by->name }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        <h3>{{ $report_title }}</h3>
        <div>Sistem Monitoring dan Pengelolaan Sampah Digital</div>
    </div>

    <!-- Period Information -->
    <div class="period-info">
        <strong>📅 Periode Laporan</strong>
        <table class="period-table" style="margin-top: 5px;">
            <tr>
                <td><strong>Dari:</strong> {{ $period['start']->format('d F Y') }}</td>
                <td><strong>Sampai:</strong> {{ $period['end']->format('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Durasi:</strong> {{ $period['days'] }} hari</td>
                <td><strong>Jenis:</strong> {{ $report_title }}</td>
            </tr>
        </table>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="section-title">📊 RINGKASAN EKSEKUTIF</div>

        <div style="text-align: center;">
            <div class="summary-card">
                <div class="value">{{ number_format($summary['total_transactions']) }}</div>
                <div class="label">Total Transaksi</div>
            </div>
            <div class="summary-card">
                <div class="value">{{ number_format($summary['total_redemptions']) }}</div>
                <div class="label">Total Penukaran</div>
            </div>
            <div class="summary-card">
                <div class="value text-success">{{ number_format($summary['total_points_earned']) }}</div>
                <div class="label">Poin Terkumpul</div>
            </div>
            <div class="summary-card">
                <div class="value text-danger">{{ number_format($summary['total_points_redeemed']) }}</div>
                <div class="label">Poin Ditukar</div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 10px;">
            <div class="summary-card">
                <div class="value currency">Rp {{ number_format($summary['total_cash_redeemed'], 0, ',', '.') }}</div>
                <div class="label">Total Uang Ditukar</div>
            </div>
            <div class="summary-card">
                <div class="value text-info">{{ number_format($summary['active_users']) }}</div>
                <div class="label">User Aktif</div>
            </div>
            <div class="summary-card">
                <div class="value text-warning">{{ number_format($summary['pending_transactions']) }}</div>
                <div class="label">Transaksi Pending</div>
            </div>
            <div class="summary-card">
                <div class="value text-success">{{ number_format($summary['completed_transactions']) }}</div>
                <div class="label">Transaksi Selesai</div>
            </div>
        </div>
    </div>

    @if (in_array($report_type, ['transactions', 'combined']) && $transactions->count() > 0)
        <!-- Transactions Section -->
        <div class="section-title">💰 DETAIL TRANSAKSI POIN</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 20%;">User</th>
                    <th style="width: 10%;">Jenis</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 8%;">Berat</th>
                    <th style="width: 10%;">Poin</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 8%;">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                        <td class="text-center">
                            <span
                                class="{{ $transaction->transaction_type === 'deposit' ? 'type-deposit' : 'type-withdrawal' }}">
                                {{ $transaction->getTypeLabel() }}
                            </span>
                        </td>
                        <td>{{ $transaction->description ?? '-' }}</td>
                        <td class="text-center">
                            @if ($transaction->percentage_deposited)
                                {{ number_format($transaction->percentage_deposited, 1) }} kg
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            <span
                                class="{{ $transaction->transaction_type === 'deposit' ? 'points-positive' : 'points-negative' }}">
                                {{ $transaction->transaction_type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->points) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span
                                class="status-badge
                        @if ($transaction->status === 'MENUNGGU_DIAMBIL') status-pending
                        @elseif($transaction->status === 'SUDAH_DIAMBIL') status-completed
                        @else status-rejected @endif">
                                {{ $transaction->getStatusLabel() }}
                            </span>
                        </td>
                        <td>{{ $transaction->processedBy->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if (in_array($report_type, ['redemptions', 'combined']) && $redemptions->count() > 0)
        <!-- Redemptions Section -->
        @if ($report_type === 'combined' && $transactions->count() > 0)
            <div class="page-break"></div>
        @endif
        <div class="section-title">🔄 DETAIL PENUKARAN POIN</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 18%;">User</th>
                    <th style="width: 15%;">Kode</th>
                    <th style="width: 8%;">Poin</th>
                    <th style="width: 12%;">Nilai</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 10%;">Selesai</th>
                    <th style="width: 8%;">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($redemptions as $index => $redemption)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $redemption->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $redemption->user->name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $redemption->redemption_code }}</td>
                        <td class="text-right points-negative">-{{ number_format($redemption->points_redeemed) }}</td>
                        <td class="text-right currency">Rp {{ number_format($redemption->cash_value, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span
                                class="status-badge
                        @if ($redemption->status === 'pending') status-pending
                        @elseif($redemption->status === 'approved') status-approved
                        @elseif($redemption->status === 'completed') status-completed
                        @else status-rejected @endif">
                                {{ $redemption->status_text }}
                            </span>
                        </td>
                        <td class="text-center">
                            {{ $redemption->completed_at ? $redemption->completed_at->format('d/m/Y') : '-' }}
                        </td>
                        <td>{{ $redemption->processedBy->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if ($transactions->isEmpty() && $redemptions->isEmpty())
        <div class="no-data">
            <h4>📭 Tidak Ada Data</h4>
            <p>Tidak ada transaksi atau penukaran untuk periode {{ $period['start']->format('d F Y') }} -
                {{ $period['end']->format('d F Y') }}</p>
        </div>
    @endif

    <!-- Analysis Section -->
    @if ($transactions->count() > 0 || $redemptions->count() > 0)
        <div class="section-title">📈 ANALISIS SINGKAT</div>
        <div class="analysis-section">
            <table class="analysis-table">
                <tr>
                    <td>
                        <h4 style="color: #007bff; margin-bottom: 8px;">Performa Sistem</h4>
                        <ul class="analysis-list">
                            <li><strong>Tingkat Aktivitas:</strong> {{ $summary['active_users'] }} user aktif dalam
                                {{ $period['days'] }} hari</li>
                            <li><strong>Rata-rata Transaksi:</strong>
                                {{ $period['days'] > 0 ? number_format($summary['total_transactions'] / $period['days'], 1) : 0 }}
                                transaksi/hari</li>
                            <li><strong>Efisiensi Pengambilan:</strong>
                                {{ $summary['total_transactions'] > 0 ? number_format(($summary['completed_transactions'] / $summary['total_transactions']) * 100, 1) : 0 }}%
                            </li>
                            @if ($summary['total_points_earned'] > 0)
                                <li><strong>Rasio Penukaran:</strong>
                                    {{ number_format(($summary['total_points_redeemed'] / $summary['total_points_earned']) * 100, 1) }}%
                                </li>
                            @endif
                        </ul>
                    </td>
                    <td>
                        <h4 style="color: #28a745; margin-bottom: 8px;">Dampak Ekonomi</h4>
                        <ul class="analysis-list">
                            <li><strong>Total Nilai Ekonomis:</strong> Rp
                                {{ number_format($summary['total_cash_redeemed'], 0, ',', '.') }}</li>
                            <li><strong>Rata-rata per User:</strong> Rp
                                {{ $summary['active_users'] > 0 ? number_format($summary['total_cash_redeemed'] / $summary['active_users'], 0, ',', '.') : 0 }}
                            </li>
                            <li><strong>Saldo Poin Tersisa:</strong>
                                {{ number_format($summary['total_points_earned'] - $summary['total_points_redeemed']) }}
                                poin</li>
                            <li><strong>Potensi Nilai Sisa:</strong> Rp
                                {{ number_format((($summary['total_points_earned'] - $summary['total_points_redeemed']) / 10) * 1000, 0, ',', '.') }}
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width: 70%;">
                    <div><strong>E-TRANK System</strong> - Sistem Monitoring Sampah Digital</div>
                    <div>Generated: {{ $generated_at->format('d F Y, H:i') }} WIB | By: {{ $generated_by->name }}
                    </div>
                </td>
                <td style="width: 30%; text-align: right;">
                    <div>Dokumen dibuat secara otomatis</div>
                    <div>{{ $company_info['address'] }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
