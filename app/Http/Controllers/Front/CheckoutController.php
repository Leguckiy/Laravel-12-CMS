<?php

namespace App\Http\Controllers\Front;

use App\Enums\CheckoutStep;
use App\Http\Controllers\FrontController;
use App\Http\Requests\Front\Checkout\CheckoutAddCustomerAddressRequest;
use App\Http\Requests\Front\Checkout\CheckoutGetPaymentMethodsRequest;
use App\Http\Requests\Front\Checkout\CheckoutGetShippingMethodsRequest;
use App\Http\Requests\Front\Checkout\CheckoutGuestRequest;
use App\Http\Requests\Front\Checkout\CheckoutSetCustomerAddressRequest;
use App\Http\Requests\Front\Checkout\CheckoutSetPaymentMethodRequest;
use App\Http\Requests\Front\Checkout\CheckoutSetShippingMethodRequest;
use App\Models\Address;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\Payment\PaymentService;
use App\Services\Shipping\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends FrontController
{
    public function index(CartService $cartService, PaymentService $paymentService, ShippingService $shippingService): View|RedirectResponse
    {
        if ($this->context->isCartEmpty()) {
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

        $checkoutStep = session('checkout_step', CheckoutStep::PersonalAddress->value);
        $shippingMethod = session('shipping_method');
        $paymentMethod = session('payment_method');
        if (! empty($shippingMethod['id'])) {
            $shippingMethod['name'] = $shippingService->getMethodTitle($shippingMethod['id']);
        }
        if (! empty($paymentMethod['id'])) {
            $paymentMethod['name'] = $paymentService->getMethodTitle($paymentMethod['id']);
        }
        $paymentInstructions = '';
        if (! empty($paymentMethod['id'])) {
            $paymentInstructions = $paymentService->getInstructionsForMethod(
                $paymentMethod['id'],
                $this->context->language->id
            );
        }

        return view('front.checkout.index', [
            'cartRows' => $display['cartRows'],
            'subtotal' => $display['subtotal'],
            'currency' => $currency,
            'countryOptions' => $countryOptions,
            'countryNames' => $countryNames,
            'customer' => $customer,
            'customerSession' => $customerSession,
            'shippingAddress' => $shippingAddress,
            'shippingMethod' => $shippingMethod,
            'paymentMethod' => $paymentMethod,
            'paymentInstructions' => $paymentInstructions,
            'addresses' => $addresses,
            'checkoutStep' => $checkoutStep,
        ]);
    }

    /**
     * Guest checkout step: register (create user + address) or continue as guest (session only).
     */
    public function submitGuestStep(CheckoutGuestRequest $request, CheckoutService $checkoutService): JsonResponse
    {
        $validated = $request->validated();

        $sessionRegenerated = false;
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
            $sessionRegenerated = true;
        } else {
            $request->session()->put('customer', $checkoutService->guestCustomerSessionFromValidated($validated));
            $request->session()->put('shipping_address', $checkoutService->guestShippingAddressSessionFromValidated($validated));
            $message = __('front/checkout.guest_details_saved');
        }

        $request->session()->forget(['shipping_method', 'payment_method']);
        $request->session()->put('checkout_step', CheckoutStep::Delivery->value);

        $payload = [
            'success' => true,
            'message' => $message,
        ];
        if ($sessionRegenerated) {
            $payload['csrf_token'] = $request->session()->token();
        }

        return response()->json($payload);
    }

    /**
     * Logged-in customer: set shipping address for checkout (select existing address).
     */
    public function setCustomerShippingAddress(CheckoutSetCustomerAddressRequest $request, CheckoutService $checkoutService): JsonResponse
    {
        $customer = $request->user('web');
        $address = $request->getAddress();

        $request->session()->put('customer', $checkoutService->customerSessionFromCustomer($customer));
        $request->session()->put('shipping_address', $address->toCheckoutSessionArray());
        $request->session()->forget(['shipping_method', 'payment_method']);
        $request->session()->put('checkout_step', CheckoutStep::Delivery->value);

        return response()->json([
            'success' => true,
            'message' => __('front/checkout.shipping_address_changed'),
        ]);
    }

    /**
     * Logged-in customer: add new shipping address and set it for checkout.
     */
    public function addCustomerShippingAddress(CheckoutAddCustomerAddressRequest $request, CheckoutService $checkoutService): JsonResponse
    {
        $customer = $request->user('web');
        $address = $this->createAddress($customer, $request->validated());

        $request->session()->put('customer', $checkoutService->customerSessionFromCustomer($customer));
        $request->session()->put('shipping_address', $address->toCheckoutSessionArray());
        $request->session()->forget(['shipping_method', 'payment_method']);
        $request->session()->put('checkout_step', CheckoutStep::Delivery->value);

        return response()->json([
            'success' => true,
            'message' => __('front/checkout.details_saved'),
        ]);
    }

    /**
     * AJAX: return available shipping methods for the address in session.
     * Address is taken from session (set on step 1); nothing is passed in the request.
     */
    public function getShippingMethods(CheckoutGetShippingMethodsRequest $request, ShippingService $shippingService): JsonResponse
    {
        $cart = $this->context->cart;
        $shippingAddress = $request->session()->get('shipping_address');
        $countryId = (int) ($shippingAddress['country_id']);
        $methods = $shippingService->getAvailableMethods($cart, $countryId, $this->context->currency);
        $shippingMethod = $request->session()->get('shipping_method');
        $selectedId = isset($shippingMethod['id']) ? $shippingMethod['id'] : null;

        return response()->json([
            'success' => true,
            'methods' => $methods,
            'selected_id' => $selectedId,
        ]);
    }

    /**
     * AJAX: save selected shipping method to session and advance to payment step.
     */
    public function setShippingMethod(CheckoutSetShippingMethodRequest $request, CartService $cartService, ShippingService $shippingService): JsonResponse
    {
        $cart = $this->context->cart;
        $shippingAddress = $request->session()->get('shipping_address');
        $countryId = (int) ($shippingAddress['country_id'] ?? 0);
        $methods = $shippingService->getAvailableMethods($cart, $countryId, $this->context->currency);

        if ($methods === []) {
            return response()->json([
                'success' => false,
                'message' => __('front/checkout.shipping_methods_none_available'),
            ], 422);
        }

        $methodId = $request->validated('method_id');
        $selected = collect($methods)->firstWhere('id', $methodId);
        if (! $selected) {
            return response()->json([
                'success' => false,
                'message' => __('front/checkout.error_generic'),
            ], 422);
        }

        $request->session()->forget('payment_method');
        $request->session()->put('shipping_method', [
            'id' => $selected['id'],
            'cost' => $selected['cost'],
        ]);
        $request->session()->put('checkout_step', CheckoutStep::Payment->value);

        $subtotal = $cartService->getSubtotal($cart);
        $orderTotal = $subtotal + (float) $selected['cost'];
        $orderTotalFormatted = $this->context->currency->formatPriceFromBase((string) $orderTotal);

        return response()->json([
            'success' => true,
            'message' => __('front/checkout.shipping_method_changed'),
            'method' => $selected,
            'order_total_formatted' => $orderTotalFormatted,
        ]);
    }

    /**
     * AJAX: return available payment methods for the address in session.
     */
    public function getPaymentMethods(CheckoutGetPaymentMethodsRequest $request, PaymentService $paymentService): JsonResponse
    {
        $cart = $this->context->cart;
        $shippingAddress = $request->session()->get('shipping_address');
        $countryId = (int) ($shippingAddress['country_id']);
        $rawMethods = $paymentService->getAvailableMethods($cart, $countryId);
        $methods = array_map(fn (array $m) => [
            'id' => $m['code'],
            'name' => $m['title'],
        ], $rawMethods);
        $paymentMethod = $request->session()->get('payment_method');
        $selectedId = isset($paymentMethod['id']) ? $paymentMethod['id'] : null;

        return response()->json([
            'success' => true,
            'methods' => $methods,
            'selected_id' => $selectedId,
        ]);
    }

    /**
     * AJAX: save selected payment method to session.
     */
    public function setPaymentMethod(CheckoutSetPaymentMethodRequest $request, PaymentService $paymentService): JsonResponse
    {
        $cart = $this->context->cart;
        $shippingAddress = $request->session()->get('shipping_address');
        $countryId = (int) ($shippingAddress['country_id'] ?? 0);
        $rawMethods = $paymentService->getAvailableMethods($cart, $countryId);

        if ($rawMethods === []) {
            return response()->json([
                'success' => false,
                'message' => __('front/checkout.payment_methods_none_available'),
            ], 422);
        }

        $methodId = $request->validated('method_id');
        $selected = collect($rawMethods)->firstWhere('code', $methodId);
        if (! $selected) {
            return response()->json([
                'success' => false,
                'message' => __('front/checkout.error_generic'),
            ], 422);
        }

        $request->session()->put('payment_method', [
            'id' => $selected['code'],
        ]);
        $request->session()->put('checkout_step', CheckoutStep::Confirmation->value);

        $instructions = $paymentService->getInstructionsForMethod(
            $selected['code'],
            $this->context->language->id
        );

        return response()->json([
            'success' => true,
            'message' => __('front/checkout.payment_method_changed'),
            'method' => ['id' => $selected['code'], 'name' => $selected['title']],
            'instructions' => $instructions,
        ]);
    }

    /**
     * Create customer + address, attach cart, login, store in session.
     *
     * @param  array<string, mixed>  $validated
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
        $request->session()->forget(['shipping_method', 'payment_method']);
        $request->session()->put('checkout_step', CheckoutStep::Delivery->value);
    }

    /**
     * @param  array<string, mixed>  $data
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
