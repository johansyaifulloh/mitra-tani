@extends('layouts.admin.app', ['navActive' => 'admin.produk.create'])
@section('page-title', 'Tambah Produk')
@section('page-sub', 'Tambah produk baru ke katalog toko')
@section('content')

<div class="panel-form-layout">
<form class="panel-form-layout__main" id="produk-form" action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data">
@csrf
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
<input type="text" id="produk-nama" name="name" class="panel-form__input" placeholder="Contoh: Pupuk Urea 50kg" data-preview="name">
<p class="panel-field-hint">Gunakan nama yang jelas dan mudah dicari pelanggan</p>
</div>
<div class="panel-form__field">
<label class="panel-form__label" for="produk-kategori">Kategori <span class="panel-form__req">*</span></label>
<select id="produk-kategori" name="category_id" class="panel-form__input" data-preview="cat">
@foreach($categories as $cat)
<option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
@endforeach
</select>
</div>
<div class="panel-form__field">
<label class="panel-form__label" for="produk-badge">Label Produk</label>
<select id="produk-badge" name="badge" class="panel-form__input" data-preview="badge">
<option value="">Tanpa label</option>
@foreach($badges as $badge)
<option value="{{ $badge }}">{{ $badge }}</option>
@endforeach
</select>
</div>
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label" for="produk-desk">Deskripsi</label>
<textarea id="produk-desk" name="description" class="panel-form__input panel-form__textarea" rows="4" placeholder="Jelaskan manfaat, spesifikasi, atau cara pakai produk..." data-preview="desc"></textarea>
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
<input type="number" id="produk-harga" name="price" class="panel-form__input panel-input-group__input" placeholder="85000" data-preview="price" value="">
</div>
</div>
<div class="panel-form__field">
<label class="panel-form__label" for="produk-stok">Stok Tersedia <span class="panel-form__req">*</span></label>
<div class="panel-input-group">
<input type="number" id="produk-stok" name="stock" class="panel-form__input panel-input-group__input" placeholder="120" data-preview="stock" value="">
<span class="panel-input-group__suffix">unit</span>
</div>
</div>
<div class="panel-form__field panel-form__field--full">
<label class="panel-form__label">Status Produk</label>
<div class="panel-form-status">
<label class="panel-form-status__item panel-form-status__item--on">
<input type="radio" name="status_preview" value="active" checked data-status-preview>
<span class="panel-form-status__dot"></span>
<span>Aktif — tampil di toko</span>
</label>
<label class="panel-form-status__item">
<input type="radio" name="status_preview" value="draft" data-status-preview>
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
<input type="text" id="produk-emoji" name="emoji" class="panel-form__input panel-emoji-picker__input" placeholder="🌱" maxlength="2" data-preview="emoji" value="🌱">
<div class="panel-emoji-picker__grid">
@foreach(['🌱','🌾','💊','🔧','🌿','🍅','💧','🌽'] as $emoji)
<button type="button" class="panel-emoji-picker__btn {{ $emoji === '🌱' ? 'active' : '' }}" data-emoji="{{ $emoji }}">{{ $emoji }}</button>
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
<p class="panel-upload__title">Seret & lepas gambar di sini</p>
<p class="panel-upload__hint">atau <span class="panel-upload__link">klik untuk pilih file</span></p>
<p class="panel-upload__meta">PNG, JPG, WEBP · Maks. 2 MB · Rasio 1:1 disarankan</p>
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
<button type="submit" name="status" value="active" class="panel-btn">Simpan Produk</button>
</div>
</form>

