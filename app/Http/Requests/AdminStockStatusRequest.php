<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStockStatusRequest extends FormRequest
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
        $stockStatus = $this->route('stock_status');
        $stockStatusId = $stockStatus ? $stockStatus->id : null;
        $rules = [];

        // Get all language IDs from request
        $nameData = $this->input('name', []);
        
        foreach ($nameData as $languageId => $value) {
            $uniqueRule = Rule::unique('stock_statuses_lang', 'name')
                ->where('language_id', $languageId);
            
            if ($stockStatusId) {
                // When updating, exclude records with current stock_status_id and language_id
                // Since we have composite primary key, we need to exclude manually
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

    /**
     * Get custom validation messages.
     */
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
