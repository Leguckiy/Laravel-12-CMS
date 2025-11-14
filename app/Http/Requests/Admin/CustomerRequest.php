<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'customer_group_id' => 'required|exists:customer_groups,id',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'nullable|string|max:32',
            'status' => 'boolean',
        ];

        $customerId = $this->route('customer') ? $this->route('customer')->id : null;

        if ($customerId) {
            $rules['password'] = 'nullable|string|min:6';
            $rules['confirm'] = 'nullable|string|same:password';
        } else {
            $rules['password'] = 'required|string|min:6';
            $rules['confirm'] = 'required|string|same:password';
        }

        $rules['email'] .= $customerId ? "|unique:customers,email,{$customerId}" : '|unique:customers,email';

        return $rules;
    }

    public function messages(): array
    {
        return [
            'customer_group_id.exists' => __('validation.customer_group_id.exists'),
            'password.required' => __('validation.password.required'),
            'password.min' => __('validation.password.min'),
            'confirm.required' => __('validation.confirm.required'),
            'confirm.same' => __('validation.confirm.same'),
            'firstname.required' => __('validation.firstname.required'),
            'lastname.required' => __('validation.lastname.required'),
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.email'),
            'email.unique' => __('validation.email.unique'),
        ];
    }
}
