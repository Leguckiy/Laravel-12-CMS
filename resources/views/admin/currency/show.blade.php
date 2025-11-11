@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.currency_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">{{ __('admin.name') }}:</td>
                            <td>{{ $currency->title }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.code') }}:</td>
                            <td>{{ $currency->code }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.symbol_left') }}:</td>
                            <td>{{ $currency->symbol_left }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.symbol_right') }}:</td>
                            <td>{{ $currency->symbol_right }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.decimal_place') }}:</td>
                            <td>{{ $currency->decimal_place }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.value') }}:</td>
                            <td>{{ $currency->value }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.status') }}:</td>
                            <td>
                                <x-admin.status-badge :status="$currency->status" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.created') }}:</td>
                            <td>{{ $currency->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @if($currency->updated_at != $currency->created_at)
                            <tr>
                                <td class="fw-bold">{{ __('admin.updated') }}:</td>
                                <td>{{ $currency->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <x-admin.detail-actions 
                :id="$currency->currency_id ?? $currency->id"
                baseName="currency"
                :itemName="__('admin.currency')"
                :confirmText="__('admin.delete_confirm', ['item' => __('admin.currency')])"
            />
        </div>
    </div>
@endsection
