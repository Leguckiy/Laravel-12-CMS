@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :id="$customer->id"
        baseName="customer"
        :itemName="__('admin.customer')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.customer')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.customer_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">{{ __('admin.name') }}:</td>
                            <td>{{ $customer->fullname }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.email') }}:</td>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.customer_group') }}:</td>
                            <td>{{ $customer->customerGroup?->name ?? __('admin.no_group') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.status') }}:</td>
                            <td>
                                <x-admin.status-badge :status="$customer->status" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.created') }}:</td>
                            <td>{{ $customer->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @if($customer->updated_at != $customer->created_at)
                            <tr>
                                <td class="fw-bold">{{ __('admin.updated') }}:</td>
                                <td>{{ $customer->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
