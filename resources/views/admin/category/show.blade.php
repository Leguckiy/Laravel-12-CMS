@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.category_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <x-admin.text-field-multilang 
                    :label="__('admin.category_name')"
                    :value="$translations['name'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    fieldName="category_name"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang 
                    :label="__('admin.friendly_url')"
                    :value="$translations['slug'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    fieldName="slug"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang 
                    :label="__('admin.description')"
                    :value="$translations['description'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    fieldName="description"
                    :allowHtml="true"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang 
                    :label="__('admin.meta_tag_title')"
                    :value="$translations['meta_title'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    fieldName="meta_title"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang 
                    :label="__('admin.meta_tag_description')"
                    :value="$translations['meta_description'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    fieldName="meta_description"
                />
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.image') }}:</span>
                </div>
                <div class="col-sm-9">
                    @if ($category->image_url)
                        <img src="{{ $category->image_url }}" alt="{{ $category->name ?? '' }}" class="img-fluid rounded border">
                    @else
                        <span class="text-muted">{{ __('admin.image_placeholder') }}</span>
                    @endif
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.sort_order') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $category->sort_order }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.status') }}:</span>
                </div>
                <div class="col-sm-9">
                    <x-admin.status-badge :status="$category->status" />
                </div>
            </div>
            <x-admin.detail-actions 
                :id="$category->id"
                baseName="category"
                :itemName="__('admin.category')"
                :confirmText="__('admin.delete_confirm', ['item' => __('admin.category')])"
            />
        </div>
    </div>
@endsection
