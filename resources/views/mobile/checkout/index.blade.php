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
<div class="panel text-center py-10 px-5 my-6">
<div class="text-5xl mb-3">🛍️</div>
<h2 class="text-lg font-bold text-gray-900 mb-2">Halaman Checkout</h2>
<p class="text-xs text-gray-500 leading-relaxed mb-6">Tidak ada item yang siap di-checkout. Silakan pilih produk dari katalog terlebih dahulu.</p>
<a href="{{ route('home') }}" class="btn-primary inline-block py-2.5 px-6 text-xs font-semibold rounded-xl">Lihat Katalog Produk</a>
</div>
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
