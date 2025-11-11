<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FeatureValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'sort_order' => 'required|integer|min:0',
        ];

        $valueData = $this->input('value', []);

        foreach ($valueData as $languageId => $value) {
            $rules["value.{$languageId}"] = [
                'required',
                'string',
                'max:255',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];
        $valueData = $this->input('value', []);

        foreach ($valueData as $languageId => $value) {
            $messages["value.{$languageId}.required"] = __('validation.feature_value.required');
            $messages["value.{$languageId}.string"] = __('validation.feature_value.string');
            $messages["value.{$languageId}.max"] = __('validation.feature_value.max', ['max' => 255]);
        }

        $messages['sort_order.integer'] = __('validation.sort_order.integer');
        $messages['sort_order.min'] = __('validation.sort_order.min');

        return $messages;
    }
}
