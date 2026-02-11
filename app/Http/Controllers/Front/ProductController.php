<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends FrontController
{
    public function show(): View
    {
        $slug = request()->route('slug');
        $languageId = $this->context->language->id;

        $product = Product::findForFrontShow($slug, $languageId);
        $translation = $product->translations->firstWhere('language_id', $languageId);
        $currency = $this->context->currency;

        $this->setLanguageUrlsFromTranslations($product->translations, $this->context->getLanguages(), 'front.product.show');

        $this->setBreadcrumbs($this->buildProductBreadcrumbs($product));

        $characteristics = $this->buildProductCharacteristics($product);

        return view('front.product.show', [
            'product' => $product,
            'translation' => $translation,
            'description' => $translation->description,
            'metaTitle' => $translation->meta_title ?? $translation->name,
            'metaDescription' => $translation->meta_description,
            'currency' => $currency,
            'characteristics' => $characteristics,
        ]);
    }

    /**
     * @return array<int, array{label: string, url: string|null}>
     */
    private function buildProductBreadcrumbs(Product $product): array
    {
        $breadcrumbs = [
            ['label' => __('front/general.breadcrumb_home'), 'url' => route('front.home', ['lang' => $this->context->language->code])],
        ];

        $category = $product->categories->first();
        $categoryTranslation = $category->translations->first();
        if ($categoryTranslation) {
            $breadcrumbs[] = [
                'label' => $categoryTranslation->name,
                'url' => route('front.category.show', ['lang' => $this->context->language->code, 'slug' => $categoryTranslation->slug]),
            ];
        }

        $breadcrumbs[] = [
            'label' => $product->translations->firstWhere('language_id', $this->context->language->id)?->name ?? $product->reference ?? '#'.$product->id,
            'url' => null,
        ];

        return $breadcrumbs;
    }

    /**
     * @return array<int, array{name: string, values: array<int, string>}>
     */
    private function buildProductCharacteristics(Product $product): array
    {
        $grouped = [];

        foreach ($product->features as $feature) {
            $featureName = $feature->translations->first()->name;
            $valueId = $feature->pivot->feature_value_id;
            $value = $feature->values->firstWhere('id', $valueId);
            $valueName = $value->translations->first()->value;

            if (! isset($grouped[$feature->id])) {
                $grouped[$feature->id] = [
                    'name' => $featureName,
                    'values' => [],
                    'sort_order' => $feature->sort_order,
                ];
            }
            $grouped[$feature->id]['values'][] = $valueName;
        }

        return collect($grouped)
            ->sortBy('sort_order')
            ->map(fn ($item) => [
                'name' => $item['name'],
                'values' => array_values(array_unique($item['values'])),
            ])
            ->values()
            ->toArray();
    }
}
