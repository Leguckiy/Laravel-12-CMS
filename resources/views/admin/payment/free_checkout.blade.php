@extends('layouts.admin')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span>{{ $errors->first() }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-pencil"></i>
            <span>{{ __('admin.edit') }}: {{ $method['name'] }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payment.update', $method['code']) }}" method="POST">
                @csrf
                @method('PUT')
                <x-admin.select-field
                    name="order_status_id"
                    :label="__('admin.payment_order_status')"
                    :options="$orderStatusOptions"
                    :value="old('order_status_id', data_get($paymentMethod->config, 'order_status_id', ''))"
                    :required="true"
                />
                <x-admin.input-field
                    type="number"
                    name="sort_order"
                    :label="__('admin.sort_order')"
                    :value="old('sort_order', $paymentMethod->sort_order ?? 0)"
                />
                <x-admin.switch-field
                    name="status"
                    :label="__('admin.status')"
                    :value="old('status', $paymentMethod->status ?? true)"
                />
                <x-admin.form-actions
                    :isEdit="true"
                    :backRoute="route('admin.payment.index')"
                />
            </form>
        </div>
    </div>
@endsection
