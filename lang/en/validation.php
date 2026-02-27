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
    'password.confirmed' => 'The password confirmation does not match.',
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

    // Customer validation
    'customer_group_id.exists' => 'Selected customer group does not exist.',

    // Customer address validation
    'address_1.required' => 'Address is required.',
    'city.required' => 'City is required.',
    'postcode.required' => 'Postcode is required for the selected country.',
    'country_id.required' => 'Please select a country.',
    'country_id.exists' => 'Selected country does not exist.',

    // Customer group validation
    'customer_group_name.required' => 'Customer group name is required.',
    'customer_group_name.string' => 'Customer group name must be a string.',
    'customer_group_name.max' => 'Customer group name must not exceed :max characters.',
    'customer_group_name.unique' => 'This customer group name already exists for this language.',

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

    // Stock Status validation
    'stock_status_name.required' => 'Stock status name is required.',
    'stock_status_name.string' => 'Stock status name must be a string.',
    'stock_status_name.max' => 'Stock status name must not exceed :max characters.',
    'stock_status_name.unique' => 'This stock status name already exists for this language.',

    // Category validation
    'slug.required' => 'Slug is required.',
    'slug.string' => 'Slug must be a string.',
    'slug.max' => 'Slug must not exceed :max characters.',
    'slug.regex' => 'Slug may only contain lowercase letters, numbers, and hyphens.',
    'slug.unique' => 'This slug already exists for this language.',
    'description.string' => 'Description must be a string.',
    'meta_title.required' => 'Meta title is required.',
    'meta_title.string' => 'Meta title must be a string.',
    'meta_title.max' => 'Meta title must not exceed :max characters.',
    'meta_description.string' => 'Meta description must be a string.',
    'meta_description.max' => 'Meta description must not exceed :max characters.',

    // Image validation
    'image.image' => 'Image must be a valid image file.',
    'image.mimes' => 'Image must be a file of type: :values.',
    'image.max' => 'Image size must not exceed :max kilobytes.',

    // Country validation
    'name.unique' => 'This country name already exists for this language.',
    'iso_code_2.string' => 'ISO code (2) must be a string.',
    'iso_code_2.alpha' => 'ISO code (2) may contain letters only.',
    'iso_code_2.size' => 'ISO code (2) must be exactly :size characters.',
    'iso_code_3.string' => 'ISO code (3) must be a string.',
    'iso_code_3.alpha' => 'ISO code (3) may contain letters only.',
    'iso_code_3.size' => 'ISO code (3) must be exactly :size characters.',

    // Order Status validation
    'order_status_name.required' => 'Order status name is required.',
    'order_status_name.string' => 'Order status name must be a string.',
    'order_status_name.max' => 'Order status name must not exceed :max characters.',
    'order_status_name.unique' => 'This order status name already exists for this language.',

    // Feature validation
    'feature_name.required' => 'Feature name is required.',
    'feature_name.string' => 'Feature name must be a string.',
    'feature_name.max' => 'Feature name must not exceed :max characters.',
    'feature_name.unique' => 'This feature name already exists for this language.',

    // Feature value validation
    'feature_value.required' => 'Feature value name is required.',
    'feature_value.string' => 'Feature value name must be a string.',
    'feature_value.max' => 'Feature value name must not exceed :max characters.',

    // Product feature validation
    'duplicate_feature_combination' => 'The combination of :attribute and value is already used.',

    // Cart validation (front)
    'product_id.required' => 'Please select a product.',
    'product_id.integer' => 'The selected product is invalid.',
    'product_id.exists' => 'The selected product does not exist.',
    'quantity.required' => 'Quantity is required.',
    'quantity.integer' => 'Quantity must be a number.',
    'quantity.min' => 'Quantity must be at least :min.',

    // Settings validation
    'config_name.required' => 'Store name is required.',
    'config_owner.required' => 'Store owner is required.',
    'config_address.required' => 'Store address is required.',

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
