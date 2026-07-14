@extends('layouts.admin.app', ['navActive' => 'admin.kategori.index'])
@section('page-title', 'Data Kategori')
@section('page-sub', 'Organisasi produk toko')
@section('content')
<nav class="panel-breadcrumb" aria-label="Breadcrumb">
<a href="{{ route('admin.dashboard') }}" class="panel-breadcrumb__link">Dashboard</a>
<span class="panel-breadcrumb__sep" aria-hidden="true">/</span>
<span class="panel-breadcrumb__current" aria-current="page">Data Kategori</span>
</nav>
<p class="panel-page-desc">Kelola kategori produk toko secara terpusat. Gunakan toggle status untuk mengatur visibilitas kategori kepada pelanggan. Klik ikon filter untuk memilih beberapa kategori sekaligus (Benih, Pupuk, dan lainnya).</p>

<div class="panel-toolbar">
<input type="search" id="kategori-search" placeholder="Cari kategori..." class="panel-form__input panel-form__input--search" autocomplete="off">
<div class="panel-filter" id="kategori-filter">
<button type="button" class="panel-filter__trigger" id="kategori-filter-trigger" aria-expanded="false" aria-haspopup="true" aria-controls="kategori-filter-panel">
<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M6 12h12M10 20h4"/></svg>
<span>Filter</span>
<span class="panel-filter__badge" id="kategori-filter-badge" hidden>0</span>
</button>
<div class="panel-filter__panel" id="kategori-filter-panel" hidden>
<div class="panel-filter__head">
<h4 class="panel-filter__title">Filter Kategori</h4>
<button type="button" class="panel-filter__close" id="kategori-filter-close" aria-label="Tutup filter">&times;</button>
</div>
<div class="panel-filter__body">
<div class="panel-filter__section">
<p class="panel-filter__label">Status</p>
<div class="panel-filter__chips" data-filter-group="status">
<button type="button" class="panel-filter__chip" data-filter-value="active">Aktif</button>
<button type="button" class="panel-filter__chip" data-filter-value="inactive">Nonaktif</button>
</div>
</div>
<div class="panel-filter__section">
<p class="panel-filter__label">Nama Kategori</p>
<div class="panel-filter__chips" data-filter-group="categories">
@foreach($filterCategories as $cat)
<button type="button" class="panel-filter__chip" data-filter-value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</button>
@endforeach
</div>
</div>
</div>
<div class="panel-filter__foot">
<button type="button" class="panel-btn panel-btn--outline panel-btn--sm" id="kategori-filter-reset">Reset</button>
<button type="button" class="panel-btn panel-btn--sm" id="kategori-filter-apply">Terapkan</button>
</div>
</div>
</div>
<a href="{{ route('admin.kategori.create') }}" class="panel-btn">+ Tambah Kategori</a>
</div>

<div class="panel-filter__active" id="kategori-active-filters" hidden></div>

