<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;
use App\Models\Category;
use App\Services\CategoryActiveFilterBuilder;
use App\Services\CategoryFilterService;
use App\Services\CategoryProductService;
use App\Support\CategoryFilterState;
use App\Support\CategoryFilterUrlBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends FrontController
{
    private const PRODUCTS_PER_PAGE = 12;

    /**
     * Display the category by slug for the current language.
     *
     * @return View|RedirectResponse
     */
    public function show(
        CategoryProductService $categoryProductService,
        CategoryFilterService $filterService,
        CategoryActiveFilterBuilder $activeFilterBuilder
    ): View|RedirectResponse {
        $slug = request()->route('slug');
        $languageId = $this->context->language->id;

        $category = Category::findBySlug($slug, $languageId);
        $translation = $category->translation($languageId);
        $currency = $this->context->currency;

        $filterState = CategoryFilterState::fromRequest(request());
        $priceMinBase = $filterState->hasPriceFilter() ? $currency->convertToBase($filterState->priceMin) : null;
        $priceMaxBase = $filterState->hasPriceFilter() ? $currency->convertToBase($filterState->priceMax) : null;
        $filterData = $filterService->buildFilterData(
            $category,
            $filterState,
            $priceMinBase,
            $priceMaxBase
        );

        $includeSortInUrl = CategoryFilterState::shouldIncludeSortInUrl($filterData->sort, request()->has('sort'));

        $canonicalQString = CategoryFilterUrlBuilder::buildQString(
            $filterData->featureValueGroups,
            $filterData->sortedFeatureIds,
            $filterState->inStock,
            $filterState->hasPriceFilter(),
            $filterState->priceMin,
            $filterState->priceMax
        );
        if ($filterState->needsCanonicalRedirect($canonicalQString)) {
            $params = CategoryFilterUrlBuilder::buildParams(
                $filterData->featureValueGroups,
                $filterData->sortedFeatureIds,
                $filterState->inStock,
                $filterState->priceMin,
                $filterState->priceMax,
                $filterState->hasPriceFilter(),
                $filterState->sort,
                $includeSortInUrl
            );

            return redirect()->to(CategoryFilterUrlBuilder::buildFullUrl(request()->url(), $params));
        }

        $products = $categoryProductService->getPaginatedProducts(
            $category,
            $filterData,
            $languageId,
            self::PRODUCTS_PER_PAGE
        );

        $inStockCount = $categoryProductService->getInStockCount($category, $filterData);
        $featuresForFilter = $categoryProductService->getFeaturesForFilter($category, $languageId, $filterData);

        $filterPriceMin = $currency->convertFromBase($filterData->priceMinBase);
        $filterPriceMax = $currency->convertFromBase($filterData->priceMaxBase);
        $priceRangeMin = $currency->convertFromBase($filterData->priceRangeMin);
        $priceRangeMax = $currency->convertFromBase($filterData->priceRangeMax);

        $this->setLanguageUrlsFromTranslations($category->translations, $this->context->getLanguages(), 'front.category.show');

        $priceFilterLabel = $filterData->hasPriceFilter
            ? $currency->formatPrice($filterPriceMin) . ' - ' . $currency->formatPrice($filterPriceMax)
            : '';
        $activeFilters = $activeFilterBuilder->build(
            $filterData,
            $featuresForFilter,
            $priceFilterLabel,
            $filterData->hasPriceFilter ? $filterPriceMin : null,
            $filterData->hasPriceFilter ? $filterPriceMax : null,
            $includeSortInUrl,
            fn (array $params) => CategoryFilterUrlBuilder::buildFullUrl(request()->url(), $params)
        );

        $filterParams = CategoryFilterUrlBuilder::buildParams(
            $filterData->featureValueGroups,
            $filterData->sortedFeatureIds,
            $filterData->inStock,
            $filterData->hasPriceFilter ? $filterPriceMin : null,
            $filterData->hasPriceFilter ? $filterPriceMax : null,
            $filterData->hasPriceFilter,
            $filterData->sort,
            $includeSortInUrl
        );

        $viewData = [
            'category' => $category,
            'title' => $translation->name,
            'description' => $translation->description,
            'metaTitle' => $translation->meta_title ?? $translation->name,
            'metaDescription' => $translation->meta_description,
            'products' => $products,
            'currentSort' => $filterData->sort,
            'sortOptions' => array_map(
                fn (string $value) => ['value' => $value, 'label' => __('front/general.sort_' . $value)],
                CategoryFilterState::SORT_OPTIONS
            ),
            'filterInStock' => $filterData->inStock,
            'filterPriceMin' => $filterPriceMin,
            'filterPriceMax' => $filterPriceMax,
            'priceRangeMin' => $priceRangeMin,
            'priceRangeMax' => $priceRangeMax,
            'currency' => $currency,
            'inStockCount' => $inStockCount,
            'featuresForFilter' => $featuresForFilter,
            'filterFeatureValueIds' => $filterData->featureValueIds,
            'activeFilters' => $activeFilters,
            'filterParams' => $filterParams,
            'sortInUrl' => request()->has('sort'),
            'hasPriceFilter' => $filterData->hasPriceFilter,
            'clearFiltersUrl' => CategoryFilterUrlBuilder::buildClearFiltersUrl(request()->url(), $filterData->sort, $includeSortInUrl),
        ];

        return view('front.category.show', $viewData);
    }
}
