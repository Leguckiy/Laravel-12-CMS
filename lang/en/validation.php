<?php

return [
    // Language validation
    'language_name.required' => 'Language name is required.',
    'code.required' => 'Language code is required.',
    'code.max' => 'Language code must not exceed 5 characters.',
    'code.unique' => 'This language code is already taken.',
    'sort_order.required' => 'Sort order is required.',
    'sort_order.integer' => 'Sort order must be a number.',
    'sort_order.min' => 'Sort order must be at least 0.',
    'status.boolean' => 'Status must be a boolean value.',
    
    // User validation
    'user_group_id.exists' => 'Selected user group does not exist.',
    'language_id.exists' => 'Selected language does not exist.',
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
    
    // User Group validation
    'user_group_name.required' => 'User group name is required.',
    'name.required' => 'The field is required.',
    'name.string' => 'The field must be a string.',
    'name.max' => 'The field must not exceed :max characters.',
    'permissions.array' => 'Permissions must be an array.',
    
    // Currency validation
    'currency_title.required' => 'Currency title is required.',
    'currency_code.required' => 'Currency code is required.',
    'currency_code.min' => 'Currency code must be 3 characters.',
    'currency_code.max' => 'Currency code must not exceed 3 characters.',
    'currency_code.unique' => 'This currency code is already taken.',
    'decimal_place.required' => 'Decimal place is required.',
    'decimal_place.integer' => 'Decimal place must be a number.',
    'decimal_place.min' => 'Decimal place must be at least 0.',
    'decimal_place.max' => 'Decimal place must not exceed 8.',
    'value.numeric' => 'Value must be a number.',
    
    // Standard Laravel validation messages
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute must be a string.',
    'max' => [
        'string' => 'The :attribute must not exceed :max characters.',
        'array' => 'The :attribute must not have more than :max items.',
    ],
    'array' => 'The :attribute must be an array.',
    'attributes' => [
        'name' => 'Name',
        'user_group_name' => 'User group name',
        'permissions' => 'Permissions',
    ],
];

