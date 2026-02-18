<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $code = $this->route('code');
        $rules = [
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'boolean',
        ];
        if (in_array($code, ['free_checkout', 'cod', 'cheque', 'bank_transfer'], true)) {
            $rules['order_status_id'] = 'required|integer|exists:order_statuses,id';
        }
        if ($code === 'cheque') {
            $rules['payable_to'] = 'required|string|max:255';
        }
        if ($code === 'bank_transfer') {
            $rules['instructions'] = 'nullable|array';
            $instructionsData = $this->input('instructions', []);
            foreach (array_keys($instructionsData) as $languageId) {
                $rules['instructions.'.$languageId] = 'nullable|string|max:65535';
            }
        }
        if (in_array($code, ['cod', 'cheque', 'bank_transfer'], true)) {
            $rules['countries'] = 'required|array|min:1';
            $rules['countries.*'] = 'integer|exists:countries,id';
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $code = $this->route('code');
        $messages = [];
        if (in_array($code, ['cod', 'cheque', 'bank_transfer'], true)) {
            $messages['countries.required'] = __('admin.payment_countries_required');
            $messages['countries.min'] = __('admin.payment_countries_required');
        }

        return $messages;
    }
}
