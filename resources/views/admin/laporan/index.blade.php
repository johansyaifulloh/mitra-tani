@extends('layouts.admin.app', ['navActive' => 'admin.laporan.index'])
@section('page-title', 'Laporan Penjualan')
@section('page-sub', 'Riwayat transaksi & rincian barang yang dibeli pelanggan')
@section('content')

<x-admin.pickup-info />

{{-- Summary cards --}}
<div class="panel-stats">
    <div class="panel-stat panel-card-hover">
        <div class="panel-stat__top">
            <span class="panel-stat__icon">💰</span>
        </div>
        <p class="panel-stat__label">Total Pendapatan (Lunas)</p>
        <p class="panel-stat__value panel-stat__value--ok">{{ \App\Support\SampleData::formatRp($summary['total_revenue'] ?? 0) }}</p>
        <p class="panel-stat__hint panel-stat__hint--up">{{ $summary['paid_count'] ?? 0 }} transaksi berhasil</p>
    </div>
    <div class="panel-stat panel-card-hover">
        <div class="panel-stat__top">
            <span class="panel-stat__icon">📦</span>
        </div>
        <p class="panel-stat__label">Total Pesanan</p>
        <p class="panel-stat__value">{{ $transactions->total() }}</p>
        <p class="panel-stat__hint">Semua status</p>
    </div>
    <div class="panel-stat panel-card-hover">
        <div class="panel-stat__top">
            <span class="panel-stat__icon">⏳</span>
        </div>
        <p class="panel-stat__label">Menunggu Pembayaran</p>
        <p class="panel-stat__value panel-stat__value--warn">{{ $summary['pending_count'] ?? 0 }}</p>
        <p class="panel-stat__hint">Belum diselesaikan pembeli</p>
    </div>
    <div class="panel-stat panel-card-hover">
        <div class="panel-stat__top">
            <span class="panel-stat__icon">✅</span>
        </div>
        <p class="panel-stat__label">Barang Sudah Diambil</p>
        <p class="panel-stat__value panel-stat__value--ok">{{ $summary['pickup_done_count'] ?? 0 }}</p>
        <p class="panel-stat__hint">Pengambilan tervalidasi</p>
    </div>
</div>

<div class="panel-card panel-card-hover">
    <div class="panel-card__head" style="flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 class="panel-card__title">Semua Transaksi</h3>
            <span class="panel-card__meta">{{ $transactions->total() }} data transaksi</span>
        </div>
        <div>
            <a href="{{ route('admin.laporan.pdf', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}" target="_blank" class="panel-btn panel-btn--sm" style="background:#059669; color:#fff; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export PDF Laporan
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.laporan.index') }}" class="panel-toolbar" style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
        <span style="font-size: 13px; font-weight: 600; color: #475569;">Filter Tanggal:</span>
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="panel-form__input panel-form__input--sm" style="max-width: 160px;">
        <span style="color: #94a3b8; font-size: 12px;">s/d</span>
        <input type="date" name="date_to" value="{{ $dateTo }}" class="panel-form__input panel-form__input--sm" style="max-width: 160px;">
        <button type="submit" class="panel-btn panel-btn--sm">Terapkan Filter</button>
        @if($dateFrom || $dateTo)
            <a href="{{ route('admin.laporan.index') }}" class="panel-btn panel-btn--sm panel-btn--outline" style="text-decoration:none;">Reset</a>
        @endif
    </form>

    <div class="panel-table-wrap">
        <table class="panel-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Rincian Barang Dibeli</th>
                    <th>Total</th>
                    <th>Metode Bayar</th>
                    <th>Pembayaran</th>
                    <th>Status Pengambilan</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td class="mono font-semibold">#{{ $trx['id'] }}</td>
                    <td>{{ $trx['date'] }}</td>
                    <td>
                        <p style="font-weight: 600; margin: 0; color: #1e293b;">{{ $trx['customer'] }}</p>
                        <p style="font-size: 11px; color: #64748b; margin: 0;">{{ $trx['phone'] ?: '-' }}</p>
                    </td>
                    <td style="max-width: 240px;">
                        <span style="font-size: 12px; color: #334155; line-height: 1.4; display: block;">
                            {{ $trx['items_summary'] }}
                        </span>
                    </td>
                    <td class="mono font-semibold" style="color: #047857;">{{ \App\Support\SampleData::formatRp($trx['total']) }}</td>
                    <td>{{ $trx['payment'] }}</td>
                    <td><x-admin.status-badge :status="$trx['payment_status']" type="payment" /></td>
                    <td><x-admin.status-badge :status="$trx['status']" /></td>
                    <td style="text-align: right;">
                        <button type="button" class="panel-btn panel-btn--sm panel-btn--outline" onclick="openDetailModal({{ json_encode($trx) }})" style="padding: 4px 10px; font-size: 12px; cursor: pointer;">
                            👁️ Rincian
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding: 30px; color: #94a3b8;">Tidak ada data transaksi ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="panel-card__foot">
        <x-admin.pagination :paginator="$transactions" />
    </div>
