@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :id="$page->id"
        baseName="page"
        :itemName="__('admin.pages')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.pages')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.page_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <x-admin.text-field-multilang
                    :label="__('admin.page_title')"
                    :value="$translations['title'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="page_title"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang
                    :label="__('admin.friendly_url')"
                    :value="$translations['slug'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="slug"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang
                    :label="__('admin.page_content')"
                    :value="$translations['content'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="content"
                    :allowHtml="true"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang
                    :label="__('admin.meta_tag_title')"
                    :value="$translations['meta_title'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="meta_title"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang
                    :label="__('admin.meta_tag_description')"
                    :value="$translations['meta_description'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="meta_description"
                />
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.sort_order') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $page->sort_order }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.status') }}:</span>
                </div>
                <div class="col-sm-9">
                    <x-admin.status-badge :status="$page->status" />
                </div>
            </div>
        </div>
    </div>
@endsection
