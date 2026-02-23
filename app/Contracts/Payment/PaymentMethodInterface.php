<?php

namespace App\Contracts\Payment;

use App\Models\Cart;

interface PaymentMethodInterface
{
    public function getTitle(): string;

    public function supports(Cart $cart, int $countryId): bool;

    /**
     * Return HTML instructions for the checkout page, or empty string if none.
     */
    public function getInstructions(int $languageId): string;
}
