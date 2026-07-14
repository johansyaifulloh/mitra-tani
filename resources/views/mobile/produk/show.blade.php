@extends('layouts.mobile.app', ['navActive' => null, 'hideNav' => true])
@section('title', $product['name'] . ' | Mantri Tani')
@section('main-class', 'px-5 py-4 pb-28 animate-fade-up')
@section('header')
<header class="page-header">
<a href="{{ route('home') }}" class="page-header__back"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg></a>
<h1 class="page-header__title">Detail Produk</h1>
<button class="page-header__action"><svg width="20" height="20" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg></button>
</header>
@endsection
@section('content')
<div class="panel overflow-hidden mb-4 p-0">
<div class="product-detail__media">
<div class="product-detail__emoji">{{ $product['emoji'] }}</div>
<span class="product-detail__badge">Stok Tersedia</span>
</div>
</div>
<span class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">{{ $product['cat'] }}</span>
<h2 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $product['name'] }}</h2>
<p class="product-detail__meta">
<span>{{ $product['sold_label'] ?? '0 terjual' }}</span>
<span aria-hidden="true">·</span>
<span>Stok {{ $product['stock'] }} unit</span>
</p>
<p class="text-sm text-gray-500 mt-4 leading-relaxed">Produk berkualitas dari Toko Mantri Tani Selorejo. Setelah bayar lunas, ambil barang langsung di toko — admin akan verifikasi saat pengambilan.</p>
<div class="mt-6 flex items-center justify-between"><p class="text-sm font-semibold text-gray-800">Jumlah</p>
<div class="qty-stepper" id="product-qty-stepper">
<button type="button" class="qty-minus" aria-label="Kurangi">−</button>
<span class="qty-value" id="product-qty">1</span>
<button type="button" class="qty-plus" aria-label="Tambah">+</button>
</div></div>
<p class="text-2xl font-extrabold text-emerald-700 mt-6">{{ $product['price'] }}</p>
@endsection
@section('footer')
<div class="sticky-bar sticky-bar--solo">
<div class="sticky-bar__inner">
<button type="button" id="add-to-cart-btn" class="btn-primary flex-1 text-center py-3.5 text-sm" data-product-id="{{ $product['id'] }}">
Tambah ke Keranjang — {{ $product['price'] }}
</button>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var productId = @json($product['id']);
    var maxStock = @json($product['stock']);
    var qtyEl = document.getElementById('product-qty');
    var stepper = document.getElementById('product-qty-stepper');
    var addBtn = document.getElementById('add-to-cart-btn');

    function getQty() {
        return Math.max(1, parseInt(qtyEl.textContent, 10) || 1);
    }

    function setQty(value) {
        qtyEl.textContent = Math.min(Math.max(1, value), maxStock);
    }

    stepper.addEventListener('click', function (e) {
        if (e.target.classList.contains('qty-minus')) {
            setQty(getQty() - 1);
        }
        if (e.target.classList.contains('qty-plus')) {
            setQty(getQty() + 1);
        }
    });

    addBtn.addEventListener('click', function () {
        if (!window.MtCart) {
            return;
        }

        if (!window.MtCart.isLoggedIn()) {
            window.MtCart.goLogin();
            return;
        }

        window.MtCart.add(productId, getQty(), {
            button: addBtn,
            redirectToCart: true,
        });
    });
});
</script>
@endpush
