<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $feature = $this->route('feature');
        $featureId = $feature ? $feature->id : null;
        $rules = [
            'sort_order' => 'required|integer|min:0',
        ];

        $nameData = $this->input('name', []);

        foreach ($nameData as $languageId => $value) {
            $uniqueRule = Rule::unique('feature_lang', 'name')
                ->where('language_id', $languageId);

            if ($featureId) {
                $uniqueRule->where(function ($query) use ($featureId, $languageId) {
                    $query->where('feature_id', '!=', $featureId)
                        ->orWhere('language_id', '!=', $languageId);
                });
            }

            $rules["name.{$languageId}"] = [
                'required',
                'string',
                'max:255',
                $uniqueRule,
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];
        $nameData = $this->input('name', []);

        foreach ($nameData as $languageId => $value) {
            $messages["name.{$languageId}.required"] = __('validation.feature_name.required');
            $messages["name.{$languageId}.string"] = __('validation.feature_name.string');
            $messages["name.{$languageId}.max"] = __('validation.feature_name.max', ['max' => 255]);
            $messages["name.{$languageId}.unique"] = __('validation.feature_name.unique');
        }

        $messages['sort_order.integer'] = __('validation.sort_order.integer');
        $messages['sort_order.min'] = __('validation.sort_order.min');

        return $messages;
    }
}
