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
            <span>{{ isset($category) ? __('admin.edit_category') : __('admin.add_category') }}</span>
        </div>
        <div class="card-body">
            <form id="form-user-group" action="{{ isset($category) ? route('admin.category.update', $category->id) : route('admin.category.store') }}" method="POST">
                @csrf
                @if(isset($category))
                    @method('PUT')
                @endif
                <x-admin.input-field-multilang
                    type="text" 
                    name="name" 
                    :label="__('admin.category_name')" 
                    :placeholder="__('admin.category_name')" 
                    :value="$translations['name'] ?? []" 
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    :required="true"
                />
                <x-admin.input-field-multilang
                    type="text" 
                    name="slug" 
                    :label="__('admin.friendly_url')" 
                    :placeholder="__('admin.friendly_url')" 
                    :value="$translations['slug'] ?? []" 
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    :required="true"
                />
                <x-admin.textarea-field-multilang
                    name="description" 
                    :label="__('admin.description')" 
                    :placeholder="__('admin.description')"
                    :value="$translations['description'] ?? []" 
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
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
                    :currentLanguageId="$currentLanguageId"
                    :required="true"
                />
                <x-admin.textarea-field-multilang
                    name="meta_description" 
                    :label="__('admin.meta_tag_description')" 
                    :placeholder="__('admin.meta_tag_description')"
                    rows="3"
                    :value="$translations['meta_description'] ?? []" 
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    :required="false"
                />
                <x-admin.input-field type="text" name="image" :label="__('admin.image')" :placeholder="__('admin.image')" :value="$category->image ?? ''"/>
                <x-admin.input-field type="number" name="sort_order" :label="__('admin.sort_order')" :value="$category->sort_order ?? '0'"/>
                <x-admin.switch-field name="status" :label="__('admin.status')" :value="$category->status ?? false"/>
                <x-admin.form-actions 
                    :isEdit="isset($category)"
                    :backRoute="route('admin.category.index')"
                />
            </form>
        </div>
    </div>
@endsection
