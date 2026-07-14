<!DOCTYPE html>
<html lang="id">
<head>@include('layouts.partials.head')</head>
<body class="app-shell">
<div class="app-frame min-h-screen flex flex-col onboard-bg relative">
<div class="flex-1"></div>
<div class="px-8 pb-14 pt-24 bg-gradient-to-t from-emerald-950/80 to-transparent text-white">
<div class="w-14 h-14 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center font-extrabold text-xl mb-6">MT</div>
<h1 class="text-3xl font-extrabold leading-tight">Belanja Pertanian<br>Lebih Mudah.</h1>
<p class="text-white/80 text-sm mt-3 leading-relaxed max-w-xs">Pupuk, benih, dan alat tani berkualitas — bayar online via Midtrans, ambil langsung di Mantri Tani Selorejo.</p>
<a href="{{ route('toko.produk.index') }}" class="btn-primary block text-center py-4 mt-8 text-base">Mulai Belanja</a>
</div></div></body></html>
