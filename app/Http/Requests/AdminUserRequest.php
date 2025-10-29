<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserRequest extends FormRequest
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
        $rules = [
            'user_group_id' => 'nullable|exists:user_groups,id',
            'language_id' => 'nullable|exists:languages,id',
            'username' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'image' => 'nullable|string|max:255',
            'status' => 'boolean',
        ];

        // Password validation rules
        if ($this->isMethod('post')) {
            // Creating new user - password is required
            $rules['password'] = 'required|string|min:6';
            $rules['confirm'] = 'required|same:password';
        } else {
            // Updating user - password is optional
            $rules['password'] = 'nullable|string|min:6';
            $rules['confirm'] = 'nullable|same:password';
        }

        // Unique validation rules
        $userId = $this->route('user') ? $this->route('user')->id : null;
        $rules['username'] .= $userId ? "|unique:users,username,{$userId}" : '|unique:users';
        $rules['email'] .= $userId ? "|unique:users,email,{$userId}" : '|unique:users';

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_group_id.exists' => __('validation.user_group_id.exists'),
            'language_id.exists' => __('validation.language_id.exists'),
            'username.required' => __('validation.username.required'),
            'username.unique' => __('validation.username.unique'),
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
