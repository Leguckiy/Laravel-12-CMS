<?php

namespace App\Contracts\Shipping;

use App\Models\Cart;

interface ShippingMethodInterface
{
    public function getTitle(): string;

    public function getCostForCart(Cart $cart): float;

    public function supports(Cart $cart, int $countryId): bool;

    /**
     * Admin / manual order context: compute cost based on a plain items array.
     *
     * @param array<int, array{price: float|int|string, quantity: int|string}> $items
     */
    public function getCostForItems(array $items): float;

    /**
     * Admin / manual order context: check availability based on a plain items array.
     *
     * @param array<int, array{price: float|int|string, quantity: int|string}> $items
     */
    public function supportsItems(array $items, int $countryId): bool;
}
