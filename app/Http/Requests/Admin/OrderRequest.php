<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer'],
            'shipping_address_id' => ['required', 'integer'],
            'language_id' => ['required', 'integer'],
            'currency_id' => ['required', 'integer'],

            'items' => ['required', 'array', 'min:1'],

            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],

            'shipping_method' => ['required', 'array'],
            'shipping_method.code' => ['required'],
            'shipping_method.name' => ['required', 'string'],
            'shipping_method.cost' => ['required', 'numeric'],

            'payment_method' => ['required', 'array'],
            'payment_method.code' => ['required'],
            'payment_method.name' => ['required', 'string'],

            'subtotal' => ['required', 'numeric'],
            'total' => ['required', 'numeric'],

            'comment' => ['nullable', 'string'],
        ];
    }
}
