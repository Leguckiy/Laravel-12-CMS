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
            'user_group_id.exists' => 'Selected user group does not exist.',
            'username.required' => 'Username is required.',
            'username.unique' => 'This username is already taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'confirm.required' => 'Password confirmation is required.',
            'confirm.same' => 'Password confirmation does not match.',
            'firstname.required' => 'First name is required.',
            'lastname.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
        ];
    }
}
