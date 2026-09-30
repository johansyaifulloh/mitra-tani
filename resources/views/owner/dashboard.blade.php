@extends('layouts.admin.app', ['navActive' => 'owner.dashboard'])
@section('page-title', 'Dashboard Owner')
@section('page-sub', 'Ikhtisar performa bisnis, penjualan & kendali sistem Mantri Tani')
@section('content')

{{-- Quick info role --}}
<div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #fff; border-radius: 18px; padding: 20px 24px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: gap: 16px; box-shadow: 0 10px 25px -5px rgba(49, 46, 129, 0.2);">
    <div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 22px;">👑</span>
            <h2 style="font-size: 18px; font-weight: 700; margin: 0;">Selamat Datang di Portal Eksekutif Owner</h2>
        </div>
        <p style="font-size: 13px; color: #c7d2fe; margin-top: 4px;">Anda memiliki hak akses penuh untuk memantau pendapatan, mengelola akun admin, dan mengatur integrasi pembayaran Midtrans.</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('owner.users.create') }}" class="panel-btn panel-btn--sm" style="background: #4f46e5; color: #fff; text-decoration: none; border-radius: 10px; font-weight: 600;">+ Buat Akun Admin</a>
        <a href="{{ route('owner.settings.midtrans') }}" class="panel-btn panel-btn--sm" style="background: rgba(255,255,255,0.15); color: #fff; text-decoration: none; border-radius: 10px;">⚙️ Midtrans</a>
    </div>
</div>

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
<a href="{{ route('owner.laporan.index') }}" class="panel-card__link">Lihat Semua</a>
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
<a href="{{ route('owner.laporan.index') }}" class="panel-link">Detail</a>
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

{{-- Quick actions for Owner --}}
<div class="panel-quick">
<a href="{{ route('owner.users.index') }}" class="panel-quick__btn panel-card-hover">👥 Manajemen Akun Admin/Owner</a>
<a href="{{ route('owner.users.create') }}" class="panel-quick__btn panel-quick__btn--outline panel-card-hover">+ Buat Akun Baru</a>
<a href="{{ route('owner.laporan.index') }}" class="panel-quick__btn panel-quick__btn--outline panel-card-hover">📊 Laporan Penjualan</a>
<a href="{{ route('owner.settings.midtrans') }}" class="panel-quick__btn panel-quick__btn--outline panel-card-hover">⚙ Pengaturan Midtrans</a>
<a href="{{ route('admin.approval.index') }}" class="panel-quick__btn panel-quick__btn--outline panel-card-hover">🔍 Cek Approval Pesanan</a>
</div>
@endsection
