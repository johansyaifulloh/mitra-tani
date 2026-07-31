@extends('layouts.admin.app', ['navActive' => 'admin.dashboard'])
@section('page-title', 'Dashboard')
@section('page-sub', 'Selamat datang di Panel Admin Toko Pertanian Mantri Tani')
@section('content')

@php
    $totalProducts = \App\Models\Product::count();
    $totalCategories = \App\Models\Category::count();
    $activeProducts = \App\Models\Product::where('status', 'aktif')->count();
    $totalStock = \App\Models\Product::sum('stock');
    $recentProducts = \App\Models\Product::with('category')->latest()->take(4)->get();
@endphp

{{-- Stat cards --}}
<div class="panel-stats">
<div class="panel-stat panel-card-hover">
<div class="panel-stat__top"><span class="panel-stat__icon">📦</span></div>
<p class="panel-stat__label">Total Produk</p>
<p class="panel-stat__value">{{ $totalProducts }}</p>
<p class="panel-stat__hint panel-stat__hint--up"><a href="{{ route('admin.produk.index') }}">Kelola Produk →</a></p>
</div>

<div class="panel-stat panel-card-hover">
<div class="panel-stat__top"><span class="panel-stat__icon">🏷️</span></div>
<p class="panel-stat__label">Total Kategori</p>
<p class="panel-stat__value">{{ $totalCategories }}</p>
<p class="panel-stat__hint panel-stat__hint--up"><a href="{{ route('admin.kategori.index') }}">Kelola Kategori →</a></p>
</div>

<div class="panel-stat panel-card-hover">
<div class="panel-stat__top"><span class="panel-stat__icon">✅</span></div>
<p class="panel-stat__label">Produk Aktif</p>
<p class="panel-stat__value panel-stat__value--green">{{ $activeProducts }}</p>
<p class="panel-stat__hint">Tampil di Katalog Toko</p>
</div>

<div class="panel-stat panel-card-hover">
<div class="panel-stat__top"><span class="panel-stat__icon">📊</span></div>
<p class="panel-stat__label">Total Stok Fisik</p>
<p class="panel-stat__value">{{ number_format($totalStock, 0, ',', '.') }}</p>
<p class="panel-stat__hint">Unit dalam Gudang</p>
</div>
</div>

{{-- Row: Categories + Recent Products --}}
<div class="panel-grid-2 panel-grid-2--equal">
<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Kategori Produk</h3>
<a href="{{ route('admin.kategori.index') }}" class="panel-card__link">Kelola</a>
</div>
<div class="panel-card__body">
<div class="panel-cat-grid panel-cat-grid--compact">
@foreach($categories as $cat)
<div class="panel-cat-card panel-cat-card--sm panel-card-hover">
<p class="panel-cat-card__icon">{{ $cat['emoji'] }}</p>
<p class="panel-cat-card__name">{{ $cat['name'] }}</p>
</div>
@endforeach
</div>
</div>
</div>

<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Produk Terbaru</h3>
<a href="{{ route('admin.produk.index') }}" class="panel-card__link">Lihat Semua</a>
</div>
<div class="panel-card__body panel-card__body--flush-top">
<div class="panel-rank-list">
@foreach($recentProducts as $product)
<div class="panel-rank-item">
<span class="panel-rank-item__emoji">{{ $product->emoji ?? '🌱' }}</span>
<div class="panel-rank-item__info">
<p class="panel-rank-item__name">{{ $product->name }}</p>
<p class="panel-rank-item__sub">Stok: {{ $product->stock }} unit · {{ $product->category?->name ?? 'Kategori' }}</p>
</div>
<span class="panel-rank-item__value">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
</div>
@endforeach
</div>
</div>
</div>
</div>

{{-- Quick actions --}}
<div class="panel-quick">
<a href="{{ route('admin.produk.create') }}" class="panel-quick__btn panel-card-hover">+ Tambah Produk Baru</a>
<a href="{{ route('admin.kategori.create') }}" class="panel-quick__btn panel-quick__btn--outline panel-card-hover">+ Tambah Kategori Baru</a>
<a href="{{ route('admin.produk.index') }}" class="panel-quick__btn panel-quick__btn--outline panel-card-hover">Kelola Data Produk</a>
</div>
@endsection
