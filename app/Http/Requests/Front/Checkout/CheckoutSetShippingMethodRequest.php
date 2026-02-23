<?php

namespace App\Http\Requests\Front\Checkout;

use App\Http\Requests\Front\Concerns\ValidatesCheckoutCart;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class CheckoutSetShippingMethodRequest extends FormRequest
{
    use ValidatesCheckoutCart;

    public function authorize(): bool
    {
        $this->ensureCartValid();

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
            'method_id.required' => __('front/checkout.validation_method_id_required'),
            'method_id.max' => __('front/checkout.validation_method_id_max'),
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
