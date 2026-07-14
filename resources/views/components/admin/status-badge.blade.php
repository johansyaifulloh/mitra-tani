@props(['status', 'type' => 'order'])

@php
use App\Support\SampleData;
$class = $type === 'payment'
    ? SampleData::paymentStatusClass($status)
    : SampleData::statusClass($status);
$label = $type === 'payment'
    ? SampleData::paymentStatusLabel($status)
    : SampleData::statusLabel($status);
@endphp

<span {{ $attributes->merge(['class' => "panel-badge $class"]) }}>{{ $label }}</span>
