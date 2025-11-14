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
            <span>{{ isset($customer) ? __('admin.edit_customer') : __('admin.add_customer') }}</span>
        </div>
        <div class="card-body">
            <form id="form-customer" action="{{ isset($customer) ? route('admin.customer.update', $customer->id) : route('admin.customer.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($customer))
                    @method('PUT')
                @endif
                <x-admin.select-field name="customer_group_id" :label="__('admin.customer_group')" :options="$customerGroupsOptions" :value="$customer->customer_group_id ?? ''"/>
                <x-admin.input-field type="text" name="firstname" :label="__('admin.first_name')" :placeholder="__('admin.first_name')" :value="$customer->firstname ?? ''" :required="true"/>
                <x-admin.input-field type="text" name="lastname" :label="__('admin.last_name')" :placeholder="__('admin.last_name')" :value="$customer->lastname ?? ''" :required="true"/>
                <x-admin.input-field type="email" name="email" :label="__('admin.email')" :placeholder="__('admin.email')" :value="$customer->email ?? ''" :required="true"/>
                <x-admin.input-field type="text" name="telephone" :label="__('admin.telephone')" :placeholder="__('admin.telephone')" :value="$customer->telephone ?? ''" :required="false"/>
                <x-admin.input-field type="password" name="password" :label="__('admin.password')" :placeholder="__('admin.password')" value="" :required="true"/>
                <x-admin.input-field type="password" name="confirm" :label="__('admin.confirm')" :placeholder="__('admin.confirm')" value="" :required="true"/>
                <x-admin.switch-field name="status" :label="__('admin.status')" :value="$customer->status ?? false"/>
                <x-admin.form-actions 
                    :isEdit="isset($customer)"
                    :backRoute="route('admin.customer.index')"
                />
            </form>
        </div>
    </div>
@endsection
