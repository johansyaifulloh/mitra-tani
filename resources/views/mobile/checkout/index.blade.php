@extends('layouts.mobile.app', ['navActive' => 'keranjang'])
@section('title', 'Checkout | Mantri Tani')
@section('main-class', 'px-5 py-4 pb-56 animate-fade-up')
@section('header')
<header class="page-header">
<a href="{{ route('toko.keranjang.index') }}" class="page-header__back"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg></a>
<h1 class="page-header__title">Checkout</h1><div class="page-header__action"></div>
</header>
@endsection
@section('content')
<form action="{{ route('toko.checkout.store') }}" method="POST" id="checkout-form">
@csrf
<div class="space-y-3">

<section class="panel">
<p class="panel__label">Pengambilan Barang</p>
<div class="flex gap-3">
<div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 text-lg">📍</div>
<div>
<p class="text-sm font-semibold text-gray-900">Ambil di Toko Mantri Tani</p>
<p class="text-xs text-gray-400 mt-0.5">Jl. Selorejo, Malang · 08.00–17.00 · Verifikasi saat pengambilan</p>
</div>
</div>
</section>

<section>
<div class="flex items-center justify-between mb-3">
<p class="section-label mb-0">Alamat Pembeli</p>
<a href="{{ route('toko.alamat.index') }}" class="address-add-link">+ Tambah Alamat</a>
</div>
@if($hasAddress)
<div class="address-list">
@foreach($addresses as $address)
@include('components.mobile.address-card', ['address' => $address, 'selected' => $address['default']])
@endforeach
</div>
@else
<div class="panel text-center py-6">
<p class="text-sm text-gray-500">Belum ada alamat tersimpan.</p>
<p class="text-xs text-gray-400 mt-1">Tambahkan alamat terlebih dahulu sebelum checkout.</p>
<a href="{{ route('toko.alamat.index') }}" class="inline-block mt-3 text-sm font-semibold text-emerald-600">Isi Alamat Sekarang</a>
</div>
@endif
</section>

<section class="panel text-sm space-y-2">
@foreach($summary['items'] as $item)
<div class="flex justify-between text-gray-600">
<span>{{ $item['name'] }} ×{{ $item['quantity'] }}</span>
<span>{{ $item['subtotal_label'] }}</span>
</div>
@endforeach
<div class="flex justify-between text-gray-600">
<span>Biaya Admin</span>
<span>{{ $summary['admin_fee_label'] }}</span>
</div>
<div class="flex justify-between font-bold text-emerald-700 border-t border-gray-100 pt-2 mt-2">
<span>Total</span>
<span id="checkout-total">{{ $summary['total_label'] }}</span>
</div>
</section>

</div>
</form>
@endsection
@section('footer')
<x-mobile.cart-footer
    :show-select-all="false"
    label="Total Pembayaran"
    :total="$summary['total_label']"
    checkout-form="checkout-form"
>
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
<span>Bayar Sekarang</span>
</x-mobile.cart-footer>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var hasAddress = @json($hasAddress);
    var alamatUrl = @json(route('toko.alamat.index'));
    var form = document.getElementById('checkout-form');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var submitBtn = document.getElementById('checkout-btn');

    if (!hasAddress && submitBtn) {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        submitBtn.style.pointerEvents = 'none';
    }

    document.querySelectorAll('.address-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.address-card').forEach(function (c) {
                c.classList.remove('address-card--active');
            });
            card.classList.add('address-card--active');
            var radio = card.querySelector('.address-card__radio');
            if (radio) radio.checked = true;
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!hasAddress) {
            MtNotify.toast('Alamat belum diisi. Silakan tambahkan alamat terlebih dahulu.', 'warning', 'Perhatian');
            setTimeout(function () { window.location.href = alamatUrl; }, 1500);
            return;
        }

        var selected = form.querySelector('input[name="address_id"]:checked');
        if (!selected) {
            MtNotify.toast('Pilih alamat pengambilan terlebih dahulu.', 'warning', 'Perhatian');
            return;
        }

        if (submitBtn) submitBtn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({ address_id: parseInt(selected.value, 10) }),
        })
        .then(function (res) {
            return res.json().then(function (json) {
                if (!res.ok) throw new Error(json.message || 'Checkout gagal.');
                return json;
            });
        })
        .then(function (result) {
            MtNotify.redirectWithToast(
                result.data.redirect_url,
                result.message,
                'success',
                'Berhasil'
            );
        })
        .catch(function (err) {
            MtNotify.toast(err.message, 'error', 'Gagal');
            if (submitBtn && hasAddress) {
                submitBtn.disabled = false;
            }
            if (err.message && err.message.toLowerCase().indexOf('alamat') !== -1) {
                setTimeout(function () { window.location.href = alamatUrl; }, 1500);
            }
        });
    });
});
</script>
@endpush
