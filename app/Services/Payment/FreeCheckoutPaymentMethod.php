<?php

namespace App\Services\Payment;

use App\Contracts\Payment\PaymentMethodInterface;
use App\Models\Cart;
use App\Models\PaymentMethod;
use App\Services\CartService;

class FreeCheckoutPaymentMethod implements PaymentMethodInterface
{
    public function __construct(
        private readonly PaymentMethod $model,
        private readonly CartService $cartService
    ) {}

    public function getTitle(): string
    {
        return 'admin.payment_method_free_checkout';
    }

    public function supports(Cart $cart, int $countryId): bool
    {
        if (! $this->model->status) {
            return false;
        }

        $subtotal = $this->cartService->getSubtotal($cart);

        return $subtotal <= 0.0;
    }

    public function getInstructions(int $languageId): string
    {
        return '';
    }
}
