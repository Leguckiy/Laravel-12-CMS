<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShippingMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $code = $this->route('code');
        $rules = [
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'boolean',
            'countries' => 'required|array|min:1',
            'countries.*' => 'integer|exists:countries,id',
        ];
        if ($code === 'flat_rate') {
            $rules['cost'] = 'required|numeric|min:0';
        }
        if ($code === 'free') {
            $rules['sub_total'] = 'required|numeric|min:0';
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'countries.required' => __('admin.shipping_countries_required'),
            'countries.min' => __('admin.shipping_countries_required'),
        ];
    }
}
