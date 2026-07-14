@extends('layouts.admin.app', ['navActive' => 'admin.settings.midtrans'])
@section('page-title', 'Pengaturan Midtrans')
@section('page-sub', 'Konfigurasi payment gateway Midtrans Snap untuk toko online')
@section('content')

<form action="{{ route('admin.settings.midtrans.update') }}" method="POST" id="midtrans-form">
@csrf
<div class="panel-grid-2 panel-grid-2--equal panel-grid-2--start">
<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Kredensial API</h3>
<span class="panel-badge {{ ($settings['is_active'] ?? false) ? 'panel-badge--ok' : 'panel-badge--neutral' }}">
{{ ($settings['is_active'] ?? false) ? 'Aktif' : 'Nonaktif' }}
</span>
</div>
<div class="panel-card__body">
<div class="panel-form panel-form--flat">
<div class="panel-form__field">
<label class="panel-form__label">Merchant ID</label>
<input type="text" class="panel-form__input panel-form__input--mono" name="merchant_id" value="{{ $settings['merchant_id'] ?? '' }}" placeholder="Mxxxxxxxxx">
<p class="panel-field-hint">ID merchant dari dashboard Midtrans (opsional untuk Snap)</p>
</div>
<div class="panel-form__field">
<label class="panel-form__label">Server Key</label>
<div class="panel-input-secret">
<input type="password" class="panel-form__input" id="server-key" name="server_key" value="{{ $settings['server_key'] ?? '' }}">
<button type="button" class="panel-input-secret__toggle" onclick="toggleSecret('server-key')">Tampilkan</button>
</div>
<p class="panel-field-hint">Server Key untuk backend (rahasia — jangan dibagikan)</p>
</div>
<div class="panel-form__field">
<label class="panel-form__label">Client Key</label>
<input type="text" class="panel-form__input panel-form__input--mono" name="client_key" value="{{ $settings['client_key'] ?? '' }}" placeholder="SB-Mid-client-...">
<p class="panel-field-hint">Client Key untuk Snap di frontend</p>
</div>
<div class="panel-form__field">
<label class="panel-form__label">Mode</label>
<select class="panel-form__input" name="mode">
<option value="sandbox" {{ ($settings['mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
<option value="production" {{ ($settings['mode'] ?? '') === 'production' ? 'selected' : '' }}>Production (Live)</option>
</select>
</div>
<div class="panel-form__field panel-form__field--toggle">
<label class="panel-toggle">
<input type="checkbox" name="is_active" value="1" {{ ($settings['is_active'] ?? false) ? 'checked' : '' }}>
<span class="panel-toggle__track"></span>
<span class="panel-toggle__label">Aktifkan Midtrans</span>
</label>
</div>
</div>
</div>

<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">URL & Notifikasi</h3>
</div>
<div class="panel-card__body">
<div class="panel-form__field">
<label class="panel-form__label">Notification URL</label>
<input type="url" class="panel-form__input panel-form__input--mono" name="notification_url" value="{{ $settings['notification_url'] ?? '' }}" placeholder="https://xxxxx.ngrok-free.dev/api/midtrans/notification">
<p class="panel-field-hint">Isi dengan URL publik (mis. ngrok) lalu daftarkan yang sama di Midtrans Dashboard → Settings → Configuration → Payment Notification URL</p>
</div>
<div class="panel-form__field">
<label class="panel-form__label">Finish Redirect URL</label>
<input type="url" class="panel-form__input panel-form__input--mono" name="finish_url" value="{{ $settings['finish_url'] ?? '' }}">
<p class="panel-field-hint">Redirect setelah pembayaran berhasil</p>
</div>
<div class="panel-form__field">
<label class="panel-form__label">Unfinish Redirect URL</label>
<input type="url" class="panel-form__input panel-form__input--mono" name="unfinish_url" value="{{ $settings['unfinish_url'] ?? '' }}">
<p class="panel-field-hint">Redirect jika pelanggan menutup Snap sebelum bayar</p>
</div>
<div class="panel-form__field">
<label class="panel-form__label">Error Redirect URL</label>
<input type="url" class="panel-form__input panel-form__input--mono" name="error_url" value="{{ $settings['error_url'] ?? '' }}">
<p class="panel-field-hint">Redirect jika pembayaran gagal</p>
</div>
<div class="panel-form__field">
<label class="panel-form__label">Batas Waktu Pembayaran (menit)</label>
<input type="number" class="panel-form__input" name="expiry_duration" value="{{ $settings['expiry_duration'] ?? 1440 }}" min="5" max="10080">
<p class="panel-field-hint">Default Midtrans: 1440 menit (24 jam)</p>
</div>
<div class="panel-midtrans-status">
<div class="panel-midtrans-status__icon">💳</div>
<div>
<p class="panel-midtrans-status__title">Midtrans Snap</p>
<p class="panel-midtrans-status__desc">Mode: <strong>{{ ucfirst($settings['mode']) }}</strong> · Client Key <strong>{{ Str::limit($settings['client_key'], 20) }}</strong></p>
</div>
</div>
</div>
</div>
</div>

@php
$channels = collect($settings['channels']);
$activeCount = $channels->where('enabled', true)->count();
$totalCount = $channels->count();
@endphp

<div class="panel-card panel-card-hover panel-card--mt">
<div class="panel-card__head">
<div>
<h3 class="panel-card__title">Metode Pembayaran</h3>
<span class="panel-card__meta">Channel Midtrans yang ditampilkan di Snap checkout</span>
</div>
<span class="panel-badge panel-badge--info" id="channel-summary">{{ $activeCount }}/{{ $totalCount }} aktif</span>
</div>
<div class="panel-card__body">
<div class="panel-pay-toolbar">
<p class="panel-pay-toolbar__hint">Aktifkan metode bayar yang ingin ditampilkan saat checkout pelanggan.</p>
<button type="button" class="panel-pay-toolbar__btn" id="toggle-all-channels">Aktifkan Semua</button>
</div>

@foreach($settings['channel_groups'] as $groupKey => $group)
@php
$groupChannels = $channels->where('group', $groupKey);
$groupActive = $groupChannels->where('enabled', true)->count();
@endphp
<div class="panel-pay-group">
<div class="panel-pay-group__head">
<div class="panel-pay-group__title-wrap">
<span class="panel-pay-group__dot panel-pay-group__dot--{{ $groupKey }}"></span>
<div>
<h4 class="panel-pay-group__title">{{ $group['label'] }}</h4>
<p class="panel-pay-group__desc">{{ $group['desc'] }}</p>
</div>
</div>
<span class="panel-pay-group__count">{{ $groupActive }}/{{ $groupChannels->count() }}</span>
</div>
<div class="panel-pay-grid">
@foreach($groupChannels as $channel)
<label class="panel-pay-card {{ $channel['enabled'] ? 'panel-pay-card--on' : '' }}" data-group="{{ $groupKey }}">
<input type="checkbox" class="panel-pay-card__input" name="channels[]" value="{{ $channel['code'] }}" {{ $channel['enabled'] ? 'checked' : '' }}>
<span class="panel-pay-card__icon" style="--pay-color: {{ $channel['color'] }}">{{ $channel['icon'] }}</span>
<div class="panel-pay-card__body">
<span class="panel-pay-card__name">{{ $channel['name'] }}</span>
<span class="panel-pay-card__code">{{ $channel['code'] }}</span>
</div>
<span class="panel-pay-card__toggle" aria-hidden="true"></span>
</label>
@endforeach
</div>
</div>
@endforeach
</div>
</div>

<div class="panel-form__actions panel-form__actions--left panel-form__actions--mt">
<button type="submit" class="panel-btn" id="midtrans-save-btn">Simpan Pengaturan</button>
<button type="button" class="panel-btn panel-btn--outline" id="midtrans-test-btn">Test Koneksi API</button>
<a href="https://docs.midtrans.com" target="_blank" rel="noopener" class="panel-btn panel-btn--outline">Dokumentasi Midtrans ↗</a>
</div>
</form>

@endsection

@push('head')
<script>
function toggleSecret(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
function updateChannelSummary() {
    const all = document.querySelectorAll('.panel-pay-card__input');
    const active = [...all].filter(function (cb) { return cb.checked; }).length;
    document.getElementById('channel-summary').textContent = active + '/' + all.length + ' aktif';
    document.querySelectorAll('.panel-pay-group').forEach(function (group) {
        const cards = group.querySelectorAll('.panel-pay-card__input');
        const on = [...cards].filter(function (cb) { return cb.checked; }).length;
        group.querySelector('.panel-pay-group__count').textContent = on + '/' + cards.length;
    });
    const btn = document.getElementById('toggle-all-channels');
    btn.textContent = active === all.length ? 'Nonaktifkan Semua' : 'Aktifkan Semua';
}
// Label sudah otomatis toggle checkbox-nya (perilaku bawaan browser).
// Cukup dengarkan event change untuk update tampilan + ringkasan.
document.querySelectorAll('.panel-pay-card').forEach(function (card) {
    card.querySelector('.panel-pay-card__input').addEventListener('change', function () {
        card.classList.toggle('panel-pay-card--on', this.checked);
        updateChannelSummary();
    });
});
document.getElementById('toggle-all-channels').addEventListener('click', function () {
    const all = document.querySelectorAll('.panel-pay-card__input');
    const turnOn = [...all].some(function (cb) { return !cb.checked; });
    all.forEach(function (cb) {
        cb.checked = turnOn;
        cb.closest('.panel-pay-card').classList.toggle('panel-pay-card--on', turnOn);
    });
    updateChannelSummary();
});
updateChannelSummary();
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('midtrans-form');
    var saveBtn = document.getElementById('midtrans-save-btn');
    var testBtn = document.getElementById('midtrans-test-btn');
    var testUrl = @json(route('admin.settings.midtrans.test'));
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    // Simpan pengaturan via AJAX (pola sama seperti produk)
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var fd = new FormData(form);
        saveBtn.disabled = true;
        saveBtn.textContent = 'Menyimpan...';

        fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
        })
        .then(function (res) {
            return res.json().then(function (json) {
                if (res.status === 422 && json.errors) {
                    var first = Object.values(json.errors)[0];
                    throw new Error(Array.isArray(first) ? first[0] : first);
                }
                if (!res.ok || !json.success) {
                    throw new Error(json.message || 'Gagal menyimpan pengaturan.');
                }
                return json;
            });
        })
        .then(function (json) {
            MtNotify.toast(json.message, 'success', 'Berhasil');
        })
        .catch(function (err) {
            MtNotify.toast(err.message, 'error', 'Gagal');
        })
        .finally(function () {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Simpan Pengaturan';
        });
    });

    // Test koneksi ke Midtrans (server key + mode saat ini)
    testBtn.addEventListener('click', function () {
        var serverKey = form.querySelector('[name="server_key"]').value.trim();
        var mode = form.querySelector('[name="mode"]').value;

        if (!serverKey) {
            MtNotify.toast('Isi Server Key terlebih dahulu.', 'warning', 'Perhatian');
            return;
        }

        testBtn.disabled = true;
        testBtn.textContent = 'Menguji...';

        fetch(testUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({ server_key: serverKey, mode: mode }),
        })
        .then(function (res) {
            return res.json().then(function (json) {
                if (!res.ok || !json.success) {
                    throw new Error(json.message || 'Test koneksi gagal.');
                }
                return json;
            });
        })
        .then(function (json) {
            MtNotify.toast(json.message, 'success', 'Koneksi Berhasil');
        })
        .catch(function (err) {
            MtNotify.toast(err.message, 'error', 'Koneksi Gagal');
        })
        .finally(function () {
            testBtn.disabled = false;
            testBtn.textContent = 'Test Koneksi API';
        });
    });
});
</script>
@endpush