</div>

{{-- Modal Detail Transaksi & Barang --}}
<div id="trx-modal" style="display:none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px);">
    <div style="background: #fff; border-radius: 20px; max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); animation: modalPop 0.2s ease-out;" onclick="event.stopPropagation()">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 24px;">🧾</span>
                <div>
                    <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a;" id="modal-code">#TRX-000000</h3>
                    <p style="margin: 0; font-size: 12px; color: #64748b;" id="modal-date">Tanggal Transaksi</p>
                </div>
            </div>
            <button type="button" onclick="closeDetailModal()" style="background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 16px; color: #64748b;">✕</button>
        </div>

        <div style="padding: 24px;">
            {{-- Info Pembeli & Status --}}
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <p style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b; margin-bottom: 4px;">Informasi Pembeli</p>
                    <p style="font-weight: 700; color: #1e293b; margin: 0; font-size: 14px;" id="modal-customer">-</p>
                    <p style="font-size: 12px; color: #475569; margin-top: 2px;" id="modal-phone">-</p>
                    <p style="font-size: 11px; color: #64748b; margin-top: 4px;" id="modal-address">-</p>
                </div>
                <div>
                    <p style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b; margin-bottom: 4px;">Status Transaksi</p>
                    <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 4px;">
                        <div>
                            <span style="font-size: 11px; color: #64748b;">Pembayaran:</span>
                            <span id="modal-payment-status" style="font-weight: 700; font-size: 12px;">-</span>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: #64748b;">Pengambilan:</span>
                            <span id="modal-pickup-status" style="font-weight: 700; font-size: 12px;">-</span>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: #64748b;">Metode:</span>
                            <strong id="modal-payment-method" style="font-size: 12px; color: #1e293b;">-</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Barang yang Dibeli --}}
            <p style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 10px;">📦 Rincian Barang yang Dibeli</p>
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead style="background: #f1f5f9; color: #475569; text-transform: uppercase; font-size: 10.5px;">
                        <tr>
                            <th style="padding: 10px 12px; text-align: left;">Nama Produk</th>
                            <th style="padding: 10px 12px; text-align: right;">Harga</th>
                            <th style="padding: 10px 12px; text-align: center;">Qty</th>
                            <th style="padding: 10px 12px; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="modal-items-tbody">
                        {{-- Injected by JS --}}
                    </tbody>
                </table>
            </div>

            {{-- Ringkasan Biaya --}}
            <div style="background: #f8fafc; border-radius: 12px; padding: 14px 16px; display: flex; flex-direction: column; gap: 8px; font-size: 13px;">
                <div style="display: flex; justify-content: space-between; color: #64748b;">
                    <span>Subtotal Produk</span>
                    <span id="modal-subtotal" style="font-family: monospace; font-weight: 600;">Rp 0</span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #64748b;">
                    <span>Biaya Layanan</span>
                    <span id="modal-admin-fee" style="font-family: monospace; font-weight: 600;">Rp 0</span>
                </div>
                <div style="border-top: 1px solid #e2e8f0; padding-top: 8px; display: flex; justify-content: space-between; font-weight: 800; font-size: 15px; color: #047857;">
                    <span>TOTAL AKHIR</span>
                    <span id="modal-total" style="font-family: monospace;">Rp 0</span>
                </div>
            </div>
            {{-- Bukti Pengambilan Barang (Jika Ada) --}}
            <div id="modal-proof-section" style="display: none; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 14px; padding: 16px; margin-top: 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <p style="font-size: 12px; font-weight: 800; color: #065f46; margin: 0; display: flex; align-items: center; gap: 6px;">
                        <span>📸</span> Bukti Pengambilan Barang
                    </p>
                    <span style="font-size: 10.5px; font-weight: 700; background: #059669; color: #fff; padding: 2px 8px; border-radius: 9999px;">✓ Terverifikasi</span>
                </div>
                <div style="display: flex; gap: 14px; align-items: center;">
                    <a id="modal-proof-link" href="#" target="_blank" rel="noopener" style="display: block; width: 100px; height: 75px; border-radius: 10px; overflow: hidden; border: 1px solid #6ee7b7; flex-shrink: 0;">
                        <img id="modal-proof-img" src="" alt="Bukti Foto" style="width: 100%; height: 100%; object-fit: cover;">
                    </a>
                    <div style="font-size: 11.5px; color: #047857; line-height: 1.5;">
                        <p style="margin: 0;"><strong>Diverifikasi oleh:</strong> <span id="modal-proof-verifier" style="color: #0f172a;">-</span></p>
                        <p style="margin: 2px 0 0;"><strong>Waktu:</strong> <span id="modal-proof-time" style="color: #0f172a;">-</span></p>
                        <p id="modal-proof-note-wrap" style="margin: 4px 0 0; color: #065f46; font-style: italic;"><strong>Catatan:</strong> <span id="modal-proof-note"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
            <button type="button" class="panel-btn" onclick="closeDetailModal()" style="background:#0f172a; color:#fff;">Tutup</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openDetailModal(trx) {
    document.getElementById('modal-code').textContent = '#' + trx.id;
    document.getElementById('modal-date').textContent = trx.date;
    document.getElementById('modal-customer').textContent = trx.customer || 'Pelanggan';
    document.getElementById('modal-phone').textContent = trx.phone || '-';
    document.getElementById('modal-address').textContent = '📍 ' + (trx.address || 'Ambil di Toko Selorejo');
    document.getElementById('modal-payment-status').textContent = trx.payment_status === 'lunas' ? '✅ Lunas' : (trx.payment_status === 'expired' ? '❌ Kedaluwarsa' : '⏳ Menunggu');
    document.getElementById('modal-pickup-status').textContent = trx.status === 'disetujui' || trx.status === 'selesai' ? '✅ Sudah Diambil' : (trx.status === 'menunggu_approval' ? '⏳ Menunggu Diambil' : '-');
    document.getElementById('modal-payment-method').textContent = trx.payment || '-';
    document.getElementById('modal-subtotal').textContent = trx.subtotal_rp || 'Rp 0';
    document.getElementById('modal-admin-fee').textContent = trx.admin_fee_rp || 'Rp 0';
    document.getElementById('modal-total').textContent = 'Rp ' + Number(trx.total).toLocaleString('id-ID');

    var tbody = document.getElementById('modal-items-tbody');
    tbody.innerHTML = '';

    if (trx.items && trx.items.length > 0) {
        trx.items.forEach(function(item) {
            var tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #f1f5f9';
            tr.innerHTML = '<td style="padding: 10px 12px; font-weight:600; color:#1e293b;">' + (item.emoji || '🌱') + ' ' + item.product_name + '</td>' +
                '<td style="padding: 10px 12px; text-align:right; font-family:monospace;">' + item.unit_price_rp + '</td>' +
                '<td style="padding: 10px 12px; text-align:center; font-weight:700;">' + item.quantity + '</td>' +
                '<td style="padding: 10px 12px; text-align:right; font-weight:700; font-family:monospace; color:#047857;">' + item.subtotal_rp + '</td>';
            tbody.appendChild(tr);
        });
    } else {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td colspan="4" style="text-align:center; padding:16px; color:#94a3b8;">Data barang dalam transaksi ini: ' + (trx.items_summary || '1 Paket Produk') + '</td>';
        tbody.appendChild(tr);
    }

    // Tampilkan Bukti Pengambilan jika ada
    var proofSection = document.getElementById('modal-proof-section');
    if (trx.pickup_proof && trx.pickup_proof.photo) {
        document.getElementById('modal-proof-img').src = trx.pickup_proof.photo;
        document.getElementById('modal-proof-link').href = trx.pickup_proof.photo;
        document.getElementById('modal-proof-verifier').textContent = trx.pickup_proof.verified_by || 'Admin';
        document.getElementById('modal-proof-time').textContent = trx.pickup_proof.verified_at || '-';
        if (trx.pickup_proof.note) {
            document.getElementById('modal-proof-note').textContent = trx.pickup_proof.note;
            document.getElementById('modal-proof-note-wrap').style.display = 'block';
        } else {
            document.getElementById('modal-proof-note-wrap').style.display = 'none';
        }
        proofSection.style.display = 'block';
    } else {
        proofSection.style.display = 'none';
    }

    var modal = document.getElementById('trx-modal');
    modal.style.display = 'flex';
}

function closeDetailModal() {
    document.getElementById('trx-modal').style.display = 'none';
}

document.getElementById('trx-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailModal();
});
</script>
@endpush
