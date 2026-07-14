<!DOCTYPE html>
<html lang="id">
<head>@include('layouts.partials.head')</head>
<body class="app-shell">
<x-mt-notify />
<div class="app-frame app-frame--catalog flex flex-col">
@include('components.mobile.hero-header', ['categories' => $categories ?? []])
<main class="app-catalog-scroll flex-1 min-h-0 overflow-y-auto overscroll-contain px-5 pt-5 pb-24 bg-white">
@yield('content')
</main>
@include('components.mobile.bottom-nav', ['active' => 'produk'])
</div>
@include('mobile.partials.cart-store')
@stack('scripts')
</body>
</html>
