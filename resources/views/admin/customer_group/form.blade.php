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
            <span>{{ isset($customerGroup) ? __('admin.edit_customer_group') : __('admin.add_customer_group') }}</span>
        </div>
        <div class="card-body">
            <form id="form-user-group" action="{{ isset($customerGroup) ? route('admin.customer_group.update', $customerGroup->id) : route('admin.customer_group.store') }}" method="POST">
                @csrf
                @if(isset($customerGroup))
                    @method('PUT')
                @endif
                <x-admin.input-field-multilang
                    type="text" 
                    name="name" 
                    :label="__('admin.customer_group_name')" 
                    :placeholder="__('admin.customer_group_name')" 
                    :value="$translations['name'] ?? ''" 
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    :required="true"
                />
                <x-admin.input-field-multilang
                    type="text" 
                    name="description" 
                    :label="__('admin.description')" 
                    :placeholder="__('admin.description')" 
                    :value="$translations['description'] ?? ''" 
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                />
                <x-admin.switch-field name="approval" :label="__('admin.customer_group_approval')" :value="$customerGroup->approval ?? false"/>
                <x-admin.input-field type="number" name="sort_order" :label="__('admin.sort_order')" :value="$customerGroup->sort_order ?? '1'"/>
                <x-admin.form-actions 
                    :isEdit="isset($customerGroup)"
                    :backRoute="route('admin.customer_group.index')"
                />
            </form>
        </div>
    </div>
@endsection
