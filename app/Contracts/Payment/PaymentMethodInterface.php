<?php

namespace App\Contracts\Payment;

use App\Models\Cart;

interface PaymentMethodInterface
{
    public function getTitle(): string;

    public function supports(Cart $cart, int $countryId): bool;
}
