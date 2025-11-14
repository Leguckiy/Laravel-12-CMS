@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.order_status_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <x-admin.text-field-multilang 
                    :label="__('admin.order_status_name')"
                    :value="$translations ?? ''"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="order_status_name"
                />
            </div>
            <x-admin.detail-actions 
                :id="$orderStatus->id"
                baseName="order_status"
                :itemName="__('admin.order_status')"
                :confirmText="__('admin.delete_confirm', ['item' => __('admin.order_status')])"
            />
        </div>
    </div>
@endsection
