<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLanguageRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'status' => 'boolean',
        ];

        // Unique validation for code field
        $languageId = $this->route('language') ? $this->route('language')->id : null;
        $codeRules = 'required|string|max:5';
        $rules['code'] = $languageId 
            ? $codeRules . "|unique:languages,code,{$languageId}" 
            : $codeRules . '|unique:languages';

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Language name is required.',
            'code.required' => 'Language code is required.',
            'code.max' => 'Language code must not exceed 5 characters.',
            'code.unique' => 'This language code is already taken.',
            'sort_order.required' => 'Sort order is required.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'status.boolean' => 'Status must be a boolean value.',
        ];
    }
}

