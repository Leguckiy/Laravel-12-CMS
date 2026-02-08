<?php

namespace App\Support;

use App\Models\FeatureValue;
use Illuminate\Http\Request;

final class CategoryFilterState
{
    public const Q_TYPE_STOCK = 'stock';

    public const Q_TYPE_PRICE = 'price';

    public const Q_TYPE_FEATURE = 'feature';

    public const SORT_OPTIONS = [
        'position',
        'name_asc',
        'name_desc',
        'price_asc',
        'price_desc',
        'reference_asc',
        'reference_desc',
    ];

    public readonly array $featureValueIds;

    /** @var array<int, array<int, int>> [featureId => [valueId, ...]] */
    public readonly array $featureValueGroups;

    /** Price in display currency (as in URL), null when not in request */
    public readonly ?float $priceMin;

    /** Price in display currency (as in URL), null when not in request */
    public readonly ?float $priceMax;

    public readonly bool $inStock;

    public readonly string $sort;

    private string $originalQString;

    private bool $hadLegacyParams;

    private bool $hadInvalidFormat;

    /**
     * @param  array<int, int>  $featureValueIds
     * @param  array<int, array<int, int>>  $featureValueGroups
     */
    private function __construct(
        array $featureValueIds,
        array $featureValueGroups,
        ?float $priceMin,
        ?float $priceMax,
        bool $inStock,
        string $sort,
        string $originalQString,
        bool $hadLegacyParams,
        bool $hadInvalidFormat
    ) {
        $this->featureValueIds = $featureValueIds;
        $this->featureValueGroups = $featureValueGroups;
        $this->priceMin = $priceMin;
        $this->priceMax = $priceMax;
        $this->inStock = $inStock;
        $this->sort = $sort;
        $this->originalQString = $originalQString;
        $this->hadLegacyParams = $hadLegacyParams;
        $this->hadInvalidFormat = $hadInvalidFormat;
    }

    public static function fromRequest(Request $request): self
    {
        $q = $request->query('q');
        $originalQString = is_string($q) ? trim($q, '/') : '';
        $hadLegacyParams = false;
        $hadInvalidFormat = is_array($q);
        foreach (array_keys($request->query()) as $key) {
            if (is_numeric($key)) {
                $hadLegacyParams = true;
                break;
            }
        }

        $legacyIds = self::normalizeFeatureValueIds($request->query('feature_value'));
        if ($legacyIds !== []) {
            $hadLegacyParams = true;
        }

        $parsed = [
            'featureValueIds' => [],
            'featureValueGroups' => [],
            'priceMin' => null,
            'priceMax' => null,
            'inStock' => false,
        ];
        if (is_string($q) && $q !== '') {
            $parsed = self::parseQString($q);
            $featureValueGroups = $parsed['featureValueGroups'];
            if ($legacyIds !== []) {
                $legacyGroups = FeatureValue::getGroupsFromIds($legacyIds);
                foreach ($legacyGroups as $featureId => $valueIds) {
                    $featureValueGroups[$featureId] = array_merge(
                        $featureValueGroups[$featureId] ?? [],
                        $valueIds
                    );
                }
            }
        } else {
            $idsFromNumeric = self::parseFeatureValueIdsFromNumericKeys($request->query());
            if ($idsFromNumeric !== []) {
                $hadLegacyParams = true;
            }
            $legacyIds = array_merge($legacyIds, $idsFromNumeric);
            $featureValueGroups = $legacyIds !== [] ? FeatureValue::getGroupsFromIds($legacyIds) : [];
        }

        $featureValueIds = self::flattenFeatureValueGroups($featureValueGroups);

        $sort = $request->query('sort', self::SORT_OPTIONS[0]);
        if (! in_array($sort, self::SORT_OPTIONS, true)) {
            $sort = self::SORT_OPTIONS[0];
        }

        return new self(
            featureValueIds: array_values(array_unique(array_filter($featureValueIds))),
            featureValueGroups: $featureValueGroups,
            priceMin: $parsed['priceMin'],
            priceMax: $parsed['priceMax'],
            inStock: $parsed['inStock'],
            sort: $sort,
            originalQString: $originalQString,
            hadLegacyParams: $hadLegacyParams,
            hadInvalidFormat: $hadInvalidFormat
        );
    }

