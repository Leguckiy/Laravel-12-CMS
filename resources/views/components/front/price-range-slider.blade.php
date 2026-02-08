@props([
    'label' => null,
    'priceRangeMin' => 0,
    'priceRangeMax' => 0,
    'valueMin' => null,
    'valueMax' => null,
    'formattedMin' => '',
    'formattedMax' => '',
    'nameMin' => 'price_min',
    'nameMax' => 'price_max',
    'hasPriceFilter' => false,
])

@php
    $min = (float) $priceRangeMin;
    $max = (float) $priceRangeMax;
    $valMin = $valueMin !== null && $valueMin !== '' ? (float) $valueMin : $min;
    $valMax = $valueMax !== null && $valueMax !== '' ? (float) $valueMax : $max;
@endphp

<div class="front-price-range-slider category-filters-price">
    @if ($label)
        <p class="category-filters__section-label">{{ $label }}</p>
    @endif
    <p class="front-price-range-slider__display small text-muted mb-2 category-price-formatted">
        {{ $formattedMin }} – {{ $formattedMax }}
    </p>
    <div class="front-price-range-slider__inputs d-flex align-items-center gap-2 mb-2 visually-hidden">
        <input type="number"
               @if($hasPriceFilter) name="{{ $nameMin }}" @endif
               class="form-control form-control-sm category-price-input category-price-input-min"
               data-name="{{ $nameMin }}"
               min="{{ $min }}"
               max="{{ $max }}"
               step="0.01"
               value="{{ $valMin }}"
               aria-label="{{ $label ? $label . ' min' : 'Price min' }}">
        <span class="text-muted">–</span>
        <input type="number"
               @if($hasPriceFilter) name="{{ $nameMax }}" @endif
               class="form-control form-control-sm category-price-input category-price-input-max"
               data-name="{{ $nameMax }}"
               min="{{ $min }}"
               max="{{ $max }}"
               step="0.01"
               value="{{ $valMax }}"
               aria-label="{{ $label ? $label . ' max' : 'Price max' }}">
    </div>
    <div class="front-price-range-slider__track-wrap category-price-slider mb-2">
        <div class="front-price-range-slider__track" aria-hidden="true"></div>
        <input type="range"
               class="form-range category-price-range category-price-range-min front-price-range-slider__range"
               min="{{ $min }}"
               max="{{ $max }}"
               step="0.01"
               value="{{ $valMin }}"
               data-target="price_min"
               aria-label="{{ $label ? $label . ' min' : 'Price min' }}">
        <input type="range"
               class="form-range category-price-range category-price-range-max front-price-range-slider__range"
               min="{{ $min }}"
               max="{{ $max }}"
               step="0.01"
               value="{{ $valMax }}"
               data-target="price_max"
               aria-label="{{ $label ? $label . ' max' : 'Price max' }}">
    </div>
</div>
