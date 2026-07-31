<!DOCTYPE html>
<html lang="id">
<head>@include('layouts.partials.head')</head>
<body class="panel-shell">
@php
    $panelUser = auth('api')->user();
    $panelUserName = $panelUser?->name ?? 'Pengguna';
    $panelUserFirstName = explode(' ', trim($panelUserName))[0];
    $panelUserInitial = strtoupper(substr($panelUserName, 0, 1));
    $panelUserRole = match ($panelUser?->role) {
        'admin' => 'Admin',
        'owner' => 'Owner',
        default => 'Staff',
    };
    $panelSubTitle = match ($panelUser?->role) {
        'admin' => 'Panel Admin',
        'owner' => 'Panel Owner',
        default => 'Panel Admin & Owner',
    };
@endphp
<x-admin.panel-toast />
<div id="panel-overlay" class="panel-overlay" onclick="togglePanelSidebar()"></div>
<div class="panel-layout">

<aside id="panel-sidebar" class="panel-sidebar">
<div class="panel-sidebar__brand">
<div class="panel-sidebar__logo">MT</div>
<div>
<p class="panel-sidebar__title">Mantri Tani</p>
<p class="panel-sidebar__sub">{{ $panelSubTitle }}</p>
</div>
</div>

<nav class="panel-sidebar__nav">
@php
$links = [
    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
    ['route' => 'admin.produk.create', 'label' => 'Tambah Produk', 'icon' => 'M12 4v16m8-8H4'],
    ['route' => 'admin.kategori.create', 'label' => 'Tambah Kategori', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z'],
    // ['route' => 'admin.approval.index', 'label' => 'Approval Pengambilan', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    // ['route' => 'admin.laporan.index', 'label' => 'Laporan', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
];
@endphp
@foreach($links as $link)
<a href="{{ route($link['route']) }}" class="panel-sidebar__link {{ ($navActive ?? '') === $link['route'] ? 'active' : '' }}">
<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/></svg>
<span>{{ $link['label'] }}</span>
</a>
@endforeach

<div class="panel-sidebar__divider"></div>
<a href="{{ route('admin.produk.index') }}" class="panel-sidebar__link {{ ($navActive ?? '') === 'admin.produk.index' ? 'active' : '' }}">
<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
<span>Data Produk</span>
</a>
<a href="{{ route('admin.kategori.index') }}" class="panel-sidebar__link {{ ($navActive ?? '') === 'admin.kategori.index' ? 'active' : '' }}">
<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
<span>Data Kategori</span>
</a>
</nav>
</nav>

<div class="panel-sidebar__footer">
<div class="panel-sidebar__user">
<div class="panel-sidebar__user-avatar">{{ $panelUserInitial }}</div>
<div class="panel-sidebar__user-info">
<p class="panel-sidebar__user-name">{{ $panelUserName }}</p>
<p class="panel-sidebar__user-role">{{ $panelUserRole }}</p>
</div>
<span class="panel-sidebar__user-status" title="Online"></span>
</div>
<button type="button" class="panel-sidebar__logout" onclick="openLogoutModal()">
<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
<span>Keluar dari Panel</span>
</button>
<a href="{{ route('home') }}" class="panel-sidebar__store">
<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
<span>Toko Online</span>
<svg class="panel-sidebar__store-arrow" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
</a>
</div>
</aside>

<div class="panel-main">
<header class="panel-header">
<div class="panel-header__left">
<button type="button" class="panel-header__toggle" onclick="togglePanelSidebar()" aria-label="Menu">
<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
</button>
<div>
<h1 class="panel-header__title">@yield('page-title')</h1>
<p class="panel-header__sub">@yield('page-sub')</p>
</div>
</div>
<div class="panel-header__user" id="panel-user-menu">
<button type="button" class="panel-header__user-btn" id="panel-user-btn" onclick="toggleUserMenu(event)" aria-expanded="false" aria-haspopup="true">
<span class="panel-header__greet">Halo, <strong>{{ $panelUserFirstName }}</strong></span>
<div class="panel-header__avatar">{{ $panelUserInitial }}</div>
<svg class="panel-header__chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
</button>
<div class="panel-header__dropdown" id="panel-user-dropdown" hidden>
<div class="panel-header__dropdown-head">
<div class="panel-header__dropdown-avatar">{{ $panelUserInitial }}</div>
<div>
<p class="panel-header__dropdown-name">{{ $panelUserName }}</p>
<p class="panel-header__dropdown-role">{{ $panelUserRole }}</p>
</div>
</div>
<div class="panel-header__dropdown-divider"></div>
<a href="{{ route('home') }}" class="panel-header__dropdown-item">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
Buka Toko Online
</a>
<a href="{{ route('admin.settings.midtrans') }}" class="panel-header__dropdown-item">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
Pengaturan Midtrans
</a>
<div class="panel-header__dropdown-divider"></div>
<button type="button" class="panel-header__dropdown-item panel-header__dropdown-item--danger" onclick="openLogoutModal()">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
Keluar dari Panel
</button>
</div>
</div>
</header>
<main class="panel-content">@yield('content')</main>
</div>
</div>

<script>
function togglePanelSidebar() {
    document.getElementById('panel-sidebar').classList.toggle('open');
    document.getElementById('panel-overlay').classList.toggle('open');
}
function toggleUserMenu(e) {
    e.stopPropagation();
    var dropdown = document.getElementById('panel-user-dropdown');
    var btn = document.getElementById('panel-user-btn');
    var open = dropdown.hidden;
    dropdown.hidden = !open;
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    btn.classList.toggle('panel-header__user-btn--open', open);
}
function closeUserMenu() {
    var dropdown = document.getElementById('panel-user-dropdown');
    var btn = document.getElementById('panel-user-btn');
    dropdown.hidden = true;
    btn.setAttribute('aria-expanded', 'false');
    btn.classList.remove('panel-header__user-btn--open');
}
function openLogoutModal() {
    closeUserMenu();
    MtNotify.confirm({
        title: 'Keluar dari Panel?',
        message: 'Sesi admin akan diakhiri. Anda perlu login kembali untuk mengakses dashboard.',
        confirmText: 'Ya, Keluar',
        variant: 'danger',
    }).then(function (ok) {
        if (!ok) return;
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = @json(route('admin.logout'));
        form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').content + '">';
        document.body.appendChild(form);
        form.submit();
    });
}
document.addEventListener('click', function (e) {
    if (!document.getElementById('panel-user-menu').contains(e.target)) closeUserMenu();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeUserMenu();
});
</script>
@stack('scripts')
</body>
</html>
