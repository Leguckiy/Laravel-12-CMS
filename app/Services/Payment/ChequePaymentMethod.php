<?php

namespace App\Services\Payment;

use App\Contracts\Payment\PaymentMethodInterface;
use App\Models\Cart;
use App\Models\PaymentMethod;

class ChequePaymentMethod implements PaymentMethodInterface
{
    public function __construct(
        private readonly PaymentMethod $model
    ) {}

    public function getTitle(): string
    {
        return 'admin.payment_method_cheque';
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

        return true;
    }
}
