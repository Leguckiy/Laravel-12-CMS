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
            <span>{{ isset($product) ? __('admin.edit_product') : __('admin.add_product') }}</span>
        </div>
        <div class="card-body">
            <form id="form-user-group" action="{{ isset($product) ? route('admin.product.update', $product->id) : route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="#tab-general" data-bs-toggle="tab" class="nav-link active">
                            {{ __('admin.tab_general') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-data" data-bs-toggle="tab" class="nav-link">
                            {{ __('admin.tab_data') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-links" data-bs-toggle="tab" class="nav-link">
                            {{ __('admin.tab_links') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-features" data-bs-toggle="tab" class="nav-link">
                            {{ __('admin.tab_features') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-image" data-bs-toggle="tab" class="nav-link">
                            {{ __('admin.tab_image') }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-general">
                        <x-admin.input-field-multilang
                            type="text" 
                            name="name" 
                            :label="__('admin.product_name')" 
                            :placeholder="__('admin.product_name')" 
                            :value="$translations['name'] ?? []" 
                            :languages="$languages"
                            :currentLanguageId="$adminLanguage->id"
                            :required="true"
                        />
                        <x-admin.input-field-multilang
                            type="text" 
                            name="slug" 
                            :label="__('admin.friendly_url')" 
                            :placeholder="__('admin.friendly_url')" 
                            :value="$translations['slug'] ?? []" 
                            :languages="$languages"
                            :currentLanguageId="$adminLanguage->id"
                            :required="true"
                        />
                        <x-admin.textarea-field-multilang
                            name="description" 
                            :label="__('admin.description')" 
                            :placeholder="__('admin.description')"
                            :value="$translations['description'] ?? []" 
                            :languages="$languages"
                            :currentLanguageId="$adminLanguage->id"
                            :autoloadRte="true"
                            :rteHeight="320"
                        />
                        <x-admin.input-field-multilang
                            type="text" 
                            name="meta_title" 
                            :label="__('admin.meta_tag_title')" 
                            :placeholder="__('admin.meta_tag_title')"
                            :value="$translations['meta_title'] ?? []" 
                            :languages="$languages"
                            :currentLanguageId="$adminLanguage->id"
                            :required="true"
                        />
                        <x-admin.textarea-field-multilang
                            name="meta_description" 
                            :label="__('admin.meta_tag_description')" 
                            :placeholder="__('admin.meta_tag_description')"
                            rows="3"
                            :value="$translations['meta_description'] ?? []" 
                            :languages="$languages"
                            :currentLanguageId="$adminLanguage->id"
                            :required="false"
                        />
                    </div>
                    <div class="tab-pane" id="tab-data">
                        <x-admin.input-field type="text" name="reference" :label="__('admin.product_reference')" :placeholder="__('admin.product_reference')" :value="data_get($product ?? null, 'reference', '')" :required="true"/>
                        <x-admin.input-field type="text" name="price" :label="__('admin.price')" :placeholder="__('admin.price')" :value="data_get($product ?? null, 'price', '')"/>
                        <x-admin.input-field type="number" name="quantity" :label="__('admin.quantity')" :placeholder="__('admin.quantity')" :value="data_get($product ?? null, 'quantity', '1')"/>
                        <x-admin.select-field name="stock_status" :label="__('admin.out_of_stock_status')" :options="$stockStatusOptions" :value="data_get($product ?? null, 'stock_status_id', '')"/>
                        <x-admin.input-field type="number" name="sort_order" :label="__('admin.sort_order')" :value="data_get($product ?? null, 'sort_order', '1')"/>
                        <x-admin.switch-field name="status" :label="__('admin.status')" :value="data_get($product ?? null, 'status', false)"/>
                    </div>
                    <div class="tab-pane" id="tab-links">
                        <x-admin.checkbox-list 
                            :items="$categories" 
                            :selectedItems="old('categories', $selectedCategories ?? [])"
                            :name="'categories[]'"
                            :label="__('admin.categories')"
                            :nameField="'name'"
                            :emptyMessage="__('admin.no_categories')"
                            itemPrefix="category"
                        />
                    </div>
                    <div class="tab-pane" id="tab-features">
                        <div class="mb-3">
                            <div
                                id="features-container"
                                data-features='@json($featureOptions)'
                                data-old-features='@json(old('features', $productFeatures ?? []))'
                                data-errors='@json($errors->getMessages())'
                                data-label-feature="{{ __('admin.feature') }}"
                                data-label-previous-feature-value="{{ __('admin.pre_defined_value') }}"
                                data-select-feature-text="{{ __('admin.select_feature') }}"
                                data-select-value-text="{{ __('admin.select_value') }}"
                            >
                                <!-- Features will be added here dynamically -->
                            </div>
                            <button 
                                type="button" 
                                class="btn btn-primary" 
                                id="add-feature-btn"
                            >
                                <i class="fa-solid fa-plus"></i>
                                {{ __('admin.add_feature') }}
                            </button>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-image">
                        <x-admin.image-upload-field
                            name="image"
                            :label="__('admin.product_cover_image')"
                            :current-path="data_get($product ?? null, 'image_path')"
                            :note="__('admin.product_cover_image_help')"
                        />
                    </div>
                </div>
                <x-admin.form-actions 
                    :isEdit="isset($product)"
                    :backRoute="route('admin.product.index')"
                />
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/admin/product.js') }}"></script>
    @endpush
@endsection
