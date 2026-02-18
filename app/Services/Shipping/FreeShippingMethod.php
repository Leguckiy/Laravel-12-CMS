<?php

namespace App\Services\Shipping;

use App\Contracts\Shipping\ShippingMethodInterface;
use App\Models\Cart;
use App\Models\ShippingMethod;
use App\Services\CartService;

class FreeShippingMethod implements ShippingMethodInterface
{
    public function __construct(
        private readonly ShippingMethod $model,
        private readonly CartService $cartService
    ) {}

    public function getTitle(): string
    {
        return 'admin.shipping_method_free';
    }

    public function getCost(Cart $cart): float
    {
        return 0.0;
    }

    public function supports(Cart $cart, int $countryId): bool
    {
        if (! $this->model->status) {
            return false;
        }

        $countries = $this->model->countries ?? [];
        if ($countries !== [] && ! in_array($countryId, $countries, true)) {
            return false;
        }

        $subTotalRequired = (float) ($this->model->config['sub_total'] ?? 0);
        $subtotal = $this->cartService->getSubtotal($cart);

        return $subtotal >= $subTotalRequired;
    }
}
