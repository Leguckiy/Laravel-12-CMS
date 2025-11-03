<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'status' => 'boolean',
        ];

        $languageId = $this->route('language') ? $this->route('language')->id : null;
        $codeRules = 'required|string|max:5';
        $rules['code'] = $languageId 
            ? $codeRules . "|unique:languages,code,{$languageId}" 
            : $codeRules . '|unique:languages';

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.language_name.required'),
            'code.required' => __('validation.code.required'),
            'code.max' => __('validation.code.max'),
            'code.unique' => __('validation.code.unique'),
            'sort_order.required' => __('validation.sort_order.required'),
            'sort_order.integer' => __('validation.sort_order.integer'),
            'sort_order.min' => __('validation.sort_order.min'),
            'status.boolean' => __('validation.status.boolean'),
        ];
    }
}


