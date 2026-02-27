@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :editRoute="route('admin.customer_address.edit', [$customer, $address])"
        :destroyRoute="route('admin.customer_address.destroy', [$customer, $address])"
        :backRoute="route('admin.customer.edit', $customer) . '#tab-addresses'"
        editPermission="admin.customer_address.edit"
        destroyPermission="admin.customer_address.destroy"
        :itemName="__('admin.address')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.address')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.address') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.first_name') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $address->firstname }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.last_name') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $address->lastname }}</span>
                </div>
            </div>
            @if ($address->company)
                <div class="row mt-2">
                    <div class="col-sm-3 fw-bold">
                        <span>{{ __('admin.company') }}:</span>
                    </div>
                    <div class="col-sm-9">
                        <span>{{ $address->company }}</span>
                    </div>
                </div>
            @endif
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.address_1') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $address->address_1 }}</span>
                </div>
            </div>
            @if ($address->address_2)
                <div class="row mt-2">
                    <div class="col-sm-3 fw-bold">
                        <span>{{ __('admin.address_2') }}:</span>
                    </div>
                    <div class="col-sm-9">
                        <span>{{ $address->address_2 }}</span>
                    </div>
                </div>
            @endif
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.city') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $address->city }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.postcode') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $address->postcode ?: '—' }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.country') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $address->country?->getName($adminLanguage->id) ?? '—' }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.address_default') }}:</span>
                </div>
                <div class="col-sm-9">
                    @if ($address->default)
                        <span class="badge bg-secondary">{{ __('admin.yes') }}</span>
                    @else
                        <span class="text-muted">{{ __('admin.no') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
