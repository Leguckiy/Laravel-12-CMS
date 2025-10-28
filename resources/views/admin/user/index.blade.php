@extends('layouts.admin')

@section('content')
    <!-- Hidden form for delete operations -->
    <form id="delete-form" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
    
    <div class="action mb-2">
        @canEdit('admin.user.create')
            <a class="btn btn-primary" href="{{ route('admin.user.create') }}">Add user</a>
        @endcanEdit
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
                                <td>{{ $user->userGroup->name }}</td>
                                <td>
                                    <x-status-badge :status="$user->status" />
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                <td class="text-end">
                                    <div class="btn-group actions" role="group">
                                        @canView('admin.user.show')
                                            <a href="{{ route('admin.user.show', $user->id) }}" class="btn btn-outline-info" title="View user">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        @endcanView
                                        @canEdit('admin.user.edit')
                                            <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-outline-primary" title="Edit user">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        @endcanEdit
                                        @canEdit('admin.user.destroy')
                                            <button 
                                                type="button"
                                                class="btn btn-outline-danger"
                                                data-delete-url="{{ route('admin.user.destroy', $user->id) }}"
                                                data-confirm="Are you sure you want to delete this user?"
                                                title="Delete user"
                                            >
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endcanEdit
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

 