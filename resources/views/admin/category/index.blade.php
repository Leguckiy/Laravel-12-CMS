@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.category.create"
            :text="__('admin.add_category')" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.category_list') }}</span>
        </div>
        <div id="category" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.id') }}</th>
                            <th>{{ __('admin.image') }}</th>
                            <th>{{ __('admin.category_name') }}</th>
                            <th>{{ __('admin.sort_order') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td class="text-center image-wrapper">
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="img-thumbnail">
                                </td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->sort_order }}</td>
                                <td>
                                    <x-admin.status-badge :status="$category->status"/>
                                </td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$category->id"
                                        baseName="category"
                                        :itemName="__('admin.category')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.category')])"
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
