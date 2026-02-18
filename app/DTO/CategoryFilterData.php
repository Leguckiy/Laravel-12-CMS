<?php

namespace App\DTO;

readonly class CategoryFilterData
{
    /**
     * @param  array<int, int>  $featureValueIds
     * @param  array<int, array<int, int>>  $featureValueGroups
     * @param  array<int, int>  $sortedFeatureIds  Feature IDs in display order
     */
    public function __construct(
        public array $featureValueIds,
        public array $featureValueGroups,
        public array $sortedFeatureIds,
        public bool $inStock,
        public float $priceMinBase,
        public float $priceMaxBase,
        public float $priceRangeMin,
        public float $priceRangeMax,
        public bool $hasPriceFilter,
        public string $sort,
    ) {}
}
