@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :id="$country->id"
        baseName="country"
        :itemName="__('admin.country')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.country')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.country_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <x-admin.text-field-multilang 
                    :label="__('admin.country_name')"
                    :value="$translations ?? ''"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="country_name"
                />
            </div>
            <div class="row mt-1">
                <div class="col-sm-3">
                    <span>{{ __('admin.iso_code_2') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $country->iso_code_2 }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3">
                    <span>{{ __('admin.iso_code_3') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $country->iso_code_3 }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3">
                    <span>{{ __('admin.postcode_required') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $country->postcode_required ? __('admin.required') : __('admin.not_required') }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3">
                    <span>{{ __('admin.status') }}:</span>
                </div>
                <div class="col-sm-9">
                    <x-admin.status-badge :status="$country->status" />
                </div>
            </div>
        </div>
    </div>
@endsection
