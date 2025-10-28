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
            <span>User group details</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 150px;">Name:</td>
                            <td>{{ $userGroup->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="mt-4">
                @canEdit('admin.user_group.edit')
                    <a href="{{ route('admin.user_group.edit', $userGroup->id) }}" class="btn btn-primary">
                        <i class="fa-solid fa-edit"></i>
                        <span>Edit User group</span>
                    </a>
                @endcanEdit
                @canEdit('admin.user_group.destroy')
                    <button type="button" class="btn btn-danger" onclick="deleteUser({{ $userGroup->id }})">
                        <i class="fa-solid fa-trash"></i>
                        <span>Delete User group</span>
                    </button>
                @endcanEdit
                <a href="{{ route('admin.user_group.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to List</span>
                </a>
            </div>
        </div>
    </div>
@endsection
