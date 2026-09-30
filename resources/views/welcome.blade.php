<!DOCTYPE html>
<html lang="id">
<head>@include('layouts.partials.head')</head>
<body class="bg-slate-50 font-sans antialiased min-h-screen">
<section class="gradient-hero text-white py-16 px-4">
<div class="max-w-4xl mx-auto text-center">
<div class="inline-flex items-center gap-2 bg-white/15 rounded-full px-4 py-1.5 text-sm mb-6">🌾 Toko Pertanian Digital</div>
<h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">Mantri Tani</h1>
<p class="text-brand-100 mt-3 text-lg max-w-xl mx-auto">Laravel + Tailwind — Toko Online Konsumen, Panel Admin & Panel Owner</p>
</div>
</section>

<div class="max-w-5xl mx-auto px-4 py-12 space-y-10">

{{-- Panel Owner --}}
<section>
<h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">👑 Panel Owner (Full Akses & Bisnis)</h2>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
<a href="{{ route('owner.dashboard') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-indigo-100">
    <p class="font-semibold text-indigo-900">Dashboard Owner</p>
    <p class="text-xs text-slate-500 mt-1">Performa bisnis & ringkasan omzet</p>
</a>
<a href="{{ route('owner.users.index') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-indigo-100">
    <p class="font-semibold text-indigo-900">👥 Manajemen Akun</p>
    <p class="text-xs text-slate-500 mt-1">Buat & kelola akun Admin/Owner</p>
</a>
<a href="{{ route('owner.settings.midtrans') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-indigo-100">
    <p class="font-semibold text-indigo-900">⚙️ Pengaturan Midtrans</p>
    <p class="text-xs text-slate-500 mt-1">API Key & konfigurasi pembayaran</p>
</a>
<a href="{{ route('owner.laporan.index') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-indigo-100">
    <p class="font-semibold text-indigo-900">📊 Laporan Penjualan</p>
    <p class="text-xs text-slate-500 mt-1">Analisis omzet & export data</p>
</a>
</div>
</section>

{{-- Panel Admin --}}
<section>
<h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">🛡️ Panel Admin (Operasional & Katalog)</h2>
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
<a href="{{ route('admin.login') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-gray-100">
    <p class="font-semibold text-emerald-900">Login Admin & Owner</p>
    <p class="text-xs text-slate-500 mt-1">Halaman masuk pengelola</p>
</a>
<a href="{{ route('admin.dashboard') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-emerald-50">
    <p class="font-semibold text-emerald-900">Dashboard Admin</p>
    <p class="text-xs text-slate-500 mt-1">Ringkasan operasional toko</p>
</a>
<a href="{{ route('admin.produk.index') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-emerald-50">
    <p class="font-semibold text-emerald-900">📦 Kelola Produk</p>
    <p class="text-xs text-slate-500 mt-1">Tambah, edit & stok produk</p>
</a>
<a href="{{ route('admin.kategori.index') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-emerald-50">
    <p class="font-semibold text-emerald-900">🏷️ Kelola Kategori</p>
    <p class="text-xs text-slate-500 mt-1">Manajemen kategori produk</p>
</a>
<a href="{{ route('admin.approval.index') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-emerald-50">
    <p class="font-semibold text-emerald-900">🔍 Approval Pengambilan</p>
    <p class="text-xs text-slate-500 mt-1">Verifikasi penyerahan barang</p>
</a>
<a href="{{ route('admin.laporan.index') }}" class="p-5 bg-white rounded-2xl shadow-soft card-hover border border-emerald-50">
    <p class="font-semibold text-emerald-900">📑 Laporan Transaksi</p>
    <p class="text-xs text-slate-500 mt-1">Daftar transaksi toko</p>
</a>
</div>
</section>

{{-- Mobile Toko --}}
<section>
<h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">📱 Toko Online Mobile (Konsumen)</h2>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
<a href="{{ route('toko.onboarding') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Onboarding</a>
<a href="{{ route('toko.produk.index') }}" class="p-4 bg-emerald-600 text-white rounded-2xl card-hover text-center text-sm font-semibold">🏪 Beranda Toko</a>
<a href="{{ route('toko.kategori.index') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Kategori</a>
<a href="{{ route('toko.keranjang.index') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Keranjang</a>
<a href="{{ route('toko.checkout.index') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Checkout</a>
<a href="{{ route('toko.profil.index') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Profil Akun</a>
<a href="{{ route('toko.login') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Login Konsumen</a>
<a href="{{ route('toko.register') }}" class="p-4 bg-white rounded-2xl border card-hover text-center text-sm">Daftar Konsumen</a>
</div>
</section>

</div>
</body>
</html>
