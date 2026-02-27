@extends('layouts.admin')

@section('page-actions')
    <div class="d-flex gap-2">
        <x-admin.action-button-add
            permission="admin.customer.create"
            :text="__('admin.add_customer')"
        />
    </div>
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.customer_list') }}</span>
        </div>
        <div id="customer" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.email') }}</th>
                            <th>{{ __('admin.customer_group') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.date_added') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>{{ $customer->fullname }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->customerGroup?->name ?? __('admin.no_group') }}</td>
                                <td>
                                    <x-admin.status-badge :status="$customer->status" />
                                </td>
                                <td>{{ $customer->created_at->format('d.m.Y') }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$customer->id"
                                        baseName="customer"
                                        :itemName="__('admin.customer')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.customer')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$customers" />
        </div>
    </div>
@endsection
