<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
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
        $country = $this->route('country');
        $countryId = $country ? $country->id : null;
        $rules = [
            'iso_code_2' => 'nullable|string|alpha|size:2',
            'iso_code_3' => 'nullable|string|alpha|size:3',
            'postcode_required' => 'boolean',
            'status' => 'boolean',
        ];

        // Get all language IDs from request
        $nameData = $this->input('name', []);
        
        foreach ($nameData as $languageId => $value) {
            $uniqueRule = Rule::unique('countries_lang', 'name')
                ->where('language_id', $languageId);
            
            if ($countryId) {
                // When updating, exclude records with current country_id and language_id
                // Since we have composite primary key, we need to exclude manually
                $uniqueRule->where(function ($query) use ($countryId, $languageId) {
                    $query->where('country_id', '!=', $countryId)
                        ->orWhere('language_id', '!=', $languageId);
                });
            }
            
            $rules["name.{$languageId}"] = [
                'required',
                'string',
                'max:255',
                $uniqueRule,
            ];
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        $messages = [];
        $nameData = $this->input('name', []);

        foreach ($nameData as $languageId => $value) {
            $messages["name.{$languageId}.required"] = __('validation.name.required');
            $messages["name.{$languageId}.string"] = __('validation.name.string');
            $messages["name.{$languageId}.max"] = __('validation.name.max', ['max' => 255]);
            $messages["name.{$languageId}.unique"] = __('validation.name.unique');
        }

        // Messages for other fields
        $messages['iso_code_2.string'] = __('validation.iso_code_2.string');
        $messages['iso_code_2.alpha'] = __('validation.iso_code_2.alpha');
        $messages['iso_code_2.size'] = __('validation.iso_code_2.size', ['size' => 2]);

        $messages['iso_code_3.string'] = __('validation.iso_code_3.string');
        $messages['iso_code_3.alpha'] = __('validation.iso_code_3.alpha');
        $messages['iso_code_3.size'] = __('validation.iso_code_3.size', ['size' => 3]);

        return $messages;
    }
}


