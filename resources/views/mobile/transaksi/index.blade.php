@extends('layouts.mobile.app', ['navActive' => 'profil'])
@section('title', 'Pesanan Saya | Mantri Tani')
@section('main-class', 'px-5 py-4 pb-24 animate-fade-up')
@section('header')
<header class="page-header">
<a href="{{ route('toko.profil.index') }}" class="page-header__back"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg></a>
<h1 class="page-header__title">Pesanan Saya</h1>
<div class="page-header__action"></div>
</header>
@endsection
@section('content')
@php
$tabs = [
    'all' => ['label' => 'Semua', 'count' => $counts['all']],
    'menunggu_pembayaran' => ['label' => 'Belum Bayar', 'count' => $counts['menunggu_pembayaran']],
    'lunas' => ['label' => 'Lunas', 'count' => $counts['lunas']],
    'expired' => ['label' => 'Kadaluarsa', 'count' => $counts['expired']],
];
$toneClass = [
    'success' => 'bg-emerald-50 text-emerald-700',
    'warning' => 'bg-amber-50 text-amber-700',
    'danger' => 'bg-rose-50 text-rose-600',
    'neutral' => 'bg-gray-100 text-gray-600',
];
$pickupToneBg = [
    'success' => 'bg-emerald-50/60',
    'warning' => 'bg-amber-50/60',
    'danger' => 'bg-rose-50/60',
    'neutral' => 'bg-gray-50/60',
];
$pickupToneText = [
    'success' => 'text-emerald-700',
    'warning' => 'text-amber-700',
    'danger' => 'text-rose-600',
    'neutral' => 'text-gray-600',
];
@endphp

<div class="flex gap-2 overflow-x-auto scrollbar-hide -mx-5 px-5 mb-4">
@foreach($tabs as $key => $tab)
<a href="{{ $key === 'all' ? route('toko.transaksi.index') : route('toko.transaksi.index', ['status' => $key]) }}"
   class="shrink-0 px-4 py-2 rounded-full text-xs font-semibold transition {{ $active === $key ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-gray-500 border border-gray-200' }}">
{{ $tab['label'] }}@if($tab['count'] > 0) <span class="opacity-70">({{ $tab['count'] }})</span>@endif
</a>
@endforeach
</div>

@if(empty($orders))
<div class="panel p-8 text-center animate-card-in">
<div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-2xl">🧾</div>
<p class="font-semibold text-gray-700 mt-3">Belum ada transaksi</p>
<p class="text-xs text-gray-400 mt-1">Pesanan kamu akan muncul di sini setelah checkout.</p>
<a href="{{ route('home') }}" class="btn-primary inline-flex mt-4 px-5 py-2.5 text-sm">Mulai Belanja</a>
</div>
@else
<div class="space-y-3">
@foreach($orders as $order)
<div class="bg-white rounded-2xl shadow-sm overflow-hidden animate-card-in">
<div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-50">
<div class="flex items-center gap-1.5 text-gray-700">
<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
<span class="text-xs font-bold">Mantri Tani</span>
</div>
<span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $toneClass[$order['status_tone']] ?? $toneClass['neutral'] }}">{{ $order['status_label'] }}</span>
</div>

<a href="{{ route('toko.pembayaran.show', $order['code']) }}" class="flex gap-3 px-4 py-3">
<div class="w-14 h-14 rounded-xl bg-emerald-50 flex items-center justify-center text-2xl shrink-0">{{ $order['first_product_emoji'] }}</div>
<div class="flex-1 min-w-0">
<p class="text-sm font-semibold text-gray-800 truncate">{{ $order['first_product_name'] }}</p>
@if($order['more_count'] > 0)
<p class="text-[11px] text-gray-400 mt-0.5">+{{ $order['more_count'] }} produk lainnya</p>
@endif
<p class="text-[11px] text-gray-400 mt-1">x{{ $order['first_product_qty'] }}</p>
</div>
<div class="text-right shrink-0">
<p class="text-[10px] text-gray-400">#{{ $order['code'] }}</p>
</div>
</a>

<div class="px-4 py-2 bg-gray-50/60 flex items-center justify-between">
<span class="text-[11px] text-gray-500">Metode: <span class="font-semibold text-gray-700">{{ $order['payment_method'] }}</span></span>
<span class="text-xs text-gray-500">{{ $order['total_qty'] }} barang · Total <span class="font-extrabold text-emerald-700">{{ $order['total_label'] }}</span></span>
</div>

@if($order['is_unpaid'] && $order['expired_at'])
<div class="px-4 py-2 flex items-center gap-1.5 text-[11px] {{ $order['is_expired_soon'] ? 'text-rose-600' : 'text-amber-600' }} border-t border-gray-50">
<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 2"/></svg>
<span>Bayar dalam <span class="font-bold js-countdown" data-expired="{{ $order['expired_ms'] }}">—</span></span>
</div>
@endif

