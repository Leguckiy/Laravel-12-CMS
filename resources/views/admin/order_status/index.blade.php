@extends('layouts.admin')

@section('page-actions')
    <div class="d-flex gap-2">
        <x-admin.action-button-add
            permission="admin.order_status.create"
            :text="__('admin.add_order_status')"
        />
    </div>
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.order_status_list') }}</span>
        </div>
        <div id="order_status" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.order_status_name') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderStatuses as $orderStatus)
                            <tr>
                                <td>{{ $orderStatus->name }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$orderStatus->id"
                                        baseName="order_status"
                                        :itemName="__('admin.order_status')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.order_status')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$orderStatuses" />
        </div>
    </div>
@endsection
