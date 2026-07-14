<!DOCTYPE html>
<html lang="id">
<head>@include('layouts.partials.head')</head>
<body class="bg-slate-50 font-sans antialiased min-h-screen">
<section class="gradient-hero text-white py-16 px-4">
<div class="max-w-4xl mx-auto text-center">
<div class="inline-flex items-center gap-2 bg-white/15 rounded-full px-4 py-1.5 text-sm mb-6">🌾 Toko Pertanian Digital</div>
<h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">Mantri Tani</h1>
<p class="text-brand-100 mt-3 text-lg max-w-xl mx-auto">Laravel + Tailwind — Toko Online Konsumen & Panel Admin/Owner</p>
</div>
</section>
<div class="max-w-5xl mx-auto px-4 py-12 space-y-12">
<section><h2 class="text-lg font-bold text-gray-900 mb-4">🖥️ Panel Desktop</h2>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
<a href="{{ route('admin.login') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-gray-100"><p class="font-semibold">Login Admin/Owner</p></a>
<a href="{{ route('admin.dashboard') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border"><p class="font-semibold">Dashboard Admin</p></a>
<a href="{{ route('admin.produk.index') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border"><p class="font-semibold">Kelola Produk</p></a>
<a href="{{ route('admin.approval.index') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border"><p class="font-semibold">Approval Pengambilan</p><p class="text-xs text-gray-500 mt-1">Verifikasi barang sudah diambil</p></a>
<a href="{{ route('owner.dashboard') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border"><p class="font-semibold">Dashboard Owner</p></a>
<a href="{{ route('owner.laporan.index') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border"><p class="font-semibold">Laporan</p></a>
</div></section>
<section><h2 class="text-lg font-bold text-gray-900 mb-4">📱 Toko Online Mobile (Konsumen)</h2>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
<a href="{{ route('toko.onboarding') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Onboarding</a>
<a href="{{ route('toko.produk.index') }}" class="p-4 bg-brand-600 text-white rounded-2xl card-hover text-center text-sm font-semibold">🏪 Beranda</a>
<a href="{{ route('toko.produk.show', 'pupuk-urea-50kg') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Detail Produk</a>
<a href="{{ route('toko.keranjang.index') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Keranjang</a>
<a href="{{ route('toko.checkout.index') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Checkout</a>
<a href="{{ route('toko.pembayaran.index') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Midtrans</a>
<a href="{{ route('toko.profil.index') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Profil</a>
<a href="{{ route('toko.login') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Login</a>
</div></section>
</div></body></html>
