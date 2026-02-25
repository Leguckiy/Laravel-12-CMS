<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;
use App\Http\Requests\Front\LoginRequest;
use App\Http\Requests\Front\RegisterRequest;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends FrontController
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('front.home', ['lang' => $this->context->getLanguage()?->code]);
        }

        $this->setBreadcrumbs([
            ['label' => __('front/general.breadcrumb_home'), 'url' => route('front.home', ['lang' => $this->context->getLanguage()?->code])],
            ['label' => __('front/auth.login_title'), 'url' => null],
        ]);

        $this->languageUrls = $this->context->getLanguages()
            ->keyBy('code')
            ->map(fn ($l) => route('front.auth.login.show', ['lang' => $l->code]))
            ->toArray();

        return view('front.auth.login');
    }

    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('front.home', ['lang' => $this->context->getLanguage()?->code]);
        }

        $this->setBreadcrumbs([
            ['label' => __('front/general.breadcrumb_home'), 'url' => route('front.home', ['lang' => $this->context->getLanguage()?->code])],
            ['label' => __('front/auth.register_title'), 'url' => null],
        ]);

        $this->languageUrls = $this->context->getLanguages()
            ->keyBy('code')
            ->map(fn ($l) => route('front.auth.register.show', ['lang' => $l->code]))
            ->toArray();

        return view('front.auth.register');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::guard('web')->attempt($credentials)) {
            $this->clearCheckoutSession();
            $customerId = (int) Auth::guard('web')->id();
            $cartToken = $request->session()->get('cart_token');
            if (is_string($cartToken)) {
                $guestCart = Cart::findByToken($cartToken);
                if ($guestCart !== null) {
                    $guestCart->delete();
                }
                $customerCart = Cart::findForCustomer($customerId);
                if ($customerCart !== null) {
                    $customerCart->syncToken($cartToken);
                }
            }

            $back = $request->input('back');
            if ($back && $this->isValidBackUrl($back)) {
                return redirect()->away($back);
            }

            return redirect()->route('front.home', ['lang' => $this->context->getLanguage()?->code]);
        }

        return redirect()->back()
            ->withErrors(['email' => __('front/auth.invalid_credentials')])
            ->withInput($request->only('email'));
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $defaultGroup = CustomerGroup::getDefaultForRegistration();
        if ($defaultGroup === null) {
            return redirect()->back()
                ->withErrors(['email' => __('front/checkout.registration_unavailable')])
                ->withInput($request->only('email', 'firstname', 'lastname'));
        }

        $customer = Customer::query()->create([
            'customer_group_id' => $defaultGroup->id,
            'firstname' => $request->validated('firstname'),
            'lastname' => $request->validated('lastname'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'status' => true,
        ]);

        if ($this->context->cart !== null) {
            $this->context->cart->attachToCustomer($customer->id);
        }

        Auth::guard('web')->login($customer);

        $this->clearCheckoutSession();

        return redirect()->route('front.home', ['lang' => $this->context->language->code])
            ->with('status', __('front/auth.registered'));
    }

    public function logout(): RedirectResponse
    {
        $this->clearCheckoutSession();
        $customerId = Auth::guard('web')->id();
        if ($customerId !== null) {
            Cart::unbindTokenForCustomer((int) $customerId);
        }
        Auth::guard('web')->logout();

        return redirect()->back();
    }

    private function clearCheckoutSession(): void
    {
        request()->session()->forget([
            'customer',
            'shipping_address',
            'shipping_method',
            'payment_method',
            'checkout_step',
        ]);
    }

    private function isValidBackUrl(string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?: request()->getHost();

        return $host !== null && strtolower($host) === strtolower($appHost);
    }
}
