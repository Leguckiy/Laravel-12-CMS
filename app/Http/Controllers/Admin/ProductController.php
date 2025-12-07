<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Product;
use App\Models\ProductLang;
use App\Models\StockStatus;
use App\Services\AdminImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'products',
            'route' => 'admin.product.index',
        ],
    ];

    protected string $title = 'products';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currentLanguageId = $this->context->language->id;

        $products = Product::with(['translations' => function ($query) use ($currentLanguageId) {
            $query->where('language_id', $currentLanguageId);
        }])->paginate(10);

        $products->getCollection()->transform(function ($product) {
            $product->name = $this->translation($product->translations)?->name ?? '';

            return $product;
        });

        return view('admin.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.product.form', $this->prepareProductFormData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $nameData = $request->input('name', []);
        $slugData = $request->input('slug', []);
        $descriptionData = $request->input('description', []);
        $metaTitleData = $request->input('meta_title', []);
        $metaDescriptionData = $request->input('meta_description', []);

        $productData = [
            'reference' => $request->input('reference'),
            'quantity' => (int) $request->input('quantity', 0),
            'stock_status_id' => (int) $request->input('stock_status'),
            'image' => $this->handleImageUpload($request),
            'price' => (float) $request->input('price', 0),
            'sort_order' => (int) $request->input('sort_order', 0),
            'status' => (bool) $request->input('status', false),
        ];

        $product = Product::create($productData);

        foreach ($nameData as $languageId => $name) {
            $product->translations()->create([
                'language_id' => (int) $languageId,
                'name' => $name,
                'slug' => $slugData[$languageId] ?? '',
                'description' => $descriptionData[$languageId] ?? null,
                'meta_title' => $metaTitleData[$languageId] ?? null,
                'meta_description' => $metaDescriptionData[$languageId] ?? null,
            ]);
        }

        $this->saveCategories($product, $request->input('categories', []));
        $this->saveFeatures($product, $request->input('features', []));

        return redirect()->route('admin.product.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product->load([
            'stockStatus.translations',
            'categories.translations',
            'features.translations',
            'features.values.translations',
        ]);

        $data = $this->prepareProductFormData($product);

        $stockStatusName = $product->stockStatus
            ? $this->translation($product->stockStatus->translations)?->name
            : null;

        $productCategoryNames = $product->categories->map(function (Category $category) {
            return $this->translation($category->translations)?->name ?? '';
        })->filter()->values()->toArray();

        $featureDetails = $product->features->map(function (Feature $feature) {
            $featureName = $this->translation($feature->translations)?->name ?? '';

            $valueTranslation = $feature->values
                ->map(function ($value) {
                    return [
                        'id' => $value->id,
                        'value' => $this->translation($value->translations)?->value ?? '',
                    ];
                })
                ->firstWhere('id', $feature->pivot?->feature_value_id);

            return [
                'feature_name' => $featureName,
                'feature_value' => $valueTranslation['value'] ?? '',
            ];
        })->values()->toArray();

        return view('admin.product.show', array_merge(
            $data,
            [
                'stockStatusName' => $stockStatusName,
                'productCategoryNames' => $productCategoryNames,
                'featureDetails' => $featureDetails,
            ]
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        return view('admin.product.form', $this->prepareProductFormData($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update([
            'reference' => $request->input('reference'),
            'quantity' => (int) $request->input('quantity', 0),
            'stock_status_id' => (int) $request->input('stock_status'),
            'image' => $this->handleImageUpload($request, $product),
            'price' => (float) $request->input('price', 0),
            'sort_order' => (int) $request->input('sort_order', 0),
            'status' => (bool) $request->input('status', false),
        ]);

        $product->translations()->delete();

        $nameData = $request->input('name', []);
        $slugData = $request->input('slug', []);
        $descriptionData = $request->input('description', []);
        $metaTitleData = $request->input('meta_title', []);
        $metaDescriptionData = $request->input('meta_description', []);

        foreach ($nameData as $languageId => $name) {
            ProductLang::create([
                'product_id' => (int) $product->id,
                'language_id' => (int) $languageId,
                'name' => $name,
                'slug' => $slugData[$languageId] ?? '',
                'description' => $descriptionData[$languageId] ?? null,
                'meta_title' => $metaTitleData[$languageId] ?? null,
                'meta_description' => $metaDescriptionData[$languageId] ?? null,
            ]);
        }

        $this->saveCategories($product, $request->input('categories', []));
        $this->saveFeatures($product, $request->input('features', []));

        return redirect()->route('admin.product.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.product.index')->with('success', __('admin.deleted_successfully'));
    }

    /**
     * Handle product image upload, optionally removing the previous image and resizing the new one.
     */
    private function handleImageUpload(ProductRequest $request, ?Product $product = null): ?string
    {
        $uploader = new AdminImageUploader;
        $currentFilename = $product?->image;
        $currentPath = $product?->image_path ?? ($currentFilename ? Product::IMAGE_DIRECTORY.'/'.$currentFilename : null);

        if ($request->boolean('image_remove')) {
            $this->deleteProductImage($uploader, $currentPath);
            $currentFilename = null;
            $currentPath = null;
        }

        if (! $request->hasFile('image')) {
            return $currentFilename;
        }

        $this->deleteProductImage($uploader, $currentPath);

        $slug = data_get($request->input('slug', []), $this->context->language->id)
            ?? collect($request->input('slug', []))->first()
            ?? 'product';

        return $uploader->uploadImage(
            Str::slug((string) $slug),
            Product::IMAGE_DIRECTORY,
            $request->file('image'),
            (int) config('image_sizes.product.width', 800),
            (int) config('image_sizes.product.height', 800)
        );
    }

    /**
     * Save product categories by syncing them.
     */
    private function saveCategories(Product $product, array $categories): void
    {
        $categoryIds = array_filter(
            array_map('intval', $categories),
            static fn (int $value): bool => $value > 0
        );

        $product->categories()->sync($categoryIds);
    }

    /**
     * Save product features by detaching all existing and attaching new ones.
     */
    private function saveFeatures(Product $product, array $features): void
    {
        $product->features()->detach();
        foreach ($features as $featureData) {
            $featureId = $featureData['feature_id'] ?? null;
            $featureValueId = $featureData['feature_value_id'] ?? null;

            if ($featureId && $featureValueId) {
                $product->features()->attach((int) $featureId, [
                    'feature_value_id' => (int) $featureValueId,
                ]);
            }
        }
    }

    /**
     * Prepare form data for product create/edit forms.
     */
    private function prepareProductFormData(?Product $product = null): array
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->context->language->id;

        $stockStatusOptions = $this->getStockStatusOptions($currentLanguageId);

        $categories = Category::with(['translations' => function ($query) use ($currentLanguageId) {
            $query->where('language_id', $currentLanguageId);
        }])->get();

        $featureOptions = $this->getFeatureOptions($currentLanguageId);

        $translations = [
            'name' => [],
            'slug' => [],
            'description' => [],
            'meta_title' => [],
            'meta_description' => [],
        ];

        $selectedCategories = [];
        $productFeatures = [];

        if ($product) {
            $product->loadMissing([
                'translations',
                'categories',
                'features',
                'features.values',
            ]);

            $fields = array_keys($translations);

            foreach ($fields as $field) {
                $translations[$field] = $product->translations->pluck($field, 'language_id')->toArray();
            }

            $selectedCategories = $product->categories->pluck('id')->toArray();

            $productFeatures = $product->features->map(function (Feature $feature) {
                return [
                    'feature_id' => $feature->id,
                    'feature_value_id' => $feature->pivot?->feature_value_id,
                ];
            })->values()->toArray();
        }

        return compact(
            'product',
            'languages',
            'translations',
            'stockStatusOptions',
            'categories',
            'featureOptions',
            'selectedCategories',
            'productFeatures'
        );
    }

    /**
     * Get stock status options formatted for select field.
     */
    private function getStockStatusOptions(int $currentLanguageId): array
    {
        return StockStatus::with('translations')->get()->map(function (StockStatus $status) use ($currentLanguageId) {
            return [
                'id' => $status->id,
                'name' => $this->translation($status->translations, $currentLanguageId)?->name ?? '',
            ];
        })->values()->toArray();
    }

    /**
     * Get feature options with their values formatted for form.
     */
    private function getFeatureOptions(int $currentLanguageId): array
    {
        return Feature::with([
            'translations',
            'values.translations',
        ])->get()->map(function (Feature $feature) use ($currentLanguageId) {
            return [
                'id' => $feature->id,
                'name' => $this->translation($feature->translations, $currentLanguageId)?->name ?? '',
                'values' => $feature->values->map(function ($value) use ($currentLanguageId) {
                    return [
                        'id' => $value->id,
                        'value' => $this->translation($value->translations, $currentLanguageId)?->value ?? '',
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();
    }

    /**
     * Delete product image from storage.
     */
    private function deleteProductImage(AdminImageUploader $uploader, ?string $path): void
    {
        if ($path) {
            $uploader->delete($path);
        }
    }
}
