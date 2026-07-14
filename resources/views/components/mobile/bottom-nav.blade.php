@php
$items = [
  'produk' => ['route' => 'home', 'label' => 'Beranda', 'path' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3'],
  'kategori' => ['route' => 'toko.kategori.index', 'label' => 'Kategori', 'path' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
  'keranjang' => ['route' => ($tokoLoggedIn ?? false) ? 'toko.keranjang.index' : 'toko.login', 'label' => 'Keranjang', 'path' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'redirect' => 'toko.keranjang.index'],
  'profil' => ['route' => 'toko.profil.index', 'label' => 'Profil', 'path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
];
@endphp
<nav class="bottom-nav fixed bottom-0 left-0 right-0 z-50 max-w-md mx-auto">
@foreach($items as $key => $item)
<a href="{{ isset($item['redirect']) && !($tokoLoggedIn ?? false) ? route($item['route'], ['redirect' => $item['redirect']]) : route($item['route']) }}" class="{{ $active === $key ? 'active' : '' }}">
<svg {{ $active === $key ? 'fill="currentColor"' : 'fill="none"' }} stroke="currentColor" stroke-width="{{ $active === $key ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['path'] }}"/>
</svg><span>{{ $item['label'] }}</span></a>
@endforeach
</nav>