@if($order['payment_status'] === 'lunas' && $order['pickup_status'] && $order['pickup_status'] !== 'ditolak')
@php
$steps = ['Dibayar', 'Menunggu Pengambilan', 'Sudah Diambil'];
$stepIndex = match($order['pickup_status']) {
    'menunggu_approval' => 1,
    'disetujui' => 3,
    'selesai' => 3,
    default => 0,
};
@endphp
<div class="px-4 py-3 border-t border-gray-50">
<div class="flex items-center">
@foreach($steps as $i => $step)
@if($i > 0)
<div class="flex-1 h-0.5 {{ $i <= $stepIndex ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>
@endif
<div class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold shrink-0 {{ $i <= $stepIndex ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400' }}">
@if($i < $stepIndex)✓@else{{ $i + 1 }}@endif
</div>
@endforeach
</div>
<div class="flex justify-between mt-1.5">
@foreach($steps as $i => $step)
<span class="text-[9px] {{ $i <= $stepIndex ? 'text-emerald-600 font-semibold' : 'text-gray-400' }} text-center leading-tight" style="width:33%">{{ $step }}</span>
@endforeach
</div>
</div>
@endif

@if($order['pickup_label'])
<div class="px-4 py-2.5 flex items-start gap-2 border-t border-gray-50 {{ $pickupToneBg[$order['pickup_tone']] ?? '' }}">
<svg width="15" height="15" class="mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 100-4h14a2 2 0 100 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8M9 12h6"/></svg>
<div class="min-w-0 flex-1">
<p class="text-[11px] font-bold {{ $pickupToneText[$order['pickup_tone']] ?? 'text-gray-700' }}">Pengambilan: {{ $order['pickup_label'] }}</p>
<p class="text-[10px] text-gray-400 mt-0.5">{{ $order['pickup_desc'] }}</p>
@if($order['proof_photo'])
<button type="button" class="js-proof-btn mt-2 inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700"
  data-photo="{{ $order['proof_photo'] }}"
  data-note="{{ $order['proof_note'] }}"
  data-verifier="{{ $order['proof_verifier'] }}"
  data-time="{{ $order['proof_verified_at'] }}"
  data-code="{{ $order['code'] }}">
<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/><circle cx="12" cy="11" r="3"/></svg>
Lihat Bukti Pengambilan
</button>
@endif
</div>
</div>
@endif

<div class="px-4 py-3 flex items-center justify-between border-t border-gray-50">
<span class="text-[11px] text-gray-400">{{ $order['date'] }}</span>
@if($order['is_unpaid'])
<a href="{{ route('toko.pembayaran.show', $order['code']) }}" class="btn-primary px-5 py-2 text-xs">Bayar Sekarang</a>
@else
<a href="{{ route('toko.pembayaran.show', $order['code']) }}" class="px-5 py-2 text-xs font-semibold text-emerald-700 border border-emerald-600 rounded-full">Lihat Detail</a>
@endif
</div>
</div>
@endforeach
</div>
@endif

<div id="proof-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-6">
<div class="bg-white w-full max-w-[300px] rounded-2xl p-4 animate-fade-up max-h-[85vh] overflow-y-auto">
<div class="flex items-center justify-between mb-2">
<h3 class="font-bold text-gray-800 text-sm">Bukti Pengambilan</h3>
<button type="button" id="proof-close" class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-sm">✕</button>
</div>
<p class="text-[11px] text-gray-400 mb-2" id="proof-code"></p>
<img id="proof-img" src="" alt="Bukti pengambilan" class="w-full rounded-xl object-cover max-h-48 bg-gray-100">
<div class="mt-3 text-sm text-gray-600 space-y-1">
<p id="proof-verifier-wrap" class="hidden">Diverifikasi oleh: <span class="font-semibold text-gray-800" id="proof-verifier"></span></p>
<p id="proof-time-wrap" class="hidden text-xs text-gray-400"></p>
<p id="proof-note-wrap" class="hidden bg-gray-50 rounded-xl p-3 text-xs text-gray-600 mt-2"><span class="font-semibold">Catatan:</span> <span id="proof-note"></span></p>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('proof-modal');
    if (modal) {
        var img = document.getElementById('proof-img');
        var verifierWrap = document.getElementById('proof-verifier-wrap');
        var verifierEl = document.getElementById('proof-verifier');
        var timeWrap = document.getElementById('proof-time-wrap');
        var noteWrap = document.getElementById('proof-note-wrap');
        var noteEl = document.getElementById('proof-note');
        var codeEl = document.getElementById('proof-code');

        function openModal() { modal.classList.remove('hidden'); modal.classList.add('flex'); }
        function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

        document.querySelectorAll('.js-proof-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                img.src = btn.dataset.photo || '';
                codeEl.textContent = btn.dataset.code ? '#' + btn.dataset.code : '';
                if (btn.dataset.verifier) { verifierEl.textContent = btn.dataset.verifier; verifierWrap.classList.remove('hidden'); }
                else { verifierWrap.classList.add('hidden'); }
                if (btn.dataset.time) { timeWrap.textContent = btn.dataset.time; timeWrap.classList.remove('hidden'); }
                else { timeWrap.classList.add('hidden'); }
                if (btn.dataset.note) { noteEl.textContent = btn.dataset.note; noteWrap.classList.remove('hidden'); }
                else { noteWrap.classList.add('hidden'); }
                openModal();
            });
        });
        document.getElementById('proof-close').addEventListener('click', closeModal);
        modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    }

    var els = document.querySelectorAll('.js-countdown');
    if (!els.length) return;

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function tick() {
        var now = Date.now();
        els.forEach(function (el) {
            var target = parseInt(el.dataset.expired, 10);
            var diff = target - now;
            if (isNaN(target)) { el.textContent = '—'; return; }
            if (diff <= 0) { el.textContent = 'Kadaluarsa'; return; }
            var totalSec = Math.floor(diff / 1000);
            var h = Math.floor(totalSec / 3600);
            var m = Math.floor((totalSec % 3600) / 60);
            var s = totalSec % 60;
            el.textContent = (h > 0 ? h + 'j ' : '') + pad(m) + 'm ' + pad(s) + 'd';
        });
    }

    tick();
    setInterval(tick, 1000);
});
</script>
@endpush
