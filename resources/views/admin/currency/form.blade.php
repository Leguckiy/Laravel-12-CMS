@extends('layouts.admin')

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
            <span>{{ isset($currency) ? __('admin.edit_currency') : __('admin.add_currency') }}</span>
        </div>
        <div class="card-body">
            <form id="form-currency" action="{{ isset($currency) ? route('admin.currency.update', $currency->currency_id ?? $currency->id) : route('admin.currency.store') }}" method="POST">
                @csrf
                @if(isset($currency))
                    @method('PUT')
                @endif
                <x-admin.input-field type="text" name="title" :label="__('admin.currency_title')" :placeholder="__('admin.currency_title')" :value="$currency->title ?? ''" :required="true"/>
                <x-admin.input-field type="text" name="code" :label="__('admin.code')" :placeholder="__('admin.code')" :value="$currency->code ?? ''" :required="true"/>
                <x-admin.input-field type="text" name="symbol_left" label="Symbol Left" placeholder="Symbol Left" :value="$currency->symbol_left ?? ''"/>
                <x-admin.input-field type="text" name="symbol_right" label="Symbol Right" placeholder="Symbol Right" :value="$currency->symbol_right ?? ''"/>
                <x-admin.input-field type="number" name="decimal_place" label="Decimal Place" placeholder="Decimal Place" :value="$currency->decimal_place ?? ''"/>
                <x-admin.input-field type="text" name="value" label="Value" placeholder="Value" :value="$currency->value ?? ''"/>
                <x-admin.switch-field name="status" :label="__('admin.status')" :value="$currency->status ?? false"/>
                <x-admin.form-actions 
                    :isEdit="isset($currency)"
                    :backRoute="route('admin.currency.index')"
                />
            </form>
        </div>
    </div>
@endsection
