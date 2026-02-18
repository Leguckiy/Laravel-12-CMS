@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.shipping_methods_list') }}</span>
        </div>
        <div id="shipping" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.sort_order') }}</th>
                            <th>{{ __('admin.status') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($methods as $method)
                            <tr>
                                <td>{{ $method['name'] }}</td>
                                <td>{{ $method['sort_order'] }}</td>
                                <td>
                                    <x-admin.status-badge :status="$method['status']" />
                                </td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row
                                        :id="$method['code']"
                                        baseName="shipping"
                                        :editRoute="route('admin.shipping.edit', $method['code'])"
                                        showRoute=""
                                        destroyRoute=""
                                        showPermission=""
                                        destroyPermission=""
                                        :itemName="__('admin.shipping_method')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.shipping_method')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
