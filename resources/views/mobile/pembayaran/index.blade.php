@extends('layouts.mobile.app', ['navActive' => 'keranjang'])
@section('title', 'Pembayaran | Mantri Tani')
@section('header')
<header class="page-header">
<a href="{{ route('toko.checkout.index') }}" class="page-header__back"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg></a>
<h1 class="page-header__title">Pembayaran</h1><div class="page-header__action"></div>
</header>
@endsection
@section('content')
<div class="panel p-6 text-center mb-4 animate-card-in">
<p class="text-xs text-gray-400 uppercase tracking-wide">Total Pembayaran</p>
<p class="text-3xl font-extrabold text-emerald-700 mt-1">{{ $order['total_label'] }}</p>
<p class="text-[10px] text-gray-400 mt-1">#{{ $order['code'] }} · Bayar sebelum {{ \Carbon\Carbon::parse($order['expired_at'])->format('d M Y H:i') }}</p>
</div>

<section class="panel mb-4 animate-card-in" style="animation-delay:.08s">
<p class="panel__label">Ringkasan Pesanan</p>
<div class="text-sm space-y-2">
@foreach($order['items'] as $item)
<div class="flex justify-between text-gray-600">
<span>{{ $item['emoji'] }} {{ $item['name'] }} ×{{ $item['quantity'] }}</span>
<span>{{ $item['subtotal_label'] }}</span>
</div>
@endforeach
<div class="flex justify-between text-gray-600 border-t border-gray-100 pt-2 mt-2">
<span>Biaya Admin</span>
<span>{{ $order['admin_fee_label'] }}</span>
</div>
</div>
</section>

@if($order['payment_status'] === 'lunas')
<div class="panel p-5 text-center border-emerald-200 bg-emerald-50 animate-card-in mb-4">
<div class="w-12 h-12 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto text-xl">✓</div>
<p class="font-bold text-emerald-800 mt-2">Pembayaran Berhasil</p>
<p class="text-xs font-mono font-bold text-emerald-700 mt-2 bg-white/60 inline-block px-3 py-1 rounded-full">Kode: {{ $order['code'] }}</p>
<p class="text-xs text-emerald-600 mt-2 leading-relaxed px-2">Tunjukkan kode transaksi saat ambil barang di toko.<br>Admin akan verifikasi pengambilan.</p>
</div>
@else
<section class="panel mb-4 animate-card-in" style="animation-delay:.16s">
<p class="panel__label">Metode Pembayaran</p>
<p class="text-sm text-gray-600 leading-relaxed">Tekan tombol di bawah untuk memilih metode pembayaran: <span class="font-medium text-gray-800">QRIS, Virtual Account (BCA/BNI/BRI/Mandiri), GoPay, ShopeePay, Dana</span>, dan lainnya.</p>
<p class="text-[11px] text-gray-400 mt-2 leading-relaxed">Pilihan metode akan muncul pada jendela pembayaran Midtrans.</p>
</section>
<button type="button" class="btn-primary w-full py-4 text-sm mb-4 flex items-center justify-center gap-2" id="snap-pay-btn">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
<span>Pilih Metode & Bayar</span>
</button>
@endif

<section class="panel mt-3 animate-card-in" style="animation-delay:.3s">
<p class="panel__label">Alamat & Pengambilan</p>
<p class="text-sm font-semibold text-gray-900">{{ $order['buyer_name'] }} · {{ $order['buyer_phone'] }}</p>
<p class="text-xs text-gray-500 mt-1 leading-relaxed">📍 {{ $order['address_text'] }}</p>
<p class="text-xs text-emerald-600 mt-2 font-medium">Ambil di Toko Mantri Tani, Selorejo</p>
</section>
@endsection

@if($order['payment_status'] !== 'lunas' && $clientKey && $order['snap_token'])
@push('scripts')
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
                MtNotify.toast('Pembayaran gagal atau dibatalkan.', 'error', 'Gagal');
                btn.disabled = false;
            },
            onClose: function () { btn.disabled = false; },
        });
    });
});
</script>
@endpush
@endif