<aside class="panel-form-layout__aside">
<div class="panel-form-preview panel-form-preview--sticky">
<div class="panel-form-preview__head">
<h4 class="panel-form-preview__title">Preview Toko</h4>
<span class="panel-form-preview__tag">Live</span>
</div>
<div class="panel-form-preview__card">
<div class="product-card product-card--preview">
<div class="product-card__media">
<span class="product-card__badge" id="preview-badge" style="display:none"></span>
<div class="product-card__icon" id="preview-emoji">🌱</div>
</div>
<div class="product-card__body">
<span class="product-card__cat" id="preview-cat">{{ $categories[0]->name ?? 'Pupuk' }}</span>
<h3 class="product-card__name" id="preview-name">Nama Produk</h3>
<div class="product-card__footer">
<span class="product-card__price" id="preview-price">Rp 0</span>
<button type="button" class="product-card__buy" tabindex="-1">Beli</button>
</div>
</div>
</div>
<div class="panel-form-preview__meta">
<div class="panel-form-preview__row">
<span>Stok</span>
<strong id="preview-stock">— unit</strong>
</div>
<div class="panel-form-preview__row">
<span>Status</span>
<strong id="preview-status" class="panel-form-preview__ok">Aktif</strong>
</div>
</div>
</div>
<p class="panel-form-preview__note">Tampilan card seperti di halaman toko mobile</p>
</aside>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function formatRp(n) {
        n = parseInt(n, 10) || 0;
        return 'Rp ' + n.toLocaleString('id-ID');
    }
    function bindPreview(el, fn) {
        if (!el) return;
        el.addEventListener('input', fn);
        el.addEventListener('change', fn);
    }
    var nameEl = document.getElementById('produk-nama');
    var catEl = document.getElementById('produk-kategori');
    var badgeEl = document.getElementById('produk-badge');
    var priceEl = document.getElementById('produk-harga');
    var stockEl = document.getElementById('produk-stok');
    var emojiEl = document.getElementById('produk-emoji');
    var form = document.getElementById('produk-form');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var indexUrl = @json(route('admin.produk.index'));

    bindPreview(nameEl, function () {
        document.getElementById('preview-name').textContent = nameEl.value || 'Nama Produk';
    });
    bindPreview(catEl, function () {
        var opt = catEl.options[catEl.selectedIndex];
        document.getElementById('preview-cat').textContent = opt ? opt.text.trim() : '';
    });
    bindPreview(badgeEl, function () {
        var b = document.getElementById('preview-badge');
        if (badgeEl.value) { b.textContent = badgeEl.value; b.style.display = ''; }
        else { b.style.display = 'none'; }
    });
    bindPreview(priceEl, function () {
        document.getElementById('preview-price').textContent = formatRp(priceEl.value);
    });
    bindPreview(stockEl, function () {
        document.getElementById('preview-stock').textContent = (stockEl.value || '—') + ' unit';
    });
    bindPreview(emojiEl, function () {
        document.getElementById('preview-emoji').textContent = emojiEl.value || '🌱';
    });
    document.querySelectorAll('.panel-emoji-picker__btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            emojiEl.value = btn.dataset.emoji;
            document.querySelectorAll('.panel-emoji-picker__btn').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            emojiEl.dispatchEvent(new Event('input'));
        });
    });
    document.querySelectorAll('.panel-form-status__item').forEach(function (item) {
        item.addEventListener('click', function () {
            document.querySelectorAll('.panel-form-status__item').forEach(function (i) { i.classList.remove('panel-form-status__item--on'); });
            item.classList.add('panel-form-status__item--on');
            item.querySelector('input').checked = true;
            var val = item.querySelector('input').value;
            var el = document.getElementById('preview-status');
            el.textContent = val === 'draft' ? 'Draft' : 'Aktif';
            el.className = val === 'draft' ? '' : 'panel-form-preview__ok';
        });
    });
    var zone = document.getElementById('upload-zone');
    var fileInput = zone.querySelector('.panel-upload__input');
    zone.addEventListener('click', function () { fileInput.click(); });
    zone.addEventListener('dragover', function (e) { e.preventDefault(); zone.classList.add('panel-upload__zone--drag'); });
    zone.addEventListener('dragleave', function () { zone.classList.remove('panel-upload__zone--drag'); });
    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('panel-upload__zone--drag');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            zone.querySelector('.panel-upload__title').textContent = e.dataTransfer.files[0].name;
        }
    });
    fileInput.addEventListener('change', function () {
        if (fileInput.files.length) zone.querySelector('.panel-upload__title').textContent = fileInput.files[0].name;
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var submitter = e.submitter;
        var fd = new FormData(form);
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
                throw new Error(result.json.message || 'Gagal menyimpan produk.');
            }
            MtNotify.redirectWithToast(indexUrl, result.json.message, 'success', 'Berhasil');
        })
        .catch(function (err) {
            MtNotify.toast(err.message || 'Gagal menyimpan produk.', 'error', 'Gagal');
        })
        .finally(function () {
            buttons.forEach(function (b) { b.disabled = false; });
        });
    });
});
</script>
@endpush
