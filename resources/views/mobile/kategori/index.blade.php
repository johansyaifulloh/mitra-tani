@extends('layouts.mobile.app', ['navActive' => 'kategori'])
@section('title', 'Kategori | Mantri Tani')
@section('header')
<header class="page-header">
<a href="{{ route('toko.produk.index') }}" class="page-header__back"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M15 19l-7-7 7-7"/></svg></a>
<h1 class="page-header__title">Kategori</h1><div class="page-header__action"></div>
</header>
@endsection
@section('content')
<div class="grid grid-cols-2 gap-3 mb-6">
@foreach([['🌱','Pupuk',18],['🌾','Benih',12],['💊','Obat',10],['🔧','Alat Tani',8]] as $i => [$icon,$name,$count])
<a href="{{ route('toko.produk.index') }}" class="panel p-5 text-center card-hover animate-card-in" style="animation-delay:{{ ($i+1)*0.05 }}s">
<span class="text-4xl">{{ $icon }}</span><p class="font-bold mt-2 text-gray-900">{{ $name }}</p><p class="text-xs text-gray-400">{{ $count }} produk</p></a>
@endforeach
</div>
<p class="section-label">Populer</p>
<div class="product-grid">
@foreach(array_slice($products, 0, 2) as $i => $product)
@include('components.mobile.product-card', ['product' => $product, 'delay' => $i])
@endforeach
</div>
@endsection
