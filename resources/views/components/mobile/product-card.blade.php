@props(['product', 'sm' => false, 'delay' => 0])
<a href="{{ route('toko.produk.show', $product['slug']) }}"
   class="product-card{{ $sm ? ' product-card--sm' : ' animate-card-in' }}"
   @unless($sm) style="animation-delay:{{ $delay * 0.07 }}s" @endunless>
<div class="product-card__media">
@if($product['badge'])<span class="product-card__badge">{{ $product['badge'] }}</span>@endif
<div class="product-card__icon">{{ $product['emoji'] }}</div>
</div>
<div class="product-card__body">
<span class="product-card__cat">{{ $product['cat'] }}</span>
<h3 class="product-card__name">{{ $product['name'] }}</h3>
<p class="product-card__sold">{{ $product['sold_label'] ?? '0 terjual' }}</p>
@unless($sm)
<div class="product-card__footer">
<span class="product-card__price">{{ $product['price'] }}</span>
<button type="button" class="product-card__buy" data-cart-add data-product-id="{{ $product['id'] }}" aria-label="Beli langsung">
<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
Beli
</button>
</div>
@else
<span class="product-card__price">{{ $product['price'] }}</span>
@endunless
</div>
</a>
