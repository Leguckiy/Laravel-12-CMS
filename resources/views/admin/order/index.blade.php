@extends('layouts.admin')

@section('page-actions')
    <div class="d-flex gap-2">
        <x-admin.action-button-add
            permission="admin.order.create"
            :text="__('admin.add_order')"
        />
    </div>
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.order_list') }}</span>
        </div>
        <div id="order" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.order_id') }}</th>
                            <th>{{ __('admin.customer') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.total') }}</th>
                            <th>{{ __('admin.date_added') }}</th>
                            <th>{{ __('admin.date_modified') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->customer?->fullname ?? $order->firstname . ' ' . $order->lastname }}</td>
                                <td>{{ $order->status_name }}</td>
                                <td>
                                    @if ($order->currency)
                                        {{ $order->currency->formatPrice($order->total) }}
                                    @else
                                        {{ number_format($order->total, 2) }}
                                    @endif
                                </td>
                                <td>{{ $order->created_at?->format('d.m.Y') }}</td>
                                <td>{{ $order->updated_at?->format('d.m.Y') }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row
                                        :id="$order->id"
                                        baseName="order"
                                        :itemName="__('admin.order')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.order')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$orders" />
        </div>
    </div>
@endsection

