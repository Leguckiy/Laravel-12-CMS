@extends('layouts.front')

@push('styles')
    <link href="{{ asset('css/front/product.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="product-main-image card">
                @if ($product->image_url)
                    <div class="product-main-image__inner">
                        <img src="{{ $product->image_url }}" alt="{{ $translation->name ?? $product->reference ?? '#'.$product->id }}">
                    </div>
                @else
                    <div class="product-main-image__placeholder d-flex align-items-center justify-content-center">
                        <span class="text-muted">{{ __('front/general.no_image') }}</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-7">
            <h1 class="mb-3">{{ $translation->name }}</h1>
            <p class="product-reference text-muted mb-2">
                @if ($product->reference)
                    {{ __('front/general.reference') }}: {{ $product->reference }}
                @endif
            </p>
            @if ($product->stockStatus)
                <p class="text-muted mb-2">
                    {{ __('front/general.stock_status') }}: {{ $product->stockStatus->translations->first()?->name ?? '' }}
                </p>
            @endif
            <div class="product-price mb-4">
                <span class="product-price__current h4 mb-0 d-inline-block">
                    {{ $currency->formatPriceFromBase($product->price) }}
                </span>
            </div>

            <div class="product-actions d-flex flex-wrap align-items-center gap-3 mb-4">
                <label for="product-quantity" class="mb-0">{{ __('front/general.quantity') }}</label>
                <input type="number" id="product-quantity" class="form-control form-control-sm" value="1" min="1" style="width: 5rem;">
                <button type="button" class="btn btn-primary">
                    <i class="fa-solid fa-cart-shopping me-1"></i>{{ __('front/general.add_to_cart') }}
                </button>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs product-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description-pane" type="button" role="tab" aria-controls="description-pane" aria-selected="true">{{ __('front/general.tab_description') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="characteristics-tab" data-bs-toggle="tab" data-bs-target="#characteristics-pane" type="button" role="tab" aria-controls="characteristics-pane" aria-selected="false">{{ __('front/general.tab_characteristics') }}</button>
                </li>
            </ul>
            <div class="tab-content product-tabs-content" id="productTabsContent">
                <div class="tab-pane fade show active" id="description-pane" role="tabpanel" aria-labelledby="description-tab">
                    <div class="product-description p-4 bg-light">
                        @if ($description)
                            {!! $description !!}
                        @else
                            <p class="text-muted mb-0">{{ __('front/general.no_description') }}</p>
                        @endif
                    </div>
                </div>
                <div class="tab-pane fade" id="characteristics-pane" role="tabpanel" aria-labelledby="characteristics-tab">
                    <div class="p-4 bg-light">
                        @if (!empty($characteristics))
                            <table class="table product-characteristics-table mb-0">
                                <tbody>
                                    @foreach ($characteristics as $char)
                                        <tr>
                                            <td class="product-char-name">{{ $char['name'] }}</td>
                                            <td class="product-char-values text-muted">
                                                @foreach ($char['values'] as $val)
                                                    <span class="d-block">{{ $val }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted mb-0">{{ __('front/general.no_characteristics') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

