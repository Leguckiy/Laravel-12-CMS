<?php

namespace App\Services\Shipping;

use App\Contracts\Shipping\ShippingMethodInterface;
use App\Models\Cart;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\App;

class ShippingService
{
    /**
     * @return array<int, array{code: string, title: string, cost: float, sort_order: int}>
     */
    public function getAvailableMethods(Cart $cart, int $countryId): array
    {
        $drivers = config('shipping.drivers', []);
        $methodCodes = array_keys($drivers);

        $models = ShippingMethod::query()
            ->whereIn('code', $methodCodes)
            ->get()
            ->keyBy('code');

        $result = [];

        foreach ($methodCodes as $code) {
            $model = $models->get($code);
            if ($model === null) {
                continue;
            }

            $driverClass = $drivers[$code] ?? null;
            if ($driverClass === null) {
                continue;
            }

            $driver = $this->resolveDriver($driverClass, $model);
            if (! $driver instanceof ShippingMethodInterface || ! $driver->supports($cart, $countryId)) {
                continue;
            }

            $result[] = [
                'code' => $model->code,
                'title' => __($driver->getTitle()),
                'cost' => $driver->getCost($cart),
                'sort_order' => (int) $model->sort_order,
            ];
        }

        usort($result, fn (array $a, array $b) => $a['sort_order'] <=> $b['sort_order']);

        return array_values($result);
    }

    private function resolveDriver(string $driverClass, ShippingMethod $model): ShippingMethodInterface
    {
        return App::make($driverClass, ['model' => $model]);
    }
}
