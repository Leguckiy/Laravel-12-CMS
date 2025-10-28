@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.user.create"
            text="Add User" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>User list</span>
        </div>
        <div id="user" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>User name</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>User group</th>
                            <th>Status</th>
                            <th>Date added</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->fullname }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->userGroup?->name ?? 'No group' }}</td>
                                <td>
                                    <x-admin.status-badge :status="$user->status" />
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$user->id"
                                        baseName="user"
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

 