<div class="panel-card panel-card-hover">
<div class="panel-card__head">
<h3 class="panel-card__title">Daftar Kategori</h3>
<span class="panel-card__meta" id="kategori-total">Memuat...</span>
</div>
<div class="panel-table-wrap">
<table class="panel-table">
<thead>
<tr>
<th class="panel-table__col-no">No</th>
<th>Kategori</th>
<th>Urutan</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody id="kategori-tbody">
<tr><td colspan="5" class="panel-table__empty">Memuat data...</td></tr>
</tbody>
</table>
</div>
<div class="panel-card__foot" id="kategori-pagination"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var state = {
        page: 1,
        search: '',
        perPage: 50,
        loading: false,
        filters: { status: [], categories: [] },
        draftFilters: { status: [], categories: [] },
    };
    var searchInput = document.getElementById('kategori-search');
    var tbody = document.getElementById('kategori-tbody');
    var totalEl = document.getElementById('kategori-total');
    var paginationEl = document.getElementById('kategori-pagination');
    var filterWrap = document.getElementById('kategori-filter');
    var filterTrigger = document.getElementById('kategori-filter-trigger');
    var filterPanel = document.getElementById('kategori-filter-panel');
    var filterBadge = document.getElementById('kategori-filter-badge');
    var filterClose = document.getElementById('kategori-filter-close');
    var filterReset = document.getElementById('kategori-filter-reset');
    var filterApply = document.getElementById('kategori-filter-apply');
    var activeFiltersEl = document.getElementById('kategori-active-filters');
    var debounceTimer = null;
    var dataUrl = @json(route('admin.kategori.data'));
    var toggleUrlTemplate = @json(route('admin.kategori.toggle-status', ['id' => '__ID__']));
    var destroyUrlTemplate = @json(route('admin.kategori.destroy', ['id' => '__ID__']));
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var openMenu = null;

    var iconDots = '<svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><circle cx="8" cy="3" r="1.5"/><circle cx="8" cy="8" r="1.5"/><circle cx="8" cy="13" r="1.5"/></svg>';
    var iconEdit = '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>';
    var iconTrash = '<svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>';

    function notify(message, type, title) {
        MtNotify.toast(message, type, title);
    }

    function escapeHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function actionUrl(template, id) {
        return template.replace('__ID__', id);
    }

    function countActiveFilters(filters) {
        return filters.status.length + filters.categories.length;
    }

    function syncDraftChips() {
        filterPanel.querySelectorAll('[data-filter-group]').forEach(function (group) {
            var key = group.dataset.filterGroup;
            var selected = state.draftFilters[key] || [];
            group.querySelectorAll('.panel-filter__chip').forEach(function (chip) {
                var value = chip.dataset.filterValue;
                var isSelected = selected.indexOf(value) !== -1;
                chip.classList.toggle('active', isSelected);
                chip.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            });
        });
    }

    function renderActiveFilters() {
        var tags = [];
        state.filters.status.forEach(function (value) {
            tags.push({
                group: 'status',
                value: value,
                label: value === 'active' ? 'Aktif' : 'Nonaktif',
            });
        });
        filterPanel.querySelectorAll('[data-filter-group="categories"] .panel-filter__chip').forEach(function (chip) {
            if (state.filters.categories.indexOf(chip.dataset.filterValue) !== -1) {
                tags.push({
                    group: 'categories',
                    value: chip.dataset.filterValue,
                    label: chip.textContent.trim(),
                });
            }
        });

        if (!tags.length) {
            activeFiltersEl.hidden = true;
            activeFiltersEl.innerHTML = '';
            filterBadge.hidden = true;
            filterTrigger.classList.remove('panel-filter__trigger--active');
            return;
        }

        activeFiltersEl.hidden = false;
        filterBadge.hidden = false;
        filterBadge.textContent = tags.length;
        filterTrigger.classList.add('panel-filter__trigger--active');
        activeFiltersEl.innerHTML = tags.map(function (tag) {
            return '<button type="button" class="panel-filter__tag" data-remove-group="' + tag.group + '" data-remove-value="' + escapeHtml(tag.value) + '">'
                + escapeHtml(tag.label)
                + '<span aria-hidden="true">&times;</span></button>';
        }).join('');

        activeFiltersEl.querySelectorAll('.panel-filter__tag').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var group = btn.dataset.removeGroup;
                var value = btn.dataset.removeValue;
                state.filters[group] = state.filters[group].filter(function (item) { return item !== value; });
                state.draftFilters[group] = state.filters[group].slice();
                syncDraftChips();
                renderActiveFilters();
                state.page = 1;
                loadCategories();
            });
        });
    }

    function openFilterPanel() {
        state.draftFilters = {
            status: state.filters.status.slice(),
            categories: state.filters.categories.slice(),
        };
        syncDraftChips();
        filterPanel.hidden = false;
        filterTrigger.setAttribute('aria-expanded', 'true');
        filterTrigger.classList.add('panel-filter__trigger--open');
    }

    function closeFilterPanel() {
        filterPanel.hidden = true;
        filterTrigger.setAttribute('aria-expanded', 'false');
        filterTrigger.classList.remove('panel-filter__trigger--open');
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
                        title: 'Hapus kategori?',
                        message: 'Kategori "' + btn.dataset.name + '" akan dihapus dari database. Hanya kategori tanpa produk yang bisa dihapus.',
                        confirmText: 'Ya, hapus',
                        variant: 'danger',
                        isDelete: true,
                        successMessage: 'Kategori berhasil dihapus.',
                        errorMessage: 'Gagal menghapus kategori.',
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
                loadCategories();
            })
            .catch(function (err) {
                notify(err.message || options.errorMessage || 'Aksi gagal.', 'error', 'Gagal');
            });
        });
    }

    function renderRows(items, meta) {
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="panel-table__empty">Tidak ada kategori ditemukan.</td></tr>';
            return;
        }

        var startNo = meta && meta.from ? meta.from : 1;

        tbody.innerHTML = items.map(function (c, index) {
            var isActive = c.is_active;
            return '<tr>'
                + '<td class="panel-table__col-no">' + (startNo + index) + '</td>'
                + '<td><span class="panel-table__product"><span class="panel-table__cat-dot" style="--kat-color:' + escapeHtml(c.color) + '"></span>' + escapeHtml(c.icon) + ' ' + escapeHtml(c.name) + '</span></td>'
                + '<td>#' + c.display_order + '</td>'
                + '<td>'
                + '<label class="panel-table-status" title="' + (isActive ? 'Nonaktifkan kategori' : 'Aktifkan kategori') + '">'
                + '<input type="checkbox" data-status-toggle data-id="' + c.id + '"' + (isActive ? ' checked' : '') + '>'
                + '<span class="panel-table-status__track" aria-hidden="true"></span>'
                + '<span class="panel-table-status__label">' + escapeHtml(c.status_label) + '</span>'
                + '</label>'
                + '</td>'
                + '<td class="panel-table__actions">'
                + '<div class="panel-table__actions-inner">'
                + '<div class="panel-row-menu">'
                + '<button type="button" class="panel-row-menu__trigger" aria-label="Aksi kategori" aria-expanded="false" aria-haspopup="true">' + iconDots + '</button>'
                + '<div class="panel-row-menu__dropdown" hidden role="menu">'
                + '<a href="' + escapeHtml(c.edit_url) + '" class="panel-row-menu__item" role="menuitem">' + iconEdit + ' Edit</a>'
                + '<button type="button" class="panel-row-menu__item panel-row-menu__item--danger" data-action="delete" data-id="' + c.id + '" data-name="' + escapeHtml(c.name) + '" role="menuitem">' + iconTrash + ' Hapus</button>'
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
                loadCategories();
            });
        });
    }

    function loadCategories() {
        if (state.loading) return;
        state.loading = true;
        tbody.innerHTML = '<tr><td colspan="5" class="panel-table__empty">Memuat data...</td></tr>';

        var params = new URLSearchParams({
            page: state.page,
            per_page: state.perPage,
            search: state.search,
        });

        state.filters.status.forEach(function (value) {
            params.append('status[]', value);
        });
        state.filters.categories.forEach(function (value) {
            params.append('categories[]', value);
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

            totalEl.textContent = json.meta.total + ' kategori';
            renderRows(json.data, json.meta);
            renderPagination(json.meta);
        })
        .catch(function () {
            tbody.innerHTML = '<tr><td colspan="5" class="panel-table__empty">Gagal memuat data. Coba refresh halaman.</td></tr>';
            totalEl.textContent = '—';
            paginationEl.innerHTML = '';
        })
        .finally(function () {
            state.loading = false;
        });
    }

    filterTrigger.addEventListener('click', function (e) {
        e.stopPropagation();
        if (filterPanel.hidden) {
            openFilterPanel();
        } else {
            closeFilterPanel();
        }
    });

    filterClose.addEventListener('click', function () {
        closeFilterPanel();
    });

    filterPanel.querySelectorAll('[data-filter-group]').forEach(function (group) {
        var key = group.dataset.filterGroup;
        group.querySelectorAll('.panel-filter__chip').forEach(function (chip) {
            chip.addEventListener('click', function () {
                var value = chip.dataset.filterValue;
                var selected = state.draftFilters[key].slice();
                var index = selected.indexOf(value);
                if (index === -1) {
                    selected.push(value);
                } else {
                    selected.splice(index, 1);
                }
                state.draftFilters[key] = selected;
                syncDraftChips();
            });
        });
    });

    filterApply.addEventListener('click', function () {
        state.filters = {
            status: state.draftFilters.status.slice(),
            categories: state.draftFilters.categories.slice(),
        };
        renderActiveFilters();
        closeFilterPanel();
        state.page = 1;
        loadCategories();
    });

    filterReset.addEventListener('click', function () {
        state.draftFilters = { status: [], categories: [] };
        state.filters = { status: [], categories: [] };
        syncDraftChips();
        renderActiveFilters();
        closeFilterPanel();
        state.page = 1;
        loadCategories();
    });

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            state.search = searchInput.value.trim();
            state.page = 1;
            loadCategories();
        }, 400);
    });

    document.addEventListener('click', function (e) {
        closeOpenMenu();
        if (!filterWrap.contains(e.target)) {
            closeFilterPanel();
        }
    });

    renderActiveFilters();
    loadCategories();
});
</script>
@endpush
