@extends('layouts.admin')

@section('content')
    <!-- Hidden form for delete operations -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
    
    <div class="action mb-2">
        @canEdit('admin.user_group.create')
            <a class="btn btn-primary" href="{{ route('admin.user_group.create') }}">Add user group</a>
        @endcanEdit
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>User group list</span>
        </div>
        <div id="user_group" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>User group name</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userGroups as $userGroup)
                            <tr>
                                <td>{{ $userGroup->name }}</td>
                                <td class="text-end">
                                    <div class="btn-group actions" role="group">
                                        @canView('admin.user_group.show')
                                            <a href="{{ route('admin.user_group.show', $userGroup->id) }}" class="btn btn-outline-info" title="View user group">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        @endcanView
                                        @canEdit('admin.user_group.edit')
                                            <a href="{{ route('admin.user_group.edit', $userGroup->id) }}" class="btn btn-outline-primary" title="Edit user group">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        @endcanEdit
                                        @canEdit('admin.user_group.destroy')
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    onclick="deleteUserGroup({{ $userGroup->id }})"
                                                    title="Delete user group">
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

@push('scripts')
<script>
function deleteUserGroup(userGroupId) {
    if (confirm('Are you sure you want to delete this user group?')) {
        // Use the hidden form for delete operation
        const form = document.getElementById('delete-form');
        form.action = `/admin/user_group/${userGroupId}`;
        form.submit();
    }
}
</script>
@endpush