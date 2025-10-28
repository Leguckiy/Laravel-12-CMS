@extends('layouts.admin')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span>Please check the form for errors!</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-pencil"></i>
            <span>{{ isset($user) ? 'Edit User' : 'Add User' }}</span>
        </div>
        <div class="card-body">
            <form id="form-user" action="{{ isset($user) ? route('admin.user.update', $user->id) : route('admin.user.store') }}" method="POST">
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif
                <x-admin.input-field type="text" name="username" label="User name" placeholder="User name" :value="$user->username ?? ''" :required="true"/>
                <x-admin.select-field name="user_group_id" label="User group" :options="$userGroupsOptions" :value="$user->user_group_id ?? ''"/>
                <x-admin.input-field type="text" name="firstname" label="First name" placeholder="First name" :value="$user->firstname ?? ''" :required="true"/>
                <x-admin.input-field type="text" name="lastname" label="Last name" placeholder="Last name" :value="$user->lastname ?? ''" :required="true"/>
                <x-admin.input-field type="email" name="email" label="Email" placeholder="Email" :value="$user->email ?? ''" :required="true"/>
                <x-admin.input-field type="text" name="image" label="Image" placeholder="Image" :value="$user->image ?? ''"/>
                <x-admin.input-field type="password" name="password" label="Password" placeholder="Password" value="" :required="true"/>
                <x-admin.input-field type="password" name="confirm" label="Confirm" placeholder="Confirm" value="" :required="true"/>
                <x-admin.switch-field name="status" label="Status" :value="$user->status ?? false"/>
                <x-admin.form-actions 
                    :isEdit="isset($user)"
                    :backRoute="route('admin.user.index')"
                />
            </form>
        </div>
    </div>
@endsection
