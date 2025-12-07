@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.user_group.create"
            :text="__('admin.add_user_group')" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.user_group_list') }}</span>
        </div>
        <div id="user_group" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.user_group_name') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userGroups as $userGroup)
                            <tr>
                                <td>{{ $userGroup->name }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$userGroup->id"
                                        baseName="user_group"
                                        :itemName="__('admin.user_group')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.user_group')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$userGroups" />
        </div>
    </div>
@endsection
