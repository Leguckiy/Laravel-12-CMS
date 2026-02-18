<?php

namespace App\Contracts\Shipping;

use App\Models\Cart;

interface ShippingMethodInterface
{
    public function getTitle(): string;

    public function getCost(Cart $cart): float;

    public function supports(Cart $cart, int $countryId): bool;
}
