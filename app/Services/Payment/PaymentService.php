<?php

namespace App\Services\Payment;

use App\Contracts\Payment\PaymentMethodInterface;
use App\Models\Cart;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\App;

class PaymentService
{
    /**
     * @return array<int, array{code: string, title: string, sort_order: int, order_status_id: int|null, config: array}>
     */
    public function getAvailableMethods(Cart $cart, int $countryId): array
    {
        $drivers = config('payment.drivers');
        $methodCodes = array_keys($drivers);

        $models = PaymentMethod::query()
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
            if (! $driver instanceof PaymentMethodInterface || ! $driver->supports($cart, $countryId)) {
                continue;
            }

            $result[] = [
                'code' => $model->code,
                'title' => __($driver->getTitle()),
                'sort_order' => (int) $model->sort_order,
                'order_status_id' => (int) $model->config['order_status_id'],
                'config' => $model->config,
            ];
        }

        usort($result, fn (array $a, array $b) => $a['sort_order'] <=> $b['sort_order']);

        return array_values($result);
    }

    private function resolveDriver(string $driverClass, PaymentMethod $model): PaymentMethodInterface
    {
        return App::make($driverClass, ['model' => $model]);
    }
}
