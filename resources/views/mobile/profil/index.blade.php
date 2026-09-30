@extends('layouts.mobile.profile')
@section('title', 'Profil | Mantri Tani')
@section('content')
<div class="space-y-4">

    {{-- KARTU PESANAN SAYA (ALA SHOPEE DENGAN NOTIFIKASI BADGE) --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 animate-card-in">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <span class="text-base">📦</span>
                <h3 class="font-extrabold text-sm text-gray-800">Pesanan Saya</h3>
            </div>
            <a href="{{ $user ? route('toko.transaksi.index') : route('toko.login', ['redirect' => 'toko.transaksi.index']) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-0.5">
                <span>Lihat Riwayat</span>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- 3 Grid Status Transaksi Utama dengan Badge Notifikasi --}}
        <div class="grid grid-cols-3 gap-2 pt-3 text-center">
            {{-- 1. Belum Bayar --}}
            <a href="{{ $user ? route('toko.transaksi.index', ['status' => 'menunggu_pembayaran']) : route('toko.login') }}" class="group relative flex flex-col items-center py-1.5 rounded-xl hover:bg-gray-50/80 transition active:scale-95">
                <div class="relative w-12 h-12 rounded-2xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center text-amber-600 transition">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    @if($user && ($orderCounts['unpaid'] ?? 0) > 0)
                    <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-[20px] bg-rose-600 text-white text-[11px] font-black rounded-full flex items-center justify-center px-1 border-2 border-white shadow-sm animate-pulse">
                        {{ $orderCounts['unpaid'] }}
                    </span>
                    @endif
                </div>
                <span class="text-xs font-semibold text-gray-700 mt-2 leading-tight">Belum Bayar</span>
            </a>

            {{-- 2. Siap Diambil / Sudah Dibayar --}}
            <a href="{{ $user ? route('toko.transaksi.index', ['status' => 'lunas']) : route('toko.login') }}" class="group relative flex flex-col items-center py-1.5 rounded-xl hover:bg-gray-50/80 transition active:scale-95">
                <div class="relative w-12 h-12 rounded-2xl bg-teal-50 group-hover:bg-teal-100 flex items-center justify-center text-teal-700 transition">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    @if($user && ($orderCounts['ready_pickup'] ?? 0) > 0)
                    <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-[20px] bg-emerald-600 text-white text-[11px] font-black rounded-full flex items-center justify-center px-1 border-2 border-white shadow-sm">
                        {{ $orderCounts['ready_pickup'] }}
                    </span>
                    @endif
                </div>
                <span class="text-xs font-semibold text-gray-700 mt-2 leading-tight">Siap Diambil</span>
            </a>

            {{-- 3. Selesai --}}
            <a href="{{ $user ? route('toko.transaksi.index', ['status' => 'selesai']) : route('toko.login') }}" class="group relative flex flex-col items-center py-1.5 rounded-xl hover:bg-gray-50/80 transition active:scale-95">
                <div class="relative w-12 h-12 rounded-2xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center text-emerald-700 transition">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    @if($user && ($orderCounts['completed'] ?? 0) > 0)
                    <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-[20px] bg-gray-600 text-white text-[11px] font-bold rounded-full flex items-center justify-center px-1 border-2 border-white">
                        {{ $orderCounts['completed'] }}
                    </span>
                    @endif
                </div>
                <span class="text-xs font-semibold text-gray-700 mt-2 leading-tight">Selesai</span>
            </a>
        </div>
    </div>

    {{-- MENU PENGATURAN & AKUN --}}
    <div class="menu-group">
        <p class="menu-group__label">Aktivitas & Alamat</p>
        @if($user)
        <a href="{{ route('toko.alamat.index') }}" class="menu-item">
            <span class="menu-item__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
            Daftar Alamat Saya
        </a>
        @else
        <a href="{{ route('toko.login', ['redirect' => 'toko.alamat.index']) }}" class="menu-item">
            <span class="menu-item__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
            Daftar Alamat Saya
        </a>
        @endif
    </div>

    <div class="menu-group">
        <p class="menu-group__label">Pengaturan Akun</p>
        @if($user)
        <a href="{{ route('toko.forgot-password') }}" class="menu-item">
            <span class="menu-item__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></span>
            Ganti Kata Sandi
        </a>
        <form action="{{ route('toko.logout') }}" method="POST">
            @csrf
            <button type="submit" class="menu-item menu-item--danger w-full text-left border-0 bg-transparent cursor-pointer">
                <span class="menu-item__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></span>
                Keluar dari Akun
            </button>
        </form>
        @else
        <a href="{{ route('toko.login') }}" class="menu-item">
            <span class="menu-item__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg></span>
            Masuk / Daftar Akun
        </a>
        @endif
    </div>

</div>
@endsection
