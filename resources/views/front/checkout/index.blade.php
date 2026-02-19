@extends('layouts.front')

@push('styles')
    <link href="{{ asset('css/front/auth.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div id="checkout-js-messages" class="visually-hidden" aria-hidden="true"
         data-error-generic="{{ __('front/checkout.error_generic') }}"
         data-validation-form="{{ __('front/checkout.validation_form_errors') }}"
         data-address-required="{{ __('front/checkout.shipping_address_not_found') }}"></div>
    <h1 class="mb-4">{{ __('front/checkout.title') }}</h1>

    @guest
        <p class="mb-4 text-muted">
            {!! __('front/checkout.login_prompt', ['url' => route('front.auth.login.show', ['lang' => request()->route('lang'), 'back' => url()->current()])]) !!}
        </p>
    @endguest

    <div class="row">
        <div class="col-lg-7 mb-4">
            @guest
                <form id="checkout-guest-form" class="checkout-form" method="post" action="{{ route('front.checkout.guest', ['lang' => request()->route('lang')]) }}" data-validation-form-message="{{ __('front/checkout.validation_form_errors') }}">
                    <input type="hidden" name="account_type" id="checkout-guest-account-type" value="{{ old('account_type', $customerSession['account_type'] ?? 'register') }}">

                    <div class="card mb-4">
                        <div class="card-header">
                            <h2 class="h6 mb-0">{{ __('front/checkout.account_type') }}</h2>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="account_type_radio" id="account-register" value="register" {{ old('account_type', $customerSession['account_type'] ?? 'register') === 'register' ? 'checked' : '' }}>
                                <label class="form-check-label" for="account-register">{{ __('front/checkout.register_account') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="account_type_radio" id="account-guest" value="guest" {{ old('account_type', $customerSession['account_type'] ?? null) === 'guest' ? 'checked' : '' }}>
                                <label class="form-check-label" for="account-guest">{{ __('front/checkout.guest_checkout') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h2 class="h6 mb-0">{{ __('front/checkout.your_personal_details') }}</h2>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <div class="col mb-3 required">
                                    <label for="checkout-guest-firstname" class="form-label">{{ __('front/auth.firstname') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="firstname" id="checkout-guest-firstname" class="form-control" value="{{ old('firstname', $customerSession['firstname'] ?? '') }}" placeholder="{{ __('front/auth.firstname') }}">
                                    <div class="invalid-feedback js-checkout-error" data-field="firstname"></div>
                                </div>
                                <div class="col mb-3 required">
                                    <label for="checkout-guest-lastname" class="form-label">{{ __('front/auth.lastname') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="lastname" id="checkout-guest-lastname" class="form-control" value="{{ old('lastname', $customerSession['lastname'] ?? '') }}" placeholder="{{ __('front/auth.lastname') }}">
                                    <div class="invalid-feedback js-checkout-error" data-field="lastname"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3 required">
                                    <label for="checkout-guest-email" class="form-label">{{ __('front/auth.email') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="email" id="checkout-guest-email" class="form-control" value="{{ old('email', $customerSession['email'] ?? '') }}" placeholder="{{ __('front/auth.email') }}" autocomplete="email">
                                    <div class="invalid-feedback js-checkout-error" data-field="email"></div>
                                </div>
                            </div>
                            <div class="row js-checkout-password-row">
                                <div class="col mb-3 required">
                                    <label for="checkout-guest-password" class="form-label">{{ __('front/checkout.your_password') }} <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="checkout-guest-password" class="form-control" placeholder="{{ __('front/auth.password') }}" autocomplete="new-password">
                                    <div class="invalid-feedback js-checkout-error" data-field="password"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h2 class="h6 mb-0">{{ __('front/checkout.shipping_address') }}</h2>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <div class="col mb-3">
                                    <label for="checkout-guest-company" class="form-label">{{ __('front/checkout.company') }}</label>
                                    <input type="text" name="company" id="checkout-guest-company" class="form-control" value="{{ old('company', $shippingAddress['company'] ?? '') }}" placeholder="{{ __('front/checkout.company') }}">
                                    <div class="invalid-feedback js-checkout-error" data-field="company"></div>
                                </div>
                                <div class="col mb-3 required">
                                    <label for="checkout-guest-address_1" class="form-label">{{ __('front/checkout.address_1') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="address_1" id="checkout-guest-address_1" class="form-control" value="{{ old('address_1', $shippingAddress['address_1'] ?? '') }}" placeholder="{{ __('front/checkout.address_1') }}">
                                    <div class="invalid-feedback js-checkout-error" data-field="address_1"></div>
                                </div>
                                <div class="col mb-3">
                                    <label for="checkout-guest-address_2" class="form-label">{{ __('front/checkout.address_2') }}</label>
                                    <input type="text" name="address_2" id="checkout-guest-address_2" class="form-control" value="{{ old('address_2', $shippingAddress['address_2'] ?? '') }}" placeholder="{{ __('front/checkout.address_2') }}">
                                    <div class="invalid-feedback js-checkout-error" data-field="address_2"></div>
                                </div>
                                <div class="col mb-3 required">
                                    <label for="checkout-guest-city" class="form-label">{{ __('front/checkout.city') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="city" id="checkout-guest-city" class="form-control" value="{{ old('city', $shippingAddress['city'] ?? '') }}" placeholder="{{ __('front/checkout.city') }}">
                                    <div class="invalid-feedback js-checkout-error" data-field="city"></div>
                                </div>
                                <div class="col mb-3 js-postcode-col">
                                    <label for="checkout-guest-postcode" class="form-label">{{ __('front/checkout.postcode') }} <span class="text-danger js-postcode-asterisk d-none">*</span></label>
                                    <input type="text" name="postcode" id="checkout-guest-postcode" class="form-control" value="{{ old('postcode', $shippingAddress['postcode'] ?? '') }}" placeholder="{{ __('front/checkout.postcode') }}">
                                    <div class="invalid-feedback js-checkout-error" data-field="postcode"></div>
                                </div>
                                <div class="col mb-3 required">
                                    <label for="checkout-guest-country_id" class="form-label">{{ __('front/checkout.country') }} <span class="text-danger">*</span></label>
                                    <select name="country_id" id="checkout-guest-country_id" class="form-select">
                                        <option value="">{{ __('front/checkout.select_country') }}</option>
                                        @foreach ($countryOptions as $opt)
                                            <option value="{{ $opt['id'] }}" data-postcode-required="{{ $opt['postcode_required'] ? 1 : 0 }}" {{ (string) old('country_id', $shippingAddress['country_id'] ?? '') === (string) $opt['id'] ? 'selected' : '' }}>{{ $opt['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback js-checkout-error" data-field="country_id"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="submit" class="btn btn-primary" id="checkout-guest-submit">{{ __('front/checkout.continue') }}</button>
                    </div>
                </form>
            @else
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="h6 mb-0">{{ __('front/checkout.shipping_address') }}</h2>
                    </div>
                    <div class="card-body">
                        @if ($addresses->isEmpty())
                            <form id="checkout-customer-address-form" class="checkout-form" method="post" action="{{ route('front.checkout.customer_address', ['lang' => request()->route('lang')]) }}" data-validation-form-message="{{ __('front/checkout.validation_form_errors') }}">
                                <div class="row row-cols-1 row-cols-md-2 g-3">
                                    <div class="col mb-3 required">
                                        <label for="checkout-customer-firstname" class="form-label">{{ __('front/auth.firstname') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="firstname" id="checkout-customer-firstname" class="form-control" value="{{ old('firstname') }}" placeholder="{{ __('front/auth.firstname') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="firstname"></div>
                                    </div>
                                    <div class="col mb-3 required">
                                        <label for="checkout-customer-lastname" class="form-label">{{ __('front/auth.lastname') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="lastname" id="checkout-customer-lastname" class="form-control" value="{{ old('lastname') }}" placeholder="{{ __('front/auth.lastname') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="lastname"></div>
                                    </div>
                                    <div class="col mb-3">
                                        <label for="checkout-customer-company" class="form-label">{{ __('front/checkout.company') }}</label>
                                        <input type="text" name="company" id="checkout-customer-company" class="form-control" value="{{ old('company') }}" placeholder="{{ __('front/checkout.company') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="company"></div>
                                    </div>
                                    <div class="col mb-3 required">
                                        <label for="checkout-customer-address_1" class="form-label">{{ __('front/checkout.address_1') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="address_1" id="checkout-customer-address_1" class="form-control" value="{{ old('address_1') }}" placeholder="{{ __('front/checkout.address_1') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="address_1"></div>
                                    </div>
                                    <div class="col mb-3">
                                        <label for="checkout-customer-address_2" class="form-label">{{ __('front/checkout.address_2') }}</label>
                                        <input type="text" name="address_2" id="checkout-customer-address_2" class="form-control" value="{{ old('address_2') }}" placeholder="{{ __('front/checkout.address_2') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="address_2"></div>
                                    </div>
                                    <div class="col mb-3 required">
                                        <label for="checkout-customer-city" class="form-label">{{ __('front/checkout.city') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="city" id="checkout-customer-city" class="form-control" value="{{ old('city') }}" placeholder="{{ __('front/checkout.city') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="city"></div>
                                    </div>
                                    <div class="col mb-3 js-postcode-col-customer">
                                        <label for="checkout-customer-postcode" class="form-label">{{ __('front/checkout.postcode') }} <span class="text-danger js-postcode-asterisk-customer d-none">*</span></label>
                                        <input type="text" name="postcode" id="checkout-customer-postcode" class="form-control" value="{{ old('postcode') }}" placeholder="{{ __('front/checkout.postcode') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="postcode"></div>
                                    </div>
                                    <div class="col mb-3 required">
                                        <label for="checkout-customer-country_id" class="form-label">{{ __('front/checkout.country') }} <span class="text-danger">*</span></label>
                                        <select name="country_id" id="checkout-customer-country_id" class="form-select">
                                            <option value="">{{ __('front/checkout.select_country') }}</option>
                                            @foreach ($countryOptions as $opt)
                                                <option value="{{ $opt['id'] }}" data-postcode-required="{{ $opt['postcode_required'] ? 1 : 0 }}" {{ (string) old('country_id') === (string) $opt['id'] ? 'selected' : '' }}>{{ $opt['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback js-checkout-error" data-field="country_id"></div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary" id="checkout-customer-address-submit">{{ __('front/checkout.continue') }}</button>
                                </div>
                            </form>
                        @else
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="checkout_address_type" id="address-existing" value="existing" checked>
                                    <label class="form-check-label" for="address-existing">{{ __('front/checkout.use_existing_address') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="checkout_address_type" id="address-new" value="new">
                                    <label class="form-check-label" for="address-new">{{ __('front/checkout.use_new_address') }}</label>
                                </div>
                            </div>
                            <div id="checkout-shipping-address-wrap" class="mb-3" data-address-required-message="{{ __('front/checkout.shipping_address_not_found') }}">
                                <label for="checkout-shipping-address-id" class="form-label">{{ __('front/checkout.shipping_address') }}</label>
                                <select name="address_id" id="checkout-shipping-address-id" class="form-select">
                                    <option value="">{{ __('front/checkout.select_address') }}</option>
                                    @foreach ($addresses as $addr)
                                        <option value="{{ $addr->id }}" {{ (isset($shippingAddress['address_id']) && (int) $shippingAddress['address_id'] === (int) $addr->id) ? 'selected' : '' }}>{{ $addr->getFormattedAddress($countryNames[$addr->country_id] ?? '') }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback js-checkout-error" data-field="address_id"></div>
                            </div>
                            <form id="checkout-customer-new-address-form" class="checkout-form d-none" method="post" action="{{ route('front.checkout.customer_address', ['lang' => request()->route('lang')]) }}" data-validation-form-message="{{ __('front/checkout.validation_form_errors') }}">
                                <div class="row row-cols-1 row-cols-md-2 g-3">
                                    <div class="col mb-3 required">
                                        <label for="checkout-customer-new-firstname" class="form-label">{{ __('front/auth.firstname') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="firstname" id="checkout-customer-new-firstname" class="form-control" value="{{ old('firstname') }}" placeholder="{{ __('front/auth.firstname') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="firstname"></div>
                                    </div>
                                    <div class="col mb-3 required">
                                        <label for="checkout-customer-new-lastname" class="form-label">{{ __('front/auth.lastname') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="lastname" id="checkout-customer-new-lastname" class="form-control" value="{{ old('lastname') }}" placeholder="{{ __('front/auth.lastname') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="lastname"></div>
                                    </div>
                                    <div class="col mb-3">
                                        <label for="checkout-customer-new-company" class="form-label">{{ __('front/checkout.company') }}</label>
                                        <input type="text" name="company" id="checkout-customer-new-company" class="form-control" value="{{ old('company') }}" placeholder="{{ __('front/checkout.company') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="company"></div>
                                    </div>
                                    <div class="col mb-3 required">
                                        <label for="checkout-customer-new-address_1" class="form-label">{{ __('front/checkout.address_1') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="address_1" id="checkout-customer-new-address_1" class="form-control" value="{{ old('address_1') }}" placeholder="{{ __('front/checkout.address_1') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="address_1"></div>
                                    </div>
                                    <div class="col mb-3">
                                        <label for="checkout-customer-new-address_2" class="form-label">{{ __('front/checkout.address_2') }}</label>
                                        <input type="text" name="address_2" id="checkout-customer-new-address_2" class="form-control" value="{{ old('address_2') }}" placeholder="{{ __('front/checkout.address_2') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="address_2"></div>
                                    </div>
                                    <div class="col mb-3 required">
                                        <label for="checkout-customer-new-city" class="form-label">{{ __('front/checkout.city') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="city" id="checkout-customer-new-city" class="form-control" value="{{ old('city') }}" placeholder="{{ __('front/checkout.city') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="city"></div>
                                    </div>
                                    <div class="col mb-3 js-postcode-col-customer">
                                        <label for="checkout-customer-new-postcode" class="form-label">{{ __('front/checkout.postcode') }} <span class="text-danger js-postcode-asterisk-customer d-none">*</span></label>
                                        <input type="text" name="postcode" id="checkout-customer-new-postcode" class="form-control" value="{{ old('postcode') }}" placeholder="{{ __('front/checkout.postcode') }}">
                                        <div class="invalid-feedback js-checkout-error" data-field="postcode"></div>
                                    </div>
                                    <div class="col mb-3 required">
                                        <label for="checkout-customer-new-country_id" class="form-label">{{ __('front/checkout.country') }} <span class="text-danger">*</span></label>
                                        <select name="country_id" id="checkout-customer-new-country_id" class="form-select">
                                            <option value="">{{ __('front/checkout.select_country') }}</option>
                                            @foreach ($countryOptions as $opt)
                                                <option value="{{ $opt['id'] }}" data-postcode-required="{{ $opt['postcode_required'] ? 1 : 0 }}" {{ (string) old('country_id') === (string) $opt['id'] ? 'selected' : '' }}>{{ $opt['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback js-checkout-error" data-field="country_id"></div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary" id="checkout-customer-new-address-submit">{{ __('front/checkout.continue') }}</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endguest
        </div>

        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">{{ __('front/checkout.shipping_method') }}</h2>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">{{ __('front/checkout.choose_shipping_method') }}</p>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">{{ __('front/checkout.payment_method') }}</h2>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">{{ __('front/checkout.choose_payment_method') }}</p>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0">{{ __('front/checkout.comment_order') }}</h2>
                </div>
                <div class="card-body">
                    <textarea class="form-control" rows="3" placeholder="{{ __('front/checkout.comment_order') }}"></textarea>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('front/checkout.product') }}</th>
                                <th class="text-end">{{ __('front/general.cart_total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartRows as $row)
                                <tr>
                                    <td>{{ $row['item']->quantity }}x {{ $row['name'] }}</td>
                                    <td class="text-end">{{ $currency->formatPriceFromBase((string) $row['rowTotal']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>{{ __('front/checkout.subtotal') }}</td>
                                <td class="text-end">{{ $currency->formatPriceFromBase((string) $subtotal) }}</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>{{ __('front/checkout.total') }}</td>
                                <td class="text-end">{{ $currency->formatPriceFromBase((string) $subtotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts-after')
    <script src="{{ asset('js/front/checkout.js') }}"></script>
@endpush
