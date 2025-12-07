@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.stock_status.create"
            :text="__('admin.add_stock_status')" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.stock_status_list') }}</span>
        </div>
        <div id="stock_status" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.stock_status_name') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stockStatuses as $stockStatus)
                            <tr>
                                <td>{{ $stockStatus->name }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$stockStatus->id"
                                        baseName="stock_status"
                                        :itemName="__('admin.stock_status')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.stock_status')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$stockStatuses" />
        </div>
    </div>
@endsection
