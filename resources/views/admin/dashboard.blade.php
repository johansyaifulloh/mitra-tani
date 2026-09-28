@extends('layouts.admin.app', ['navActive' => 'admin.dashboard'])
@section('page-title', 'Dashboard')
@section('page-sub', 'Ringkasan toko & penjualan')
@section('content')

<x-admin.pickup-info />

{{-- Stat cards --}}
<div class="panel-stats">
@foreach($stats as $i => $stat)
<div class="panel-stat panel-card-hover" style="animation-delay:{{ $i * 0.08 }}s">
<div class="panel-stat__top">
<span class="panel-stat__icon">{{ $stat['icon'] }}</span>
</div>
<p class="panel-stat__label">{{ $stat['label'] }}</p>
<p class="panel-stat__value {{ isset($stat['value_class']) ? 'panel-stat__value--'.$stat['value_class'] : '' }} {{ $stat['hint_type'] === 'warn' ? 'panel-stat__value--warn' : '' }}">{{ $stat['value'] }}</p>
<p class="panel-stat__hint {{ $stat['hint_type'] === 'up' ? 'panel-stat__hint--up' : '' }}">
@if($stat['label'] === 'Menunggu Pengambilan')
<a href="{{ route('admin.approval.index') }}">{{ $stat['hint'] }} →</a>
@else
{{ $stat['hint'] }}
@endif
</p>
</div>
@endforeach
</div>

{{-- Row: Chart + Status --}}
<div class="panel-grid-2 panel-grid-2--equal">
<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Grafik Pendapatan 6 Bulan</h3>
<span class="panel-card__meta">Feb – Jul 2026</span>
</div>
<div class="panel-card__body">
@php $maxChart = max(array_column($chart, 'value')); @endphp
<div class="panel-chart">
<div class="panel-chart__bars">
@foreach($chart as $i => $bar)
<div class="panel-chart__col">
<div class="panel-chart__bar-wrap">
<div class="panel-chart__bar" style="--h:{{ round($bar['value'] / $maxChart * 100) }}%;animation-delay:{{ $i * 0.1 }}s">
<span class="panel-chart__tooltip">Rp {{ $bar['label'] }}</span>
</div>
</div>
<span class="panel-chart__label">{{ $bar['month'] }}</span>
</div>
@endforeach
</div>
<div class="panel-chart__legend">
<span class="panel-chart__legend-item"><i style="background:#059669"></i> Pendapatan</span>
<span class="panel-chart__legend-note">Total Jul: Rp 12,4 jt</span>
</div>
</div>
</div>
</div>

<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Status Pengambilan Barang</h3>
<span class="panel-card__meta">{{ count(\App\Support\SampleData::transactions()) }} total</span>
</div>
<div class="panel-card__body">
@foreach($statusSummary as $item)
<div class="panel-progress">
<div class="panel-progress__head">
<span><x-admin.status-badge :status="$item['status']" /></span>
<span class="panel-progress__count">{{ $item['count'] }} ({{ $item['percent'] }}%)</span>
</div>
<div class="panel-progress__track">
<div class="panel-progress__fill" style="width:{{ $item['percent'] }}%"></div>
</div>
</div>
@endforeach
</div>
</div>
</div>

{{-- Row: Transactions + Top Products --}}
<div class="panel-grid-2">
<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Transaksi Terbaru</h3>
<a href="{{ route('admin.laporan.index') }}" class="panel-card__link">Lihat Semua</a>
</div>
<div class="panel-table-wrap">
<table class="panel-table">
<thead>
<tr>
<th>ID</th>
<th>Pelanggan</th>
<th>Total</th>
<th>Pembayaran</th>
<th>Status Pengambilan</th>
<th></th>
</tr>
</thead>
<tbody>
@foreach($transactions as $trx)
<tr>
<td class="mono">#{{ $trx['id'] }}</td>
<td>{{ $trx['customer'] }}</td>
<td>{{ \App\Support\SampleData::formatRp($trx['total']) }}</td>
<td><x-admin.status-badge :status="$trx['payment_status']" type="payment" /></td>
<td><x-admin.status-badge :status="$trx['status']" /></td>
<td>
@if($trx['status'] === 'menunggu_approval')
<a href="{{ route('admin.approval.index', ['order' => $trx['id']]) }}" class="panel-link">Verifikasi</a>
@else
<a href="{{ route('admin.laporan.index') }}" class="panel-link">Detail</a>
@endif
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="panel-card__foot">
<x-admin.pagination :paginator="$transactions" />
</div>
</div>

<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Produk Terlaris</h3>
<span class="panel-card__meta">Bulan ini</span>
</div>
<div class="panel-card__body panel-card__body--flush-top">
<div class="panel-rank-list">
@foreach($topProducts as $i => $product)
<div class="panel-rank-item">
<span class="panel-rank-item__num">{{ $i + 1 }}</span>
<span class="panel-rank-item__emoji">{{ $product['emoji'] }}</span>
<div class="panel-rank-item__info">
<p class="panel-rank-item__name">{{ $product['name'] }}</p>
<p class="panel-rank-item__sub">{{ $product['sold'] }} terjual</p>
</div>
<span class="panel-rank-item__value">{{ \App\Support\SampleData::formatRp($product['revenue']) }}</span>
</div>
@endforeach
</div>
</div>
</div>
</div>

{{-- Row: Categories + Activity --}}
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
<h3 class="panel-card__title">Aktivitas Terbaru</h3>
<span class="panel-card__meta">Live feed</span>
</div>
<div class="panel-card__body panel-card__body--flush-top">
<div class="panel-activity">
@foreach($activities as $act)
<div class="panel-activity__item">
<span class="panel-activity__icon">{{ $act['icon'] }}</span>
<div class="panel-activity__content">
<p class="panel-activity__text">{{ $act['text'] }}</p>
<p class="panel-activity__time">{{ $act['time'] }}</p>
</div>
</div>
@endforeach
</div>
</div>
</div>
</div>

{{-- Quick actions --}}
<div class="panel-quick">
<a href="{{ route('admin.produk.create') }}" class="panel-quick__btn panel-card-hover">+ Tambah Produk</a>
<a href="{{ route('admin.kategori.create') }}" class="panel-quick__btn panel-quick__btn--outline panel-card-hover">+ Tambah Kategori</a>
<a href="{{ route('admin.approval.index') }}" class="panel-quick__btn panel-quick__btn--outline panel-card-hover">Verifikasi Pengambilan (4)</a>
<a href="{{ route('admin.laporan.index') }}" class="panel-quick__btn panel-quick__btn--outline panel-card-hover">Lihat Laporan</a>
<a href="{{ route('admin.settings.midtrans') }}" class="panel-quick__btn panel-quick__btn--outline panel-card-hover">⚙ Pengaturan Midtrans</a>
</div>
@endsection
