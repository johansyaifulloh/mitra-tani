@extends('layouts.admin.app', ['navActive' => 'admin.laporan.index'])
@section('page-title', 'Laporan Penjualan')
@section('page-sub', 'Riwayat transaksi & status pengambilan barang')
@section('content')

<x-admin.pickup-info />

<div class="panel-toolbar">
<input type="date" class="panel-form__input panel-form__input--sm">
<input type="date" class="panel-form__input panel-form__input--sm">
<button type="button" class="panel-btn panel-btn--sm">Filter</button>
<button type="button" class="panel-btn panel-btn--outline panel-btn--sm">Export PDF</button>
</div>
<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Semua Transaksi</h3>
<span class="panel-card__meta">{{ $transactions->total() }} data</span>
</div>
<div class="panel-table-wrap">
<table class="panel-table">
<thead>
<tr>
<th>ID</th>
<th>Tanggal</th>
<th>Pelanggan</th>
<th>Total</th>
<th>Metode Bayar</th>
<th>Pembayaran</th>
<th>Status Pengambilan</th>
</tr>
</thead>
<tbody>
@foreach($transactions as $trx)
<tr>
<td class="mono">#{{ $trx['id'] }}</td>
<td>{{ $trx['date'] }}</td>
<td>{{ $trx['customer'] }}</td>
<td>{{ \App\Support\SampleData::formatRp($trx['total']) }}</td>
<td>{{ $trx['payment'] }}</td>
<td><x-admin.status-badge :status="$trx['payment_status']" type="payment" /></td>
<td><x-admin.status-badge :status="$trx['status']" /></td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="panel-card__foot">
<x-admin.pagination :paginator="$transactions" />
</div>
</div>
@endsection
