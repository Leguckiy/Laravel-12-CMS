<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:32',
            'symbol_left' => 'nullable|string|max:12',
            'symbol_right' => 'nullable|string|max:12',
            'decimal_place' => 'required|integer|min:0|max:8',
            'value' => 'nullable|numeric',
            'status' => 'boolean',
        ];

        // Unique validation for code field
        $currencyId = $this->route('currency') ? $this->route('currency')->id : null;
        $codeRules = 'required|string|min:3|max:3';
        $rules['code'] = $currencyId
            ? $codeRules . "|unique:currencies,code,{$currencyId}"
            : $codeRules . '|unique:currencies,code';

        return $rules;
    }

    /**
     * Custom messages (optional, using generic ones by default)
     */
    public function messages(): array
    {
        return [
            'title.required' => __('validation.currency_title.required'),
            'code.required' => __('validation.currency_code.required'),
            'code.min' => __('validation.currency_code.min'),
            'code.max' => __('validation.currency_code.max'),
            'code.unique' => __('validation.currency_code.unique'),
            'decimal_place.required' => __('validation.decimal_place.required'),
            'decimal_place.integer' => __('validation.decimal_place.integer'),
            'decimal_place.min' => __('validation.decimal_place.min'),
            'decimal_place.max' => __('validation.decimal_place.max'),
            'value.numeric' => __('validation.value.numeric'),
            'status.boolean' => __('validation.status.boolean'),
        ];
    }
}
