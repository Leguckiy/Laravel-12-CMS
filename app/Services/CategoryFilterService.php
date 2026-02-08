<?php

namespace App\Services;

use App\DTOs\CategoryFilterData;
use App\Models\Category;
use App\Models\Feature;
use App\Support\CategoryFilterState;

class CategoryFilterService
{
    public function __construct(
        protected CategoryProductService $categoryProductService
    ) {}

    /**
     * Resolve full filter state from request (filterState) + DB (price range).
     * Controller provides priceMinBase/priceMaxBase when user set price in URL (converted from display).
     */
    public function buildFilterData(
        Category $category,
        CategoryFilterState $filterState,
        ?float $priceMinBaseFromRequest,
        ?float $priceMaxBaseFromRequest
    ): CategoryFilterData {
        $featureValueIds = $filterState->featureValueIds;
        $featureValueGroups = $filterState->featureValueGroups;
        $sortedFeatureIds = Feature::getIdsSorted(array_keys($featureValueGroups))->values()->all();
        $inStock = $filterState->inStock;

        $priceRangeBase = $this->categoryProductService->getPriceRange($category, $inStock, $featureValueGroups);

        if ($filterState->hasPriceFilter() && $priceMinBaseFromRequest !== null && $priceMaxBaseFromRequest !== null) {
            $priceMinBase = $priceMinBaseFromRequest;
            $priceMaxBase = $priceMaxBaseFromRequest;
        } else {
            $priceMinBase = $priceRangeBase['min'];
            $priceMaxBase = $priceRangeBase['max'];
        }

        return new CategoryFilterData(
            featureValueIds: $featureValueIds,
            featureValueGroups: $featureValueGroups,
            sortedFeatureIds: $sortedFeatureIds,
            inStock: $inStock,
            priceMinBase: $priceMinBase,
            priceMaxBase: $priceMaxBase,
            priceRangeMin: $priceRangeBase['min'],
            priceRangeMax: $priceRangeBase['max'],
            hasPriceFilter: $filterState->hasPriceFilter(),
            sort: $filterState->sort,
        );
    }
}
