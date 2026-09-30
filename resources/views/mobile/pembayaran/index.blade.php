@extends('layouts.mobile.app', ['navActive' => 'transaksi'])
@section('title', 'Rincian Pesanan #' . $order['code'] . ' | Mantri Tani')
@section('header')
<header class="page-header">
<a href="{{ route('toko.transaksi.index') }}" class="page-header__back" title="Kembali ke Riwayat Transaksi">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg>
</a>
<h1 class="page-header__title">Rincian Pesanan</h1>
<div class="page-header__action"></div>
</header>
@endsection

@section('content')
<div class="space-y-3 pb-8">

{{-- Status Banner Ala Shopee --}}
@if(in_array($order['pickup_status'], ['disetujui', 'selesai']) || !empty($order['proof']))
<div class="rounded-2xl p-4 text-white shadow-sm flex items-start gap-3.5" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0 text-xl font-bold">✓</div>
    <div class="min-w-0 flex-1">
        <h2 class="font-extrabold text-base leading-tight">Pesanan Selesai / Sudah Diambil</h2>
        <p class="text-xs text-emerald-50 mt-1 leading-relaxed">Barang telah diambil pelanggan di Toko Mantri Tani & diverifikasi admin.</p>
        @if(!empty($order['proof']['verified_at']))
        <p class="text-[11px] text-emerald-100 mt-1.5 font-medium">🕒 Diverifikasi: {{ $order['proof']['verified_at'] }}</p>
        @endif
    </div>
</div>
@elseif($order['payment_status'] === 'lunas')
<div class="rounded-2xl p-4 text-white shadow-sm flex items-start gap-3.5" style="background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);">
    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0 text-xl">🏪</div>
    <div class="min-w-0 flex-1">
        <h2 class="font-extrabold text-base leading-tight">Menunggu Pengambilan</h2>
        <p class="text-xs text-teal-50 mt-1 leading-relaxed">Pembayaran lunas. Tunjukkan kode transaksi ke kasir Toko Mantri Tani Selorejo.</p>
    </div>
</div>
@elseif($order['payment_status'] === 'menunggu_pembayaran')
<div class="rounded-2xl p-4 text-white shadow-sm flex items-start gap-3.5" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0 text-xl">⏳</div>
    <div class="min-w-0 flex-1">
        <h2 class="font-extrabold text-base leading-tight">Menunggu Pembayaran</h2>
        <p class="text-xs text-amber-50 mt-1 leading-relaxed">Selesaikan pembayaran sebelum {{ $order['expired_at_label'] ?? 'batas waktu' }}.</p>
    </div>
</div>
@else
<div class="rounded-2xl p-4 text-white shadow-sm flex items-start gap-3.5" style="background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%);">
    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0 text-xl">✕</div>
    <div class="min-w-0 flex-1">
        <h2 class="font-extrabold text-base leading-tight">Pesanan Dibatalkan / Kadaluarsa</h2>
        <p class="text-xs text-rose-50 mt-1 leading-relaxed">Waktu pembayaran telah habis atau pesanan dibatalkan.</p>
    </div>
</div>
@endif

