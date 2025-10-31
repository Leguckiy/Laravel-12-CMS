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
            <span>{{ isset($stockStatus) ? __('admin.edit_stock_status') : __('admin.add_stock_status') }}</span>
        </div>
        <div class="card-body">
            <form id="form-user-group" action="{{ isset($stockStatus) ? route('admin.stock_status.update', $stockStatus->id) : route('admin.stock_status.store') }}" method="POST">
                @csrf
                @if(isset($stockStatus))
                    @method('PUT')
                @endif
                <x-admin.input-field-multilang
                    type="text" 
                    name="name" 
                    :label="__('admin.stock_status_name')" 
                    :placeholder="__('admin.stock_status_name')" 
                    :value="$translations ?? ''" 
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    :required="true"
                />
                <x-admin.form-actions 
                    :isEdit="isset($stockStatus)"
                    :backRoute="route('admin.stock_status.index')"
                />
            </form>
        </div>
    </div>
@endsection
