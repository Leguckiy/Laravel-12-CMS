<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $stockStatus = $this->route('stock_status');
        $stockStatusId = $stockStatus ? $stockStatus->id : null;
        $rules = [];

        $nameData = $this->input('name', []);

        foreach ($nameData as $languageId => $value) {
            $uniqueRule = Rule::unique('stock_status_lang', 'name')
                ->where('language_id', $languageId);

            if ($stockStatusId) {
                $uniqueRule->where(function ($query) use ($stockStatusId, $languageId) {
                    $query->where('stock_status_id', '!=', $stockStatusId)
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
            $messages["name.{$languageId}.required"] = __('validation.stock_status_name.required');
            $messages["name.{$languageId}.string"] = __('validation.stock_status_name.string');
            $messages["name.{$languageId}.max"] = __('validation.stock_status_name.max', ['max' => 32]);
            $messages["name.{$languageId}.unique"] = __('validation.stock_status_name.unique');
        }

        return $messages;
    }
}
