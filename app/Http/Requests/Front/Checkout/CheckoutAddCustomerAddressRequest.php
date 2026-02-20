<?php

namespace App\Http\Requests\Front\Checkout;

use App\Http\Requests\Front\Concerns\FailsIfCartEmpty;
use App\Models\Country;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class CheckoutAddCustomerAddressRequest extends FormRequest
{
    use FailsIfCartEmpty;

    public function authorize(): bool
    {
        if ($this->user('web') === null) {
            return false;
        }

        $this->failIfCartEmpty();

        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
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
            'firstname.required' => __('validation.firstname.required'),
            'lastname.required' => __('validation.lastname.required'),
            'address_1.required' => __('front/checkout.validation_address_1_required'),
            'city.required' => __('front/checkout.validation_city_required'),
            'postcode.required' => __('front/checkout.validation_postcode_required'),
            'country_id.required' => __('front/checkout.validation_country_required'),
            'country_id.exists' => __('front/checkout.validation_country_exists'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            new JsonResponse([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ], 422)
        );
    }
}
