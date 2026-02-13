@extends('layouts.admin')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <span>{{ __('admin.form_errors') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
        </div>
    @endif
    
    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-pencil"></i>
            <span>{{ __('admin.edit_setting') }}</span>
        </div>
        <div class="card-body">
            <form id="form-user-group" action="{{ route('admin.setting.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="#tab-general" data-bs-toggle="tab" class="nav-link active">
                            {{ __('admin.tab_general') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-store" data-bs-toggle="tab" class="nav-link">
                            {{ __('admin.tab_store') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-local" data-bs-toggle="tab" class="nav-link">
                            {{ __('admin.tab_local') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-customer" data-bs-toggle="tab" class="nav-link">
                            {{ __('admin.tab_customer') }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-general">
                        <x-admin.input-field
                            type="text"
                            name="config_name"
                            :label="__('admin.store_name')"
                            :placeholder="__('admin.store_name')"
                            :value="$settings['config_name'] ?? null"
                            :required="true"
                        />
                        <x-admin.input-field-multilang
                            type="text" 
                            name="config_meta_title" 
                            :label="__('admin.meta_tag_title')" 
                            :placeholder="__('admin.meta_tag_title')" 
                            :value="$translations['config_meta_title'] ?? []" 
                            :languages="$languages"
                            :currentLanguageId="$adminLanguage->id"
                            :required="true"
                        />
                        <x-admin.textarea-field-multilang
                            name="config_meta_description" 
                            :label="__('admin.meta_tag_description')" 
                            :placeholder="__('admin.meta_tag_description')"
                            :value="$translations['config_meta_description'] ?? []" 
                            :languages="$languages"
                            :currentLanguageId="$adminLanguage->id"
                        />
                        <x-admin.image-upload-field
                            name="config_logo"
                            :label="__('admin.logo')"
                            :current-path="$settings['config_logo'] ?? null"
                        />
                        <x-admin.image-upload-field
                            name="config_icon"
                            :label="__('admin.icon')"
                            :current-path="$settings['config_icon'] ?? null"
                        />
                    </div>
                    <div class="tab-pane" id="tab-store">
                        <x-admin.input-field
                            type="text"
                            name="config_owner"
                            :label="__('admin.store_owner')"
                            :placeholder="__('admin.store_owner')"
                            :value="$settings['config_owner'] ?? null"
                            :required="true"
                        />
                        <x-admin.textarea-field
                            name="config_address"
                            :label="__('admin.store_address')"
                            :placeholder="__('admin.store_address')"
                            :value="$settings['config_address'] ?? null"
                            :required="true"
                            :rows="3"
                        />
                        <x-admin.input-field
                            type="text"
                            name="config_email"
                            :label="__('admin.email')"
                            :placeholder="__('admin.email')"
                            :value="$settings['config_email'] ?? null"
                            :required="true"
                        />
                        <x-admin.input-field
                            type="text"
                            name="config_telephone"
                            :label="__('admin.telephone')"
                            :placeholder="__('admin.telephone')"
                            :value="$settings['config_telephone'] ?? null"
                        />
                        <x-admin.textarea-field
                            name="config_open"
                            :label="__('admin.opening_times')"
                            :placeholder="__('admin.opening_times')"
                            :value="$settings['config_open'] ?? null"
                            :required="false"
                            :rows="3"
                        />
                    </div>
                    <div class="tab-pane" id="tab-local">
                        <x-admin.select-field
                            name="config_country_id"
                            :label="__('admin.country')"
                            :options="$countriesOptions"
                            :value="$settings['config_country_id'] ?? null"
                        />
                        <x-admin.select-field
                            name="config_language_id"
                            :label="__('admin.language')"
                            :options="$languagesOptions"
                            :value="$settings['config_language_id'] ?? null"
                        />
                        <x-admin.select-field
                            name="config_currency_id"
                            :label="__('admin.currency')"
                            :options="$currenciesOptions"
                            :value="$settings['config_currency_id'] ?? null"
                        />
                    </div>
                    <div class="tab-pane" id="tab-customer">
                        <x-admin.select-field
                            name="config_customer_group_id"
                            :label="__('admin.customer_group')"
                            :options="$customerGroupsOptions"
                            :value="$settings['config_customer_group_id'] ?? null"
                        />
                    </div>
                </div>
                <x-admin.form-actions 
                    isEdit="true"
                    :backRoute="route('admin.dashboard')"
                />
            </form>
        </div>
    </div>
@endsection
