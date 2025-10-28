@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add baseName="user_group" text="Add user group" />
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
                                    <x-admin.action-buttons-row 
                                        :id="$userGroup->id"
                                        baseName="user_group"
                                        itemName="user group"
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

 