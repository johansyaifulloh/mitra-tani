<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Mantri Tani - {{ date('Ymd') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #047857;
            --primary-light: #ecfdf5;
            --dark: #0f172a;
            --gray: #64748b;
            --border: #e2e8f0;
            --bg-table-head: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            font-size: 12px;
            line-height: 1.5;
            padding: 20px;
        }

        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 20px auto;
            background: #064e3b;
            color: #fff;
            padding: 14px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .no-print-bar__info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .no-print-bar__btn {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all .2s;
        }

        .no-print-bar__btn:hover {
            background: #059669;
        }

        .no-print-bar__btn-back {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            margin-right: 8px;
        }

        .pdf-page {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 35px 40px;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        /* Kop Surat */
        .kop-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #0f172a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .kop-header__left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .kop-header__logo {
            width: 52px;
            height: 52px;
            background: #047857;
            color: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
        }

        .kop-header__title {
            font-size: 20px;
            font-weight: 800;
            color: #064e3b;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .kop-header__subtitle {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .kop-header__address {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        .kop-header__right {
            text-align: right;
            font-size: 11px;
            color: #475569;
        }

        .kop-header__badge {
            display: inline-block;
            background: #ecfdf5;
            color: #047857;
            font-weight: 700;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid #a7f3d0;
            margin-bottom: 4px;
        }

        /* Judul Laporan */
        .report-title-box {
            text-align: center;
            margin-bottom: 24px;
        }

        .report-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-meta {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Summary Cards / KPI */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .summary-card {
            border: 1px solid var(--border);
            background: #fafafa;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .summary-card--primary {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .summary-card__label {
            font-size: 10.5px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;
        }

        .summary-card--primary .summary-card__label {
            color: #047857;
        }

        .summary-card__value {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }

        .summary-card--primary .summary-card__value {
            color: #065f46;
        }

        .summary-card__hint {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .summary-card--primary .summary-card__hint {
            color: #059669;
        }

        /* Table */
        .report-table-wrap {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            text-align: left;
        }

        .report-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            padding: 9px 10px;
            border-bottom: 2px solid var(--border);
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .report-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }

        .report-table tr:nth-child(even) {
            background: #fbfcfe;
        }

        .report-table tfoot td {
            background: #f8fafc;
            border-top: 2px solid var(--border);
            font-weight: 800;
            font-size: 11.5px;
            padding: 10px;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
        }

        .badge-success {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-warning {
            background: #fef9c3;
            color: #a16207;
        }

        .badge-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-neutral {
            background: #f1f5f9;
            color: #475569;
        }

        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 35px;
            page-break-inside: avoid;
        }

        .sign-box {
            text-align: center;
            width: 220px;
        }

        .sign-date {
            font-size: 11px;
            color: #475569;
            margin-bottom: 8px;
        }

        .sign-role {
            font-weight: 700;
            font-size: 11.5px;
            color: #0f172a;
        }

        .sign-space {
            height: 65px;
        }

        .sign-name {
            font-weight: 700;
            font-size: 12px;
            color: #0f172a;
            border-bottom: 1px solid #0f172a;
            display: inline-block;
            padding-bottom: 2px;
            min-width: 160px;
        }

        .sign-nip {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Print Media Styling */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .pdf-page {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
                max-width: 100%;
            }

            @page {
                size: A4 portrait;
                margin: 15mm 12mm 15mm 12mm;
            }

            .report-table {
                page-break-inside: auto;
            }

            .report-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

{{-- Interactive Action Bar for Screen --}}
<div class="no-print-bar">
    <div class="no-print-bar__info">
        <span style="font-size: 20px;">📄</span>
        <div>
            <strong style="font-size: 14px;">Pratinjau Dokumen PDF Laporan</strong>
            <p style="font-size: 11px; color: #a7f3d0;">Klik tombol di kanan untuk langsung menyimpan sebagai file PDF atau mencetak dokumen.</p>
        </div>
    </div>
    <div>
        <a href="javascript:history.back()" class="no-print-bar__btn-back">← Kembali</a>
        <button type="button" class="no-print-bar__btn" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak / Simpan PDF
        </button>
    </div>
</div>

<div class="pdf-page">

    {{-- Kop Surat Resmi --}}
    <div class="kop-header">
        <div class="kop-header__left">
            <div class="kop-header__logo">MT</div>
            <div>
                <h1 class="kop-header__title">MANTRI TANI</h1>
                <p class="kop-header__subtitle">Toko Sarana & Produksi Pertanian Terpercaya</p>
                <p class="kop-header__address">Jl. Raya Selorejo, Kec. Selorejo, Kab. Blitar, Jawa Timur | Telp/WA: 0813-0000-0001</p>
            </div>
        </div>
        <div class="kop-header__right">
            <span class="kop-header__badge">Dokumen Resmi Sistem</span>
            <p>Dicetak: <strong>{{ $printed_at }}</strong></p>
            <p>Oleh: <strong>{{ $userName }} ({{ $userRole }})</strong></p>
        </div>
    </div>

    {{-- Judul & Periode --}}
    <div class="report-title-box">
        <h2 class="report-title">Laporan Rekapitulasi Penjualan & Transaksi</h2>
        <p class="report-meta">
            Periode: 
            <strong>
                @if($date_from && $date_to)
                    {{ date('d F Y', strtotime($date_from)) }} s/d {{ date('d F Y', strtotime($date_to)) }}
                @elseif($date_from)
                    Mulai {{ date('d F Y', strtotime($date_from)) }} s/d Sekarang
                @elseif($date_to)
                    Sampai Dengan {{ date('d F Y', strtotime($date_to)) }}
                @else
                    Semua Data Transaksi Toko
                @endif
            </strong>
        </p>
    </div>

    {{-- Ringkasan KPI Eksekutif --}}
    <div class="summary-grid">
        <div class="summary-card summary-card--primary">
            <p class="summary-card__label">Total Omzet Lunas</p>
            <p class="summary-card__value">{{ \App\Support\SampleData::formatRp($summary['total_revenue'] ?? 0) }}</p>
            <p class="summary-card__hint">{{ $summary['paid_count'] ?? 0 }} transaksi berhasil</p>
        </div>
        <div class="summary-card">
            <p class="summary-card__label">Total Pesanan</p>
            <p class="summary-card__value">{{ $summary['total_orders'] ?? count($transactions) }}</p>
            <p class="summary-card__hint">Seluruh transaksi masuk</p>
        </div>
        <div class="summary-card">
            <p class="summary-card__label">Menunggu Bayar</p>
            <p class="summary-card__value" style="color: #b45309;">{{ $summary['pending_count'] ?? 0 }}</p>
            <p class="summary-card__hint">Belum lunas / proses</p>
        </div>
        <div class="summary-card">
            <p class="summary-card__label">Barang Diambil</p>
            <p class="summary-card__value" style="color: #047857;">{{ $summary['pickup_done_count'] ?? 0 }}</p>
            <p class="summary-card__hint">Pengambilan tervalidasi</p>
        </div>
    </div>

    {{-- Tabel Rincian Data Transaksi --}}
    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 28px;">No</th>
                    <th style="width: 85px;">Kode</th>
                    <th style="width: 75px;">Tanggal</th>
                    <th>Pelanggan</th>
                    <th style="width: 180px;">Rincian Barang Dibeli</th>
                    <th>Metode</th>
                    <th class="text-center" style="width: 75px;">Status</th>
                    <th class="text-right" style="width: 95px;">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $index => $trx)
                <tr>
                    <td class="text-center mono">{{ $index + 1 }}</td>
                    <td class="mono" style="font-weight: 700; color: #0f172a;">#{{ $trx['id'] }}</td>
                    <td>{{ $trx['date'] }}</td>
                    <td>
                        <strong style="display:block; color:#0f172a;">{{ $trx['customer'] }}</strong>
                        <span style="font-size:9.5px; color:#64748b;">{{ $trx['phone'] ?: '-' }}</span>
                    </td>
                    <td style="font-size: 10px; color: #334155; line-height: 1.35;">
                        {{ $trx['items_summary'] }}
                    </td>
                    <td>{{ $trx['payment'] }}</td>
                    <td class="text-center">
                        @if($trx['payment_status'] === 'lunas')
                            <span class="badge badge-success">Lunas</span>
                        @elseif($trx['payment_status'] === 'expired')
                            <span class="badge badge-danger">Kadaluarsa</span>
                        @else
                            <span class="badge badge-warning">Belum Bayar</span>
                        @endif
                    </td>
                    <td class="text-right mono" style="font-weight: 700; color: #047857;">
                        {{ \App\Support\SampleData::formatRp($trx['total']) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 30px; color: #94a3b8;">
                        Tidak ada transaksi pada periode yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="text-right">TOTAL PENDAPATAN (TRANSAKSI LUNAS):</td>
                    <td class="text-right mono" style="color: #047857; font-size: 13px;">
                        {{ \App\Support\SampleData::formatRp($summary['total_revenue'] ?? 0) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Tanda Tangan / Otorisasi --}}
    <div class="signatures">
        <div class="sign-box">
            <p class="sign-date">Mengetahui,</p>
            <p class="sign-role">Petugas Operasional / Admin</p>
            <div class="sign-space"></div>
            <p class="sign-name">{{ $userRole === 'Admin' ? $userName : 'Budi Santoso' }}</p>
            <p class="sign-nip">Mantri Tani Selorejo</p>
        </div>

        <div class="sign-box">
            <p class="sign-date">Selorejo, {{ date('d F Y') }}</p>
            <p class="sign-role">Owner / Pemilik Usaha</p>
            <div class="sign-space"></div>
            <p class="sign-name">{{ $userRole === 'Owner' ? $userName : 'Owner Mantri Tani' }}</p>
            <p class="sign-nip">Pimpinan Toko</p>
        </div>
    </div>

</div>

</body>
</html>
