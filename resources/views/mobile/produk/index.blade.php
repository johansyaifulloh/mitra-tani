@extends('layouts.mobile.home')
@section('title', 'Beranda | Mantri Tani')
@section('content')
<div class="flex items-center gap-2 overflow-x-auto scrollbar-hide -mx-5 px-5 py-1 mb-4" id="category-pills">
    <button type="button" class="category-pill category-pill--active shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-700 text-white transition-all shadow-sm" data-pill-cat="all">Semua</button>
    @foreach($categories ?? [] as $cat)
    <button type="button" class="category-pill shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-all" data-pill-cat="{{ $cat['id'] }}">
        {{ $cat['icon'] }} {{ $cat['name'] }}
    </button>
    @endforeach
</div>
<p class="section-label" id="catalog-section-label">Semua Produk</p>
<div class="product-grid" id="produk-grid">
<div class="product-grid__loading" id="produk-loading">Memuat produk...</div>
</div>
<div class="mobile-pagination" id="produk-pagination"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var urlParams = new URLSearchParams(window.location.search);
    var initialCats = [];
    urlParams.forEach(function (value, key) {
        if (key === 'category' || key === 'category_id' || key === 'categories' || key.indexOf('categories') === 0) {
            if (value && initialCats.indexOf(String(value)) === -1) {
                initialCats.push(String(value));
            }
        }
    });

    var state = {
        page: 1,
        search: '',
        categoryIds: initialCats,
        draftCategoryIds: initialCats.slice(),
        perPage: 8,
        loading: false,
    };
    var searchInput = document.getElementById('produk-search');
    var grid = document.getElementById('produk-grid');
    var paginationEl = document.getElementById('produk-pagination');
    var filterTrigger = document.getElementById('produk-filter-trigger');
    var filterSheet = document.getElementById('produk-filter-sheet');
    var filterOverlay = document.getElementById('produk-filter-overlay');
    var filterClose = document.getElementById('produk-filter-close');
    var filterReset = document.getElementById('produk-filter-reset');
    var filterApply = document.getElementById('produk-filter-apply');
    var filterBadge = document.getElementById('produk-filter-badge');
    var activeFiltersEl = document.getElementById('produk-active-filters');
    var filterInputs = filterSheet.querySelectorAll('[data-filter-cat]');
    var debounceTimer = null;
    var dataUrl = @json(route('toko.produk.data'));
    var showUrlTemplate = @json(route('toko.produk.show', ['slug' => '__SLUG__']));

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function showUrl(slug) {
        return showUrlTemplate.replace('__SLUG__', slug);
    }

    function renderCard(product, index) {
        var badge = product.badge
            ? '<span class="product-card__badge">' + escapeHtml(product.badge) + '</span>'
            : '';
        return ''
            + '<a href="' + escapeHtml(showUrl(product.slug)) + '" class="product-card animate-card-in" style="animation-delay:' + (index * 0.07) + 's">'
            + '<div class="product-card__media">'
            + badge
            + '<div class="product-card__icon">' + escapeHtml(product.emoji) + '</div>'
            + '</div>'
            + '<div class="product-card__body">'
            + '<span class="product-card__cat">' + escapeHtml(product.cat) + '</span>'
            + '<h3 class="product-card__name">' + escapeHtml(product.name) + '</h3>'
            + '<p class="product-card__sold">' + escapeHtml(product.sold_label || '0 terjual') + '</p>'
            + '<div class="product-card__footer">'
            + '<span class="product-card__price">' + escapeHtml(product.price) + '</span>'
            + '<button type="button" class="product-card__buy" data-cart-add data-product-id="' + product.id + '" aria-label="Beli langsung">'
            + '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
            + 'Beli'
            + '</button>'
            + '</div>'
            + '</div>'
            + '</a>';
    }

    function renderRows(items, append) {
        if (!items.length && !append) {
            grid.innerHTML = '<div class="product-grid__empty">Tidak ada produk ditemukan.</div>';
            return;
        }

        var html = items.map(function (p, i) { return renderCard(p, i); }).join('');

        if (append) {
            grid.insertAdjacentHTML('beforeend', html);
        } else {
            grid.innerHTML = html;
        }
    }

    function renderPagination(meta) {
        if (!meta.total || meta.current_page >= meta.last_page) {
            paginationEl.innerHTML = '';
            return;
        }

        paginationEl.innerHTML = '<button type="button" class="mobile-pagination__btn" id="produk-load-more">Muat lebih banyak</button>';

        document.getElementById('produk-load-more').addEventListener('click', function () {
            state.page += 1;
            loadProducts(true);
        });
    }

    function syncDraftCheckboxes() {
        filterInputs.forEach(function (input) {
            input.checked = state.draftCategoryIds.indexOf(input.value) !== -1;
        });
    }

    function syncCategoryPills() {
        var pills = document.querySelectorAll('#category-pills [data-pill-cat]');
        pills.forEach(function (pill) {
            var catId = pill.dataset.pillCat;
            var isActive = (catId === 'all' && state.categoryIds.length === 0) || (state.categoryIds.indexOf(catId) !== -1 && state.categoryIds.length === 1);
            if (isActive) {
                pill.className = 'category-pill category-pill--active shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-700 text-white transition-all shadow-sm';
            } else {
                pill.className = 'category-pill shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-all';
            }
        });
    }

    function renderActiveFilters() {
        syncCategoryPills();
        var sectionLabel = document.getElementById('catalog-section-label');
        if (!state.categoryIds.length) {
            if (sectionLabel) sectionLabel.textContent = 'Semua Produk';
            activeFiltersEl.hidden = true;
            activeFiltersEl.innerHTML = '';
            filterBadge.hidden = true;
            filterTrigger.classList.remove('search-bar__filter--active');
            return;
        }

        var activeLabels = [];
        filterBadge.hidden = false;
        filterBadge.textContent = state.categoryIds.length;
        filterTrigger.classList.add('search-bar__filter--active');
        activeFiltersEl.hidden = false;
        activeFiltersEl.innerHTML = state.categoryIds.map(function (id) {
            var input = filterSheet.querySelector('[data-filter-cat][value="' + id + '"]');
            var pill = document.querySelector('#category-pills [data-pill-cat="' + id + '"]');
            var label = input ? input.dataset.label : (pill ? pill.textContent.trim() : 'Kategori');
            activeLabels.push(label.trim());
            return '<button type="button" class="store-active-filters__tag" data-remove-cat="' + escapeHtml(id) + '">'
                + escapeHtml(label)
                + '<span aria-hidden="true">&times;</span></button>';
        }).join('');

        if (sectionLabel && activeLabels.length) {
            sectionLabel.textContent = 'Kategori: ' + activeLabels.join(', ');
        }

        activeFiltersEl.querySelectorAll('[data-remove-cat]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.dataset.removeCat;
                state.categoryIds = state.categoryIds.filter(function (item) { return item !== id; });
                state.draftCategoryIds = state.categoryIds.slice();
                syncDraftCheckboxes();
                renderActiveFilters();
                loadProducts(false);
            });
        });
    }

    function openFilterSheet() {
        state.draftCategoryIds = state.categoryIds.slice();
        syncDraftCheckboxes();
        filterSheet.hidden = false;
        document.body.classList.add('store-filter-open');
        filterTrigger.setAttribute('aria-expanded', 'true');
    }

    function closeFilterSheet() {
        filterSheet.hidden = true;
        document.body.classList.remove('store-filter-open');
        filterTrigger.setAttribute('aria-expanded', 'false');
    }

    function loadProducts(append) {
        if (state.loading) return;
        state.loading = true;

        if (!append) {
            state.page = 1;
            grid.innerHTML = '<div class="product-grid__loading">Memuat produk...</div>';
            paginationEl.innerHTML = '';
        } else {
            var loadMore = document.getElementById('produk-load-more');
            if (loadMore) {
                loadMore.disabled = true;
                loadMore.textContent = 'Memuat...';
            }
        }

        var params = new URLSearchParams({
            page: state.page,
            per_page: state.perPage,
            search: state.search,
        });
        state.categoryIds.forEach(function (id) {
            params.append('categories[]', id);
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

            renderRows(json.data, append);
            renderPagination(json.meta);
        })
        .catch(function () {
            if (!append) {
                grid.innerHTML = '<div class="product-grid__empty">Gagal memuat produk. Coba refresh halaman.</div>';
            }
            if (append) {
                var btn = document.getElementById('produk-load-more');
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Muat lebih banyak';
                }
            }
        })
        .finally(function () {
            state.loading = false;
        });
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            state.search = searchInput.value.trim();
            loadProducts(false);
        }, 400);
    });

    filterTrigger.addEventListener('click', function () {
        if (filterSheet.hidden) {
            openFilterSheet();
        } else {
            closeFilterSheet();
        }
    });

    filterClose.addEventListener('click', closeFilterSheet);
    filterOverlay.addEventListener('click', closeFilterSheet);

    filterReset.addEventListener('click', function () {
        state.draftCategoryIds = [];
        state.categoryIds = [];
        syncDraftCheckboxes();
        renderActiveFilters();
        closeFilterSheet();
        loadProducts(false);
    });

    filterApply.addEventListener('click', function () {
        state.categoryIds = state.draftCategoryIds.slice();
        renderActiveFilters();
        closeFilterSheet();
        loadProducts(false);
    });

    filterInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            var value = input.value;
            if (input.checked) {
                if (state.draftCategoryIds.indexOf(value) === -1) {
                    state.draftCategoryIds.push(value);
                }
            } else {
                state.draftCategoryIds = state.draftCategoryIds.filter(function (item) { return item !== value; });
            }
        });
    });

    document.querySelectorAll('#category-pills [data-pill-cat]').forEach(function (pill) {
        pill.addEventListener('click', function () {
            var catId = pill.dataset.pillCat;
            if (catId === 'all') {
                state.categoryIds = [];
            } else {
                state.categoryIds = [catId];
            }
            state.draftCategoryIds = state.categoryIds.slice();
            syncDraftCheckboxes();
            renderActiveFilters();
            loadProducts(false);
        });
    });

    renderActiveFilters();
    loadProducts(false);
});
</script>
@endpush
