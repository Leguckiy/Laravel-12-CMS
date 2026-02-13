<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        $category = $this->route('category');
        $categoryId = $category ? $category->id : null;
        $rules = [
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'boolean',
        ];

        // Get all language IDs from request
        $nameData = $this->input('name', []);

        foreach ($nameData as $languageId => $value) {
            $uniqueSlugRule = Rule::unique('category_lang', 'slug')
                ->where('language_id', $languageId);

            if ($categoryId) {
                $uniqueSlugRule->where(function ($query) use ($categoryId, $languageId) {
                    $query->where('category_id', '!=', $categoryId)
                        ->orWhere('language_id', '!=', $languageId);
                });
            }

            $rules["name.{$languageId}"] = [
                'required',
                'string',
                'max:255',
            ];

            $rules["slug.{$languageId}"] = [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $uniqueSlugRule,
            ];

            $rules["description.{$languageId}"] = [
                'nullable',
                'string',
            ];

            $rules["meta_title.{$languageId}"] = [
                'required',
                'string',
                'max:255',
            ];

            $rules["meta_description.{$languageId}"] = [
                'nullable',
                'string',
                'max:255',
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

            $messages["slug.{$languageId}.required"] = __('validation.slug.required');
            $messages["slug.{$languageId}.string"] = __('validation.slug.string');
            $messages["slug.{$languageId}.max"] = __('validation.slug.max', ['max' => 255]);
            $messages["slug.{$languageId}.regex"] = __('validation.slug.regex');
            $messages["slug.{$languageId}.unique"] = __('validation.slug.unique');

            $messages["description.{$languageId}.string"] = __('validation.description.string');

            $messages["meta_title.{$languageId}.required"] = __('validation.meta_title.required');
            $messages["meta_title.{$languageId}.string"] = __('validation.meta_title.string');
            $messages["meta_title.{$languageId}.max"] = __('validation.meta_title.max', ['max' => 255]);

            $messages["meta_description.{$languageId}.string"] = __('validation.meta_description.string');
            $messages["meta_description.{$languageId}.max"] = __('validation.meta_description.max', ['max' => 255]);
        }

        $messages['image.image'] = __('validation.image.image');
        $messages['image.mimes'] = __('validation.image.mimes', ['values' => 'jpg, jpeg, png, webp, gif']);
        $messages['image.max'] = __('validation.image.max', ['max' => 2048]);

        $messages['sort_order.integer'] = __('validation.sort_order.integer');
        $messages['sort_order.min'] = __('validation.sort_order.min', ['min' => 0]);

        return $messages;
    }
}
