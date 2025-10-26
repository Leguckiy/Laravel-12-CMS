@extends('layouts.admin')

@section('content')
    <!-- Hidden form for delete operations -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    
    <div class="action mb-2">
        <a class="btn btn-primary" href="{{ route('admin.user.create') }}">Add user</a>
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
                            <th>Action</th>
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
                                    @if($user->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <div class="btn-group actions" role="group">
                                        <a href="{{ route('admin.user.show', $user->id) }}" class="btn btn-outline-info" title="View user">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-outline-primary" title="Edit user">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                onclick="deleteUser({{ $user->id }})"
                                                title="Delete user">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
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

@push('scripts')
<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        // Use the hidden form for delete operation
        const form = document.getElementById('delete-form');
        form.action = `/admin/user/${userId}`;
        form.submit();
    }
}
</script>
@endpush