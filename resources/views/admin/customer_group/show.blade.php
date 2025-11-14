@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.customer_group_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <x-admin.text-field-multilang 
                    :label="__('admin.customer_group_name')"
                    :value="$translations['name'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="customer_group_name"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang 
                    :label="__('admin.description')"
                    :value="$translations['description'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="description"
                    :allowHtml="true"
                />
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.customer_group_approval') }}:</span>
                </div>
                <div class="col-sm-9">
                    @if ($customerGroup->approval)
                        <span class="badge text-bg-success">{{ __('admin.yes') }}</span>
                    @else
                        <span class="badge text-bg-danger">{{ __('admin.no') }}</span>
                    @endif
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.sort_order') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $customerGroup->sort_order }}</span>
                </div>
            </div>
            <x-admin.detail-actions 
                :id="$customerGroup->id"
                baseName="customer_group"
                :itemName="__('admin.customer_group')"
                :confirmText="__('admin.delete_confirm', ['item' => __('admin.customer_group')])"
            />
        </div>
    </div>
@endsection
