<?php

namespace App\Services;

use App\DTOs\CategoryFilterData;
use App\Models\Category;
use App\Models\Feature;
use App\Models\FeatureValue;
use App\Support\CategoryFilterState;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CategoryProductService
{
    public function getPaginatedProducts(
        Category $category,
        CategoryFilterData $filters,
        int $languageId,
        int $perPage = 12
    ): LengthAwarePaginator {
        $query = $this->buildBaseQuery($category, $languageId);
        $this->applyFilters($query, $filters);
        $this->applySort($query, $filters->sort, $languageId);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<int, array<int, int>>  $featureValueGroups
     * @return array{min: float, max: float}
     */
    public function getPriceRange(Category $category, bool $inStock, array $featureValueGroups): array
    {
        $query = $this->buildBaseQuery($category);
        $this->applyFiltersForPriceRange($query, $inStock, $featureValueGroups);

        return $this->extractMinMax($query);
    }

    public function getInStockCount(Category $category, CategoryFilterData $filters): int
    {
        $query = $this->buildBaseQuery($category);
        $this->applyFilters($query, $filters);

        return (clone $query)->where('quantity', '>', 0)->count();
    }

    /**
     * @return array<int, array{feature: array{id: int, name: string, sort_order: int}, values: array<int, array{id: int, name: string, count: int, sort_order: int}>}>
     */
    public function getFeaturesForFilter(Category $category, int $languageId, CategoryFilterData $filters): array
    {
        $query = $this->buildBaseQuery($category);
        $this->applyFilters($query, $filters);
        $productIds = $query->pluck('products.id')->toArray();

        if ($productIds === []) {
            return [];
        }

        $valueIds = FeatureValue::query()
            ->whereIn('feature_id', array_keys($filters->featureValueGroups))
            ->pluck('id')
            ->toArray();
        $valueIdsFromCounts = FeatureValue::query()
            ->whereHas('products', fn ($q) => $q->whereIn('product_id', $productIds))
            ->pluck('id')
            ->toArray();
        $valueIds = array_values(array_unique(array_merge($valueIds, $valueIdsFromCounts)));

        if ($valueIds === []) {
            return [];
        }

        $featureValues = FeatureValue::forFilterWithCounts($valueIds, $productIds, $languageId);
        $grouped = [];
        foreach ($featureValues as $fv) {
            $feature = $fv->feature;
            $featureLang = $feature->translations->first();
            $featureName = $featureLang ? $featureLang->name : (string) $feature->id;
            $valueLang = $fv->translations->first();
            $valueName = $valueLang ? $valueLang->value : (string) $fv->id;
            $count = (int) $fv->products_count;

            if (! isset($grouped[$feature->id])) {
                $grouped[$feature->id] = [
                    'feature' => ['id' => $feature->id, 'name' => $featureName, 'sort_order' => $feature->sort_order],
                    'values' => [],
                ];
            }
            $grouped[$feature->id]['values'][] = [
                'id' => $fv->id,
                'name' => $valueName,
                'count' => $count,
                'sort_order' => $fv->sort_order,
            ];
        }

        foreach ($grouped as $fid => $data) {
            usort($grouped[$fid]['values'], fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);
        }
        uasort($grouped, fn ($a, $b) => $a['feature']['sort_order'] <=> $b['feature']['sort_order']);

        return array_values($grouped);
    }

    /**
     * @param  int|null  $languageId  When set, eager loads translations for that language.
     * @return BelongsToMany
     */
    protected function buildBaseQuery(Category $category, ?int $languageId = null): BelongsToMany
    {
        $query = $category->products()->where('status', true);

        if ($languageId !== null) {
            $query->with(['translations' => fn ($q) => $q->where('language_id', $languageId)]);
        }

        return $query;
    }

    /**
     * @param  array<int, array<int, int>>  $featureValueGroups
     */
    protected function applyFiltersForPriceRange(BelongsToMany $query, bool $inStock, array $featureValueGroups): void
    {
        if ($inStock) {
            $query->where('quantity', '>', 0);
        }

        $query->withFeatureValueFilters($featureValueGroups);
    }

    protected function applyFilters(BelongsToMany $query, CategoryFilterData $filters, bool $includePrice = true): void
    {
        $this->applyFiltersForPriceRange($query, $filters->inStock, $filters->featureValueGroups);

        if ($includePrice) {
            $query->where('price', '>=', $filters->priceMinBase)
                ->where('price', '<=', $filters->priceMaxBase);
        }
    }

    /**
     * @return array{min: float, max: float}
     */
    protected function extractMinMax(BelongsToMany $query): array
    {
        $prices = (clone $query)->pluck('products.price')->filter();

        return [
            'min' => $prices->isEmpty() ? 0.0 : (float) $prices->min(),
            'max' => $prices->isEmpty() ? 0.0 : (float) $prices->max(),
        ];
    }

    protected function applySort(BelongsToMany $query, string $sort, int $languageId): void
    {
        if (! in_array($sort, CategoryFilterState::SORT_OPTIONS, true)) {
            $sort = CategoryFilterState::SORT_OPTIONS[0];
        }

        match ($sort) {
            'name_asc' => $query->join('products_lang', function ($join) use ($languageId) {
                $join->on('products.id', '=', 'products_lang.product_id')
                    ->where('products_lang.language_id', '=', $languageId);
            })->orderBy('products_lang.name', 'asc')->select('products.*'),
            'name_desc' => $query->join('products_lang', function ($join) use ($languageId) {
                $join->on('products.id', '=', 'products_lang.product_id')
                    ->where('products_lang.language_id', '=', $languageId);
            })->orderBy('products_lang.name', 'desc')->select('products.*'),
            'price_asc' => $query->orderBy('products.price', 'asc'),
            'price_desc' => $query->orderBy('products.price', 'desc'),
            'reference_asc' => $query->orderBy('products.reference', 'asc'),
            'reference_desc' => $query->orderBy('products.reference', 'desc'),
            default => $query->orderBy('products.sort_order', 'asc'),
        };
    }
}
