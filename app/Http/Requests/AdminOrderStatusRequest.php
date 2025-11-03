<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminOrderStatusRequest extends FormRequest
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
        $orderStatus = $this->route('order_status');
        $orderStatusId = $orderStatus ? $orderStatus->id : null;
        $rules = [];

        // Get all language IDs from request
        $nameData = $this->input('name', []);
        
        foreach ($nameData as $languageId => $value) {
            $uniqueRule = Rule::unique('order_statuses_lang', 'name')
                ->where('language_id', $languageId);
            
            if ($orderStatusId) {
                // When updating, exclude records with current order_status_id and language_id
                // Since we have composite primary key, we need to exclude manually
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

    /**
     * Get custom validation messages.
     */
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

