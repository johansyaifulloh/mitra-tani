@extends('layouts.mobile.app')
@section('header')
<header class="page-header">
@if($back ?? true)
<a href="{{ $backUrl ?? route('toko.produk.index') }}" class="page-header__back">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg>
</a>
@else
<div class="page-header__back" style="visibility:hidden"></div>
@endif
<h1 class="page-header__title">{{ $pageTitle ?? '' }}</h1>
@yield('header-action', '<div class="page-header__action"></div>')
</header>
@endsection
