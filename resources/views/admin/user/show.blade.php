@extends('layouts.admin')

@section('content')
    <!-- Hidden form for delete operations -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>User Details</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 150px;">Username:</td>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Name:</td>
                            <td>{{ $user->fullname }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Email:</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">User Group:</td>
                            <td>{{ $user->userGroup->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status:</td>
                            <td>
                                <x-status-badge :status="$user->status" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Created:</td>
                            <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @if($user->updated_at != $user->created_at)
                        <tr>
                            <td class="fw-bold">Updated:</td>
                            <td>{{ $user->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                @if($user->image)
                <div class="col-md-4">
                    <div class="text-center">
                        <img src="{{ $user->image }}" class="img-fluid rounded" alt="User photo" style="max-height: 200px;">
                    </div>
                </div>
                @endif
            </div>
            
            <div class="mt-4">
                @canEdit('admin.user.edit')
                    <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-primary">
                        <i class="fa-solid fa-edit"></i> Edit User
                    </a>
                @endcanEdit
                @canEdit('admin.user.destroy')
                    <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }})">
                        <i class="fa-solid fa-trash"></i> Delete User
                    </button>
                @endcanEdit
                <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
@endsection
