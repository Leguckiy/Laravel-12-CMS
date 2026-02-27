@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :id="$language->id"
        baseName="language"
        :itemName="__('admin.language')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.language')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.language_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">{{ __('admin.name') }}:</td>
                            <td>{{ $language->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.code') }}:</td>
                            <td>{{ $language->code }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.sort_order') }}:</td>
                            <td>{{ $language->sort_order }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.status') }}:</td>
                            <td>
                                <x-admin.status-badge :status="$language->status" />
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
