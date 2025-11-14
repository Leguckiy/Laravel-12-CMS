<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerGroup = $this->route('customer_group');
        $customerGroupId = $customerGroup ? $customerGroup->id : null;
        $rules = [
            'approval' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ];

        $nameData = $this->input('name', []);

        foreach ($nameData as $languageId => $value) {
            $uniqueRule = Rule::unique('customer_groups_lang', 'name')
                ->where('language_id', $languageId);

            if ($customerGroupId) {
                $uniqueRule->where(function ($query) use ($customerGroupId, $languageId) {
                    $query->where('customer_group_id', '!=', $customerGroupId)
                        ->orWhere('language_id', '!=', $languageId);
                });
            }

            $rules["name.{$languageId}"] = [
                'required',
                'string',
                'max:32',
                $uniqueRule,
            ];
        }

        $descriptionData = $this->input('description', []);
        foreach ($descriptionData as $languageId => $value) {
            $rules["description.{$languageId}"] = [
                'nullable',
                'string',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];
        $nameData = $this->input('name', []);

        foreach ($nameData as $languageId => $value) {
            $messages["name.{$languageId}.required"] = __('validation.customer_group_name.required');
            $messages["name.{$languageId}.string"] = __('validation.customer_group_name.string');
            $messages["name.{$languageId}.max"] = __('validation.customer_group_name.max', ['max' => 32]);
            $messages["name.{$languageId}.unique"] = __('validation.customer_group_name.unique');
        }

        return $messages;
    }
}
