<!DOCTYPE html>
<html lang="id">
<head>@include('layouts.partials.head')</head>
<body class="app-shell auth-page">
<x-auth-toast />
<div class="auth-page__frame">

    {{-- Hero --}}
    <div class="auth-hero">
        <div class="auth-hero__pattern"></div>
        <div class="auth-hero__content">
            <a href="{{ route('home') }}" class="auth-hero__back" aria-label="Kembali">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="auth-hero__brand">
                <div class="auth-hero__logo">MT</div>
                <div>
                    <p class="auth-hero__tagline">Toko Pertanian Terpercaya</p>
                    <h1 class="auth-hero__title">Mantri Tani</h1>
                </div>
            </div>
            <p class="auth-hero__desc">Belanja pupuk, benih, dan kebutuhan tani dengan mudah — ambil langsung di toko Selorejo.</p>
            <div class="auth-hero__stats">
                <div class="auth-hero__stat"><strong>500+</strong><span>Petani</span></div>
                <div class="auth-hero__stat"><strong>120+</strong><span>Produk</span></div>
                <div class="auth-hero__stat"><strong>4.9</strong><span>Rating</span></div>
            </div>
        </div>
    </div>

    {{-- Form card --}}
    <div class="auth-card">
        <div class="auth-card__header">
            <h2 class="auth-card__title">Selamat Datang Kembali</h2>
            <p class="auth-card__subtitle">Masuk untuk melanjutkan belanja Anda</p>
        </div>

        @if(session('info'))
        <div class="auth-alert">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
        @endif

        @if($redirect === 'checkout' || $redirect === 'toko.checkout.index')
        <div class="auth-alert auth-alert--checkout">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
            <span>Login diperlukan untuk melanjutkan checkout</span>
        </div>
        @endif

        <form action="{{ route('toko.login.submit') }}" method="POST" class="auth-form">
            @csrf
            <input type="hidden" name="redirect" value="{{ $redirect }}">

            <div class="auth-field">
                <label class="auth-field__label" for="identifier">Email / No. Handphone</label>
                <div class="auth-field__wrap">
                    <span class="auth-field__icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                    </span>
                    <input id="identifier" name="identifier" type="text" class="auth-field__input" placeholder="contoh@email.com atau 0812..." value="{{ old('identifier') }}" required autofocus>
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-field__label" for="password">Password</label>
                <div class="auth-field__wrap">
                    <span class="auth-field__icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input id="password" name="password" type="password" class="auth-field__input" placeholder="Masukkan password" required minlength="4">
                    <button type="button" class="auth-field__toggle" onclick="togglePassword()" aria-label="Tampilkan password">
                        <svg id="eye-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>

            <div class="auth-form__extras">
                <label class="auth-checkbox">
                    <input type="checkbox" name="remember">
                    <span>Ingat saya</span>
                </label>
                <a href="#" class="auth-link">Lupa password?</a>
            </div>

            <button type="submit" class="auth-submit">
                <span>Masuk Sekarang</span>
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </form>

        <p class="auth-footer-text">
            Belum punya akun?
            <a href="{{ route('toko.register') }}" class="auth-link auth-link--bold">Daftar Gratis</a>
        </p>
    </div>

    <p class="auth-trust">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Data Anda aman & terenkripsi
    </p>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
