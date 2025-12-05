<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Product;
use App\Models\ProductLang;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languageIds = Language::query()
            ->whereIn('code', ['en', 'uk'])
            ->pluck('id', 'code');

        $path = database_path('data/products.json');
        $products = json_decode((string) file_get_contents($path), true) ?? [];

        foreach ($products as $productData) {
            $translations = $productData['translations'] ?? [];
            $featuresData = $productData['features'] ?? [];
            $categoryId = (int) ($productData['category_id'] ?? 1);

            // Convert features keys and values from strings to integers (JSON stores them as strings)
            // Support both single value and array of values for each feature
            $features = [];
            foreach ($featuresData as $featureId => $featureValueData) {
                $featureId = (int) $featureId;
                
                // Handle both single value and array of values
                if (is_array($featureValueData)) {
                    // Multiple values for one feature
                    foreach ($featureValueData as $featureValueId) {
                        $features[] = [
                            'feature_id' => $featureId,
                            'feature_value_id' => (int) $featureValueId,
                        ];
                    }
                } else {
                    // Single value for one feature
                    $features[] = [
                        'feature_id' => $featureId,
                        'feature_value_id' => (int) $featureValueData,
                    ];
                }
            }

            unset($productData['translations'], $productData['features'], $productData['category_id']);

            // Ensure stock_status_id is set to 2 (In Stock) if not provided
            if (! isset($productData['stock_status_id'])) {
                $productData['stock_status_id'] = 2;
            }

            // Create product
            $product = Product::create([
                'reference' => $productData['reference'],
                'price' => $productData['price'],
                'quantity' => $productData['quantity'],
                'stock_status_id' => $productData['stock_status_id'],
                'sort_order' => $productData['sort_order'],
                'status' => $productData['status'],
            ]);

            // Create translations per language
            foreach ($translations as $languageCode => $translation) {
                $languageId = $languageIds[$languageCode] ?? null;

                if ($languageId === null) {
                    continue;
                }

                ProductLang::create([
                    'product_id' => $product->id,
                    'language_id' => $languageId,
                    'name' => $translation['name'],
                    'slug' => $translation['slug'],
                    'description' => $translation['description'] ?? null,
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_description' => $translation['meta_description'] ?? null,
                ]);
            }

            // Attach to category
            $product->categories()->attach($categoryId);

            // Attach features with values (features array contains feature_id and feature_value_id pairs)
            foreach ($features as $featureData) {
                $product->features()->attach($featureData['feature_id'], [
                    'feature_value_id' => $featureData['feature_value_id'],
                ]);
            }
        }
    }
}
