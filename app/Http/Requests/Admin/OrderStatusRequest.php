<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $orderStatus = $this->route('order_status');
        $orderStatusId = $orderStatus ? $orderStatus->id : null;
        $rules = [];

        $nameData = $this->input('name', []);

        foreach ($nameData as $languageId => $value) {
            $uniqueRule = Rule::unique('order_status_lang', 'name')
                ->where('language_id', $languageId);

            if ($orderStatusId) {
                $uniqueRule->where(function ($query) use ($orderStatusId, $languageId) {
                    $query->where('order_status_id', '!=', $orderStatusId)
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

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];
        $nameData = $this->input('name', []);

        foreach ($nameData as $languageId => $value) {
            $messages["name.{$languageId}.required"] = __('validation.order_status_name.required');
            $messages["name.{$languageId}.string"] = __('validation.order_status_name.string');
            $messages["name.{$languageId}.max"] = __('validation.order_status_name.max', ['max' => 32]);
            $messages["name.{$languageId}.unique"] = __('validation.order_status_name.unique');
        }

        return $messages;
    }
}
