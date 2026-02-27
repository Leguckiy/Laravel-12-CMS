@extends('layouts.admin')

@section('page-actions')
    <x-admin.form-actions
        :isEdit="isset($feature)"
        :backRoute="route('admin.feature.index')"
        formId="form-feature"
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
            <span>{{ isset($feature) ? __('admin.edit_feature') : __('admin.add_feature') }}</span>
        </div>
        <div class="card-body">
            <form id="form-feature" action="{{ isset($feature) ? route('admin.feature.update', $feature->id) : route('admin.feature.store') }}" method="POST">
                @csrf
                @if(isset($feature))
                    @method('PUT')
                @endif
                <x-admin.input-field-multilang
                    type="text" 
                    name="name" 
                    :label="__('admin.feature_name')" 
                    :placeholder="__('admin.feature_name')" 
                    :value="$translations ?? ''" 
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    :required="true"
                />
                <x-admin.input-field type="number" name="sort_order" :label="__('admin.sort_order')" :value="$feature->sort_order ?? '0'"/>
            </form>
        </div>
    </div>
@endsection
