<?php

namespace App\Http\Requests\Admin;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;

class CustomerAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $countryId = $this->filled('country_id') ? (int) $this->input('country_id') : null;
        $postcodeRequired = Country::isPostcodeRequired($countryId);

        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:128',
            'postcode' => [$postcodeRequired ? 'required' : 'nullable', 'string', 'max:10'],
            'country_id' => 'required|exists:countries,id',
            'default' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'firstname.required' => __('validation.firstname.required'),
            'lastname.required' => __('validation.lastname.required'),
            'address_1.required' => __('validation.address_1.required'),
            'city.required' => __('validation.city.required'),
            'postcode.required' => __('validation.postcode.required'),
            'country_id.required' => __('validation.country_id.required'),
            'country_id.exists' => __('validation.country_id.exists'),
        ];
    }
}
