@extends('layouts.admin')

@section('page-actions')
    <div class="d-flex gap-2">
        <x-admin.action-button-add
            permission="admin.customer_group.create"
            :text="__('admin.add_customer_group')"
        />
    </div>
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.customer_group_list') }}</span>
        </div>
        <div id="customer_group" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.customer_group_name') }}</th>
                            <th>{{ __('admin.sort_order') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customerGroups as $customerGroup)
                            <tr>
                                <td>{{ $customerGroup->name }}</td>
                                <td>{{ $customerGroup->sort_order }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$customerGroup->id"
                                        baseName="customer_group"
                                        :itemName="__('admin.customer_group')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.customer_group')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$customerGroups" />
        </div>
    </div>
@endsection
