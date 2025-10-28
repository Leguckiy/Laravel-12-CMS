@extends('layouts.admin')

@php
    use Illuminate\Support\Str;
@endphp

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
            <span>{{ isset($userGroup) ? 'Edit User group' : 'Add User group' }}</span>
        </div>
        <div class="card-body">
            <form id="form-user-group" action="{{ isset($userGroup) ? route('admin.user_group.update', $userGroup->id) : route('admin.user_group.store') }}" method="POST">
                @csrf
                @if(isset($userGroup))
                    @method('PUT')
                @endif
                <x-admin.input-field type="text" name="name" label="User group name" placeholder="User group name" :value="$userGroup->name ?? ''" :required="true"/>
                
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Permissions</label>
                    <div class="col-sm-10">
                        <div class="form-control" style="height: 400px; overflow: auto; padding: 0;">
                            <table class="table table-borderless table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th class="w-50"></th>
                                        <th class="text-center">Access</th>
                                        <th class="text-center">Modify</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($adminRoutes as $key => $moduleData)
                                        <tr>
                                            <td><span>{{ $moduleData }}</span></td>
                                            <td class="text-center">
                                                <input
                                                    type="checkbox" 
                                                    name="permissions[view][]" 
                                                    value="{{ $moduleData }}"
                                                    id="access-{{ $key }}" 
                                                    class="form-check-input"
                                                    @checked(isset($userGroup) && $userGroup->hasPermission($moduleData, 'view'))
                                                />
                                            </td>
                                            <td class="text-center">
                                                <input 
                                                    type="checkbox" 
                                                    name="permissions[edit][]" 
                                                    value="{{ $moduleData }}"
                                                    id="edit-{{ $key }}" 
                                                    class="form-check-input"
                                                    @checked(isset($userGroup) && $userGroup->hasPermission($moduleData, 'edit'))
                                                />
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No admin routes found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <x-admin.form-actions 
                    :isEdit="isset($userGroup)"
                    :backRoute="route('admin.user_group.index')"
                />
            </form>
        </div>
    </div>
@endsection
