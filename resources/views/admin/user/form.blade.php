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
                <x-input-field type="text" name="username" label="User name" placeholder="User name" :value="$user->username ?? ''" :required="true"/>
                <x-select-field name="user_group_id" label="User group" :options="$userGroupsOptions" :value="$user->user_group_id ?? ''"/>
                <x-input-field type="text" name="firstname" label="First name" placeholder="First name" :value="$user->firstname ?? ''" :required="true"/>
                <x-input-field type="text" name="lastname" label="Last name" placeholder="Last name" :value="$user->lastname ?? ''" :required="true"/>
                <x-input-field type="email" name="email" label="Email" placeholder="Email" :value="$user->email ?? ''" :required="true"/>
                <x-input-field type="text" name="image" label="Image" placeholder="Image" :value="$user->image ?? ''"/>
                <x-input-field type="password" name="password" label="Password" placeholder="Password" value="" :required="true"/>
                <x-input-field type="password" name="confirm" label="Confirm" placeholder="Confirm" value="" :required="true"/>
                <x-switch-field name="status" label="Status" :value="$user->status ?? false"/>
                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i>
                            {{ isset($user) ? 'Update' : 'Save' }}
                        </button>
                        <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
