@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.currency.create"
            :text="__('admin.add_currency')" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.currency_list') }}</span>
        </div>
        <div id="currency" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.currency_title') }}</th>
                            <th>{{ __('admin.code') }}</th>
                            <th>{{ __('admin.value') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th>{{ __('admin.date_updated') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($currencies as $currency)
                            <tr>
                                <td>{{ $currency->title }}</td>
                                <td>{{ $currency->code }}</td>
                                <td>{{ $currency->value }}</td>
                                <td>
                                    <x-admin.status-badge :status="$currency->status" />
                                </td>
                                <td>{{ $currency->updated_at->format('d.m.Y') }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$currency->id"
                                        baseName="currency"
                                        :itemName="__('admin.currency')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.currency')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$currencies" />
        </div>
    </div>
@endsection
