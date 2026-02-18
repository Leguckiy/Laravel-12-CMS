<?php

namespace App\Services\Shipping;

use App\Contracts\Shipping\ShippingMethodInterface;
use App\Models\Cart;
use App\Models\ShippingMethod;

class FlatRateShippingMethod implements ShippingMethodInterface
{
    public function __construct(
        private readonly ShippingMethod $model
    ) {}

    public function getTitle(): string
    {
        return 'admin.shipping_method_flat_rate';
    }

    public function getCost(Cart $cart): float
    {
        $cost = (float) ($this->model->config['cost']);

        return max(0, $cost);
    }

    public function supports(Cart $cart, int $countryId): bool
    {
        if (! $this->model->status) {
            return false;
        }

        $countries = $this->model->countries ?? [];
        if ($countries === []) {
            return true;
        }

        return in_array($countryId, $countries, true);
    }
}