{{-- KARTU BUKTI PENGAMBILAN BARANG (ALA SHOPEE) --}}
@if(!empty($order['proof']))
<div class="panel p-4 border border-emerald-200 bg-white rounded-2xl shadow-sm">
    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <span class="text-lg">📸</span>
            <h3 class="font-bold text-sm text-gray-800">Bukti Pengambilan Barang</h3>
        </div>
        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Terverifikasi
        </span>
    </div>

    <div class="mt-3">
        @if(!empty($order['proof']['photo']))
        <div class="relative group cursor-pointer overflow-hidden rounded-xl bg-gray-100 border border-gray-200" id="btn-open-lightbox">
            <img src="{{ $order['proof']['photo'] }}"
                 alt="Foto bukti pengambilan {{ $order['code'] }}"
                 class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'p-8 text-center text-gray-400\'><span class=\'text-3xl\'>📦</span><p class=\'text-xs font-semibold mt-2\'>Foto Bukti Pengambilan</p></div>';">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-90 flex items-end p-3 text-white">
                <div class="flex items-center justify-between w-full">
                    <span class="text-[11px] font-medium flex items-center gap-1">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                        Ketuk untuk perbesar foto
                    </span>
                    <span class="text-[10px] bg-white/30 px-2 py-0.5 rounded backdrop-blur-sm">Ukuran Penuh</span>
                </div>
            </div>
        </div>
        @endif

        <div class="mt-3 space-y-1.5 text-xs">
            <div class="flex justify-between text-gray-600">
                <span>Diverifikasi Oleh</span>
                <span class="font-semibold text-gray-800">{{ $order['proof']['verified_by'] }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Waktu Verifikasi</span>
                <span class="font-semibold text-gray-800">{{ $order['proof']['verified_at'] }}</span>
            </div>
            @if(!empty($order['proof']['note']))
            <div class="mt-2 p-2.5 bg-emerald-50/70 border border-emerald-100 rounded-xl text-xs text-emerald-800 leading-relaxed">
                <span class="font-bold">Catatan Toko:</span> {{ $order['proof']['note'] }}
            </div>
            @endif
        </div>
    </div>
</div>
@endif

{{-- KODE TRANSAKSI & PENGAMBILAN --}}
<div class="panel p-4 bg-white rounded-2xl shadow-sm">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">No. Pesanan / Kode Pengambilan</p>
            <p class="text-lg font-black text-gray-800 mt-0.5 font-mono select-all" id="trx-code-text">#{{ $order['code'] }}</p>
        </div>
        <button type="button" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-full active:scale-95 transition-transform" id="btn-copy-code">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <span id="btn-copy-label">Salin</span>
        </button>
    </div>
    <div class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
        <span>Waktu Pemesanan</span>
        <span>{{ $order['created_at_label'] }}</span>
    </div>
</div>

{{-- RINCIAN PRODUK DIBELI (ALA SHOPEE) --}}
<div class="panel p-4 bg-white rounded-2xl shadow-sm">
    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
        <div class="flex items-center gap-1.5">
            <span class="text-sm font-bold text-gray-800">🛍️ Rincian Produk</span>
        </div>
        <span class="text-xs text-gray-500">{{ count($order['items']) }} Item</span>
    </div>

    <div class="divide-y divide-gray-100">
        @foreach($order['items'] as $item)
        <div class="py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-xl shrink-0 border border-emerald-100">
                    {{ $item['emoji'] }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-800 truncate">{{ $item['name'] }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Jumlah: ×{{ $item['quantity'] }}</p>
                </div>
            </div>
            <span class="text-xs font-bold text-gray-800 shrink-0">{{ $item['subtotal_label'] }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- RINCIAN PEMBAYARAN --}}
<div class="panel p-4 bg-white rounded-2xl shadow-sm">
    <p class="text-xs font-bold text-gray-800 pb-2 border-b border-gray-100">💳 Rincian Pembayaran</p>
    <div class="mt-3 space-y-2 text-xs">
        <div class="flex justify-between text-gray-600">
            <span>Metode Pembayaran</span>
            <span class="font-semibold text-gray-800">{{ $order['payment_method'] }}</span>
        </div>
        <div class="flex justify-between text-gray-600">
            <span>Subtotal Produk</span>
            <span class="font-semibold text-gray-800">{{ $order['subtotal_label'] }}</span>
        </div>
        <div class="flex justify-between text-gray-600">
            <span>Biaya Layanan Admin</span>
            <span class="font-semibold text-gray-800">{{ $order['admin_fee_label'] }}</span>
        </div>
        <div class="flex justify-between text-gray-900 font-extrabold text-sm pt-2 border-t border-gray-100">
            <span>Total Pembayaran</span>
            <span class="text-emerald-700 text-base font-black">{{ $order['total_label'] }}</span>
        </div>
    </div>
</div>

{{-- INFORMASI PENGAMBILAN & ALAMAT --}}
<div class="panel p-4 bg-white rounded-2xl shadow-sm">
    <p class="text-xs font-bold text-gray-800 pb-2 border-b border-gray-100">📍 Lokasi Pengambilan Toko</p>
    <div class="mt-3 text-xs space-y-1.5">
        <div class="p-3 bg-emerald-50/70 border border-emerald-100 rounded-xl">
            <p class="font-bold text-emerald-900">Toko Mitra Tani Selorejo</p>
            <p class="text-emerald-700 text-[11px] mt-0.5">Jl. Raya Selorejo, Malang, Jawa Timur</p>
            <p class="text-emerald-600 text-[11px] mt-1 font-medium">Buka Setiap Hari: 07.30 - 17.00 WIB</p>
        </div>
        <div class="pt-2 text-gray-600">
            <p class="text-[11px] text-gray-400 font-semibold uppercase">Nama Pembeli:</p>
            <p class="font-bold text-gray-800 mt-0.5">{{ $order['buyer_name'] }} · {{ $order['buyer_phone'] }}</p>
            <p class="text-gray-500 text-[11px] mt-0.5">Alamat: {{ $order['address_text'] }}</p>
        </div>
    </div>
</div>

{{-- Tombol Bayar jika masih belum lunas --}}
@if($order['payment_status'] === 'menunggu_pembayaran')
<button type="button" class="btn-primary w-full py-4 text-sm font-bold flex items-center justify-center gap-2 shadow-lg shadow-emerald-700/20" id="snap-pay-btn">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    <span>Pilih Metode & Bayar Sekarang</span>
</button>
@endif

</div>

{{-- Modal Lightbox untuk Perbesar Foto Bukti Pengambilan (Ala Shopee) --}}
@if(!empty($order['proof']['photo']))
<div id="lightbox-modal" class="fixed inset-0 z-[99] hidden items-center justify-center bg-black/90 p-4 backdrop-blur-sm animate-fade-in" onclick="closeLightbox()">
    <div class="relative max-w-lg w-full bg-slate-900 rounded-3xl overflow-hidden shadow-2xl p-4 border border-white/10" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 text-white border-b border-white/10">
            <div>
                <h4 class="font-bold text-sm">Foto Bukti Pengambilan</h4>
                <p class="text-[11px] text-gray-400 font-mono">#{{ $order['code'] }}</p>
            </div>
            <button type="button" onclick="closeLightbox()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white text-base">✕</button>
        </div>
        <div class="mt-3">
            <img src="{{ $order['proof']['photo'] }}" alt="Bukti pengambilan penuh" class="w-full max-h-[65vh] object-contain rounded-2xl bg-black">
        </div>
        <div class="mt-3 pt-3 border-t border-white/10 text-xs text-gray-300 space-y-1">
            <p><span class="text-gray-400">Diverifikasi oleh:</span> <strong class="text-white">{{ $order['proof']['verified_by'] }}</strong></p>
            <p><span class="text-gray-400">Waktu:</span> <span class="text-white">{{ $order['proof']['verified_at'] }}</span></p>
            @if(!empty($order['proof']['note']))
            <p class="text-emerald-400"><span class="text-gray-400">Catatan:</span> {{ $order['proof']['note'] }}</p>
            @endif
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Salin Kode Transaksi
    var copyBtn = document.getElementById('btn-copy-code');
    var copyLabel = document.getElementById('btn-copy-label');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var text = '{{ $order['code'] }}';
            navigator.clipboard.writeText(text).then(function () {
                copyLabel.textContent = 'Tersalin!';
                copyBtn.classList.add('bg-emerald-600', 'text-white');
                setTimeout(function () {
                    copyLabel.textContent = 'Salin';
                    copyBtn.classList.remove('bg-emerald-600', 'text-white');
                }, 2000);
            });
        });
    }

    // Lightbox modal foto bukti
    var lightbox = document.getElementById('lightbox-modal');
    var openBtn = document.getElementById('btn-open-lightbox');
    if (openBtn && lightbox) {
        openBtn.addEventListener('click', function () {
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
        });
    }
});

