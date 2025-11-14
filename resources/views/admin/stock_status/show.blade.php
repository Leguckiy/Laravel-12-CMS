@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.stock_status_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <x-admin.text-field-multilang 
                    :label="__('admin.stock_status_name')"
                    :value="$translations ?? ''"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="stock_status_name"
                />
            </div>
            <x-admin.detail-actions 
                :id="$stockStatus->id"
                baseName="stock_status"
                :itemName="__('admin.stock_status')"
                :confirmText="__('admin.delete_confirm', ['item' => __('admin.stock_status')])"
            />
        </div>
    </div>
@endsection
