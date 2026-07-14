@extends('layouts.admin.app', ['navActive' => 'admin.produk.index'])
@section('page-title', 'Edit Produk')
@section('page-sub', 'Perbarui data produk di katalog toko')
@section('content')

<div class="panel-form-layout">
<form class="panel-form-layout__main" id="produk-form" action="{{ route('admin.produk.update', $product->id) }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="panel-form-section">
<div class="panel-form-section__head">
<span class="panel-form-section__icon">📦</span>
<div>
<h3 class="panel-form-section__title">Informasi Dasar</h3>
<p class="panel-form-section__desc">Nama, kategori, dan deskripsi produk</p>
</div>
</div>
<div class="panel-form-section__body">
<div class="panel-form__grid">
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label" for="produk-nama">Nama Produk <span class="panel-form__req">*</span></label>
<input type="text" id="produk-nama" name="name" class="panel-form__input" value="{{ old('name', $product->name) }}" required>
</div>
<div class="panel-form__field">
<label class="panel-form__label" for="produk-kategori">Kategori <span class="panel-form__req">*</span></label>
<select id="produk-kategori" name="category_id" class="panel-form__input" required>
@foreach($categories as $cat)
<option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->icon }} {{ $cat->name }}</option>
@endforeach
</select>
</div>
<div class="panel-form__field">
<label class="panel-form__label" for="produk-badge">Label Produk</label>
<select id="produk-badge" name="badge" class="panel-form__input">
<option value="">Tanpa label</option>
@foreach($badges as $badge)
<option value="{{ $badge }}" @selected(old('badge', $product->badge) === $badge)>{{ $badge }}</option>
@endforeach
</select>
</div>
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label" for="produk-desk">Deskripsi</label>
<textarea id="produk-desk" name="description" class="panel-form__input panel-form__textarea" rows="4">{{ old('description', $product->description) }}</textarea>
</div>
</div>
</div>
</div>

<div class="panel-form-section">
<div class="panel-form-section__head">
<span class="panel-form-section__icon">💰</span>
<div>
<h3 class="panel-form-section__title">Harga & Stok</h3>
<p class="panel-form-section__desc">Kelola harga jual dan ketersediaan barang</p>
</div>
</div>
<div class="panel-form-section__body">
<div class="panel-form__grid">
<div class="panel-form__field">
<label class="panel-form__label" for="produk-harga">Harga Jual <span class="panel-form__req">*</span></label>
<div class="panel-input-group">
<span class="panel-input-group__prefix">Rp</span>
<input type="number" id="produk-harga" name="price" class="panel-form__input panel-input-group__input" value="{{ old('price', $product->price) }}" required>
</div>
</div>
<div class="panel-form__field">
<label class="panel-form__label" for="produk-stok">Stok Tersedia <span class="panel-form__req">*</span></label>
<div class="panel-input-group">
<input type="number" id="produk-stok" name="stock" class="panel-form__input panel-input-group__input" value="{{ old('stock', $product->stock) }}" required>
<span class="panel-input-group__suffix">unit</span>
</div>
</div>
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label">Status Produk</label>
<div class="panel-form-status">
<label class="panel-form-status__item {{ old('status', $product->status) === 'active' ? 'panel-form-status__item--on' : '' }}">
<input type="radio" name="status_preview" value="active" @checked(old('status', $product->status) === 'active')>
<span class="panel-form-status__dot"></span>
<span>Aktif — tampil di toko</span>
</label>
<label class="panel-form-status__item {{ old('status', $product->status) === 'draft' ? 'panel-form-status__item--on' : '' }}">
<input type="radio" name="status_preview" value="draft" @checked(old('status', $product->status) === 'draft')>
<span class="panel-form-status__dot"></span>
<span>Draft — disimpan sementara</span>
</label>
</div>
</div>
</div>
</div>
</div>

<div class="panel-form-section">
<div class="panel-form-section__head">
<span class="panel-form-section__icon">🖼️</span>
<div>
<h3 class="panel-form-section__title">Tampilan & Media</h3>
<p class="panel-form-section__desc">Icon produk dan gambar untuk katalog</p>
</div>
</div>
<div class="panel-form-section__body">
<div class="panel-form__grid">
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label">Icon / Emoji Produk</label>
<div class="panel-emoji-picker">
<input type="text" id="produk-emoji" name="emoji" class="panel-form__input panel-emoji-picker__input" maxlength="2" value="{{ old('emoji', $product->emoji ?? '🌱') }}">
<div class="panel-emoji-picker__grid">
@foreach(['🌱','🌾','💊','🔧','🌿','🍅','💧','🌽'] as $emoji)
<button type="button" class="panel-emoji-picker__btn {{ old('emoji', $product->emoji ?? '🌱') === $emoji ? 'active' : '' }}" data-emoji="{{ $emoji }}">{{ $emoji }}</button>
@endforeach
</div>
</div>
</div>
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label">Gambar Produk</label>
<div class="panel-upload">
<div class="panel-upload__zone" id="upload-zone">
<div class="panel-upload__icon">
<svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
</div>
<p class="panel-upload__title">{{ $product->image_path ? basename($product->image_path) : 'Seret & lepas gambar di sini' }}</p>
<p class="panel-upload__hint">atau <span class="panel-upload__link">klik untuk ganti file</span></p>
<p class="panel-upload__meta">PNG, JPG, WEBP · Maks. 2 MB</p>
<input type="file" name="image" class="panel-upload__input" accept="image/*" hidden>
</div>
</div>
</div>
</div>
</div>
</div>

<div class="panel-form__actions panel-form__actions--left panel-form__actions--bar">
<a href="{{ route('admin.produk.index') }}" class="panel-btn panel-btn--outline">Batal</a>
<button type="submit" name="status" value="draft" class="panel-btn panel-btn--outline">Simpan Draft</button>
<button type="submit" name="status" value="active" class="panel-btn">Simpan Perubahan</button>
</div>
</form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('produk-form');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var indexUrl = @json(route('admin.produk.index'));

    document.querySelectorAll('.panel-form-status__item').forEach(function (item) {
        item.addEventListener('click', function () {
            document.querySelectorAll('.panel-form-status__item').forEach(function (i) { i.classList.remove('panel-form-status__item--on'); });
            item.classList.add('panel-form-status__item--on');
            item.querySelector('input').checked = true;
        });
    });

    var zone = document.getElementById('upload-zone');
    var fileInput = zone.querySelector('.panel-upload__input');
    zone.addEventListener('click', function () { fileInput.click(); });
    fileInput.addEventListener('change', function () {
        if (fileInput.files.length) zone.querySelector('.panel-upload__title').textContent = fileInput.files[0].name;
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var submitter = e.submitter;
        var fd = new FormData(form);
        fd.set('_method', 'PUT');
        if (submitter && submitter.name === 'status') {
            fd.set('status', submitter.value);
        } else {
            fd.set('status', 'active');
        }

        var buttons = form.querySelectorAll('button[type="submit"]');
        buttons.forEach(function (b) { b.disabled = true; });

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
                throw new Error(result.json.message || 'Gagal memperbarui produk.');
            }
            MtNotify.redirectWithToast(indexUrl, result.json.message, 'success', 'Berhasil');
        })
        .catch(function (err) {
            MtNotify.toast(err.message || 'Gagal memperbarui produk.', 'error', 'Gagal');
        })
        .finally(function () {
            buttons.forEach(function (b) { b.disabled = false; });
        });
    });
});
</script>
@endpush
