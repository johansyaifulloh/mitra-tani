@extends('layouts.admin.app', ['navActive' => 'admin.kategori.index'])
@section('page-title', 'Edit Kategori')
@section('page-sub', 'Perbarui data kategori produk')
@section('content')

<div class="panel-form-layout">
<form class="panel-form-layout__main" id="kategori-form" action="{{ route('admin.kategori.update', $category->id) }}" method="POST">
@csrf
@method('PUT')
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
<input type="text" id="kat-nama" name="name" class="panel-form__input" value="{{ old('name', $category->name) }}" required>
</div>
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label" for="kat-desk">Deskripsi</label>
<textarea id="kat-desk" name="description" class="panel-form__input panel-form__textarea" rows="3">{{ old('description', $category->description) }}</textarea>
</div>
<div class="panel-form__field">
<label class="panel-form__label" for="kat-urut">Urutan Tampil</label>
<div class="panel-input-group">
<input type="number" id="kat-urut" name="display_order" class="panel-form__input panel-input-group__input" value="{{ old('display_order', $category->display_order) }}" min="0">
<span class="panel-input-group__suffix">#</span>
</div>
</div>
<div class="panel-form__field">
<label class="panel-form__label">Status</label>
<label class="panel-toggle">
<input type="hidden" name="is_active" value="0">
<input type="checkbox" id="kat-aktif" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
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
<input type="text" id="kat-emoji" name="icon" class="panel-form__input panel-emoji-picker__input" maxlength="2" value="{{ old('icon', $category->icon) }}">
<div class="panel-emoji-picker__grid">
@foreach($emojiPresets as $emoji)
<button type="button" class="panel-emoji-picker__btn {{ old('icon', $category->icon) === $emoji ? 'active' : '' }}" data-emoji="{{ $emoji }}" data-target="kat-emoji">{{ $emoji }}</button>
@endforeach
</div>
</div>
</div>
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label">Warna Tema</label>
<div class="panel-color-picker">
@foreach($colorPresets as $color)
<button type="button" class="panel-color-picker__swatch {{ old('color', $category->color) === $color['value'] ? 'active' : '' }}" data-color="{{ $color['value'] }}" title="{{ $color['label'] }}" style="--swatch: {{ $color['value'] }}"></button>
@endforeach
<input type="hidden" id="kat-color" name="color" value="{{ old('color', $category->color) }}">
</div>
</div>
</div>
</div>
</div>

<div class="panel-form__actions panel-form__actions--left panel-form__actions--bar">
<a href="{{ route('admin.kategori.index') }}" class="panel-btn panel-btn--outline">Batal</a>
<button type="submit" class="panel-btn">Simpan Perubahan</button>
</div>
</form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('kategori-form');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var indexUrl = @json(route('admin.kategori.index'));
    var emojiEl = document.getElementById('kat-emoji');

    document.querySelectorAll('.panel-emoji-picker__btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            emojiEl.value = btn.dataset.emoji;
            document.querySelectorAll('.panel-emoji-picker__btn').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
        });
    });
    document.querySelectorAll('.panel-color-picker__swatch').forEach(function (sw) {
        sw.addEventListener('click', function () {
            document.querySelectorAll('.panel-color-picker__swatch').forEach(function (s) { s.classList.remove('active'); });
            sw.classList.add('active');
            document.getElementById('kat-color').value = sw.dataset.color;
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;

        var fd = new FormData(form);
        fd.append('_method', 'PUT');

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
                throw new Error(result.json.message || 'Gagal memperbarui kategori.');
            }
            MtNotify.redirectWithToast(indexUrl, result.json.message, 'success', 'Berhasil');
        })
        .catch(function (err) {
            MtNotify.toast(err.message || 'Gagal memperbarui kategori.', 'error', 'Gagal');
        })
        .finally(function () {
            submitBtn.disabled = false;
        });
    });
});
</script>
@endpush
