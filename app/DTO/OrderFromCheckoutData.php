<?php

namespace App\DTO;

readonly class OrderFromCheckoutData
{
    public function __construct(
        public ?int $customerId,
        public ?int $shippingAddressId,
        public string $firstname,
        public string $lastname,
        public string $email,
        public string $shippingFirstname,
        public string $shippingLastname,
        public ?string $shippingCompany,
        public string $shippingAddress1,
        public ?string $shippingAddress2,
        public string $shippingCity,
        public ?string $shippingPostcode,
        public int $shippingCountryId,
        public string $shippingMethodCode,
        public float $shippingCost,
        public string $paymentMethodCode,
        public int $languageId,
        public int $currencyId,
        public ?string $comment = null,
        public ?string $ip = null,
        public ?string $userAgent = null,
    ) {}

    /**
     * Build from checkout state: session data (customer, address, shipping method, payment method)
     * plus request/context (language, currency, ip, user agent). Use in controller after session is validated.
     *
     * @param  array<string, mixed>  $customerSession  session('customer')
     * @param  array<string, mixed>  $shippingAddress  session('shipping_address') (may contain address_id for linked Address)
     * @param  array{code: string, cost?: float}  $shippingMethod  session('shipping_method')
     * @param  array{code: string}  $paymentMethod  session('payment_method')
     */
    public static function create(
        array $customerSession,
        array $shippingAddress,
        array $shippingMethod,
        array $paymentMethod,
        int $languageId,
        int $currencyId,
        ?string $comment = null,
        ?string $ip = null,
        ?string $userAgent = null,
    ): self {
        return new self(
            customerId: $customerSession['customer_id'] ?? null,
            firstname: $customerSession['firstname'],
            lastname: $customerSession['lastname'],
            email: $customerSession['email'],
            shippingAddressId: ! empty($shippingAddress['address_id']) ? (int) $shippingAddress['address_id'] : null,
            shippingFirstname: $shippingAddress['firstname'],
            shippingLastname: $shippingAddress['lastname'],
            shippingCompany: $shippingAddress['company'] ?? null,
            shippingAddress1: $shippingAddress['address_1'],
            shippingAddress2: $shippingAddress['address_2'] ?? null,
            shippingCity: $shippingAddress['city'],
            shippingPostcode: $shippingAddress['postcode'] ?? null,
            shippingCountryId: $shippingAddress['country_id'],
            shippingMethodCode: $shippingMethod['code'] ?? '',
            shippingCost: (float) ($shippingMethod['cost'] ?? 0),
            paymentMethodCode: $paymentMethod['code'] ?? '',
            languageId: $languageId,
            currencyId: $currencyId,
            comment: $comment,
            ip: $ip,
            userAgent: $userAgent,
        );
    }
}
