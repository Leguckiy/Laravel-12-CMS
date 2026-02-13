<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'firstname.required' => __('validation.firstname.required'),
            'lastname.required' => __('validation.lastname.required'),
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.email'),
            'email.unique' => __('validation.email.unique'),
            'password.required' => __('validation.password.required'),
            'password.min' => __('validation.password.min'),
        ];
    }
}
