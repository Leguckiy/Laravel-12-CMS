<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;
use App\Http\Requests\Front\LoginRequest;
use App\Http\Requests\Front\RegisterRequest;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            $request->session()->regenerate();

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
        $defaultGroupId = (int) Setting::get('config_customer_group_id');
        $defaultGroup = CustomerGroup::query()->find($defaultGroupId);

        $customer = Customer::query()->create([
            'customer_group_id' => $defaultGroup->id,
            'firstname' => $request->validated('firstname'),
            'lastname' => $request->validated('lastname'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'status' => true,
        ]);

        Auth::guard('web')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('front.home', ['lang' => $this->context->language->code])
            ->with('status', __('front/auth.registered'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->regenerate();

        return redirect()->back();
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
