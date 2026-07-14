@props(['proof', 'transactionId' => ''])

<div class="panel-proof-view">
<div class="panel-proof-view__head">
<h4 class="panel-proof-view__title">📸 Bukti Pengambilan Barang</h4>
<span class="panel-badge panel-badge--ok">Terverifikasi</span>
</div>
<div class="panel-proof-view__photo">
@if(!empty($proof['photo']))
<a href="{{ $proof['photo'] }}" target="_blank" rel="noopener">
<img src="{{ $proof['photo'] }}" alt="Foto bukti pengambilan {{ $transactionId }}" style="width:100%;border-radius:12px;object-fit:cover;max-height:320px;display:block;">
</a>
@else
<div class="panel-proof-view__mock" aria-label="Foto bukti pengambilan {{ $transactionId }}">
<div class="panel-proof-view__mock-inner">
<span class="panel-proof-view__mock-icon">📦</span>
<p class="panel-proof-view__mock-label">Foto Bukti Pengambilan</p>
<p class="panel-proof-view__mock-id">#{{ $transactionId }}</p>
</div>
</div>
@endif
</div>
<div class="panel-proof-view__meta">
<div class="panel-proof-view__row">
<span>Diverifikasi</span>
<strong>{{ $proof['verified_at'] }}</strong>
</div>
<div class="panel-proof-view__row">
<span>Oleh</span>
<strong>{{ $proof['verified_by'] }}</strong>
</div>
@if(!empty($proof['note']))
<div class="panel-proof-view__note">
<span>Catatan:</span> {{ $proof['note'] }}
</div>
@endif
</div>
</div>
