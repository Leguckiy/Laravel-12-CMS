@extends('layouts.front')

@push('styles')
    <link href="{{ asset('css/front/cart.css') }}" rel="stylesheet">
@endpush

@push('scripts-after')
    <script src="{{ asset('js/front/cart.js') }}"></script>
@endpush

@section('content')
<h1 class="mb-4">{{ __('front/general.cart_title') }}</h1>

    @if (empty($cartRows))
        <p class="text-muted" id="cart-empty-message">{{ __('front/general.cart_empty') }}</p>
    @else
        <div id="cart-content">
            <div class="table-responsive">
                <table class="table table-bordered cart-table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('front/general.cart_image') }}</th>
                            <th scope="col">{{ __('front/general.cart_name') }}</th>
                            <th scope="col">{{ __('front/general.cart_quantity') }}</th>
                            <th scope="col">{{ __('front/general.cart_price_per_unit') }}</th>
                            <th scope="col">{{ __('front/general.cart_total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartRows as $row)
                            @php
                                $item = $row['item'];
                                $product = $row['product'];
                                $slug = $row['slug'];
                                $name = $row['name'];
                                $rowTotal = $row['rowTotal'];
                            @endphp
                            <tr class="cart-row">
                                <td class="cart-table__image">
                                    @if ($product->image_url)
                                        <a href="{{ $slug ? route('front.product.show', ['lang' => request()->route('lang'), 'slug' => $slug]) : '#' }}">
                                            <img src="{{ $product->image_url }}" alt="{{ $name }}" class="img-fluid">
                                        </a>
                                    @else
                                        <span class="text-muted small">{{ __('front/general.no_image') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($slug)
                                        <a href="{{ route('front.product.show', ['lang' => request()->route('lang'), 'slug' => $slug]) }}">{{ $name }}</a>
                                    @else
                                        {{ $name }}
                                    @endif
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-2 flex-wrap">
                                        <form action="{{ route('front.cart.update', ['lang' => request()->route('lang')]) }}" method="post" class="d-inline-flex align-items-center gap-2 js-cart-update-form">
                                            @method('PUT')
                                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm cart-quantity-input">
                                            <button type="submit" class="btn btn-primary btn-sm" title="{{ __('front/general.cart_update') }}">
                                                <i class="fa-solid fa-arrows-rotate"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('front.cart.destroy', ['lang' => request()->route('lang')]) }}" method="post" class="d-inline js-cart-remove-form">
                                            @method('DELETE')
                                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                            <button type="submit" class="btn btn-danger btn-sm" title="{{ __('front/general.cart_remove') }}">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td class="cart-unit-price">{{ $currency->formatPriceFromBase($item->price) }}</td>
                                <td class="cart-row-total">{{ $currency->formatPriceFromBase($rowTotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="cart-totals mt-4">
                <div class="row justify-content-end">
                    <div class="col-md-5 col-lg-5">
                        <table class="table table-sm table-bordered mb-0">
                            <tr>
                                <td>{{ __('front/general.cart_subtotal') }}</td>
                                <td class="text-end cart-subtotal">{{ $currency->formatPriceFromBase((string) $subtotal) }}</td>
                            </tr>
                            @if(!empty($shippingMethod['name']))
                            <tr>
                                <td>{{ $shippingMethod['name'] }}</td>
                                <td class="text-end">{{ $currency->formatPriceFromBase((string) ($shippingMethod['cost'] ?? 0)) }}</td>
                            </tr>
                            @endif
                            <tr class="fw-bold">
                                <td>{{ __('front/general.cart_total_label') }}</td>
                                <td class="text-end cart-total">{{ $currency->formatPriceFromBase((string) $orderTotal) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-6">
                    <a href="{{ route('front.home', ['lang' => request()->route('lang')]) }}" class="btn btn-secondary">{{ __('front/general.cart_continue_shopping') }}</a>
                </div>
                <div class="col-6 text-end">
                    <a href="{{ route('front.checkout.index', ['lang' => request()->route('lang')]) }}" class="btn btn-primary">{{ __('front/general.cart_checkout') }}</a>
                </div>
            </div>
        </div>
        <p class="text-muted d-none" id="cart-empty-placeholder">{{ __('front/general.cart_empty') }}</p>
    @endif
@endsection
