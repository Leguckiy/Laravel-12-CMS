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
            <span>{{ isset($country) ? __('admin.edit_country') : __('admin.add_country') }}</span>
        </div>
        <div class="card-body">
            <form id="form-user-group" action="{{ isset($country) ? route('admin.country.update', $country->id) : route('admin.country.store') }}" method="POST">
                @csrf
                @if(isset($country))
                    @method('PUT')
                @endif
                <x-admin.input-field-multilang
                    type="text" 
                    name="name" 
                    :label="__('admin.country_name')" 
                    :placeholder="__('admin.country_name')" 
                    :value="$translations ?? ''" 
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    :required="true"
                />
                <x-admin.input-field type="text" name="iso_code_2" :label="__('admin.iso_code_2')" :placeholder="__('admin.iso_code_2')" :value="$country->iso_code_2 ?? ''" :required="true"/>
                <x-admin.input-field type="text" name="iso_code_3" :label="__('admin.iso_code_3')" :placeholder="__('admin.iso_code_3')" :value="$country->iso_code_3 ?? ''" :required="true"/>
                <x-admin.switch-field name="postcode_required" :label="__('admin.postcode_required')" :value="$country->postcode_required ?? false"/>
                <x-admin.switch-field name="status" :label="__('admin.status')" :value="$country->status ?? false"/>
                <x-admin.form-actions 
                    :isEdit="isset($country)"
                    :backRoute="route('admin.country.index')"
                />
            </form>
        </div>
    </div>
@endsection
