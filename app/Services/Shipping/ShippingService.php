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
    public function getAvailableMethods(Cart $cart, int $countryId, Currency $currency): array
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

            $cost = $driver->getCost($cart);
            $result[] = [
                'id' => $model->code,
                'name' => __($driver->getTitle()),
                'cost' => $cost,
                'formatted' => $currency->formatPriceFromBase((string) $cost),
            ];
        }

        return $result;
    }

    private function resolveDriver(string $driverClass, ShippingMethod $model): ShippingMethodInterface
    {
        return App::make($driverClass, ['model' => $model]);
    }
}
