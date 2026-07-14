<!DOCTYPE html>
<html lang="id">
<head>
@include('layouts.partials.head')
<title>Login Admin — Mantri Tani</title>
</head>
<body class="panel-login-shell">
<x-auth-toast />

<div class="panel-login-bg" aria-hidden="true">
<div class="panel-login-bg__blob panel-login-bg__blob--1"></div>
<div class="panel-login-bg__blob panel-login-bg__blob--2"></div>
<div class="panel-login-bg__blob panel-login-bg__blob--3"></div>
<div class="panel-login-bg__grid"></div>
</div>

<div class="panel-login-layout">
<aside class="panel-login-hero">
<div class="panel-login-hero__inner">
<div class="panel-login-hero__brand panel-login-drop panel-login-drop--1">
<div class="panel-login-hero__logo">
<span>MT</span>
<div class="panel-login-hero__logo-ring"></div>
</div>
<div>
<p class="panel-login-hero__eyebrow">Mantri Tani · Selorejo</p>
<h1 class="panel-login-hero__title">Panel Admin & Owner</h1>
</div>
</div>
<p class="panel-login-hero__desc panel-login-drop panel-login-drop--2">Kelola katalog produk, verifikasi pengambilan barang, pantau laporan penjualan, dan konfigurasi Midtrans — semua dalam satu dashboard.</p>

<div class="panel-login-hero__features-wrap panel-login-drop panel-login-drop--2">
<div class="panel-login-hero__timeline" aria-hidden="true"><span class="panel-login-hero__timeline-fill"></span></div>
<ul class="panel-login-hero__features">
<li class="panel-login-hero__feature panel-login-feature-drop panel-login-feature-drop--1">
<span class="panel-login-hero__feature-dot"></span>
<span class="panel-login-hero__feature-icon">📦</span>
<div><strong>Manajemen Produk</strong><span>Tambah & edit katalog toko</span></div>
</li>
<li class="panel-login-hero__feature panel-login-feature-drop panel-login-feature-drop--2">
<span class="panel-login-hero__feature-dot"></span>
<span class="panel-login-hero__feature-icon">✅</span>
<div><strong>Verifikasi Pengambilan</strong><span>Upload bukti & approve barang</span></div>
</li>
<li class="panel-login-hero__feature panel-login-feature-drop panel-login-feature-drop--3">
<span class="panel-login-hero__feature-dot"></span>
<span class="panel-login-hero__feature-icon">💳</span>
<div><strong>Pengaturan Midtrans</strong><span>Payment gateway Snap & channel bayar</span></div>
</li>
</ul>
</div>

<div class="panel-login-hero__stats panel-login-drop panel-login-drop--6">
<div class="panel-login-hero__stat panel-login-stat-pop panel-login-stat-pop--1"><strong>120+</strong><span>Produk</span></div>
<div class="panel-login-hero__stat panel-login-stat-pop panel-login-stat-pop--2"><strong>48</strong><span>Transaksi/hari</span></div>
<div class="panel-login-hero__stat panel-login-stat-pop panel-login-stat-pop--3"><strong>99%</strong><span>Uptime</span></div>
</div>
</div>
<div class="panel-login-hero__footer panel-login-drop panel-login-drop--7">
<span class="panel-login-hero__badge">🔒 Akses terbatas admin & owner</span>
</div>
</aside>

<main class="panel-login-main panel-login-slide-in">
<div class="panel-login-card">
<div class="panel-login-card__mobile-brand">
<div class="panel-login-card__mobile-logo">MT</div>
<div>
<p class="panel-login-card__mobile-eyebrow">Mantri Tani</p>
<p class="panel-login-card__mobile-sub">Panel Admin</p>
</div>
</div>

<ul class="panel-login-mobile-features">
<li class="panel-login-mobile-features__item panel-login-feature-drop panel-login-feature-drop--1">
<span>📦</span><div><strong>Manajemen</strong><span>Katalog produk</span></div>
</li>
<li class="panel-login-mobile-features__item panel-login-feature-drop panel-login-feature-drop--2">
<span>✅</span><div><strong>Verifikasi</strong><span>Bukti pengambilan</span></div>
</li>
<li class="panel-login-mobile-features__item panel-login-feature-drop panel-login-feature-drop--3">
<span>💳</span><div><strong>Midtrans</strong><span>Payment gateway Snap</span></div>
</li>
</ul>
<div class="panel-login-card__header">
<h2 class="panel-login-card__title">Selamat Datang</h2>
<p class="panel-login-card__sub">Masuk ke dashboard untuk melanjutkan</p>
</div>

<form action="{{ route('admin.login.submit') }}" method="POST" class="panel-login-form" id="login-form">
@csrf
<div class="panel-login-field">
<label class="panel-login-field__label" for="admin-user">Username / Email</label>
<div class="panel-login-field__wrap">
<span class="panel-login-field__icon">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
</span>
<input id="admin-user" name="identifier" type="text" class="panel-login-field__input" placeholder="admin@mantri-tani.test" value="{{ old('identifier') }}" required autofocus>
</div>
</div>

<div class="panel-login-field">
<label class="panel-login-field__label" for="admin-pass">Password</label>
<div class="panel-login-field__wrap">
<span class="panel-login-field__icon">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
</span>
<input id="admin-pass" name="password" type="password" class="panel-login-field__input" placeholder="••••••••" required>
<button type="button" class="panel-login-field__toggle" id="toggle-pass" aria-label="Tampilkan password">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
</button>
</div>
</div>

<div class="panel-login-form__extras">
<label class="panel-login-check">
<input type="checkbox" checked>
<span class="panel-login-check__box"></span>
<span>Ingat sesi login</span>
</label>
<a href="#" class="panel-login-link">Lupa password?</a>
</div>

<button type="submit" class="panel-login-submit" id="login-btn">
<span class="panel-login-submit__text">Masuk Dashboard</span>
<span class="panel-login-submit__icon">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
</span>
<span class="panel-login-submit__loader" hidden></span>
</button>
</form>

<div class="panel-login-divider"><span>atau</span></div>

<a href="{{ route('home') }}" class="panel-login-store">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
Buka Mobile Store
<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
</a>

<p class="panel-login-trust">
<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
Sesi admin terenkripsi & dilindungi
</p>
</div>
</main>
</div>

<script>
(function () {
    var pass = document.getElementById('admin-pass');
    document.getElementById('toggle-pass').addEventListener('click', function () {
        pass.type = pass.type === 'password' ? 'text' : 'password';
    });
    document.getElementById('login-form').addEventListener('submit', function () {
        var btn = document.getElementById('login-btn');
        btn.classList.add('panel-login-submit--loading');
        btn.querySelector('.panel-login-submit__text').textContent = 'Memproses...';
        btn.querySelector('.panel-login-submit__loader').hidden = false;
        btn.querySelector('.panel-login-submit__icon').hidden = true;
    });
})();
</script>
</body>
</html>
