<?php

namespace App\Http\Requests\Front\Checkout;

use App\Enums\CheckoutStep;
use App\Http\Requests\Front\Concerns\FailsIfCartEmpty;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class CheckoutSetPaymentMethodRequest extends FormRequest
{
    use FailsIfCartEmpty;

    public function authorize(): bool
    {
        $this->failIfCartEmpty();

        $step = (int) $this->session()->get('checkout_step', 0);
        if ($step !== CheckoutStep::Payment->value) {
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => __('front/checkout.payment_after_shipping'),
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
            'method_id' => ['required', 'string', 'max:64'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'method_id.required' => __('front/checkout.validation_payment_method_id_required'),
            'method_id.max' => __('front/checkout.validation_payment_method_id_max'),
        ];
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
