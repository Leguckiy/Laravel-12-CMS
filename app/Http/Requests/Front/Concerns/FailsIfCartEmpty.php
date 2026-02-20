<?php

namespace App\Http\Requests\Front\Concerns;

use App\Support\FrontContext;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

trait FailsIfCartEmpty
{
    protected function failIfCartEmpty(): void
    {
        $context = $this->container->make(FrontContext::class);
        if ($context->isCartEmpty()) {
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => __('front/checkout.cart_empty'),
                ], 422)
            );
        }
    }
}
