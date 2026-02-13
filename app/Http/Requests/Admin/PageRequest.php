<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $page = $this->route('page');
        $pageId = $page?->id;
        $rules = [
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'boolean',
        ];

        $titleData = $this->input('title', []);

        foreach ($titleData as $languageId => $value) {
            $uniqueSlugRule = Rule::unique('page_lang', 'slug')
                ->where('language_id', $languageId);

            if ($pageId) {
                $uniqueSlugRule->where(function ($query) use ($pageId, $languageId) {
                    $query->where('page_id', '!=', $pageId)
                        ->orWhere('language_id', '!=', $languageId);
                });
            }

            $rules["title.{$languageId}"] = ['required', 'string', 'max:255'];
            $rules["slug.{$languageId}"] = [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                $uniqueSlugRule,
            ];
            $rules["content.{$languageId}"] = ['nullable', 'string'];
            $rules["meta_title.{$languageId}"] = ['required', 'string', 'max:255'];
            $rules["meta_description.{$languageId}"] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        $messages = [];
        $titleData = $this->input('title', []);

        foreach ($titleData as $languageId => $value) {
            $messages["title.{$languageId}.required"] = __('validation.name.required');
            $messages["title.{$languageId}.string"] = __('validation.name.string');
            $messages["title.{$languageId}.max"] = __('validation.name.max', ['max' => 255]);
            $messages["slug.{$languageId}.required"] = __('validation.slug.required');
            $messages["slug.{$languageId}.string"] = __('validation.slug.string');
            $messages["slug.{$languageId}.max"] = __('validation.slug.max', ['max' => 255]);
            $messages["slug.{$languageId}.regex"] = __('validation.slug.regex');
            $messages["slug.{$languageId}.unique"] = __('validation.slug.unique');
            $messages["meta_title.{$languageId}.required"] = __('validation.meta_title.required');
            $messages["meta_title.{$languageId}.string"] = __('validation.meta_title.string');
            $messages["meta_title.{$languageId}.max"] = __('validation.meta_title.max', ['max' => 255]);
            $messages["meta_description.{$languageId}.string"] = __('validation.meta_description.string');
            $messages["meta_description.{$languageId}.max"] = __('validation.meta_description.max', ['max' => 255]);
        }

        $messages['sort_order.integer'] = __('validation.sort_order.integer');
        $messages['sort_order.min'] = __('validation.sort_order.min', ['min' => 0]);

        return $messages;
    }
}
