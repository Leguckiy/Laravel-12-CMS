@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.user.create"
            :text="__('admin.add_user')" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.user_list') }}</span>
        </div>
        <div id="user" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.user_name') }}</th>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.email') }}</th>
                            <th>{{ __('admin.user_group') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.date_added') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->fullname }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->userGroup?->name ?? __('admin.no_group') }}</td>
                                <td>
                                    <x-admin.status-badge :status="$user->status" />
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$user->id"
                                        baseName="user"
                                        :itemName="__('admin.user')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.user')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

 