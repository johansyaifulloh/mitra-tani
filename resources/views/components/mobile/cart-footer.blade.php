@props(['total' => 'Rp 197.500', 'items' => 2, 'checkoutRoute' => null, 'showSelectAll' => true, 'label' => 'Total', 'checkoutForm' => null])
<div class="cart-footer">
<div class="cart-footer__inner" @unless($showSelectAll) style="padding-top:12px" @endunless>
@if($showSelectAll)
<label class="cart-footer__select"><input type="checkbox" id="select-all" checked> Pilih Semua</label>
@endif
<div class="cart-footer__actions">
<div class="cart-footer__total">
<span class="cart-footer__total-label">{{ $label }}@if($showSelectAll) (<span id="cart-item-count">{{ $items }}</span> item)@endif</span>
<span class="cart-footer__total-price" id="cart-total">{{ $total }}</span>
</div>
@if($checkoutForm)
<button type="submit" form="{{ $checkoutForm }}" class="cart-footer__btn" id="checkout-btn">{{ $slot }}</button>
@else
<a href="{{ $checkoutRoute ?? route('toko.checkout.index') }}" class="cart-footer__btn" id="checkout-btn">{{ $slot }}</a>
@endif
</div>
</div>
</div>
