<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductSearchService
{
    /**
     * Search products by translated name or reference.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, int $languageId, int $limit = 10): array
    {
        $q = trim($query);

        if ($q === '') {
            return [];
        }

        $builder = Product::query()
            ->where('status', true)
            ->with(['translations' => function ($qTrans) use ($languageId): void {
                $qTrans->where('language_id', $languageId);
            }])
            ->orderBy('reference')
            ->limit($limit);

        $builder->where(function ($productQuery) use ($q, $languageId): void {
            $like = '%' . Str::lower($q) . '%';

            $productQuery
                ->whereRaw('LOWER(reference) LIKE ?', [$like])
                ->orWhereHas('translations', function ($qTrans) use ($like, $languageId): void {
                    $qTrans
                        ->where('language_id', $languageId)
                        ->whereRaw('LOWER(name) LIKE ?', [$like]);
                });
        });

        $products = $builder->get();

        return $products->map(function (Product $product) use ($languageId): array {
            $translation = $product->translations->first();

            return [
                'id' => $product->id,
                'name' => $translation?->name ?? '',
                'reference' => $product->reference,
                'price' => (float) $product->price,
                'quantity' => (int) $product->quantity,
            ];
        })->values()->all();
    }
}
