<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;
use App\Http\Requests\Front\CheckoutCustomerAddressRequest;
use App\Http\Requests\Front\CheckoutGuestRequest;
use App\Models\Address;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends FrontController
{
    public function index(CartService $cartService): View|RedirectResponse
    {
        if ($this->isCartEmpty()) {
            return redirect()
                ->route('front.cart.show', ['lang' => $this->context->getLanguage()->code])
                ->with('error', __('front/checkout.cart_empty'));
        }

        $cart = $this->context->cart;
        $languageId = $this->context->language->id;
        $currency = $this->context->currency;
        $display = $cartService->getCartRowsForDisplay($cart, $languageId);

        $countryOptions = Country::getOptionsForCheckout($languageId);
        $countryNames = collect($countryOptions)->pluck('name', 'id')->all();

        $this->setBreadcrumbs([
            ['label' => __('front/general.breadcrumb_home'), 'url' => route('front.home', ['lang' => $this->context->getLanguage()?->code])],
            ['label' => __('front/general.cart_title'), 'url' => route('front.cart.show', ['lang' => $this->context->getLanguage()?->code])],
            ['label' => __('front/checkout.title'), 'url' => null],
        ]);

        $this->languageUrls = $this->context->getLanguages()
            ->keyBy('code')
            ->map(fn ($l) => route('front.checkout.index', ['lang' => $l->code]))
            ->toArray();

        $customer = $this->context->getCustomer();
        $customerSession = session('customer', []);
        $shippingAddress = session('shipping_address', []);
        $addresses = $customer?->getAddressesForCheckout() ?? collect();

        return view('front.checkout.index', [
            'cartRows' => $display['cartRows'],
            'subtotal' => $display['subtotal'],
            'currency' => $currency,
            'countryOptions' => $countryOptions,
            'countryNames' => $countryNames,
            'customer' => $customer,
            'customerSession' => $customerSession,
            'shippingAddress' => $shippingAddress,
            'addresses' => $addresses,
        ]);
    }

    /**
     * Guest checkout step: register (create user + address) or continue as guest (session only).
     */
    public function submitGuestStep(CheckoutGuestRequest $request, CheckoutService $checkoutService): JsonResponse
    {
        $cartErrorResponse = $this->validateCartForCheckout();
        if ($cartErrorResponse !== null) {
            return $cartErrorResponse;
        }

        $validated = $request->validated();

        if ($validated['account_type'] === 'register') {
            $defaultGroup = CustomerGroup::getDefaultForRegistration();
            if ($defaultGroup === null) {
                return response()->json([
                    'success' => false,
                    'message' => __('front/checkout.registration_unavailable'),
                ], 503);
            }
            $this->handleGuestRegister($request, $validated, $checkoutService, $defaultGroup);
            $message = __('front/checkout.details_saved');
        } else {
            $request->session()->put('customer', $checkoutService->guestCustomerSessionFromValidated($validated));
            $request->session()->put('shipping_address', $checkoutService->guestShippingAddressSessionFromValidated($validated));
            $message = __('front/checkout.guest_details_saved');
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Logged-in customer: set shipping address for checkout.
     * Either select existing address (address_id in request) or create a new one (address fields).
     */
    public function setCustomerShippingAddress(CheckoutCustomerAddressRequest $request, CheckoutService $checkoutService): JsonResponse
    {
        $cartErrorResponse = $this->validateCartForCheckout();
        if ($cartErrorResponse !== null) {
            return $cartErrorResponse;
        }

        $customer = $request->user('web');
        $validated = $request->validated();

        // Select existing address (form sent address_id from dropdown).
        if (! empty($validated['address_id'])) {
            $addressId = (int) $validated['address_id'];
            $address = $customer->addresses()->whereKey($addressId)->first();
            if (! $address) {
                return response()->json([
                    'success' => false,
                    'message' => __('front/checkout.shipping_address_not_found'),
                ], 422);
            }

            $request->session()->put('customer', $checkoutService->customerSessionFromCustomer($customer));
            $request->session()->put('shipping_address', $address->toCheckoutSessionArray());

            return response()->json([
                'success' => true,
                'message' => __('front/checkout.shipping_address_changed'),
            ]);
        }

        // Create new address and set it for checkout.
        $address = $this->createAddress($customer, $validated);

        $request->session()->put('customer', $checkoutService->customerSessionFromCustomer($customer));
        $request->session()->put('shipping_address', $address->toCheckoutSessionArray());

        return response()->json([
            'success' => true,
            'message' => __('front/checkout.details_saved'),
        ]);
    }

    /**
     * Create customer + address, attach cart, login, store in session.
     *
     * @param array<string, mixed> $validated
     */
    private function handleGuestRegister(CheckoutGuestRequest $request, array $validated, CheckoutService $checkoutService, CustomerGroup $defaultGroup): void
    {
        $customer = Customer::query()->create([
            'customer_group_id' => $defaultGroup->id,
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'status' => true,
        ]);

        $address = $this->createAddress($customer, $validated);

        if ($this->context->cart !== null) {
            $this->context->cart->attachToCustomer($customer->id);
        }

        Auth::guard('web')->login($customer);

        $request->session()->put('customer', $checkoutService->customerSessionFromCustomer($customer));
        $request->session()->put('shipping_address', $address->toCheckoutSessionArray());
    }

    private function isCartEmpty(): bool
    {
        $cart = $this->context->cart;

        return $cart === null || $cart->items()->count() === 0;
    }

    /**
     * Return 422 JSON response if cart is empty or missing; otherwise null.
     */
    private function validateCartForCheckout(): ?JsonResponse
    {
        if ($this->isCartEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('front/checkout.cart_empty'),
            ], 422);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createAddress(Customer $customer, array $data): Address
    {
        return Address::query()->create([
            'customer_id' => $customer->id,
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'company' => $data['company'],
            'address_1' => $data['address_1'],
            'address_2' => $data['address_2'],
            'city' => $data['city'],
            'postcode' => $data['postcode'],
            'country_id' => $data['country_id'],
        ]);
    }
}
