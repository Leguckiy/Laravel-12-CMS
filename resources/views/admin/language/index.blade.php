@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.language.create"
            :text="__('admin.add_language')" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.language_list') }}</span>
        </div>
        <div id="language" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.language_name') }}</th>
                            <th>{{ __('admin.code') }}</th>
                            <th>{{ __('admin.sort_order') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($languages as $language)
                            <tr>
                                <td>{{ $language->name }}</td>
                                <td>{{ $language->code }}</td>
                                <td>{{ $language->sort_order }}</td>
                                <td>
                                    <x-admin.status-badge :status="$language->status" />
                                </td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$language->id"
                                        baseName="language"
                                        :itemName="__('admin.language')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.language')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$languages" />
        </div>
    </div>
@endsection
