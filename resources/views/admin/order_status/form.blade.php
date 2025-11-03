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
            <span>{{ isset($orderStatus) ? __('admin.edit_order_status') : __('admin.add_order_status') }}</span>
        </div>
        <div class="card-body">
            <form id="form-user-group" action="{{ isset($orderStatus) ? route('admin.order_status.update', $orderStatus->id) : route('admin.order_status.store') }}" method="POST">
                @csrf
                @if(isset($orderStatus))
                    @method('PUT')
                @endif
                <x-admin.input-field-multilang
                    type="text" 
                    name="name" 
                    :label="__('admin.order_status_name')" 
                    :placeholder="__('admin.order_status_name')" 
                    :value="$translations ?? ''" 
                    :languages="$languages"
                    :currentLanguageId="$currentLanguageId"
                    :required="true"
                />
                <x-admin.form-actions 
                    :isEdit="isset($orderStatus)"
                    :backRoute="route('admin.order_status.index')"
                />
            </form>
        </div>
    </div>
@endsection
