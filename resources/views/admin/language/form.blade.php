@extends('layouts.admin')

@section('page-actions')
    <x-admin.form-actions
        :isEdit="isset($language)"
        :backRoute="route('admin.language.index')"
        formId="form-language"
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
            <span>{{ isset($language) ? __('admin.edit_language') : __('admin.add_language') }}</span>
        </div>
        <div class="card-body">
            <form id="form-language" action="{{ isset($language) ? route('admin.language.update', $language->id) : route('admin.language.store') }}" method="POST">
                @csrf
                @if(isset($language))
                    @method('PUT')
                @endif
                <x-admin.input-field type="text" name="name" :label="__('admin.language_name')" :placeholder="__('admin.language_name')" :value="$language->name ?? ''" :required="true"/>
                <x-admin.input-field type="text" name="code" :label="__('admin.code')" :placeholder="__('admin.code')" :value="$language->code ?? ''" :required="true"/>
                <x-admin.input-field type="number" name="sort_order" :label="__('admin.sort_order')" :value="$language->sort_order ?? '0'"/>
                <x-admin.switch-field name="status" :label="__('admin.status')" :value="$language->status ?? false"/>
            </form>
        </div>
    </div>
@endsection
