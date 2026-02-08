<?php

namespace App\Support;

/**
 * Builds filter URL query params from filter state.
 * Receives featureValueGroups and sortedFeatureIds from infrastructure (controller).
 */
final class CategoryFilterUrlBuilder
{
    /**
     * Build q string: stock-1/price-min-max/feature-id-valueIds.
     * Caller provides featureValueGroups and sortedFeatureIds (from Feature, FeatureValue).
     *
     * @param  array<int, array<int, int>>  $featureValueGroups  [featureId => [valueId, ...]]
     * @param  array<int, int>  $sortedFeatureIds  Feature IDs in display order
     */
    public static function buildQString(
        array $featureValueGroups,
        array $sortedFeatureIds,
        bool $includeStock,
        bool $includePrice,
        ?float $priceMinDisplay,
        ?float $priceMaxDisplay
    ): string {
        $parts = [];
        if ($includeStock) {
            $parts[] = CategoryFilterState::Q_TYPE_STOCK . '-1';
        }
        if ($includePrice && $priceMinDisplay !== null && $priceMaxDisplay !== null) {
            $parts[] = CategoryFilterState::Q_TYPE_PRICE . '-' . (int) round($priceMinDisplay) . '-' . (int) round($priceMaxDisplay);
        }
        foreach ($sortedFeatureIds as $featureId) {
            $valueIds = $featureValueGroups[$featureId] ?? [];
            if ($valueIds !== []) {
                $parts[] = CategoryFilterState::Q_TYPE_FEATURE . '-' . $featureId . '-' . implode(',', $valueIds);
            }
        }

        return implode('/', $parts);
    }

    /**
     * Build full query params for a filter state.
     *
     * @param  array<int, array<int, int>>  $featureValueGroups
     * @param  array<int, int>  $sortedFeatureIds
     * @return array<string, string>
     */
    public static function buildParams(
        array $featureValueGroups,
        array $sortedFeatureIds,
        bool $inStock,
        ?float $priceMinDisplay,
        ?float $priceMaxDisplay,
        bool $includePrice,
        string $sort,
        bool $includeSortInUrl
    ): array {
        $params = [];
        $qString = self::buildQString(
            $featureValueGroups,
            $sortedFeatureIds,
            $inStock,
            $includePrice,
            $priceMinDisplay,
            $priceMaxDisplay
        );
        if ($qString !== '') {
            $params['q'] = $qString;
        }
        if ($includeSortInUrl && in_array($sort, CategoryFilterState::SORT_OPTIONS, true)) {
            $params['sort'] = $sort;
        }

        return $params;
    }

    /**
     * Build full URL with filter query params (readable slash in q param).
     *
     * @param  array<string, string>  $params
     */
    public static function buildFullUrl(string $baseUrl, array $params): string
    {
        $queryString = str_replace(['%2F', '%2C'], ['/', ','], http_build_query($params));

        return $queryString !== '' ? $baseUrl . '?' . $queryString : $baseUrl;
    }

    /**
     * Build URL for "clear filters" link (optionally preserve sort).
     */
    public static function buildClearFiltersUrl(string $baseUrl, string $sort, bool $includeSortInUrl): string
    {
        if ($includeSortInUrl) {
            return $baseUrl . '?sort=' . urlencode($sort);
        }

        return $baseUrl;
    }
}
