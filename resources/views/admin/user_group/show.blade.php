@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :id="$userGroup->id"
        baseName="user_group"
        :itemName="__('admin.user_group')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.user_group')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.user_group_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold" style="width: 150px;">{{ __('admin.name') }}:</td>
                            <td>{{ $userGroup->name }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
