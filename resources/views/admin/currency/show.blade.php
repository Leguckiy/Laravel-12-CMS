@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>Currency Details</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 180px;">{{ __('admin.name') }}:</td>
                            <td>{{ $currency->title }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="width: 180px;">{{ __('admin.code') }}:</td>
                            <td>{{ $currency->code }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="width: 180px;">Symbol Left:</td>
                            <td>{{ $currency->symbol_left }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="width: 180px;">Symbol Right:</td>
                            <td>{{ $currency->symbol_right }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="width: 180px;">Decimal Place:</td>
                            <td>{{ $currency->decimal_place }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold" style="width: 180px;">Value:</td>
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
                :itemName="'Currency'"
                :confirmText="__('admin.delete_confirm', ['item' => 'Currency'])"
            />
        </div>
    </div>
@endsection
