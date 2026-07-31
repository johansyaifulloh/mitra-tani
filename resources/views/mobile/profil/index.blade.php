@extends('layouts.mobile.profile')
@section('title', 'Profil | Mantri Tani')
@section('content')
<div class="menu-group">
<p class="menu-group__label">Akun</p>
@if($user)
<form action="{{ route('toko.logout') }}" method="POST">
@csrf
<button type="submit" class="menu-item menu-item--danger w-full text-left border-0 bg-transparent cursor-pointer">
<span class="menu-item__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></span>
Keluar
</button>
</form>
@else
<a href="{{ route('toko.login') }}" class="menu-item">
<span class="menu-item__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg></span>
Masuk / Daftar
</a>
@endif
</div>
@endsection
