@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :editRoute="route('admin.feature_value.edit', ['feature' => $feature->id, 'feature_value' => $featureValue->id])"
        :destroyRoute="route('admin.feature_value.destroy', ['feature' => $feature->id, 'feature_value' => $featureValue->id])"
        :backRoute="route('admin.feature_value.index', $feature)"
        editPermission="admin.feature_value.edit"
        destroyPermission="admin.feature_value.destroy"
        :itemName="__('admin.feature_value')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.feature_value')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.feature_value_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <x-admin.text-field-multilang 
                    :label="__('admin.feature_value_name')"
                    :value="$translations ?? ''"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="feature_name"
                />
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.sort_order') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $featureValue->sort_order }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
