@extends('layouts.mobile.app', ['navActive' => 'keranjang'])
@section('title', 'Keranjang | Mantri Tani')
@section('main-class', 'px-5 py-4 pb-52 animate-fade-up')
@section('header')
<header class="page-header">
<a href="{{ route('home') }}" class="page-header__back"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg></a>
<h1 class="page-header__title">Keranjang</h1>
<div class="page-header__action"></div>
</header>
@endsection
@section('content')
<div class="panel text-center py-10 px-5 my-6">
<div class="text-5xl mb-3">🛒</div>
<h2 class="text-lg font-bold text-gray-900 mb-2">Keranjang Belanja</h2>
<p class="text-xs text-gray-500 leading-relaxed mb-6">Keranjang belanja Anda saat ini masih kosong.</p>
<a href="{{ route('home') }}" class="btn-primary inline-block py-2.5 px-6 text-xs font-semibold rounded-xl">Lihat Katalog Produk</a>
</div>
@endsection
@section('footer')
<x-mobile.cart-footer
    :checkout-route="$checkoutRoute"
    total="Rp 0"
    :items="0"
>
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
<span>Checkout Sekarang</span>
</x-mobile.cart-footer>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var loggedIn = @json($loggedIn ?? false);
    var dataUrl = @json(route('toko.keranjang.data'));
    var updateUrl = @json(url('/toko/keranjang'));
    var selectAllUrl = @json(route('toko.keranjang.select-all'));
    var loginUrl = @json(route('toko.login', ['redirect' => 'checkout']));
    var checkoutUrl = @json($checkoutRoute);
    var alamatUrl = @json($alamatUrl ?? route('toko.alamat.index'));
    var hasAddress = @json($hasAddress ?? false);
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    var cartList = document.getElementById('cart-list');
    var selectAll = document.getElementById('select-all');
    var totalEl = document.getElementById('cart-total');
    var countEl = document.getElementById('cart-item-count');
    var checkoutBtn = document.getElementById('checkout-btn');
    var loading = false;

    function formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderEmpty(message) {
        cartList.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">' + escapeHtml(message) + '</p>';
        updateFooter({ selected_count: 0, total_label: 'Rp 0' });
    }

    function renderItems(items) {
        if (!items.length) {
            renderEmpty('Keranjang masih kosong.');
            return;
        }

        cartList.innerHTML = items.map(function (item) {
            return '<article class="cart-item" data-unit="' + item.unit + '" data-id="' + item.id + '">' +
                '<input type="checkbox" class="cart-item__check accent-emerald-600 mt-1 shrink-0" data-id="' + item.id + '"' + (item.is_selected ? ' checked' : '') + '>' +
                '<div class="cart-item__thumb">' + escapeHtml(item.emoji) + '</div>' +
                '<div class="flex-1 min-w-0">' +
                '<h3 class="text-sm font-semibold text-gray-900">' + escapeHtml(item.name) + '</h3>' +
                '<p class="font-bold text-emerald-700 mt-1">' + escapeHtml(item.price) + '</p>' +
                '<div class="flex items-center justify-between mt-2">' +
                '<div class="qty-stepper text-sm" data-id="' + item.id + '">' +
                '<button type="button" class="qty-minus" aria-label="Kurangi">−</button>' +
                '<span class="qty-value px-2">' + item.qty + '</span>' +
                '<button type="button" class="qty-plus" aria-label="Tambah">+</button>' +
                '</div>' +
                '<span class="font-bold text-sm cart-item__subtotal">' + formatRp(item.unit * item.qty) + '</span>' +
                '</div>' +
                '</div>' +
                '</article>';
        }).join('');
    }

    function updateFooter(meta) {
        if (!meta) return;
        if (typeof meta.has_address !== 'undefined') {
            hasAddress = meta.has_address;
        }
        totalEl.textContent = meta.total_label || 'Rp 0';
        countEl.textContent = meta.selected_count || 0;
        var enabled = (meta.selected_count || 0) > 0;
        checkoutBtn.style.opacity = enabled ? '1' : '0.5';
        checkoutBtn.style.pointerEvents = enabled ? 'auto' : 'none';
    }

    function syncSelectAll(items) {
        if (!selectAll || !items) return;
        var checks = items.map(function (item) { return item.is_selected; });
        selectAll.checked = checks.length > 0 && checks.every(Boolean);
        selectAll.indeterminate = !selectAll.checked && checks.some(Boolean);
    }

    function patchCart(result, silent) {
        if (result.data) renderItems(result.data);
        if (result.meta) updateFooter(result.meta);
        if (result.data) syncSelectAll(result.data);
        if (!silent && result.message) MtNotify.toast(result.message, 'success', 'Berhasil');
    }

    function request(url, method, body) {
        if (loading) return Promise.resolve();
        loading = true;

        return fetch(url, {
            method: method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: body ? JSON.stringify(body) : undefined,
        })
        .then(function (res) {
            return res.json().then(function (json) {
                if (!res.ok) throw new Error(json.message || 'Permintaan gagal.');
                return json;
            });
        })
        .finally(function () {
            loading = false;
        });
    }

    function loadCart() {
        if (!loggedIn) {
            renderEmpty('Masuk untuk melihat keranjang Anda.');
            checkoutBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.location.href = loginUrl;
            });
            return;
        }

        request(dataUrl, 'GET')
            .then(function (result) {
                renderItems(result.data || []);
                updateFooter(result.meta || {});
                syncSelectAll(result.data || []);
            })
            .catch(function (err) {
                renderEmpty(err.message || 'Gagal memuat keranjang.');
                MtNotify.toast(err.message || 'Gagal memuat keranjang.', 'error', 'Gagal');
            });
    }

    cartList.addEventListener('click', function (e) {
        if (!loggedIn) return;

        var minus = e.target.closest('.qty-minus');
        var plus = e.target.closest('.qty-plus');
        if (!minus && !plus) return;

        var stepper = e.target.closest('.qty-stepper');
        var id = stepper.dataset.id;
        var valueEl = stepper.querySelector('.qty-value');
        var qty = parseInt(valueEl.textContent, 10);

        if (minus) qty = Math.max(0, qty - 1);
        if (plus) qty += 1;

        request(updateUrl + '/' + id, 'PUT', { quantity: qty })
            .then(function (result) { patchCart(result, true); })
            .catch(function (err) {
                MtNotify.toast(err.message, 'error', 'Gagal');
            });
    });

    cartList.addEventListener('change', function (e) {
        if (!loggedIn || !e.target.classList.contains('cart-item__check')) return;

        request(updateUrl + '/' + e.target.dataset.id, 'PUT', {
            is_selected: e.target.checked,
        })
            .then(function (result) { patchCart(result, true); })
            .catch(function (err) {
                e.target.checked = !e.target.checked;
                MtNotify.toast(err.message, 'error', 'Gagal');
            });
    });

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            if (!loggedIn) return;

            request(selectAllUrl, 'POST', { is_selected: selectAll.checked })
                .then(function (result) { patchCart(result, true); })
                .catch(function (err) {
                    MtNotify.toast(err.message, 'error', 'Gagal');
                });
        });
    }

    if (checkoutBtn && loggedIn) {
        checkoutBtn.addEventListener('click', function (e) {
            e.preventDefault();
            var count = parseInt(countEl.textContent, 10) || 0;
            if (count < 1) {
                MtNotify.toast('Pilih minimal satu produk untuk checkout.', 'warning', 'Perhatian');
                return;
            }
            if (!hasAddress) {
                MtNotify.toast('Alamat belum diisi. Silakan tambahkan alamat terlebih dahulu.', 'warning', 'Perhatian');
                setTimeout(function () { window.location.href = alamatUrl; }, 1500);
                return;
            }
            window.location.href = checkoutUrl;
        });
    }

    loadCart();
});
</script>
@endpush
