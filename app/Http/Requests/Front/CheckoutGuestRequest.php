<?php

namespace App\Http\Requests\Front;

use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutGuestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $isRegister = $this->input('account_type') === 'register';

        $countryId = $this->filled('country_id') ? (int) $this->input('country_id') : null;
        $postcodeRequired = Country::isPostcodeRequired($countryId);

        $rules = [
            'account_type' => ['required', 'string', Rule::in(['register', 'guest'])],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                $isRegister ? 'unique:customers,email' : [],
            ],
            'company' => ['nullable', 'string', 'max:255'],
            'address_1' => ['required', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:128'],
            'postcode' => [$postcodeRequired ? 'required' : 'nullable', 'string', 'max:10'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ];

        if ($isRegister) {
            $rules['password'] = ['required', 'string', 'min:6'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'account_type.required' => __('front/checkout.validation_account_type_required'),
            'account_type.in' => __('front/checkout.validation_account_type_in'),
            'firstname.required' => __('validation.firstname.required'),
            'lastname.required' => __('validation.lastname.required'),
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.email'),
            'email.unique' => __('validation.email.unique'),
            'password.required' => __('validation.password.required'),
            'password.min' => __('validation.password.min'),
            'address_1.required' => __('front/checkout.validation_address_1_required'),
            'city.required' => __('front/checkout.validation_city_required'),
            'postcode.required' => __('front/checkout.validation_postcode_required'),
            'country_id.required' => __('front/checkout.validation_country_required'),
            'country_id.exists' => __('front/checkout.validation_country_exists'),
        ];
    }
}
