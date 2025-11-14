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
            <span>{{ isset($featureValue) ? __('admin.edit_feature_value') : __('admin.add_feature_value') }}</span>
        </div>
        <div class="card-body">
            <form
                id="form-feature-value"
                action="{{ isset($featureValue)
                    ? route('admin.feature_value.update', ['feature' => $feature->id, 'feature_value' => $featureValue->id])
                    : route('admin.feature_value.store', ['feature' => $feature->id]) }}"
                method="POST"
            >
                @csrf
                @if(isset($featureValue))
                    @method('PUT')
                @endif

                <x-admin.input-field-multilang
                    type="text" 
                    name="value"
                    :label="__('admin.feature_value_name')"
                    :placeholder="__('admin.feature_value_name')"
                    :value="$translations ?? ''" 
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    :required="true"
                />

                <x-admin.input-field
                    type="number"
                    name="sort_order"
                    :label="__('admin.sort_order')"
                    :value="old('sort_order', $featureValue->sort_order ?? 0)"
                />

                <x-admin.form-actions 
                    :isEdit="isset($featureValue)"
                    :backRoute="route('admin.feature_value.index', ['feature' => $feature->id])"
                />
            </form>
        </div>
    </div>
@endsection
