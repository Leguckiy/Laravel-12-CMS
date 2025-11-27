<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'config_name' => 'required|string|max:255',
            'config_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'config_icon' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'config_owner' => 'required|string|max:255',
            'config_address' => 'required|string|max:2000',
            'config_email' => 'required|email|max:255',
            'config_telephone' => 'nullable|string|max:32',
            'config_open' => 'nullable|string|max:2000',
            'config_country_id' => 'nullable|exists:countries,id',
            'config_language_id' => 'nullable|exists:languages,id',
            'config_currency_id' => 'nullable|exists:currencies,id',
        ];

        $titleData = $this->input('config_meta_title', []);
        $descriptionData = $this->input('config_meta_description', []);

        foreach ($titleData as $languageId => $value) {
            $rules["config_meta_title.{$languageId}"] = [
                'required',
                'string',
                'max:255',
            ];
        }

        foreach ($descriptionData as $languageId => $value) {
            $rules["config_meta_description.{$languageId}"] = [
                'nullable',
                'string',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];

        $titleData = $this->input('config_meta_title', []);
        $descriptionData = $this->input('config_meta_description', []);

        foreach ($titleData as $languageId => $value) {
            $messages["config_meta_title.{$languageId}.required"] = __('validation.meta_title.required');
            $messages["config_meta_title.{$languageId}.string"] = __('validation.meta_title.string');
            $messages["config_meta_title.{$languageId}.max"] = __('validation.meta_title.max', ['max' => 255]);
        }

        foreach ($descriptionData as $languageId => $value) {
            $messages["config_meta_description.{$languageId}.string"] = __('validation.meta_description.string');
        }

        $messages['config_logo.image'] = __('validation.image.image');
        $messages['config_logo.mimes'] = __('validation.image.mimes', ['values' => 'jpg, jpeg, png, webp, gif']);
        $messages['config_logo.max'] = __('validation.image.max', ['max' => 2048]);

        $messages['config_icon.image'] = __('validation.image.image');
        $messages['config_icon.mimes'] = __('validation.image.mimes', ['values' => 'jpg, jpeg, png, webp, gif']);
        $messages['config_icon.max'] = __('validation.image.max', ['max' => 2048]);

        $messages['config_name.required'] = __('validation.config_name.required');
        $messages['config_owner.required'] = __('validation.config_owner.required');
        $messages['config_address.required'] = __('validation.config_address.required');
        $messages['config_email.required'] = __('validation.email.required');
        $messages['config_email.email'] = __('validation.email.email');

        return $messages;
    }
}
