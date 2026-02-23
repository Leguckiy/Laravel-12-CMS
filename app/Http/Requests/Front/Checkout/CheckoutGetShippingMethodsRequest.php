<?php

namespace App\Http\Requests\Front\Checkout;

use App\Http\Requests\Front\Concerns\ValidatesCheckoutCart;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class CheckoutGetShippingMethodsRequest extends FormRequest
{
    use ValidatesCheckoutCart;

    public function authorize(): bool
    {
        $this->ensureCartValid();

        if (empty($this->session()->get('shipping_address'))) {
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => __('front/checkout.shipping_address_required'),
                ], 422)
            );
        }

        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [];
    }
}
