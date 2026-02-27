@extends('layouts.admin')

@section('page-actions')
    <x-admin.form-actions
        :isEdit="isset($page)"
        :backRoute="route('admin.page.index')"
        formId="form-page"
    />
@endsection

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
            <span>{{ isset($page) ? __('admin.edit_page') : __('admin.add_page') }}</span>
        </div>
        <div class="card-body">
            <form id="form-page" action="{{ isset($page) ? route('admin.page.update', $page->id) : route('admin.page.store') }}" method="POST">
                @csrf
                @if (isset($page))
                    @method('PUT')
                @endif
                <x-admin.input-field-multilang
                    type="text"
                    name="title"
                    :label="__('admin.page_title')"
                    :placeholder="__('admin.page_title')"
                    :value="$translations['title'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    :required="true"
                />
                <x-admin.input-field-multilang
                    type="text"
                    name="slug"
                    :label="__('admin.friendly_url')"
                    :placeholder="__('admin.friendly_url')"
                    :value="$translations['slug'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    :required="true"
                />
                <x-admin.textarea-field-multilang
                    name="content"
                    :label="__('admin.page_content')"
                    :placeholder="__('admin.page_content')"
                    :value="$translations['content'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    :autoloadRte="true"
                    :rteHeight="320"
                />
                <x-admin.input-field-multilang
                    type="text"
                    name="meta_title"
                    :label="__('admin.meta_tag_title')"
                    :placeholder="__('admin.meta_tag_title')"
                    :value="$translations['meta_title'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    :required="true"
                />
                <x-admin.textarea-field-multilang
                    name="meta_description"
                    :label="__('admin.meta_tag_description')"
                    :placeholder="__('admin.meta_tag_description')"
                    rows="3"
                    :value="$translations['meta_description'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    :required="false"
                />
                <x-admin.input-field type="number" name="sort_order" :label="__('admin.sort_order')" :value="isset($page) ? $page->sort_order : 0" />
                <x-admin.switch-field name="status" :label="__('admin.status')" :value="isset($page) ? $page->status : true" />
            </form>
        </div>
    </div>
@endsection
