@extends('layouts.admin.app', ['navActive' => 'admin.kategori.create'])
@section('page-title', 'Tambah Kategori')
@section('page-sub', 'Buat kategori produk baru untuk katalog toko')
@section('content')

<div class="panel-form-layout">
<form class="panel-form-layout__main" id="kategori-form" action="{{ route('admin.kategori.store') }}" method="POST">
@csrf
<div class="panel-form-section">
<div class="panel-form-section__head">
<span class="panel-form-section__icon">🏷️</span>
<div>
<h3 class="panel-form-section__title">Detail Kategori</h3>
<p class="panel-form-section__desc">Nama dan deskripsi untuk mengelompokkan produk</p>
</div>
</div>
<div class="panel-form-section__body">
<div class="panel-form__grid">
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label" for="kat-nama">Nama Kategori <span class="panel-form__req">*</span></label>
<input type="text" id="kat-nama" name="name" class="panel-form__input" placeholder="Contoh: Pupuk" data-preview="name" value="" required>
<p class="panel-field-hint">Nama singkat yang mudah dikenali pelanggan</p>
</div>
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label" for="kat-desk">Deskripsi</label>
<textarea id="kat-desk" name="description" class="panel-form__input panel-form__textarea" rows="3" placeholder="Produk pupuk dan nutrisi tanaman..." data-preview="desc"></textarea>
</div>
<div class="panel-form__field">
<label class="panel-form__label" for="kat-urut">Urutan Tampil</label>
<div class="panel-input-group">
<input type="number" id="kat-urut" name="display_order" class="panel-form__input panel-input-group__input" placeholder="1" value="1" min="0">
<span class="panel-input-group__suffix">#</span>
</div>
<p class="panel-field-hint">Angka kecil = tampil lebih dulu</p>
</div>
<div class="panel-form__field">
<label class="panel-form__label">Status</label>
<label class="panel-toggle">
<input type="hidden" name="is_active" value="0">
<input type="checkbox" id="kat-aktif" name="is_active" value="1" checked>
<span class="panel-toggle__track"></span>
<span class="panel-toggle__label">Tampilkan di toko</span>
</label>
</div>
</div>
</div>
</div>

<div class="panel-form-section">
<div class="panel-form-section__head">
<span class="panel-form-section__icon">🎨</span>
<div>
<h3 class="panel-form-section__title">Tampilan Visual</h3>
<p class="panel-form-section__desc">Icon dan warna identitas kategori</p>
</div>
</div>
<div class="panel-form-section__body">
<div class="panel-form__grid">
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label">Icon / Emoji</label>
<div class="panel-emoji-picker">
<input type="text" id="kat-emoji" name="icon" class="panel-form__input panel-emoji-picker__input" placeholder="🌱" maxlength="2" value="🌱">
<div class="panel-emoji-picker__grid">
@foreach($emojiPresets as $emoji)
<button type="button" class="panel-emoji-picker__btn {{ $loop->first ? 'active' : '' }}" data-emoji="{{ $emoji }}" data-target="kat-emoji">{{ $emoji }}</button>
@endforeach
</div>
</div>
</div>
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label">Warna Tema</label>
<div class="panel-color-picker">
@foreach($colorPresets as $color)
<button type="button" class="panel-color-picker__swatch {{ $loop->first ? 'active' : '' }}" data-color="{{ $color['value'] }}" title="{{ $color['label'] }}" style="--swatch: {{ $color['value'] }}"></button>
@endforeach
<input type="hidden" id="kat-color" name="color" value="{{ $colorPresets[0]['value'] }}">
</div>
</div>
</div>
</div>
</div>

<div class="panel-form__actions panel-form__actions--left panel-form__actions--bar">
<a href="{{ route('admin.kategori.index') }}" class="panel-btn panel-btn--outline">Batal</a>
<button type="submit" class="panel-btn">Simpan Kategori</button>
</div>
</form>

<aside class="panel-form-layout__aside">
<div class="panel-form-preview panel-form-preview--sticky">
<div class="panel-form-preview__head">
<h4 class="panel-form-preview__title">Preview Kategori</h4>
<span class="panel-form-preview__tag">Live</span>
</div>
<div class="panel-cat-card panel-cat-card--preview" id="preview-kat-card" style="--kat-color: {{ $colorPresets[0]['value'] }}">
<p class="panel-cat-card__icon" id="preview-kat-icon">🌱</p>
<p class="panel-cat-card__name" id="preview-kat-name">Nama Kategori</p>
<p class="panel-cat-card__count">0 produk</p>
</div>
<div class="panel-form-preview__chip" id="preview-kat-chip" style="--kat-color: {{ $colorPresets[0]['value'] }}">
<span id="preview-kat-chip-icon">🌱</span>
<span id="preview-kat-chip-name">Nama Kategori</span>
</div>
<p class="panel-form-preview__note">Card kategori & chip filter di toko mobile</p>
</div>
</aside>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('kategori-form');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var indexUrl = @json(route('admin.kategori.index'));
    var nameEl = document.getElementById('kat-nama');
    var emojiEl = document.getElementById('kat-emoji');
    var colorEl = document.getElementById('kat-color');
    var card = document.getElementById('preview-kat-card');
    var chip = document.getElementById('preview-kat-chip');

    function updateName() {
        var v = nameEl.value || 'Nama Kategori';
        document.getElementById('preview-kat-name').textContent = v;
        document.getElementById('preview-kat-chip-name').textContent = v;
    }
    function updateEmoji() {
        var v = emojiEl.value || '🌱';
        document.getElementById('preview-kat-icon').textContent = v;
        document.getElementById('preview-kat-chip-icon').textContent = v;
    }
    function updateColor(c) {
        colorEl.value = c;
        card.style.setProperty('--kat-color', c);
        chip.style.setProperty('--kat-color', c);
    }

    nameEl.addEventListener('input', updateName);
    emojiEl.addEventListener('input', updateEmoji);
    document.querySelectorAll('.panel-emoji-picker__btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = document.getElementById(btn.dataset.target || 'kat-emoji');
            target.value = btn.dataset.emoji;
            btn.closest('.panel-emoji-picker__grid').querySelectorAll('.panel-emoji-picker__btn').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            updateEmoji();
        });
    });
    document.querySelectorAll('.panel-color-picker__swatch').forEach(function (sw) {
        sw.addEventListener('click', function () {
            document.querySelectorAll('.panel-color-picker__swatch').forEach(function (s) { s.classList.remove('active'); });
            sw.classList.add('active');
            updateColor(sw.dataset.color);
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;

        var fd = new FormData(form);

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
                return { ok: res.ok, status: res.status, json: json };
            });
        })
        .then(function (result) {
            if (result.status === 422 && result.json.errors) {
                var first = Object.values(result.json.errors)[0];
                throw new Error(Array.isArray(first) ? first[0] : first);
            }
            if (!result.ok || !result.json.success) {
                throw new Error(result.json.message || 'Gagal menyimpan kategori.');
            }
            MtNotify.redirectWithToast(indexUrl, result.json.message, 'success', 'Berhasil');
        })
        .catch(function (err) {
            MtNotify.toast(err.message || 'Gagal menyimpan kategori.', 'error', 'Gagal');
        })
        .finally(function () {
            submitBtn.disabled = false;
        });
    });
});
</script>
@endpush
