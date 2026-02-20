<?php

namespace App\Http\Requests\Front\Checkout;

use App\Http\Requests\Front\Concerns\FailsIfCartEmpty;
use App\Models\Address;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class CheckoutSetCustomerAddressRequest extends FormRequest
{
    use FailsIfCartEmpty;

    private ?Address $resolvedAddress = null;

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
        return [
            'address_id' => ['required', 'integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'address_id.required' => __('front/checkout.validation_address_required'),
        ];
    }

    protected function passedValidation(): void
    {
        try {
            $this->resolvedAddress = $this->user('web')->addresses()->findOrFail($this->validated('address_id'));
        } catch (ModelNotFoundException) {
            throw new HttpResponseException(
                new JsonResponse([
                    'success' => false,
                    'message' => __('front/checkout.validation_address_exists'),
                    'errors' => ['address_id' => [__('front/checkout.validation_address_exists')]],
                ], 422)
            );
        }
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

    /**
     * Get the address resolved in passedValidation().
     * Must be called only after successful validation (e.g. from the controller).
     */
    public function getAddress(): Address
    {
        return $this->resolvedAddress;
    }
}
