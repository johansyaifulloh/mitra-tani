<header class="app-header app-header--hero app-header--fixed shrink-0">

<div class="app-header__top">

<div class="app-header__brand">

<div class="app-header__logo">MT</div>

<div><p class="app-header__title">Mantri Tani</p><p class="app-header__sub">📍 Selorejo, Malang</p></div>

</div>

{{-- Ikon Keranjang disembunyikan untuk Progress 40% --}}
</div>

<p class="app-header__greet">Halo, selamat belanja! 👋</p>

<div class="search-bar search-bar--light search-bar--with-filter">

<svg width="18" height="18" fill="none" stroke="#9ca89f" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>

<input type="search" id="produk-search" placeholder="Cari pupuk, benih, alat tani..." autocomplete="off">

<button type="button" class="search-bar__filter" id="produk-filter-trigger" aria-expanded="false" aria-controls="produk-filter-sheet" aria-label="Filter kategori">

<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M6 12h12M10 20h4"/></svg>

<span class="search-bar__filter-badge" id="produk-filter-badge" hidden>0</span>

</button>

</div>

<div class="store-active-filters scrollbar-hide" id="produk-active-filters" hidden></div>

</header>



<div class="store-filter-sheet" id="produk-filter-sheet" hidden>

<div class="store-filter-sheet__frame">

<div class="store-filter-sheet__overlay" id="produk-filter-overlay"></div>

<div class="store-filter-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="produk-filter-title">

<div class="store-filter-sheet__handle" aria-hidden="true"></div>

<div class="store-filter-sheet__head">

<h3 class="store-filter-sheet__title" id="produk-filter-title">Filter Kategori</h3>

<button type="button" class="store-filter-sheet__close" id="produk-filter-close" aria-label="Tutup filter">&times;</button>

</div>

<div class="store-filter-sheet__body">

@foreach($categories ?? [] as $cat)

<label class="store-filter-check">

<input type="checkbox" class="store-filter-check__input" data-filter-cat value="{{ $cat['id'] }}" data-label="{{ $cat['icon'] }} {{ $cat['name'] }}">

<span class="store-filter-check__box" aria-hidden="true"></span>

<span class="store-filter-check__label">{{ $cat['icon'] }} {{ $cat['name'] }}</span>

</label>

@endforeach

</div>

<div class="store-filter-sheet__foot">

<button type="button" class="store-filter-sheet__btn store-filter-sheet__btn--outline" id="produk-filter-reset">Reset</button>

<button type="button" class="store-filter-sheet__btn" id="produk-filter-apply">Terapkan</button>

</div>

</div>

</div>

</div>

