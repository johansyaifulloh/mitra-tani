<!DOCTYPE html>
<html lang="id">
<head>@include('layouts.partials.head')</head>
<body class="app-shell">
<x-mt-notify />
<div class="app-frame flex flex-col min-h-screen">
@yield('header')
<main class="flex-1 @yield('main-class', 'px-5 py-4 pb-[72px]') animate-fade-up">
@yield('content')
</main>
@yield('footer')
@if(empty($hideNav))
@include('components.mobile.bottom-nav', ['active' => $navActive ?? ''])
@endif
</div>
@include('mobile.partials.cart-store')
@stack('scripts')
</body>
</html>
