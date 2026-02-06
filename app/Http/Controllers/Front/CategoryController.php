<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;
use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends FrontController
{
    protected const SORT_OPTIONS = [
        'position',
        'name_asc',
        'name_desc',
        'price_asc',
        'price_desc',
        'reference_asc',
        'reference_desc',
    ];

    /**
     * Display the category by slug for the current language.
     */
    public function show(): View
    {
        $slug = request()->route('slug');
        $languageId = $this->context->language->id;

        $category = Category::query()
            ->where('status', true)
            ->whereHas('translations', function ($query) use ($languageId, $slug) {
                $query->where('language_id', $languageId)
                    ->where('slug', $slug);
            })
            ->with(['translations' => function ($query) use ($languageId) {
                $query->where('language_id', $languageId);
            }])
            ->firstOrFail();

        $translation = $category->translations->first();

        $productsQuery = $category->products()
            ->where('status', true)
            ->with(['translations' => function ($query) use ($languageId) {
                $query->where('language_id', $languageId);
            }]);

        $sort = request()->query('sort', self::SORT_OPTIONS[0]);
        if (! in_array($sort, self::SORT_OPTIONS, true)) {
            $sort = self::SORT_OPTIONS[0];
        }
        $productsQuery = $this->applySort($productsQuery, $sort, $languageId);

        $products = $productsQuery->paginate(12)->withQueryString();

        $currency = $this->context->currency;
        $products->getCollection()->each(function ($product) use ($currency) {
            $product->formattedPrice = $currency->formatPriceFromBase($product->price);
        });

        $category->load(['translations.language']);
        $this->languageUrls = $category->translations->keyBy(fn ($t) => $t->language->code)
            ->map(fn ($t) => route('front.category.show', ['lang' => $t->language->code, 'slug' => $t->slug]))
            ->toArray();

        return view('front.category.show', [
            'category' => $category,
            'title' => $translation->name,
            'description' => $translation->description,
            'metaTitle' => $translation->meta_title ?? $translation->name,
            'metaDescription' => $translation->meta_description,
            'products' => $products,
            'currentSort' => $sort,
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applySort($query, string $sort, int $languageId)
    {
        switch ($sort) {
            case 'name_asc':
                return $query->join('products_lang', function ($join) use ($languageId) {
                    $join->on('products.id', '=', 'products_lang.product_id')
                        ->where('products_lang.language_id', '=', $languageId);
                })->orderBy('products_lang.name', 'asc')->select('products.*');
            case 'name_desc':
                return $query->join('products_lang', function ($join) use ($languageId) {
                    $join->on('products.id', '=', 'products_lang.product_id')
                        ->where('products_lang.language_id', '=', $languageId);
                })->orderBy('products_lang.name', 'desc')->select('products.*');
            case 'price_asc':
                return $query->orderBy('products.price', 'asc');
            case 'price_desc':
                return $query->orderBy('products.price', 'desc');
            case 'reference_asc':
                return $query->orderBy('products.reference', 'asc');
            case 'reference_desc':
                return $query->orderBy('products.reference', 'desc');
            default:
                return $query->orderBy('products.sort_order', 'asc');
        }
    }
}
