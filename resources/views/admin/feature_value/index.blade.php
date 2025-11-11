@extends('layouts.admin')

@section('content')
    <x-admin.delete-form />
    
    <div class="action mb-2">
        <x-admin.action-button-add 
            permission="admin.feature_value.create"
            :route="route('admin.feature_value.create', ['feature' => $feature->id])"
            :text="__('admin.add_feature_value')" 
        />
    </div>
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-list"></i>
            <span>{{ __('admin.feature_value_list') }}</span>
        </div>
        <div id="feature_value" class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('admin.feature_value_name') }}</th>
                            <th>{{ __('admin.sort_order') }}</th>
                            <th class="text-end">{{ __('admin.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($values as $featureValue)
                            <tr>
                                <td>{{ $featureValue->value }}</td>
                                <td>{{ $featureValue->sort_order }}</td>
                                <td class="text-end">
                                    <x-admin.action-buttons-row 
                                        :id="$featureValue->id"
                                        baseName="feature_value"
                                        :showRoute="route('admin.feature_value.show', ['feature' => $feature->id, 'feature_value' => $featureValue->id])"
                                        :editRoute="route('admin.feature_value.edit', ['feature' => $feature->id, 'feature_value' => $featureValue->id])"
                                        :destroyRoute="route('admin.feature_value.destroy', ['feature' => $feature->id, 'feature_value' => $featureValue->id])"
                                        :itemName="__('admin.feature_value')"
                                        :confirmText="__('admin.delete_confirm', ['item' => __('admin.feature_value')])"
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
