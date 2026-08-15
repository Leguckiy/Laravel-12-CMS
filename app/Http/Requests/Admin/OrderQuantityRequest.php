<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderQuantityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'min:1', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'currency_id' => ['required', 'integer', 'min:1', 'exists:currencies,id'],
            'order_id' => ['nullable', 'integer', 'min:1', 'exists:orders,id'],
        ];
    }
}

