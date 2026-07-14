<!DOCTYPE html>
<html lang="id">
<head>@include('layouts.partials.head')</head>
<body class="app-shell auth-page">
<x-auth-toast error-title="Pendaftaran gagal" />
<div class="auth-page__frame">

    {{-- Hero --}}
    <div class="auth-hero">
        <div class="auth-hero__pattern"></div>
        <div class="auth-hero__content">
            <a href="{{ route('toko.login') }}" class="auth-hero__back" aria-label="Kembali">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="auth-hero__brand">
                <div class="auth-hero__logo">MT</div>
                <div>
                    <p class="auth-hero__tagline">Toko Pertanian Terpercaya</p>
                    <h1 class="auth-hero__title">Mantri Tani</h1>
                </div>
            </div>
            <p class="auth-hero__desc">Daftar sekarang dan nikmati kemudahan belanja pupuk, benih, dan kebutuhan tani — ambil langsung di toko Selorejo.</p>
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
            <h2 class="auth-card__title">Buat Akun Baru</h2>
            <p class="auth-card__subtitle">Lengkapi data untuk mulai berbelanja</p>
        </div>

        @if(session('info'))
        <div class="auth-alert">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('info') }}</span>
        </div>
        @endif

        <form action="{{ route('toko.register.store') }}" method="POST" class="auth-form">
            @csrf

            <div class="auth-field">
                <label class="auth-field__label" for="name">Nama Lengkap</label>
                <div class="auth-field__wrap">
                    <span class="auth-field__icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <input id="name" name="name" type="text" class="auth-field__input" placeholder="Nama lengkap Anda" value="{{ old('name') }}" required autofocus>
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-field__label" for="email">Email <span class="auth-field__optional">(opsional)</span></label>
                <div class="auth-field__wrap">
                    <span class="auth-field__icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input id="email" name="email" type="email" class="auth-field__input" placeholder="contoh@email.com" value="{{ old('email') }}">
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-field__label" for="phone">No. WhatsApp</label>
                <div class="auth-field__wrap">
                    <span class="auth-field__icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <input id="phone" name="phone" type="text" class="auth-field__input" placeholder="0812..." value="{{ old('phone') }}" required>
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-field__label" for="password">Password</label>
                <div class="auth-field__wrap">
                    <span class="auth-field__icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input id="password" name="password" type="password" class="auth-field__input" placeholder="Minimal 6 karakter" required minlength="6">
                    <button type="button" class="auth-field__toggle" onclick="togglePassword('password', 'eye-icon-1')" aria-label="Tampilkan password">
                        <svg id="eye-icon-1" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>

            <div class="auth-field">
                <label class="auth-field__label" for="password_confirmation">Konfirmasi Password</label>
                <div class="auth-field__wrap">
                    <span class="auth-field__icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="auth-field__input" placeholder="Ulangi password" required minlength="6">
                    <button type="button" class="auth-field__toggle" onclick="togglePassword('password_confirmation', 'eye-icon-2')" aria-label="Tampilkan password">
                        <svg id="eye-icon-2" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="auth-submit">
                <span>Buat Akun</span>
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </form>

        <p class="auth-footer-text">
            Sudah punya akun?
            <a href="{{ route('toko.login') }}" class="auth-link auth-link--bold">Masuk</a>
        </p>
    </div>

    <p class="auth-trust">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Data Anda aman & terenkripsi
    </p>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