    /**
     * Whether the request needs redirect to canonical URL.
     * Caller provides canonicalQString built by CategoryFilterUrlBuilder.
     */
    public function needsCanonicalRedirect(string $canonicalQString): bool
    {
        if ($this->hadLegacyParams || $this->hadInvalidFormat) {
            return true;
        }

        return $this->originalQString !== $canonicalQString;
    }

    public function hasPriceFilter(): bool
    {
        return $this->priceMin !== null && $this->priceMax !== null;
    }

    /**
     * Whether to add sort param to URL (preserve when user had it, or add when non-default).
     */
    public static function shouldIncludeSortInUrl(string $sort, bool $requestHasSort): bool
    {
        return ($sort !== self::SORT_OPTIONS[0] || $requestHasSort)
            && in_array($sort, self::SORT_OPTIONS, true);
    }

    /**
     * @return array{featureValueIds: array<int, int>, featureValueGroups: array<int, array<int, int>>, priceMin: float|null, priceMax: float|null, inStock: bool}
     */
    private static function parseQString(string $q): array
    {
        $featureValueGroups = [];
        $featureValueIds = [];
        $priceMin = null;
        $priceMax = null;
        $inStock = false;
        $groups = explode('/', trim($q, '/'));
        foreach ($groups as $group) {
            $parts = explode('-', $group, 2);
            $type = $parts[0] ?? '';
            $rest = $parts[1] ?? '';
            switch ($type) {
                case self::Q_TYPE_STOCK:
                    $inStock = true;
                    break;
                case self::Q_TYPE_PRICE:
                    $range = explode('-', $rest, 2);
                    if (count($range) >= 2 && $range[0] !== '' && $range[1] !== '') {
                        $priceMin = (float) $range[0];
                        $priceMax = (float) $range[1];
                    }
                    break;
                case self::Q_TYPE_FEATURE:
                    $featureParts = explode('-', $rest, 2);
                    if (count($featureParts) >= 2) {
                        $featureId = (int) $featureParts[0];
                        $valueIds = array_map('intval', explode(',', $featureParts[1]));
                        if ($featureId > 0 && $valueIds !== []) {
                            $featureValueGroups[$featureId] = array_merge(
                                $featureValueGroups[$featureId] ?? [],
                                $valueIds
                            );
                            $featureValueIds = array_merge($featureValueIds, $valueIds);
                        }
                    }
                    break;
            }
        }

        return [
            'featureValueIds' => $featureValueIds,
            'featureValueGroups' => $featureValueGroups,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
            'inStock' => $inStock,
        ];
    }

    /**
     * @param  array<int, array<int, int>>  $featureValueGroups
     * @return array<int, int>
     */
    private static function flattenFeatureValueGroups(array $featureValueGroups): array
    {
        $ids = [];
        foreach ($featureValueGroups as $valueIds) {
            $ids = array_merge($ids, $valueIds);
        }

        return $ids;
    }

    /**
     * @param  mixed  $value
     * @return array<int, int>
     */
    private static function normalizeFeatureValueIds(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_map('intval', array_filter($value)));
        }
        if (is_scalar($value) && (string) $value !== '') {
            return [(int) $value];
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<int, int>
     */
    private static function parseFeatureValueIdsFromNumericKeys(array $query): array
    {
        $ids = [];
        foreach ($query as $key => $value) {
            if (! is_numeric($key)) {
                continue;
            }
            if (is_array($value)) {
                $ids = array_merge($ids, array_map('intval', array_filter($value)));
            } elseif (is_scalar($value) && (string) $value !== '') {
                $ids = array_merge($ids, array_map('intval', explode(',', (string) $value)));
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }
}
