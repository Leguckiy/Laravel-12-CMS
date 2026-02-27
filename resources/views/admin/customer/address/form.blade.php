@extends('layouts.admin')

@section('page-actions')
    <x-admin.form-actions
        :isEdit="(bool) $address"
        :backRoute="route('admin.customer.edit', $customer) . '#tab-addresses'"
        formId="form-customer-address"
    />
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span>{{ __('admin.form_errors') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-pencil"></i>
            <span>{{ $address ? __('admin.edit_address') : __('admin.add_address') }}</span>
        </div>
        <div class="card-body">
            <form id="form-customer-address" action="{{ $address ? route('admin.customer_address.update', [$customer, $address]) : route('admin.customer_address.store', $customer) }}" method="POST">
                @csrf
                @if ($address)
                    @method('PUT')
                @endif
                <x-admin.input-field type="text" name="firstname" :label="__('admin.first_name')" :placeholder="__('admin.first_name')" :value="old('firstname', $address?->firstname ?? '')" :required="true"/>
                <x-admin.input-field type="text" name="lastname" :label="__('admin.last_name')" :placeholder="__('admin.last_name')" :value="old('lastname', $address?->lastname ?? '')" :required="true"/>
                <x-admin.input-field type="text" name="company" :label="__('admin.company')" :placeholder="__('admin.company')" :value="old('company', $address?->company ?? '')" :required="false"/>
                <x-admin.input-field type="text" name="address_1" :label="__('admin.address_1')" :placeholder="__('admin.address_1')" :value="old('address_1', $address?->address_1 ?? '')" :required="true"/>
                <x-admin.input-field type="text" name="address_2" :label="__('admin.address_2')" :placeholder="__('admin.address_2')" :value="old('address_2', $address?->address_2 ?? '')" :required="false"/>
                <x-admin.input-field type="text" name="city" :label="__('admin.city')" :placeholder="__('admin.city')" :value="old('city', $address?->city ?? '')" :required="true"/>
                <div class="row mb-3">
                    <label for="input-country_id" class="col-sm-2 col-form-label required">{{ __('admin.country') }}</label>
                    <div class="col-sm-10">
                        <select name="country_id" id="input-country_id" class="form-select @error('country_id') is-invalid @enderror">
                            @foreach ($countryOptions as $option)
                                <option
                                    value="{{ $option['id'] }}"
                                    data-postcode-required="{{ isset($option['postcode_required']) && $option['postcode_required'] ? '1' : '0' }}"
                                    {{ old('country_id', $address?->country_id ?? '') == $option['id'] ? 'selected' : '' }}
                                >
                                    {{ $option['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('country_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3 js-admin-address-postcode-row">
                    <label for="input-postcode" class="col-sm-2 col-form-label">{{ __('admin.postcode') }}</label>
                    <div class="col-sm-10">
                        <input type="text" name="postcode" id="input-postcode" value="{{ old('postcode', $address?->postcode ?? '') }}" placeholder="{{ __('admin.postcode') }}" class="form-control @error('postcode') is-invalid @enderror">
                        @error('postcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <x-admin.switch-field name="default" :label="__('admin.address_default')" :value="old('default', $address?->default ?? false)"/>
            </form>
        </div>
    </div>
@endsection
