@extends('layouts.admin')

@section('page-actions')
    <div class="d-flex gap-2">
        <x-admin.action-button-add
            permission="admin.country.create"
            :text="__('admin.add_country')"
        />
    </div>
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.country_list') }}</span>
        </div>
        <div id="country" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.country_name') }}</th>
                            <th>{{ __('admin.iso_code_2') }}</th>
                            <th>{{ __('admin.iso_code_3') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($countries as $country)
                            <tr>
                                <td>{{ $country->name }}</td>
                                <td>{{ $country->iso_code_2 }}</td>
                                <td>{{ $country->iso_code_3 }}</td>
                                <td>
                                    <x-admin.status-badge :status="$country->status"/>
                                </td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$country->id"
                                        baseName="country"
                                        :itemName="__('admin.country')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.country')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$countries" />
        </div>
    </div>
@endsection
