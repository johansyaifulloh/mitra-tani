@php
$user = $user ?? auth('api')->user();
$initials = $user ? strtoupper(substr($user->name, 0, 2)) : '?';
@endphp
<div class="profile-hero">
<a href="{{ route('home') }}" class="profile-hero__back"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg></a>
<div class="profile-hero__avatar">{{ $initials }}</div>
@if($user)
<h1 class="profile-hero__name">{{ $user->name }}</h1>
<p class="profile-hero__email">{{ $user->email ?: $user->phone }}</p>
@else
<h1 class="profile-hero__name">Tamu</h1>
<p class="profile-hero__email">Masuk untuk akses penuh</p>
@endif
<div class="profile-stats profile-stats--single">
<div class="profile-stat"><strong>{{ $user ? '—' : '—' }}</strong><span>Pesanan</span></div>
</div>
</div>
