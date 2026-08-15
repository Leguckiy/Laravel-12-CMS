@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :id="$order->id"
        baseName="order"
        :itemName="__('admin.order')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.order')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card mb-3">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.order_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="fw-bold">{{ __('admin.order_id') }}:</td>
                            <td>#{{ $order->id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.customer') }}:</td>
                            <td>
                                @if ($order->customer)
                                    <a href="{{ route('admin.customer.show', $order->customer->id) }}">
                                        {{ $order->customer->fullname }}
                                    </a>
                                @else
                                    {{ $order->firstname }} {{ $order->lastname }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.date_added') }}:</td>
                            <td>{{ $order->created_at?->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.language') }}:</td>
                            <td>{{ $order->language?->name ?? $order->language?->code ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.currency') }}:</td>
                            <td>{{ $order->currency?->title ?? $order->currency?->code ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.shipping_address') }}:</td>
                            <td>
                                {{ $order->shipping_firstname }} {{ $order->shipping_lastname }}<br>
                                {{ $order->shipping_address_1 }}@if($order->shipping_address_2), {{ $order->shipping_address_2 }}@endif<br>
                                {{ $order->shipping_city }}@if($order->shipping_postcode), {{ $order->shipping_postcode }}@endif<br>
                                {{ $order->shippingCountry?->translations->first()?->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.shipping_method') }}:</td>
                            <td>{{ $order->shipping_method['name'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.payment_method') }}:</td>
                            <td>{{ $order->payment_method['name'] ?? '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>{{ __('admin.products') }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.product') }}</th>
                            <th class="text-center">{{ __('admin.quantity') }}</th>
                            <th class="text-end">{{ __('admin.price') }}</th>
                            <th class="text-end">{{ __('admin.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order->products as $orderProduct)
                            <tr>
                                <td>
                                    @if ($orderProduct->product)
                                        <a href="{{ route('admin.product.show', $orderProduct->product->id) }}">
                                            {{ $orderProduct->name }}
                                        </a>
                                    @else
                                        {{ $orderProduct->name }}
                                    @endif
                                </td>
                                <td class="text-center">{{ $orderProduct->quantity }}</td>
                                <td class="text-end">
                                    @if ($order->currency)
                                        {{ $order->currency->formatPrice($orderProduct->price) }}
                                    @else
                                        {{ number_format($orderProduct->price, 2) }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($order->currency)
                                        {{ $order->currency->formatPrice($orderProduct->total) }}
                                    @else
                                        {{ number_format($orderProduct->total, 2) }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    {{ __('admin.no_items') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-4 ms-auto">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-end fw-bold">{{ __('admin.shipping_sub_total') }}:</td>
                            <td class="text-end">
                                @if ($order->currency)
                                    {{ $order->currency->formatPrice($order->subtotal) }}
                                @else
                                    {{ number_format($order->subtotal, 2) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end fw-bold">{{ $order->shipping_method['name'] ?? '' }}:</td>
                            <td class="text-end">
                                @if ($order->currency)
                                    {{ $order->currency->formatPrice($order->shipping_cost) }}
                                @else
                                    {{ number_format($order->shipping_cost, 2) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end fw-bold">{{ __('admin.total') }}:</td>
                            <td class="text-end">
                                @if ($order->currency)
                                    {{ $order->currency->formatPrice($order->total) }}
                                @else
                                    {{ number_format($order->total, 2) }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>{{ __('admin.history') }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.date_added') }}</th>
                            <th>{{ __('admin.comment') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.customer_notified') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order->histories as $history)
                            <tr>
                                <td>{{ $history->created_at?->format('d.m.Y') }}</td>
                                <td>{!! nl2br(e($history->comment)) !!}</td>
                                <td>{{ $history->orderStatus?->translations->first()?->name ?? '' }}</td>
                                <td>{{ $history->notify ? __('admin.yes') : __('admin.no') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    {{ __('admin.no_items') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

