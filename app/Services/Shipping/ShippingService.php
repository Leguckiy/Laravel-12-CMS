<?php

namespace App\Services\Shipping;

use App\Contracts\Shipping\ShippingMethodInterface;
use App\Models\Cart;
use App\Models\Currency;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\App;

class ShippingService
{
    /**
     * Get shipping methods available for the given cart and country.
     *
     * @return array<int, array{id: string, name: string, cost: float, formatted: string}>
     */
    public function getAvailableMethodsForCart(Cart $cart, int $countryId, Currency $currency): array
    {
        $drivers = config('shipping.drivers', []);
        $models = ShippingMethod::query()
            ->where('status', true)
            ->whereIn('code', array_keys($drivers))
            ->orderBy('sort_order')
            ->get();

        $result = [];
        foreach ($models as $model) {
            $driverClass = $drivers[$model->code] ?? null;
            if ($driverClass === null) {
                continue;
            }

            $driver = $this->resolveDriver($driverClass, $model);
            if (! $driver instanceof ShippingMethodInterface || ! $driver->supports($cart, $countryId)) {
                continue;
            }

            $cost = $driver->getCostForCart($cart);
            $result[] = [
                'id' => $model->code,
                'name' => __($driver->getTitle()),
                'cost' => $cost,
                'formatted' => $currency->formatPriceFromBase((string) $cost),
            ];
        }

        return $result;
    }

    /**
     * Get shipping methods available for a manual order described by raw items and country.
     * Used in admin order form where we operate on a manual order rather than a session cart.
     *
     * @param array<int, array{price: float|int|string, quantity: int|string}> $items
     * @return array<int, array{id: string, name: string, cost: float, formatted: string}>
     */
    public function getAvailableMethodsForItems(array $items, int $countryId, Currency $currency): array
    {
        $drivers = config('shipping.drivers', []);
        $models = ShippingMethod::query()
            ->where('status', true)
            ->whereIn('code', array_keys($drivers))
            ->orderBy('sort_order')
            ->get();

        $result = [];

        foreach ($models as $model) {
            $driverClass = $drivers[$model->code] ?? null;
            if ($driverClass === null) {
                continue;
            }

            $driver = $this->resolveDriver($driverClass, $model);
            if (! $driver instanceof ShippingMethodInterface || ! $driver->supportsItems($items, $countryId)) {
                continue;
            }

            $cost = $driver->getCostForItems($items);

            $result[] = [
                'id' => $model->code,
                'name' => __($driver->getTitle()),
                'cost' => $cost,
                'formatted' => $currency->formatPriceFromBase((string) $cost),
            ];
        }

        return $result;
    }

    /**
     * Get translated title for a shipping method by code (current locale). No DB query.
     * Lang key convention: admin.shipping_method_{code}
     */
    public function getMethodTitle(string $code): string
    {
        return __('admin.shipping_method_' . $code);
    }

    private function resolveDriver(string $driverClass, ShippingMethod $model): ShippingMethodInterface
    {
        return App::make($driverClass, ['model' => $model]);
    }
}
