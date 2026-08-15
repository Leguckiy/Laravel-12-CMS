@extends('layouts.admin')

@section('page-actions')
    <x-admin.form-actions
        :isEdit="isset($order)"
        :backRoute="route('admin.order.index')"
        formId="form-order"
        :showSubmit="false"
    />
@endsection

@section('content')
    <x-admin.delete-form />
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span>{{ __('admin.form_errors') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
        </div>
    @endif

    <div 
        class="card"
        id="order-form-card"
        data-current-currency-id="{{ old('currency_id', $order->currency_id ?? $defaultCurrencyId ?? '') }}"
        data-current-currency-decimals="{{ $currentCurrency->decimal_place ?? 2 }}"
        data-current-currency-symbol-left="{{ $currentCurrency->symbol_left ?? '' }}"
        data-current-currency-symbol-right="{{ $currentCurrency->symbol_right ?? '' }}"
        data-change-currency-url="{{ route('admin.order.change_currency') }}"
        data-check-quantity-url="{{ route('admin.order.check_quantity') }}"
    >
        <div class="card-header">
            <i class="fa-solid fa-pencil"></i>
            <span>{{ isset($order) ? __('admin.edit_order') : __('admin.add_order') }}</span>
        </div>
        <div class="card-body">
            <form
                id="form-order"
                action="{{ isset($order) ? route('admin.order.update', $order) : route('admin.order.store') }}"
                method="POST"
                @if(isset($order)) data-order-id="{{ $order->id }}" @endif
            >
                @csrf
                @if(isset($order))
                    @method('PUT')
                @endif

                <div class="row mb-3 required">
                    <label for="order-customer-search" class="col-sm-2 col-form-label">
                        {{ __('admin.customer') }}
                    </label>
                    <div class="col-sm-10 position-relative">
                        <input 
                            type="text"
                            id="order-customer-search"
                            class="form-control"
                            autocomplete="off"
                            placeholder="{{ __('admin.customer') }}"
                            value="{{ isset($order) && $order->customer ? $order->customer->fullname.' ('.$order->customer->email.')' : '' }}"
                            data-search-url="{{ route('admin.customer.search') }}"
                            data-empty-label="{{ __('admin.customer_none') }}"
                            data-no-addresses-message="{{ __('admin.customer_has_no_addresses') }}"
                            required
                        >
                        <input 
                            type="hidden"
                            name="customer_id"
                            id="order-customer-id"
                            value="{{ old('customer_id', $order->customer_id ?? '') }}"
                        >
                    </div>
                </div>

                <div class="row mb-3 required">
                    <label for="order-shipping-address" class="col-sm-2 col-form-label">
                        {{ __('admin.shipping_address') }}
                    </label>
                    <div class="col-sm-10">
                        <select name="shipping_address_id"
                                id="order-shipping-address"
                                class="form-select"
                                data-placeholder="{{ __('admin.please_select') }}"
                                required>
                            <option value="" @if(!isset($order) || !$order->shippingAddress) selected @endif>
                                {{ __('admin.please_select') }}
                            </option>
                            @if(isset($order) && $order->shippingAddress)
                                <option value="{{ $order->shippingAddress->id }}" selected>
                                    {{ $order->shippingAddress->getFormattedAddress($order->shippingCountry?->getName($order->language_id ?? $order->language?->id ?? 0) ?? '') }}
                                </option>
                            @endif
                        </select>
                    </div>
                </div>

                <x-admin.select-field
                    name="language_id"
                    :label="__('admin.language')"
                    :options="$languagesOptions"
                    :value="old('language_id', $order->language_id ?? '')"
                    :required="true"
                />

                <x-admin.select-field
                    name="currency_id"
                    :label="__('admin.currency')"
                    :options="$currenciesOptions"
                    :value="old('currency_id', $order->currency_id ?? $defaultCurrencyId ?? '')"
                    :required="true"
                />

                <x-admin.input-field
                    type="text"
                    name="comment"
                    :label="__('admin.comment')"
                    :value="old('comment', $order->comment ?? '')"
                    :required="false"
                />

                <div class="border rounded p-3 mb-3" id="order-items-step">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">
                            {{ __('admin.order_details') }}
                        </h5>
                        <button type="button"
                                class="btn btn-primary"
                                id="order-add-item-btn">
                            <i class="fa-solid fa-plus"></i>
                            {{ __('admin.add_product') }}
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.product') }}</th>
                                    <th class="text-center" style="width: 120px;">
                                        {{ __('admin.quantity') }}
                                    </th>
                                    <th class="text-center" style="width: 120px;">
                                        {{ __('admin.available') }}
                                    </th>
                                    <th class="text-end" style="width: 160px;">
                                        {{ __('admin.price') }}
                                    </th>
                                    <th class="text-end" style="width: 160px;">
                                        {{ __('admin.total') }}
                                    </th>
                                    <th class="text-center" style="width: 80px;">
                                        {{ __('admin.action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="order-items-body"
                                   data-next-index="{{ isset($order) ? $order->products->count() : 0 }}">
                                @forelse(isset($order) ? $order->products : [] as $index => $orderProduct)
                                    <tr class="order-item-row">
                                        <td>
                                            <input 
                                                type="hidden"
                                                name="items[{{ $index }}][product_id]"
                                                value="{{ $orderProduct->product_id }}"
                                            >
                                            <input 
                                                type="text"
                                                name="items[{{ $index }}][name]"
                                                class="form-control order-item-name"
                                                value="{{ $orderProduct->name }}"
                                            >
                                        </td>
                                        <td class="text-center">
                                            <input 
                                                type="number"
                                                min="1"
                                                name="items[{{ $index }}][quantity]"
                                                class="form-control text-center order-item-qty"
                                                value="{{ $orderProduct->quantity }}"
                                            >
                                        </td>
                                        <td class="text-center">
                                            <span class="order-item-available">
                                                {{ optional($orderProduct->product)->quantity ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="order-item-price-display">
                                                @if(isset($order->currency))
                                                    {{ $order->currency->formatPrice($orderProduct->price) }}
                                                @else
                                                    {{ number_format($orderProduct->price, 2) }}
                                                @endif
                                            </span>
                                            <input 
                                                type="hidden"
                                                name="items[{{ $index }}][price]"
                                                class="order-item-price-input"
                                                value="{{ $orderProduct->price }}"
                                            >
                                        </td>
                                        <td class="text-end">
                                            <span class="order-item-total-display">
                                                {{ number_format($orderProduct->total, 2) }}
                                            </span>
                                            <input 
                                                type="hidden"
                                                name="items[{{ $index }}][total]"
                                                class="order-item-total-input"
                                                value="{{ $orderProduct->total }}"
                                            >
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-danger order-item-remove-btn">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="order-items-empty-row">
                                        <td colspan="6" class="text-center text-muted">
                                            {{ __('admin.no_items') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3 d-none" id="order-summary-wrapper">
                        <div class="col-md-4 ms-auto">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-end fw-bold">
                                        {{ __('admin.shipping_sub_total') }}:
                                    </td>
                                    <td class="text-end">
                                        <span id="order-summary-subtotal">
                                            0.00
                                        </span>
                                    </td>
                                </tr>
                                <tr id="order-summary-shipping-row" class="d-none" data-shipping-amount="{{ isset($order) ? $order->shipping_cost : 0 }}">
                                    <td class="text-end fw-bold">
                                        <span id="order-summary-shipping-label">
                                            {{ isset($order) ? ($order->shipping_method['name'] ?? '') : '' }}
                                        </span>:
                                    </td>
                                    <td class="text-end">
                                        <span id="order-summary-shipping-amount">
                                            0.00
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-bold">
                                        {{ __('admin.total') }}:
                                    </td>
                                    <td class="text-end fw-bold">
                                        <span id="order-summary-total">
                                            0.00
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <input 
                                type="hidden"
                                name="subtotal"
                                id="order-subtotal-input"
                                value="{{ isset($order) ? $order->subtotal : 0 }}"
                            >
                            <input 
                                type="hidden"
                                name="total"
                                id="order-total-input"
                                value="{{ isset($order) ? $order->total : 0 }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card h-100" id="order-shipping-method-card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('front/checkout.shipping_method') }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-2">
                                    {{ __('front/checkout.choose_shipping_method') }}
                                </p>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-truck" aria-hidden="true"></i>
                                    </span>
                                    <input 
                                        type="text"
                                        id="order-shipping-method-input"
                                        class="form-control"
                                        placeholder="{{ __('front/checkout.choose_shipping_method') }}"
                                        readonly
                                    >
                                    <button 
                                        type="button"
                                        class="btn btn-primary"
                                        id="order-shipping-method-choose-btn"
                                        data-shipping-methods-url="{{ route('admin.order.shipping_methods') }}"
                                        data-set-shipping-method-url="{{ route('admin.order.set_shipping_method') }}"
                                        disabled
                                    >
                                        {{ __('front/checkout.choose') }}
                                    </button>
                                </div>
                            </div>

                            <input 
                                type="hidden"
                                name="shipping_method[code]"
                                id="order-shipping-method-code"
                                value="{{ $order->shipping_method['code'] ?? '' }}"
                            >
                            <input 
                                type="hidden"
                                name="shipping_method[name]"
                                id="order-shipping-method-name"
                                value="{{ $order->shipping_method['name'] ?? '' }}"
                            >
                            <input 
                                type="hidden"
                                name="shipping_method[cost]"
                                id="order-shipping-method-cost"
                                value="{{ $order->shipping_cost ?? 0 }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100" id="order-payment-method-card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('front/checkout.payment_method') }}</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-2">
                                    {{ __('front/checkout.choose_payment_method') }}
                                </p>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-credit-card" aria-hidden="true"></i>
                                    </span>
                                    <input
                                        type="text"
                                        id="order-payment-method-input"
                                        class="form-control"
                                        placeholder="{{ __('front/checkout.choose_payment_method') }}"
                                        readonly
                                    >
                                    <button 
                                        type="button"
                                        class="btn btn-primary"
                                        id="order-payment-method-choose-btn"
                                        data-payment-methods-url="{{ route('admin.order.payment_methods') }}"
                                        data-set-payment-method-url="{{ route('admin.order.set_payment_method') }}"
                                        disabled
                                    >
                                        {{ __('front/checkout.choose') }}
                                    </button>
                                </div>
                            </div>

                            <input 
                                type="hidden"
                                name="payment_method[code]"
                                id="order-payment-method-code"
                                value="{{ $order->payment_method['code'] ?? '' }}"
                            >
                            <input
                                type="hidden"
                                name="payment_method[name]"
                                id="order-payment-method-name"
                                value="{{ $order->payment_method['name'] ?? '' }}"
                            >
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary" id="order-confirm-btn" disabled>
                        <i class="fa-solid fa-check"></i>
                        {{ __('front/checkout.confirm_order') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Add product to order --}}
    <div class="modal fade" id="order-product-modal" tabindex="-1" aria-labelledby="order-product-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5" id="order-product-modal-label">
                        {{ __('admin.add_product') }}
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('admin.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 position-relative">
                        <label for="order-product-modal-name" class="form-label">
                            {{ __('admin.product') }}
                        </label>
                        <input 
                            type="text"
                            id="order-product-modal-name"
                            class="form-control"
                            autocomplete="off"
                            data-search-url="{{ route('admin.product.search') }}"
                        >
                        <input type="hidden" id="order-product-modal-id">
                        <input type="hidden" id="order-product-modal-available">
                    </div>
                    <div class="mb-3">
                        <label for="order-product-modal-qty" class="form-label">
                            {{ __('admin.quantity') }}
                        </label>
                        <input 
                            type="number"
                            id="order-product-modal-qty"
                            class="form-control"
                            min="1"
                            value="1"
                        >
                    </div>
                    <input type="hidden" id="order-product-modal-price" value="0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('admin.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="order-product-modal-confirm">
                        {{ __('admin.add') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(isset($order) && $order->id)
        <div class="mt-4">
            <h5 class="mb-3">
                {{ __('admin.add_history') }}
            </h5>
            <form action="{{ route('admin.order.history.store', $order) }}" method="POST" id="order-history-form">
                @csrf
                <x-admin.select-field
                    name="order_status_id"
                    :label="__('admin.order_status')"
                    :options="$orderStatusesOptions"
                    :value="$order->order_status_id"
                    :required="true"
                />
                <x-admin.textarea-field
                    name="comment"
                    :label="__('admin.comment')"
                    :value="old('comment')"
                    :rows="4"
                    :required="false"
                />
                <x-admin.switch-field
                    name="notify"
                    :label="__('admin.customer_notified')"
                />
                <div class="row mb-3">
                    <div class="col-sm-10 offset-sm-2">
                        <input type="hidden" name="current_order_status_id" value="{{ $order->order_status_id }}">
                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="order-history-add-btn"
                            data-history-url="{{ route('admin.order.history.store', $order) }}"
                        >
                            <i class="fa-solid fa-plus"></i>
                            {{ __('admin.add_history') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <div class="modal fade" id="order-shipping-method-modal" tabindex="-1" aria-labelledby="order-shipping-method-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5 d-flex align-items-center gap-2" id="order-shipping-method-modal-label">
                        <i class="fas fa-truck text-muted" aria-hidden="true"></i>
                        {{ __('front/checkout.shipping_method_modal_title') }}
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="order-shipping-method-modal-loading" class="text-center py-4 d-none">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="ms-2">{{ __('front/general.loading') }}</span>
                    </div>
                    <div id="order-shipping-method-modal-content" class="d-none">
                        <p class="text-muted mb-3">{{ __('front/checkout.shipping_method_modal_instruction') }}</p>
                        <div id="order-shipping-method-modal-list"></div>
                    </div>
                    <div id="order-shipping-method-modal-error" class="alert alert-danger small mb-0 d-none" role="alert"></div>
                </div>
                <div class="modal-footer d-none" id="order-shipping-method-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('front/general.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="order-shipping-method-modal-confirm">
                        {{ __('front/checkout.choose') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="order-payment-method-modal" tabindex="-1" aria-labelledby="order-payment-method-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title h5" id="order-payment-method-modal-label">
                        {{ __('front/checkout.payment_method') }}
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="order-payment-method-modal-loading" class="text-center py-4 d-none">
                        <span class="spinner-border spinner-border-sm"></span>
                        <span class="ms-2">
                            {{ __('front/general.loading') }}
                        </span>
                    </div>
                    <div id="order-payment-method-modal-content" class="d-none">
                        <div id="order-payment-method-modal-list"></div>
                    </div>
                    <div id="order-payment-method-modal-error" class="alert alert-danger small mb-0 d-none"></div>
                </div>
                <div class="modal-footer d-none" id="order-payment-method-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('front/general.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="order-payment-method-modal-confirm">
                        {{ __('front/checkout.choose') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/admin/order.js') }}"></script>
    @endpush
@endsection
