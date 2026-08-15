<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_status_id' => ['required', 'integer', 'exists:order_statuses,id'],
            'comment' => ['nullable', 'string'],
            'notify' => ['nullable', 'boolean'],
        ];
    }
}
