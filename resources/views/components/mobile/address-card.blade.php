@props(['address', 'selected' => false, 'selectable' => true])
<label class="address-card{{ $selected ? ' address-card--active' : '' }}{{ !$selectable ? ' address-card--static' : '' }}">
@if($selectable)
<input type="radio" name="address_id" value="{{ $address['id'] }}" class="address-card__radio" {{ $selected ? 'checked' : '' }}>
@endif
<div class="address-card__head">
<div class="address-card__labels">
<span class="address-card__tag">{{ $address['label'] }}</span>
@if($address['default'])
<span class="address-card__default">Utama</span>
@endif
</div>
@if($selectable)
<span class="address-card__check">✓</span>
@endif
</div>
<p class="address-card__name">{{ $address['name'] }}</p>
<p class="address-card__phone">{{ $address['phone'] }}</p>
<p class="address-card__street">{{ $address['street'] }}</p>
<p class="address-card__district">{{ $address['district'] }}</p>
</label>
