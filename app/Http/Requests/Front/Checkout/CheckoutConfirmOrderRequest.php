<?php

namespace App\Http\Requests\Front\Checkout;

use App\Enums\CheckoutStep;
use App\Http\Requests\Front\Concerns\ValidatesCheckoutCart;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class CheckoutConfirmOrderRequest extends FormRequest
{
    use ValidatesCheckoutCart;

    public function authorize(): bool
    {
        $this->ensureCartValid();

        if (empty($this->session()->get('customer')) || ! $this->hasRequiredCustomerFields()) {
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => __('front/checkout.error_generic'),
                ], 422)
            );
        }

        if (empty($this->session()->get('shipping_address')) || ! $this->hasRequiredShippingAddressFields()) {
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => __('front/checkout.shipping_address_required'),
                ], 422)
            );
        }

        $shippingMethod = $this->session()->get('shipping_method', []);
        if (empty($shippingMethod['code'])) {
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => __('front/checkout.payment_after_shipping'),
                ], 422)
            );
        }

        $paymentMethod = $this->session()->get('payment_method', []);
        if (empty($paymentMethod['code'])) {
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => __('front/checkout.validation_payment_method_id_required'),
                ], 422)
            );
        }

        $step = (int) $this->session()->get('checkout_step', 0);
        if ($step !== CheckoutStep::Confirmation->value) {
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => __('front/checkout.error_generic'),
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
        return [
            'comment' => ['nullable', 'string', 'max:65535'],
        ];
    }

    protected function hasRequiredCustomerFields(): bool
    {
        $customer = $this->session()->get('customer', []);

        return ! empty($customer['firstname']) && ! empty($customer['lastname']) && ! empty($customer['email']);
    }

    protected function hasRequiredShippingAddressFields(): bool
    {
        $address = $this->session()->get('shipping_address', []);

        return ! empty($address['firstname'])
            && ! empty($address['lastname'])
            && ! empty($address['address_1'])
            && ! empty($address['city'])
            && ! empty($address['country_id']);
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            new JsonResponse([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ], 422)
        );
    }
}
