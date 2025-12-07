@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.feature.create"
            :text="__('admin.add_feature')" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.feature_list') }}</span>
        </div>
        <div id="feature" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.feature_name') }}</th>
                            <th>{{ __('admin.values') }}</th>
                            <th>{{ __('admin.manage_values') }}</th>
                            <th>{{ __('admin.sort_order') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($features as $feature)
                            <tr>
                                <td>{{ $feature->name }}</td>
                                <td>{{ $feature->values_count }}</td>
                                <td>
                                    <a href="{{ route('admin.feature_value.index', ['feature' => $feature->id]) }}" class="btn btn-outline-success btn-sm">
                                        {{ __('admin.open_list') }}
                                    </a>
                                    <a href="{{ route('admin.feature_value.create', ['feature' => $feature->id]) }}" class="btn btn-outline-primary btn-sm">
                                        {{ __('admin.add_feature_value') }}
                                    </a>
                                </td>
                                <td>{{ $feature->sort_order }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$feature->id"
                                        baseName="feature"
                                        :itemName="__('admin.feature')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.feature')])"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-admin.pagination :paginator="$features" />
        </div>
    </div>
@endsection
