<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cart_token' => $this->session()->get('cart_token'),
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $rules = [
            'cart_token' => ['required', 'string'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ];

        $rules['quantity'] = in_array($this->method(), ['POST', 'PUT'], true)
            ? ['required', 'integer', 'min:1']
            : ['sometimes', 'nullable', 'integer', 'min:1'];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cart_token.required' => __('front/general.session_required'),
            'product_id.required' => __('validation.product_id.required'),
            'product_id.integer' => __('validation.product_id.integer'),
            'product_id.exists' => __('validation.product_id.exists'),
            'quantity.required' => __('validation.quantity.required'),
            'quantity.integer' => __('validation.quantity.integer'),
            'quantity.min' => __('validation.quantity.min'),
        ];
    }
}
