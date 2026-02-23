<?php

namespace App\Http\Requests\Front\Concerns;

use App\Enums\CheckoutStep;
use App\Services\CartService;
use App\Support\FrontContext;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

trait ValidatesCheckoutCart
{
    protected function ensureCartValid(): void
    {
        $this->ensureCartNotEmpty();
        $this->ensureCartStockValid();
    }

    protected function rollbackCheckoutStepForCartError(): void
    {
        $step = (int) $this->session()->get('checkout_step', CheckoutStep::PersonalAddress->value);
        if ($step >= CheckoutStep::Delivery->value) {
            $this->session()->put('checkout_step', CheckoutStep::Delivery->value);
            $this->session()->forget(['shipping_method', 'payment_method']);
        }
    }

    protected function ensureCartNotEmpty(): void
    {
        $context = $this->container->make(FrontContext::class);
        if ($context->isCartEmpty()) {
            $this->rollbackCheckoutStepForCartError();
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => __('front/checkout.cart_empty'),
                    'redirect_to_cart' => true,
                ], 422)
            );
        }
    }

    protected function ensureCartStockValid(): void
    {
        $context = $this->container->make(FrontContext::class);
        $cart = $context->cart;
        if ($cart === null) {
            return;
        }

        $cartService = $this->container->make(CartService::class);
        try {
            $cartService->validateCartStock($cart);
        } catch (InvalidArgumentException $e) {
            $this->rollbackCheckoutStepForCartError();
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'redirect_to_cart' => true,
                ], 422)
            );
        }
    }
}
