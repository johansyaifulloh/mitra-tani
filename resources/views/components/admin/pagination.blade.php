@props(['paginator'])

@if ($paginator->total() > 0)
<div class="panel-pagination">
<div class="panel-pagination__info">
    Menampilkan <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
    dari <strong>{{ $paginator->total() }}</strong> data
</div>
@if ($paginator->hasPages())
<nav class="panel-pagination__nav" aria-label="Pagination">
    @if ($paginator->onFirstPage())
    <span class="panel-pagination__btn panel-pagination__btn--disabled">← Prev</span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}" class="panel-pagination__btn">← Prev</a>
    @endif

    @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
    @if ($page == $paginator->currentPage())
    <span class="panel-pagination__page active">{{ $page }}</span>
    @else
    <a href="{{ $url }}" class="panel-pagination__page">{{ $page }}</a>
    @endif
    @endforeach

    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="panel-pagination__btn">Next →</a>
    @else
    <span class="panel-pagination__btn panel-pagination__btn--disabled">Next →</span>
    @endif
</nav>
@endif
</div>
@endif
