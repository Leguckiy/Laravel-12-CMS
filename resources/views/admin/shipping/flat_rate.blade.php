@extends('layouts.admin')

@section('page-actions')
    <x-admin.form-actions
        :isEdit="true"
        :backRoute="route('admin.shipping.index')"
        formId="form-shipping"
    />
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span>{{ $errors->first() }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-pencil"></i>
            <span>{{ __('admin.edit') }}: {{ $method['name'] }}</span>
        </div>
        <div class="card-body">
            <form id="form-shipping" action="{{ route('admin.shipping.update', $method['code']) }}" method="POST">
                @csrf
                @method('PUT')
                <x-admin.input-field
                    type="number"
                    name="cost"
                    :label="__('admin.shipping_cost')"
                    :placeholder="__('admin.shipping_cost')"
                    :value="old('cost', data_get($shippingMethod->config, 'cost', ''))"
                    :required="true"
                />
                <x-admin.input-field
                    type="number"
                    name="sort_order"
                    :label="__('admin.sort_order')"
                    :value="old('sort_order', $shippingMethod->sort_order ?? 0)"
                />
                <x-admin.checkbox-list
                    :items="$countryOptions"
                    :selectedItems="(array) (old('countries', $selectedCountryIds ?? []) ?? [])"
                    name="countries[]"
                    :label="__('admin.shipping_countries')"
                    :emptyMessage="__('admin.no_items')"
                    itemPrefix="country"
                    :height="220"
                />
                <x-admin.switch-field
                    name="status"
                    :label="__('admin.status')"
                    :value="old('status', $shippingMethod->status ?? true)"
                />
            </form>
        </div>
    </div>
@endsection
