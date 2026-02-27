@extends('layouts.admin')

@section('page-actions')
    <x-admin.form-actions
        :isEdit="isset($user)"
        :backRoute="route('admin.user.index')"
        formId="form-user"
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
            <span>{{ isset($user) ? __('admin.edit_user') : __('admin.add_user') }}</span>
        </div>
        <div class="card-body">
            <form id="form-user" action="{{ isset($user) ? route('admin.user.update', $user->id) : route('admin.user.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif
                <x-admin.input-field type="text" name="username" :label="__('admin.user_name')" :placeholder="__('admin.user_name')" :value="$user->username ?? ''" :required="true"/>
                <x-admin.select-field name="user_group_id" :label="__('admin.user_group')" :options="$userGroupsOptions" :value="$user->user_group_id ?? ''"/>
                <x-admin.select-field name="language_id" :label="__('admin.language')" :options="$languagesOptions" :value="$user->language_id ?? ''"/>
                <x-admin.input-field type="text" name="firstname" :label="__('admin.first_name')" :placeholder="__('admin.first_name')" :value="$user->firstname ?? ''" :required="true"/>
                <x-admin.input-field type="text" name="lastname" :label="__('admin.last_name')" :placeholder="__('admin.last_name')" :value="$user->lastname ?? ''" :required="true"/>
                <x-admin.input-field type="email" name="email" :label="__('admin.email')" :placeholder="__('admin.email')" :value="$user->email ?? ''" :required="true"/>
                <x-admin.image-upload-field
                    name="image"
                    :label="__('admin.image')"
                    :current-path="$user->image_path ?? null"
                />
                <x-admin.input-field type="password" name="password" :label="__('admin.password')" :placeholder="__('admin.password')" value="" :required="true"/>
                <x-admin.input-field type="password" name="confirm" :label="__('admin.confirm')" :placeholder="__('admin.confirm')" value="" :required="true"/>
                <x-admin.switch-field name="status" :label="__('admin.status')" :value="$user->status ?? false"/>
            </form>
        </div>
    </div>
@endsection
