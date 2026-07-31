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
<span class="text-[11px] font-semibold text-emerald-600">Lihat →</span>
</div>
@else
<span class="product-card__price">{{ $product['price'] }}</span>
@endunless
</div>
</a>
