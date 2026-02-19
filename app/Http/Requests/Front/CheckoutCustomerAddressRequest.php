<?php

namespace App\Http\Requests\Front;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutCustomerAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('web') !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $customerId = (int) $this->user('web')->id;
        $addressId = $this->input('address_id');

        if ($addressId !== null && $addressId !== '') {
            return [
                'address_id' => [
                    'required',
                    'integer',
                    Rule::exists('addresses', 'id')->where('customer_id', $customerId),
                ],
            ];
        }

        $countryId = $this->filled('country_id') ? (int) $this->input('country_id') : null;
        $postcodeRequired = Country::isPostcodeRequired($countryId);

        return [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'address_1' => ['required', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:128'],
            'postcode' => [$postcodeRequired ? 'required' : 'nullable', 'string', 'max:10'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'address_id.required' => __('front/checkout.validation_address_required'),
            'address_id.exists' => __('front/checkout.validation_address_exists'),
            'firstname.required' => __('validation.firstname.required'),
            'lastname.required' => __('validation.lastname.required'),
            'address_1.required' => __('front/checkout.validation_address_1_required'),
            'city.required' => __('front/checkout.validation_city_required'),
            'postcode.required' => __('front/checkout.validation_postcode_required'),
            'country_id.required' => __('front/checkout.validation_country_required'),
            'country_id.exists' => __('front/checkout.validation_country_exists'),
        ];
    }
}
