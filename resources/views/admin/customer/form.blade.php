@extends('layouts.admin')

@section('page-actions')
    <x-admin.form-actions
        :isEdit="isset($customer)"
        :backRoute="route('admin.customer.index')"
        formId="form-customer"
    />
@endsection

@section('content')
    <x-admin.delete-form />
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span>{{ __('admin.form_errors') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-pencil"></i>
            <span>{{ isset($customer) ? __('admin.edit_customer') : __('admin.add_customer') }}</span>
        </div>
        <div class="card-body">
            <form id="form-customer" action="{{ isset($customer) ? route('admin.customer.update', $customer->id) : route('admin.customer.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($customer))
                    @method('PUT')
                @endif
                @if(isset($customer))
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#tab-general" data-bs-toggle="tab" class="nav-link active">
                                {{ __('admin.tab_general') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-addresses" data-bs-toggle="tab" class="nav-link">
                                {{ __('admin.tab_addresses') }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general">
                @endif
                            <x-admin.select-field name="customer_group_id" :label="__('admin.customer_group')" :options="$customerGroupsOptions" :value="$customer->customer_group_id ?? ''"/>
                            <x-admin.input-field type="text" name="firstname" :label="__('admin.first_name')" :placeholder="__('admin.first_name')" :value="$customer->firstname ?? ''" :required="true"/>
                            <x-admin.input-field type="text" name="lastname" :label="__('admin.last_name')" :placeholder="__('admin.last_name')" :value="$customer->lastname ?? ''" :required="true"/>
                            <x-admin.input-field type="email" name="email" :label="__('admin.email')" :placeholder="__('admin.email')" :value="$customer->email ?? ''" :required="true"/>
                            <x-admin.input-field type="text" name="telephone" :label="__('admin.telephone')" :placeholder="__('admin.telephone')" :value="$customer->telephone ?? ''" :required="false"/>
                            <x-admin.input-field type="password" name="password" :label="__('admin.password')" :placeholder="__('admin.password')" value="" :required="true"/>
                            <x-admin.input-field type="password" name="confirm" :label="__('admin.confirm')" :placeholder="__('admin.confirm')" value="" :required="true"/>
                            <x-admin.switch-field name="status" :label="__('admin.status')" :value="$customer->status ?? false"/>
                @if(isset($customer))
                        </div>
                        <div class="tab-pane" id="tab-addresses">
                            <h3 class="h6 mb-3">{{ __('admin.addresses') }}</h3>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('admin.address_name') }}</th>
                                            <th>{{ __('admin.address') }}</th>
                                            <th>{{ __('admin.city') }}</th>
                                            <th>{{ __('admin.postcode') }}</th>
                                            <th>{{ __('admin.country') }}</th>
                                            <th>{{ __('admin.address_default') }}</th>
                                            <th class="text-end" style="width: 150px;">{{ __('admin.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($customer->addresses as $address)
                                            <tr>
                                                <td>{{ $address->firstname }} {{ $address->lastname }}</td>
                                                <td>{{ $address->address_1 }}{{ $address->address_2 ? ', ' . $address->address_2 : '' }}</td>
                                                <td>{{ $address->city }}</td>
                                                <td>{{ $address->postcode }}</td>
                                                <td>{{ $address->country?->getName($adminLanguage->id) ?? '' }}</td>
                                                <td>
                                                    @if($address->default)
                                                        <span class="badge bg-secondary">{{ __('admin.address_default') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group actions" role="group">
                                                        <a href="{{ route('admin.customer_address.show', [$customer, $address]) }}" class="btn btn-outline-info" title="{{ __('admin.view_item', ['item' => __('admin.address')]) }}">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.customer_address.edit', [$customer, $address]) }}" class="btn btn-outline-primary" title="{{ __('admin.edit_item', ['item' => __('admin.address')]) }}">
                                                            <i class="fa-solid fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" data-delete-url="{{ route('admin.customer_address.destroy', [$customer, $address]) }}" data-confirm="{{ __('admin.delete_confirm', ['item' => __('admin.address')]) }}" title="{{ __('admin.delete_item', ['item' => __('admin.address')]) }}">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-muted">{{ __('admin.no_addresses') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <a href="{{ route('admin.customer_address.create', $customer) }}" class="btn btn-primary">
                                <i class="fa-solid fa-plus"></i>
                                {{ __('admin.add_address') }}
                            </a>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection
