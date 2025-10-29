@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.language.create"
            text="Add language" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>Language list</span>
        </div>
        <div id="language" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Language name</th>
                            <th>Code</th>
                            <th>Sort order</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($languages as $language)
                            <tr>
                                <td>{{ $language->name }}</td>
                                <td>{{ $language->code }}</td>
                                <td>{{ $language->sort_order }}</td>
                                <td>
                                    <x-admin.status-badge :status="$language->status" />
                                </td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$language->id"
                                        baseName="language"
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

 