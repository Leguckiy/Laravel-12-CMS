@extends('layouts.front')

@push('styles')
    <link href="{{ asset('css/front/category.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container py-5">
    <div class="row align-items-start mb-3 category-header-row">
        <div class="col-lg-7 col-xl-8">
            <h1 class="mb-4">{{ $title }}</h1>
            @if ($description)
                <div class="category-description">
                    {!! $description !!}
                </div>
            @endif
        </div>
        @if ($category->image_url)
            <div class="col-lg-5 col-xl-4 mt-4 mt-lg-0 text-lg-end">
                <div class="category-header-image">
                    <img src="{{ $category->image_url }}" alt="{{ $title }}">
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <div id="left-column" class="col-12 col-md-4 col-lg-3 mb-4 mb-md-0">
            <div class="category-filters card">
                <div class="card-header d-flex align-items-center justify-content-between gap-2">
                    <h2 class="h6 mb-0">{{ __('front/general.filter_title') }}</h2>
                    @if (!empty($activeFilters))
                        <a href="{{ $clearFiltersUrl }}" class="btn btn-sm btn-outline-secondary" aria-label="{{ __('front/general.filter_clear_all') }}">&times; {{ __('front/general.filter_clear_all') }}</a>
                    @endif
                </div>
                <div class="card-body">
                    <form method="get" action="{{ request()->url() }}" id="category-filters-form" data-sort-in-url="{{ $sortInUrl ? '1' : '0' }}">
                        <input type="hidden" name="q" value="{{ $filterParams['q'] ?? '' }}" id="filter-q">
                        <div class="mb-3">
                            <p class="category-filters__section-label">{{ __('front/general.filter_availability') }}</p>
                            <div class="form-check">
                                <input type="checkbox" value="1" id="filter-in-stock" class="form-check-input category-filter-in-stock"
                                    {{ $filterInStock ? 'checked' : '' }}>
                                <label for="filter-in-stock" class="form-check-label">{{ __('front/general.filter_in_stock_count', ['count' => $inStockCount]) }}</label>
                            </div>
                        </div>
                        @if ($priceRangeMax > 0 || $priceRangeMin > 0)
                            <x-front.price-range-slider
                                :label="__('front/general.filter_price')"
                                :price-range-min="$priceRangeMin"
                                :price-range-max="$priceRangeMax"
                                :value-min="$filterPriceMin"
                                :value-max="$filterPriceMax"
                                :formatted-min="$currency->formatPrice($filterPriceMin)"
                                :formatted-max="$currency->formatPrice($filterPriceMax)"
                                :has-price-filter="$hasPriceFilter"
                            />
                        @endif
                        @foreach ($featuresForFilter as $featureBlock)
                            <div class="mb-3">
                                <p class="category-filters__section-label">{{ $featureBlock['feature']['name'] }}</p>
                                <div class="category-filters__feature-values">
                                    @foreach ($featureBlock['values'] as $fv)
                                        @if ($fv['count'] > 0)
                                        <div class="form-check">
                                            <input type="checkbox" name="feature_value[]" value="{{ $fv['id'] }}" id="filter-fv-{{ $fv['id'] }}" class="form-check-input category-filter-feature-value"
                                                data-feature-id="{{ $featureBlock['feature']['id'] }}" data-value-id="{{ $fv['id'] }}"
                                                {{ in_array($fv['id'], $filterFeatureValueIds, true) ? 'checked' : '' }}>
                                            <label for="filter-fv-{{ $fv['id'] }}" class="form-check-label">{{ $fv['name'] }} ({{ $fv['count'] }})</label>
                                        </div>
                                        @endif
                        @endforeach
                        </div>
                            </div>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
        <div id="content-wrapper" class="js-content-wrapper col-12 col-md-8 col-lg-9">
            @if ($products->isEmpty())
                <p class="text-muted">{{ __('front/general.no_products') }}</p>
            @else
                @if (!empty($activeFilters))
                    <div class="category-active-filters d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="text-muted me-2">{{ __('front/general.active_filters') }}:</span>
                        @foreach ($activeFilters as $af)
                            <a href="{{ $af['url'] }}" class="btn btn-sm btn-outline-secondary me-1 mb-1" aria-label="{{ __('front/general.active_filters') }}">{{ $af['label'] }} <span aria-hidden="true">&times;</span></a>
                        @endforeach
                    </div>
                @endif
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <p class="mb-0 text-muted">{{ __('front/general.products_count', ['count' => $products->total()]) }}</p>
                    <form method="get" action="{{ request()->url() }}" id="category-sort-form" class="d-flex align-items-center gap-2">
                        <label for="category-sort" class="mb-0">{{ __('front/general.sort_by') }}:</label>
                        <select name="sort" id="category-sort" class="form-select form-select-sm" style="width: auto;">
                            @foreach ($sortOptions as $opt)
                                <option value="{{ $opt['value'] }}" {{ $currentSort === $opt['value'] ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="row g-4 mb-4">
                    @foreach ($products as $product)
                        <div class="col-6 col-md-4">
                            <div class="card h-100 category-product-card">
                                @if ($product->image_url)
                                    <div class="category-product-card__image card-img-top">
                                        <a class="w-100 h-100" href="{{ route('front.product.show', ['lang' => $languageCode, 'slug' => $product->translations->first()->slug]) }}">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->translations->first()->name}}">
                                        </a>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('front.product.show', ['lang' => $languageCode, 'slug' => $product->translations->first()->slug]) }}" class="text-decoration-none">
                                            {{ $product->translations->first()->name}}
                                        </a>
                                    </h5>
                                    <p class="card-text mb-0">
                                        <strong>{{ $currency->formatPriceFromBase($product->price) }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/front/category.js') }}" defer></script>
@endpush
@endsection
