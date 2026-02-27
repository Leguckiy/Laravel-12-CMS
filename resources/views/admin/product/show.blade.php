@extends('layouts.admin')

@section('page-actions')
    <x-admin.detail-actions
        :id="$product->id"
        baseName="product"
        :itemName="__('admin.product')"
        :confirmText="__('admin.delete_confirm', ['item' => __('admin.product')])"
    />
@endsection

@section('content')
    <x-admin.delete-form />

    <div class="card">
        <div class="card-header">
            <i class="fa-solid fa-eye"></i>
            <span>{{ __('admin.product') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <x-admin.text-field-multilang 
                    :label="__('admin.product_name')"
                    :value="$translations['name'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="product_name"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang 
                    :label="__('admin.friendly_url')"
                    :value="$translations['slug'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="slug"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang 
                    :label="__('admin.description')"
                    :value="$translations['description'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="description"
                    :allowHtml="true"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang 
                    :label="__('admin.meta_tag_title')"
                    :value="$translations['meta_title'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="meta_title"
                />
            </div>
            <div class="row mt-1">
                <x-admin.text-field-multilang 
                    :label="__('admin.meta_tag_description')"
                    :value="$translations['meta_description'] ?? []"
                    :languages="$languages"
                    :currentLanguageId="$adminLanguage->id"
                    fieldName="meta_description"
                />
            </div>
            <div class="row mt-1">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.product_reference') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $product->reference }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.price') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ number_format((float) $product->price, 2) }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.quantity') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $product->quantity }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.out_of_stock_status') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $stockStatusName ?: '—' }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.categories') }}:</span>
                </div>
                <div class="col-sm-9">
                    @if (!empty($productCategoryNames))
                        <ul class="mb-0 ps-3">
                            @foreach ($productCategoryNames as $categoryName)
                                <li>{{ $categoryName }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">{{ __('admin.no_categories') }}</span>
                    @endif
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.features') }}:</span>
                </div>
                <div class="col-sm-9">
                    @if (!empty($featureDetails))
                        <ul class="mb-0 ps-3">
                            @foreach ($featureDetails as $feature)
                                <li>{{ $feature['feature_name'] }}: {{ $feature['feature_value'] }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">{{ __('admin.no_items') }}</span>
                    @endif
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.image') }}:</span>
                </div>
                <div class="col-sm-9">
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $translations['name'][$adminLanguage->id] ?? '' }}" class="img-fluid rounded border" style="max-width: 400px; max-height: 400px;">
                    @else
                        <span class="text-muted">{{ __('admin.image_placeholder') }}</span>
                    @endif
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.sort_order') }}:</span>
                </div>
                <div class="col-sm-9">
                    <span>{{ $product->sort_order }}</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3 fw-bold">
                    <span>{{ __('admin.status') }}:</span>
                </div>
                <div class="col-sm-9">
                    <x-admin.status-badge :status="$product->status" />
                </div>
            </div>
        </div>
    </div>
@endsection
