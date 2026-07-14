<!DOCTYPE html>
<html lang="id">
<head>@include('layouts.partials.head')</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased">
<x-mt-notify />
<div class="flex min-h-screen">
<aside class="hidden lg:flex w-64 bg-slate-900 text-white flex-col">
<div class="p-6"><p class="font-bold text-lg">Mantri Tani</p><p class="text-xs text-slate-400">Owner Panel</p></div>
<nav class="flex-1 px-4 space-y-1">
@foreach([
  ['route' => 'owner.dashboard', 'label' => 'Dashboard'],
  ['route' => 'owner.laporan.index', 'label' => 'Laporan Penjualan'],
] as $link)
<a href="{{ route($link['route']) }}" class="block px-4 py-3 rounded-xl text-sm font-medium {{ ($navActive ?? '') === $link['route'] ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10' }}">{{ $link['label'] }}</a>
@endforeach
</nav>
<div class="p-4 text-sm text-slate-400"><a href="{{ route('admin.login') }}" class="hover:text-white">Logout</a></div>
</aside>
<div class="flex-1">
<header class="bg-white border-b px-8 py-5"><h1 class="text-2xl font-bold">@yield('page-title')</h1><p class="text-gray-500 text-sm">@yield('page-sub')</p></header>
<main class="p-8">@yield('content')</main>
</div>
</div>
</body>
</html>
