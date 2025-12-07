@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.product.create"
            :text="__('admin.add_product')" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.product_list') }}</span>
        </div>
        <div id="product" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.id') }}</th>
                            <th>{{ __('admin.image') }}</th>
                            <th>{{ __('admin.product_name') }}</th>
                            <th>{{ __('admin.product_reference') }}</th>
                            <th>{{ __('admin.price') }}</th>
                            <th>{{ __('admin.quantity') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td class="text-center image-wrapper">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="img-thumbnail">
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->reference }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>{{ $product->status }}</td>
                                <td>
                                    <x-admin.status-badge :status="$product->status"/>
                                </td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$product->id"
                                        baseName="product"
                                        :itemName="__('admin.product')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.product')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
