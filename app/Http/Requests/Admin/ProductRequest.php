<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $product = $this->route('product');
        $productId = $product ? $product->id : null;

        $rules = [
            'reference' => 'required|string|max:64',
            'quantity' => 'nullable|integer|min:0',
            'stock_status' => 'required|exists:stock_statuses,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'price' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'features' => 'nullable|array',
        ];

        $nameData = $this->input('name', []);

        foreach ($nameData as $languageId => $value) {
            $uniqueSlugRule = Rule::unique('product_lang', 'slug')
                ->where('language_id', $languageId);

            if ($productId) {
                $uniqueSlugRule->where(function ($query) use ($productId, $languageId) {
                    $query->where('product_id', '!=', $productId)
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
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $features = $this->input('features', []);

            if (empty($features)) {
                return;
            }

            // Filter out empty feature rows (where both feature_id and feature_value_id are empty)
            $combinations = [];

            foreach ($features as $index => $feature) {
                $featureId = $feature['feature_id'] ?? null;
                $featureValueId = $feature['feature_value_id'] ?? null;

                // If both are empty, skip this row (user added row but didn't fill it)
                if (empty($featureId) && empty($featureValueId)) {
                    continue;
                }

                // If one is filled but other is not, it's an error
                if (empty($featureId)) {
                    $validator->errors()->add(
                        "features.{$index}.feature_id",
                        __('validation.required', ['attribute' => __('admin.feature')])
                    );

                    continue;
                }

                if (empty($featureValueId)) {
                    $validator->errors()->add(
                        "features.{$index}.feature_value_id",
                        __('validation.required', ['attribute' => __('admin.pre_defined_value')])
                    );

                    continue;
                }

                // Check for duplicates
                $combination = $featureId.'_'.$featureValueId;
                if (in_array($combination, $combinations, true)) {
                    $errorMessage = __('validation.duplicate_feature_combination', [
                        'attribute' => __('admin.feature'),
                    ]);
                    $validator->errors()->add(
                        "features.{$index}.feature_id",
                        $errorMessage
                    );
                    $validator->errors()->add(
                        "features.{$index}.feature_value_id",
                        $errorMessage
                    );

                    continue;
                }

                $combinations[] = $combination;
            }
        });
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

        $messages['reference.required'] = __('validation.required', ['attribute' => 'reference']);
        $messages['reference.string'] = __('validation.string', ['attribute' => 'reference']);
        $messages['reference.max'] = __('validation.max.string', ['attribute' => 'reference', 'max' => 64]);

        $messages['stock_status.required'] = __('validation.required', ['attribute' => 'stock_status']);
        $messages['stock_status.exists'] = __('validation.exists', ['attribute' => 'stock_status']);

        $messages['image.image'] = __('validation.image.image');
        $messages['image.mimes'] = __('validation.image.mimes', ['values' => 'jpg, jpeg, png, webp, gif']);
        $messages['image.max'] = __('validation.image.max', ['max' => 2048]);

        $messages['quantity.integer'] = __('validation.integer', ['attribute' => 'quantity']);
        $messages['quantity.min'] = __('validation.min.numeric', ['attribute' => 'quantity', 'min' => 0]);

        $messages['price.numeric'] = __('validation.numeric', ['attribute' => 'price']);
        $messages['price.min'] = __('validation.min.numeric', ['attribute' => 'price', 'min' => 0]);

        $messages['sort_order.integer'] = __('validation.integer', ['attribute' => 'sort_order']);
        $messages['sort_order.min'] = __('validation.min.numeric', ['attribute' => 'sort_order', 'min' => 0]);

        $features = $this->input('features', []);
        foreach ($features as $index => $feature) {
            $messages["features.{$index}.feature_id.required"] = __('validation.required', ['attribute' => __('admin.feature')]);
            $messages["features.{$index}.feature_id.duplicate_feature_combination"] = __('validation.duplicate_feature_combination', ['attribute' => __('admin.feature')]);

            $messages["features.{$index}.feature_value_id.required"] = __('validation.required', ['attribute' => __('admin.pre_defined_value')]);
        }

        return $messages;
    }
}
