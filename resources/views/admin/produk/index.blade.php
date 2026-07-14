@extends('layouts.admin.app', ['navActive' => 'admin.produk.index'])
@section('page-title', 'Data Produk')
@section('page-sub', 'Kelola katalog produk pertanian')
@section('content')
<nav class="panel-breadcrumb" aria-label="Breadcrumb">
<a href="{{ route('admin.dashboard') }}" class="panel-breadcrumb__link">Dashboard</a>
<span class="panel-breadcrumb__sep" aria-hidden="true">/</span>
<span class="panel-breadcrumb__current" aria-current="page">Data Produk</span>
</nav>
<p class="panel-page-desc">Kelola katalog produk pertanian toko secara terpusat. Gunakan toggle status untuk mengatur visibilitas produk kepada pelanggan. Produk dengan status Aktif akan ditampilkan pada katalog dan dapat dibeli, sedangkan produk dengan status Nonaktif akan diarsipkan sehingga tidak ditampilkan pada halaman pelanggan tanpa menghapus data produk. Gunakan menu ⋮ untuk mengelola produk melalui fitur Edit atau Hapus Permanen.</p>

<div class="panel-toolbar">
<input type="search" id="produk-search" placeholder="Cari produk..." class="panel-form__input panel-form__input--search" autocomplete="off">
<a href="{{ route('admin.produk.create') }}" class="panel-btn">+ Tambah Produk</a>
</div>
<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Daftar Produk</h3>
<span class="panel-card__meta" id="produk-total">Memuat...</span>
</div>
<div class="panel-table-wrap">
<table class="panel-table">
<thead>
<tr>
<th class="panel-table__col-no">No</th>
<th>Produk</th>
<th>Kategori</th>
<th>Harga</th>
<th>Stok</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody id="produk-tbody">
<tr><td colspan="7" class="panel-table__empty">Memuat data...</td></tr>
</tbody>
</table>
</div>
<div class="panel-card__foot" id="produk-pagination"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var state = { page: 1, search: '', perPage: 8, loading: false };
    var searchInput = document.getElementById('produk-search');
    var tbody = document.getElementById('produk-tbody');
    var totalEl = document.getElementById('produk-total');
    var paginationEl = document.getElementById('produk-pagination');
    var debounceTimer = null;
    var dataUrl = @json(route('admin.produk.data'));
    var toggleUrlTemplate = @json(route('admin.produk.toggle-status', ['id' => '__ID__']));
    var destroyUrlTemplate = @json(route('admin.produk.destroy', ['id' => '__ID__']));
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var openMenu = null;

    var iconDots = '<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><circle cx="8" cy="3" r="1.5"/><circle cx="8" cy="8" r="1.5"/><circle cx="8" cy="13" r="1.5"/></svg>';
    var iconEdit = '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>';
    var iconTrash = '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>';

    function notify(message, type, title) {
        MtNotify.toast(message, type, title);
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function actionUrl(template, id) {
        return template.replace('__ID__', id);
    }

    function closeOpenMenu() {
        if (!openMenu) return;
        openMenu.dropdown.hidden = true;
        openMenu.trigger.setAttribute('aria-expanded', 'false');
        openMenu = null;
    }

    function bindRowMenus() {
        tbody.querySelectorAll('.panel-row-menu').forEach(function (menu) {
            var trigger = menu.querySelector('.panel-row-menu__trigger');
            var dropdown = menu.querySelector('.panel-row-menu__dropdown');

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                if (openMenu && openMenu.dropdown === dropdown) {
                    closeOpenMenu();
                    return;
                }
                closeOpenMenu();
                dropdown.hidden = false;
                trigger.setAttribute('aria-expanded', 'true');
                openMenu = { trigger: trigger, dropdown: dropdown };
            });

            menu.querySelectorAll('[data-action="delete"]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    closeOpenMenu();
                    postAction(actionUrl(destroyUrlTemplate, btn.dataset.id), btn.dataset.name, {
                        title: 'Hapus permanen?',
                        message: 'Produk "' + btn.dataset.name + '" akan dihapus dari database. Tindakan ini tidak bisa dibatalkan.',
                        confirmText: 'Ya, hapus',
                        variant: 'danger',
                        isDelete: true,
                        successMessage: 'Produk berhasil dihapus.',
                        errorMessage: 'Gagal menghapus produk.',
                    });
                });
            });
        });
    }

    function bindStatusToggles() {
        tbody.querySelectorAll('[data-status-toggle]').forEach(function (input) {
            input.addEventListener('change', function () {
                var id = input.dataset.id;
                var prevChecked = !input.checked;
                input.disabled = true;

                var fd = new FormData();
                fd.append('_token', csrf);

                fetch(actionUrl(toggleUrlTemplate, id), {
                    method: 'POST',
                    body: fd,
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                })
                .then(function (res) { return res.json().then(function (json) { return { ok: res.ok, json: json }; }); })
                .then(function (result) {
                    if (!result.ok || !result.json.success) {
                        throw new Error(result.json.message || 'Gagal mengubah status.');
                    }
                    var label = input.closest('.panel-table-status').querySelector('.panel-table-status__label');
                    label.textContent = result.json.status_label;
                    notify(result.json.message, 'success', 'Berhasil');
                })
                .catch(function (err) {
                    input.checked = prevChecked;
                    notify(err.message || 'Gagal mengubah status.', 'error', 'Gagal');
                })
                .finally(function () {
                    input.disabled = false;
                });
            });
        });
    }

    function postAction(url, name, options) {
        MtNotify.confirm({
            title: options.title,
            message: options.message,
            confirmText: options.confirmText,
            variant: options.variant || 'warning',
        }).then(function (ok) {
            if (!ok) return;

            var fd = new FormData();
            fd.append('_token', csrf);
            if (options.isDelete) {
                fd.append('_method', 'DELETE');
            }

            fetch(url, {
                method: 'POST',
                body: fd,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            })
            .then(function (res) { return res.json().then(function (json) { return { ok: res.ok, json: json }; }); })
            .then(function (result) {
                if (!result.ok || !result.json.success) {
                    throw new Error(result.json.message || options.errorMessage || 'Aksi gagal.');
                }
                notify(result.json.message || options.successMessage, 'success', 'Berhasil');
                loadProducts();
            })
            .catch(function (err) {
                notify(err.message || options.errorMessage || 'Aksi gagal.', 'error', 'Gagal');
            });
        });
    }

    function renderRows(items, meta) {
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="panel-table__empty">Tidak ada produk ditemukan.</td></tr>';
            return;
        }

        var startNo = meta && meta.from ? meta.from : 1;

        tbody.innerHTML = items.map(function (p, index) {
            var isActive = p.status === 'active';
            return '<tr>'
                + '<td class="panel-table__col-no">' + (startNo + index) + '</td>'
                + '<td><span class="panel-table__product">' + escapeHtml(p.emoji) + ' ' + escapeHtml(p.name) + '</span></td>'
                + '<td>' + escapeHtml(p.category_name) + '</td>'
                + '<td>' + escapeHtml(p.price_label) + '</td>'
                + '<td>' + p.stock + '</td>'
                + '<td>'
                + '<label class="panel-table-status" title="' + (isActive ? 'Nonaktifkan produk' : 'Aktifkan produk') + '">'
                + '<input type="checkbox" data-status-toggle data-id="' + p.id + '"' + (isActive ? ' checked' : '') + '>'
                + '<span class="panel-table-status__track" aria-hidden="true"></span>'
                + '<span class="panel-table-status__label">' + escapeHtml(p.status_label) + '</span>'
                + '</label>'
                + '</td>'
                + '<td class="panel-table__actions">'
                + '<div class="panel-table__actions-inner">'
                + '<div class="panel-row-menu">'
                + '<button type="button" class="panel-row-menu__trigger" aria-label="Aksi produk" aria-expanded="false" aria-haspopup="true">' + iconDots + '</button>'
                + '<div class="panel-row-menu__dropdown" hidden role="menu">'
                + '<a href="' + escapeHtml(p.edit_url) + '" class="panel-row-menu__item" role="menuitem">' + iconEdit + ' Edit</a>'
                + '<button type="button" class="panel-row-menu__item panel-row-menu__item--danger" data-action="delete" data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '" role="menuitem">' + iconTrash + ' Hapus</button>'
                + '</div>'
                + '</div>'
                + '</div>'
                + '</td>'
                + '</tr>';
        }).join('');

        bindRowMenus();
        bindStatusToggles();
    }

    function renderPagination(meta) {
        if (!meta.total) {
            paginationEl.innerHTML = '';
            return;
        }

        var html = '<div class="panel-pagination">';
        html += '<div class="panel-pagination__info">Menampilkan <strong>' + meta.from + '</strong>–<strong>' + meta.to + '</strong> dari <strong>' + meta.total + '</strong> data</div>';

        if (meta.last_page > 1) {
            html += '<nav class="panel-pagination__nav" aria-label="Pagination">';

            if (meta.current_page <= 1) {
                html += '<span class="panel-pagination__btn panel-pagination__btn--disabled">← Prev</span>';
            } else {
                html += '<button type="button" class="panel-pagination__btn" data-page="' + (meta.current_page - 1) + '">← Prev</button>';
            }

            var start = Math.max(1, meta.current_page - 2);
            var end = Math.min(meta.last_page, meta.current_page + 2);

            for (var i = start; i <= end; i++) {
                if (i === meta.current_page) {
                    html += '<span class="panel-pagination__page active">' + i + '</span>';
                } else {
                    html += '<button type="button" class="panel-pagination__page" data-page="' + i + '">' + i + '</button>';
                }
            }

            if (meta.current_page >= meta.last_page) {
                html += '<span class="panel-pagination__btn panel-pagination__btn--disabled">Next →</span>';
            } else {
                html += '<button type="button" class="panel-pagination__btn" data-page="' + (meta.current_page + 1) + '">Next →</button>';
            }

            html += '</nav>';
        }

        html += '</div>';
        paginationEl.innerHTML = html;

        paginationEl.querySelectorAll('[data-page]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                state.page = parseInt(btn.dataset.page, 10);
                loadProducts();
            });
        });
    }

    function loadProducts() {
        if (state.loading) return;
        state.loading = true;
        tbody.innerHTML = '<tr><td colspan="7" class="panel-table__empty">Memuat data...</td></tr>';

        var params = new URLSearchParams({
            page: state.page,
            per_page: state.perPage,
            search: state.search,
        });

        fetch(dataUrl + '?' + params.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (json) {
            if (!json.success) throw new Error('Gagal memuat data');

            totalEl.textContent = json.meta.total + ' produk';
            renderRows(json.data, json.meta);
            renderPagination(json.meta);
        })
        .catch(function () {
            tbody.innerHTML = '<tr><td colspan="7" class="panel-table__empty">Gagal memuat data. Coba refresh halaman.</td></tr>';
            totalEl.textContent = '—';
            paginationEl.innerHTML = '';
        })
        .finally(function () {
            state.loading = false;
        });
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            state.search = searchInput.value.trim();
            state.page = 1;
            loadProducts();
        }, 400);
    });

    document.addEventListener('click', function () {
        closeOpenMenu();
    });

    loadProducts();
});
</script>
@endpush
