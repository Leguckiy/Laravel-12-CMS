<?php

namespace App\Services;

use App\DTO\CategoryFilterData;
use App\Support\CategoryFilterUrlBuilder;

class CategoryActiveFilterBuilder
{
    /**
     * Build list of active filters for display (PrestaShop-style) with URL to remove each.
     * Controller provides priceFilterLabel (formatted) and urlBuilder. No Currency in this class.
     *
     * @param  array<int, array{feature: array{id: int, name: string}, values: array<int, array{id: int, name: string, count: int}>}>  $featuresForFilter
     * @return array<int, array{label: string, url: string}>
     */
    public function build(
        CategoryFilterData $filterData,
        array $featuresForFilter,
        string $priceFilterLabel,
        ?float $priceMinDisplay,
        ?float $priceMaxDisplay,
        bool $includeSortInUrl,
        callable $urlBuilder
    ): array {
        $active = [];
        $featureValueGroups = $filterData->featureValueGroups;
        $sortedFeatureIds = $filterData->sortedFeatureIds;
        $priceDisplay = $filterData->hasPriceFilter ? [$priceMinDisplay, $priceMaxDisplay] : [null, null];

        if ($filterData->inStock) {
            $params = CategoryFilterUrlBuilder::buildParams(
                $featureValueGroups,
                $sortedFeatureIds,
                false,
                $priceDisplay[0],
                $priceDisplay[1],
                $filterData->hasPriceFilter,
                $filterData->sort,
                $includeSortInUrl
            );
            $active[] = [
                'label' => __('front/general.filter_in_stock'),
                'url' => $urlBuilder($params),
            ];
        }

        if ($filterData->hasPriceFilter) {
            $params = CategoryFilterUrlBuilder::buildParams(
                $featureValueGroups,
                $sortedFeatureIds,
                $filterData->inStock,
                null,
                null,
                false,
                $filterData->sort,
                $includeSortInUrl
            );
            $active[] = [
                'label' => __('front/general.filter_price') . ': ' . $priceFilterLabel,
                'url' => $urlBuilder($params),
            ];
        }

        foreach ($featuresForFilter as $block) {
            $featureName = $block['feature']['name'];
            foreach ($block['values'] as $fv) {
                if (! in_array($fv['id'], $filterData->featureValueIds, true)) {
                    continue;
                }
                $reducedGroups = $this->reduceFeatureValueGroups($featureValueGroups, $fv['id']);
                $params = CategoryFilterUrlBuilder::buildParams(
                    $reducedGroups,
                    $this->getSortedFeatureIdsForGroups($reducedGroups, $sortedFeatureIds),
                    $filterData->inStock,
                    $priceDisplay[0],
                    $priceDisplay[1],
                    $filterData->hasPriceFilter,
                    $filterData->sort,
                    $includeSortInUrl
                );
                $active[] = [
                    'label' => $featureName . ': ' . $fv['name'],
                    'url' => $urlBuilder($params),
                ];
            }
        }

        return $active;
    }

    /**
     * @param  array<int, array<int, int>>  $featureValueGroups
     * @return array<int, array<int, int>>
     */
    private function reduceFeatureValueGroups(array $featureValueGroups, int $removeValueId): array
    {
        $reduced = [];
        foreach ($featureValueGroups as $featureId => $valueIds) {
            $remaining = array_values(array_filter($valueIds, fn ($id) => $id !== $removeValueId));
            if ($remaining !== []) {
                $reduced[$featureId] = $remaining;
            }
        }

        return $reduced;
    }

    /**
     * Get feature IDs from groups in display order (from full sorted list, no DB query).
     *
     * @param  array<int, array<int, int>>  $featureValueGroups
     * @param  array<int, int>  $sortedFeatureIds
     * @return array<int, int>
     */
    private function getSortedFeatureIdsForGroups(array $featureValueGroups, array $sortedFeatureIds): array
    {
        $groupFeatureIds = array_keys($featureValueGroups);

        return array_values(array_intersect($sortedFeatureIds, $groupFeatureIds));
    }
}
