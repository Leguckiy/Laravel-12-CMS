@extends('layouts.front')

@push('styles')
    <link href="{{ asset('css/front/category.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container py-5">
    <div class="row align-items-start mb-5">
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
        <div class="col-12">
            @if ($products->isEmpty())
                <p class="text-muted">{{ __('front/general.no_products') }}</p>
            @else
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                    <p class="mb-0 text-muted">{{ __('front/general.products_count', ['count' => $products->total()]) }}</p>
                    <form method="get" action="{{ request()->url() }}" class="d-flex align-items-center gap-2">
                        @foreach (request()->except('sort', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label for="category-sort" class="mb-0">{{ __('front/general.sort_by') }}:</label>
                        <select name="sort" id="category-sort" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            <option value="position" {{ ($currentSort ?? '') === 'position' ? 'selected' : '' }}>{{ __('front/general.sort_position') }}</option>
                            <option value="name_asc" {{ ($currentSort ?? '') === 'name_asc' ? 'selected' : '' }}>{{ __('front/general.sort_name_asc') }}</option>
                            <option value="name_desc" {{ ($currentSort ?? '') === 'name_desc' ? 'selected' : '' }}>{{ __('front/general.sort_name_desc') }}</option>
                            <option value="price_asc" {{ ($currentSort ?? '') === 'price_asc' ? 'selected' : '' }}>{{ __('front/general.sort_price_asc') }}</option>
                            <option value="price_desc" {{ ($currentSort ?? '') === 'price_desc' ? 'selected' : '' }}>{{ __('front/general.sort_price_desc') }}</option>
                            <option value="reference_asc" {{ ($currentSort ?? '') === 'reference_asc' ? 'selected' : '' }}>{{ __('front/general.sort_reference_asc') }}</option>
                            <option value="reference_desc" {{ ($currentSort ?? '') === 'reference_desc' ? 'selected' : '' }}>{{ __('front/general.sort_reference_desc') }}</option>
                        </select>
                    </form>
                </div>
                <div class="row g-4 mb-4">
                    @foreach ($products as $product)
                        @php
                            $translation = $product->translations->first();
                            $productName = $translation?->name ?? $product->reference ?? '#'.$product->id;
                        @endphp
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card h-100 category-product-card">
                                @if ($product->image_url)
                                    <div class="category-product-card__image card-img-top">
                                        <img src="{{ $product->image_url }}" alt="{{ $productName }}">
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $productName }}</h5>
                                    <p class="card-text mb-0">
                                        <strong>{{ number_format($product->price, 2) }}</strong>
                                        {{ $frontCurrency?->code ?? '' }}
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
@endsection
