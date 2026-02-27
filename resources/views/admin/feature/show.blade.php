@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :id="$feature->id"
        baseName="feature"
        :itemName="__('admin.feature')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.feature')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.feature_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <x-admin.text-field-multilang 
                    :label="__('admin.feature_name')"
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
                    <span>{{ $feature->sort_order }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