function closeLightbox() {
    var lightbox = document.getElementById('lightbox-modal');
    if (lightbox) {
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
    }
}
</script>

@if($order['payment_status'] !== 'lunas' && ($clientKey ?? null) && ($order['snap_token'] ?? null))
@if($isProduction ?? false)
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
@else
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('snap-pay-btn');
    var snapToken = @json($order['snap_token']);
    var syncUrl = @json(route('toko.pembayaran.sync', $order['code']));
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    if (!btn || !window.snap) return;

    function syncResult(result) {
        return fetch(syncUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                payment_type: (result && result.payment_type) || null,
                transaction_status: (result && result.transaction_status) || null,
                transaction_id: (result && result.transaction_id) || null,
            }),
        }).catch(function () {});
    }

    btn.addEventListener('click', function () {
        btn.disabled = true;
        snap.pay(snapToken, {
            onSuccess: function (result) { syncResult(result).then(function () { window.location.reload(); }); },
            onPending: function (result) { syncResult(result).then(function () { window.location.reload(); }); },
            onError: function () {
                if (window.MtNotify) MtNotify.toast('Pembayaran gagal atau dibatalkan.', 'error', 'Gagal');
                btn.disabled = false;
            },
            onClose: function () { btn.disabled = false; },
        });
    });
});
</script>
@endif
@endpush
