@extends('layouts.admin.app', ['navActive' => 'admin.approval.index'])
@section('page-title', 'Approval Pengambilan Barang')
@section('page-sub', 'Masukkan kode transaksi untuk verifikasi pengambilan')
@section('content')

<x-admin.pickup-info />

{{-- Cari kode transaksi --}}
<div class="panel-card panel-card-hover panel-card--mb">
<div class="panel-card__body">
<h3 class="panel-card__title panel-card__title--mb">Cari Kode Transaksi</h3>
<p class="panel-text-muted panel-text-muted--block">Masukkan kode transaksi pelanggan (contoh: <strong>TRX-001</strong>) untuk menampilkan data pembeli dan alamat.</p>
<form method="GET" action="{{ route('admin.approval.index') }}" class="panel-code-search">
<input type="text" name="code" class="panel-form__input panel-code-search__input" placeholder="TRX-001" value="{{ $codeInput ?? '' }}" required autofocus>
<button type="submit" class="panel-btn">Cari Transaksi</button>
</form>
@if($codeError ?? false)
<p class="panel-code-search__error">Kode transaksi tidak ditemukan. Periksa kembali kode dari pelanggan.</p>
@endif
</div>
</div>

@if($selected)
<article class="panel-card panel-card-hover panel-card--mb panel-card--highlight">
<div class="panel-card__body">
<div class="panel-detail-header">
<div>
<h3 class="panel-card__title">#{{ $selected['id'] }}</h3>
<p class="panel-detail-meta">{{ $selected['date'] }} · {{ $selected['payment'] }}</p>
</div>
<div class="panel-detail-badges">
<x-admin.status-badge :status="$selected['payment_status']" type="payment" />
<x-admin.status-badge :status="$selected['status']" />
</div>
</div>

<div class="panel-buyer-grid">
<div class="panel-buyer-box">
<p class="panel-buyer-box__label">Nama Pembeli</p>
<p class="panel-buyer-box__value">{{ $selected['customer'] }}</p>
</div>
<div class="panel-buyer-box">
<p class="panel-buyer-box__label">No. Handphone</p>
<p class="panel-buyer-box__value">{{ $selected['phone'] }}</p>
</div>
<div class="panel-buyer-box panel-buyer-box--full">
<p class="panel-buyer-box__label">Alamat Pembeli</p>
<p class="panel-buyer-box__value panel-buyer-box__value--address">📍 {{ $selected['address'] }}</p>
</div>
</div>

<p class="panel-text-muted panel-text-muted--block">{{ \App\Support\SampleData::pickupStatusDescription($selected['status']) }}</p>

<ul class="panel-detail-list">
@forelse($selected['items'] ?? [] as $item)
<li><span>{{ $item['name'] }} ×{{ $item['quantity'] }}</span><span>{{ $item['subtotal_label'] }}</span></li>
@empty
<li><span>Tidak ada rincian item</span><span>—</span></li>
@endforelse
<li class="panel-detail-list__total"><span>Total</span><span>{{ \App\Support\SampleData::formatRp($selected['total']) }}</span></li>
</ul>

@if($selected['status'] === 'menunggu_approval')
<div class="panel-info-banner panel-info-banner--sm">
<div class="panel-info-banner__icon">ℹ️</div>
<div>
<p class="panel-info-banner__text">Pastikan identitas pembeli sesuai, barang sudah diambil, dan <strong>foto bukti dari kamera</strong> (bukan galeri) sudah diambil sebelum verifikasi.</p>
</div>
</div>

<form action="{{ route('admin.approval.verify') }}" method="POST" enctype="multipart/form-data" id="approval-verify-form">
@csrf
<input type="hidden" name="order_id" value="{{ $selected['order_id'] ?? '' }}">
<input type="file" name="photo" id="approval-photo-input" accept="image/*" capture="environment" hidden>
<x-admin.pickup-proof-upload :transaction-id="$selected['id']" />

<div class="panel-form__actions panel-form__actions--left">
<button type="submit" class="panel-btn" id="btn-verify" disabled title="Ambil foto bukti dari kamera terlebih dahulu">✓ Verifikasi Barang Sudah Diambil</button>
</div>
</form>
<form action="{{ route('admin.approval.reject') }}" method="POST" class="panel-form__actions panel-form__actions--left" style="margin-top:0">
@csrf
<input type="hidden" name="order_id" value="{{ $selected['order_id'] ?? '' }}">
<button type="submit" class="panel-btn panel-btn--outline panel-btn--danger">Tolak</button>
</form>
@elseif(in_array($selected['status'], ['disetujui', 'selesai']) && ($selected['pickup_proof'] ?? null))
<x-admin.pickup-proof-view :proof="$selected['pickup_proof']" :transaction-id="$selected['id']" />
<p class="panel-text-info panel-text-info--mt">Barang sudah diambil pelanggan dan diverifikasi admin.</p>
@elseif($selected['status'] === 'disetujui')
<p class="panel-text-info">Barang sudah diambil pelanggan dan diverifikasi admin.</p>
@elseif($selected['status'] === 'selesai')
<p class="panel-text-info">Transaksi selesai.</p>
@endif
</div>
</article>
@endif

<div class="panel-flow panel-card-hover panel-card--mb">
@foreach(\App\Support\SampleData::pickupFlowSteps() as $step)
<div class="panel-flow__step">
<span class="panel-flow__num">{{ $step['step'] }}</span>
<div>
<p class="panel-flow__title">{{ $step['title'] }}</p>
<p class="panel-flow__desc">{{ $step['desc'] }}</p>
</div>
</div>
@endforeach
</div>

<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Antrian Verifikasi Pengambilan</h3>
<span class="panel-card__meta">{{ $transactions->total() }} pesanan</span>
</div>
<div class="panel-table-wrap">
<table class="panel-table">
<thead>
<tr>
<th>Kode</th>
<th>Pelanggan</th>
<th>Alamat</th>
<th>Total</th>
<th>Pembayaran</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
@forelse($transactions as $trx)
<tr class="{{ ($selected['id'] ?? '') === $trx['id'] ? 'panel-table__row--active' : '' }}">
<td class="mono">#{{ $trx['id'] }}</td>
<td>
<p class="panel-table__name">{{ $trx['customer'] }}</p>
<p class="panel-table__sub">{{ $trx['phone'] }}</p>
</td>
<td class="panel-table__address">{{ Str::limit($trx['address'], 40) }}</td>
<td>{{ \App\Support\SampleData::formatRp($trx['total']) }}</td>
<td><x-admin.status-badge :status="$trx['payment_status']" type="payment" /></td>
<td><x-admin.status-badge :status="$trx['status']" /></td>
<td>
<a href="{{ route('admin.approval.index', ['code' => $trx['id'], 'page' => request('page')]) }}" class="panel-btn panel-btn--sm">
@if($trx['status'] === 'menunggu_approval') Verifikasi @else Detail @endif
</a>
</td>
</tr>
@empty
<tr><td colspan="7" class="panel-table__empty">Tidak ada pesanan menunggu verifikasi pengambilan.</td></tr>
@endforelse
</tbody>
</table>
</div>
<div class="panel-card__foot">
<x-admin.pagination :paginator="$transactions" />
</div>
</div>

@endsection
