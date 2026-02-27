@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :id="$user->id"
        baseName="user"
        :itemName="__('admin.user')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.user')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.user_details') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold">{{ __('admin.user_name') }}:</td>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.name') }}:</td>
                            <td>{{ $user->fullname }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.email') }}:</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.user_group') }}:</td>
                            <td>{{ $user->userGroup?->name ?? __('admin.no_group') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.language') }}:</td>
                            <td>{{ $user->language?->name ?? __('admin.no_language') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.status') }}:</td>
                            <td>
                                <x-admin.status-badge :status="$user->status" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">{{ __('admin.created') }}:</td>
                            <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @if($user->updated_at != $user->created_at)
                        <tr>
                            <td class="fw-bold">{{ __('admin.updated') }}:</td>
                            <td>{{ $user->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                @if($user->image_url)
                    <div class="col-md-4">
                        <div class="text-center">
                            <img src="{{ $user->image_url }}" class="rounded-circle border" alt="{{ __('admin.user_photo') }}" style="width: 160px; height: 160px; object-fit: cover;">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
