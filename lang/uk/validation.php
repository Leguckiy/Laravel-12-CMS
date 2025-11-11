<?php

return [
    // Language validation
    'language_name.required' => 'Назва мови є обов\'язковою.',
    'code.required' => 'Код мови є обов\'язковим.',
    'code.max' => 'Код мови не повинен перевищувати 5 символів.',
    'code.unique' => 'Цей код мови вже використовується.',
    'sort_order.required' => 'Порядок сортування є обов\'язковим.',
    'sort_order.integer' => 'Порядок сортування повинен бути числом.',
    'sort_order.min' => 'Порядок сортування повинен бути не менше 0.',
    'status.boolean' => 'Статус повинен бути логічним значенням.',
    
    // User validation
    'user_group_id.exists' => 'Вибрана група користувача не існує.',
    'language_id.exists' => 'Вибрана мова не існує.',
    'username.required' => "Ім'я користувача є обов'язковим.",
    'username.unique' => "Це ім'я користувача вже використовується.",
    'password.required' => 'Пароль є обов\'язковим.',
    'password.min' => 'Пароль повинен містити принаймні 6 символів.',
    'confirm.required' => 'Підтвердження пароля є обов\'язковим.',
    'confirm.same' => 'Підтвердження пароля не співпадає.',
    'firstname.required' => "Ім'я є обов'язковим.",
    'lastname.required' => "Прізвище є обов'язковим.",
    'email.required' => 'Email є обов\'язковим.',
    'email.email' => 'Будь ласка, введіть валідна адресу email.',
    'email.unique' => 'Цей email вже зареєстрований.',
    
    // User Group validation
    'user_group_name.required' => 'Назва групи користувача є обов\'язковою.',
    'name.required' => 'Поле є обов\'язковим.',
    'name.string' => 'Поле має бути рядком.',
    'name.max' => 'Поле не повинно перевищувати :max символів.',
    'permissions.array' => 'Дозволи мають бути масивом.',
    
    // Currency validation
    'currency_title.required' => 'Назва валюти є обов\'язковою.',
    'currency_code.required' => 'Код валюти є обов\'язковим.',
    'currency_code.min' => 'Код валюти має містити рівно 3 символи.',
    'currency_code.max' => 'Код валюти не повинен перевищувати 3 символи.',
    'currency_code.unique' => 'Цей код валюти вже використовується.',
    'decimal_place.required' => 'Кількість знаків після коми є обов\'язковою.',
    'decimal_place.integer' => 'Кількість знаків після коми має бути числом.',
    'decimal_place.min' => 'Кількість знаків після коми повинна бути не менше 0.',
    'decimal_place.max' => 'Кількість знаків після коми не повинна перевищувати 8.',
    'value.numeric' => 'Значення має бути числом.',
    
    // Stock Status validation
    'stock_status_name.required' => 'Назва статусу наявності є обов\'язковою.',
    'stock_status_name.string' => 'Назва статусу наявності має бути рядком.',
    'stock_status_name.max' => 'Назва статусу наявності не повинна перевищувати :max символів.',
    'stock_status_name.unique' => 'Ця назва статусу наявності вже існує для цієї мови.',

    // Category validation
    'slug.required' => 'Slug є обов\'язковим.',
    'slug.string' => 'Slug має бути рядком.',
    'slug.max' => 'Slug не повинен перевищувати :max символів.',
    'slug.regex' => 'Slug може містити лише малі літери, цифри та дефіси.',
    'slug.unique' => 'Цей slug вже існує для цієї мови.',
    'description.string' => 'Опис має бути рядком.',
    'meta_title.required' => 'Meta title є обов\'язковим.',
    'meta_title.string' => 'Meta title має бути рядком.',
    'meta_title.max' => 'Meta title не повинен перевищувати :max символів.',
    'meta_description.string' => 'Meta description має бути рядком.',
    'meta_description.max' => 'Meta description не повинен перевищувати :max символів.',

    // Image validation
    'image.image' => 'Файл повинен бути зображенням.',
    'image.mimes' => 'Зображення має бути у форматі: :values.',
    'image.max' => 'Розмір зображення не повинен перевищувати :max кілобайт.',

    // Country validation
    'name.unique' => 'Ця назва країни вже існує для цієї мови.',
    'iso_code_2.string' => 'ISO-код (2) має бути рядком.',
    'iso_code_2.alpha' => 'ISO-код (2) може містити лише літери.',
    'iso_code_2.size' => 'ISO-код (2) має містити рівно :size символи.',
    'iso_code_3.string' => 'ISO-код (3) має бути рядком.',
    'iso_code_3.alpha' => 'ISO-код (3) може містити лише літери.',
    'iso_code_3.size' => 'ISO-код (3) має містити рівно :size символи.',

    // Order Status validation
    'order_status_name.required' => 'Назва статусу замовлення є обов\'язковою.',
    'order_status_name.string' => 'Назва статусу замовлення має бути рядком.',
    'order_status_name.max' => 'Назва статусу замовлення не повинна перевищувати :max символів.',
    'order_status_name.unique' => 'Ця назва статусу замовлення вже існує для цієї мови.',

    // Feature validation
    'feature_name.required' => 'Характеристика є обов\'язковою.',
    'feature_name.string' => 'Характеристика має бути рядком.',
    'feature_name.max' => 'Характеристика не повинна перевищувати :max символів.',
    'feature_name.unique' => 'Ця характеристика вже існує для цієї мови.',

    // Feature value validation
    'feature_value.required' => 'Назва характеристики є обов\'язковою.',
    'feature_value.string' => 'Назва характеристики має бути рядком.',
    'feature_value.max' => 'Назва характеристики не повинна перевищувати :max символів.',
    
    // Standard Laravel validation messages
    'required' => 'Поле :attribute є обов\'язковим.',
    'string' => 'Поле :attribute має бути рядком.',
    'max' => [
        'string' => 'Поле :attribute не повинно перевищувати :max символів.',
        'array' => 'Поле :attribute не повинно містити більше :max елементів.',
    ],
    'array' => 'Поле :attribute має бути масивом.',
    'attributes' => [
        'name' => 'Назва',
        'user_group_name' => 'Назва групи користувача',
        'permissions' => 'Дозволи',
    ],
];
