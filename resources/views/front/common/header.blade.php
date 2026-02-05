<header id="header">
    <nav class="header-nav">
        <div class="container">
            <div class="row align-items-center">
                <div class="d-none d-md-block">
                    <div class="row">
                        <div class="col-md-5 col-12">
                            <div id="contact-link">
                                <a href="#">{{ __('front/general.contact_us') }}</a>
                            </div>
                        </div>
                        <div class="col-md-7 d-flex justify-content-end gap-4">
                            <div id="_desktop_language_selector">
                                <div class="language-selector dropdown js-dropdown">
                                    <span class="language-selector-label">{{ __('front/general.language') }}:</span>
                                    <span class="expand-more _gray-darker" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $frontLanguage->name }} <i class="fa-solid fa-angle-down"></i>
                                    </span>
                                    <ul class="dropdown-menu">
                                        @foreach ($frontLanguages as $language)
                                            <li>
                                                <a class="dropdown-item {{ $language->id === $frontLanguage->id ? 'active' : '' }}" href="{{ route(Route::currentRouteName() ?? 'front.home', ['lang' => $language->code]) }}" data-iso-code="{{ $language->code }}">
                                                    {{ $language->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div id="_desktop_currency_selector">
                                <div class="currency-selector dropdown js-dropdown">
                                    <span class="currency-selector-label">{{ __('front/general.currency') }}:</span>
                                    <span class="expand-more _gray-darker" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $frontCurrency->code }} {{ trim($frontCurrency->symbol_left . $frontCurrency->symbol_right) }} <i class="fa-solid fa-angle-down"></i>
                                    </span>
                                    <ul class="dropdown-menu">
                                        @foreach ($frontCurrencies as $currency)
                                            <li>
                                                <a class="dropdown-item {{ $currency->id === $frontCurrency->id ? 'active' : '' }}" href="#" rel="nofollow" data-currency-id="{{ $currency->id }}">
                                                    {{ $currency->code }} {{ trim($currency->symbol_left . $currency->symbol_right) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div id="_desktop_user_info">
                                <div class="user-info">
                                    <a href="#" title="{{ __('front/general.sign_in_title') }}" rel="nofollow">
                                        <i class="fa-solid fa-user"></i>
                                        <span class="d-none d-md-inline">{{ __('front/general.sign_in') }}</span>
                                    </a>
                                </div>
                            </div>
                            <div id="_desktop_cart">
                                <div class="blockcart">
                                    <a href="#" title="{{ __('front/general.cart_title') }}">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                        <span class="d-none d-md-inline">{{ __('front/general.cart') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-md-none mobile w-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <button
                                id="menu-icon"
                                class="btn p-0 me-2"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#mobile_offcanvas_menu"
                                aria-controls="mobile_offcanvas_menu"
                                aria-expanded="false"
                                aria-label="Toggle navigation"
                            >
                                <i class="fa-solid fa-bars"></i>
                            </button>
                            <div class="top-logo" id="_mobile_logo">
                                <a href="{{ route('front.home') }}">
                                    <img src="{{ asset('storage/' . $frontSettings['config_logo']['value']) }}" alt="{{ config('app.name') }}" class="logo img-fluid">
                                </a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div id="_mobile_user_info" class="me-2">
                                <div class="user-info">
                                    <a href="#" title="{{ __('front/general.sign_in_title') }}" rel="nofollow">
                                        <i class="fa-solid fa-user"></i>
                                        <span class="d-none d-sm-inline">{{ __('front/general.sign_in') }}</span>
                                    </a>
                                </div>
                            </div>
                            <div id="_mobile_cart">
                                <div class="blockcart cart-preview inactive">
                                    <div class="header">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                        <span class="d-none d-sm-inline">{{ __('front/general.cart') }}</span>
                                        <span class="cart-products-count">(0)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="header-top">
        <div class="container">
            <div class="row">
                <div class="col-md-2 d-none d-md-block" id="_desktop_logo">
                    <a href="{{ route('front.home') }}">
                        <img src="{{ asset('storage/' . $frontSettings['config_logo']['value']) }}" alt="{{ config('app.name') }}" class="logo img-fluid">
                    </a>
                </div>
                <div class="header-top-right col-md-10 col-sm-12 d-flex align-items-center">
                    <nav class="menu js-top-menu position-static d-none d-md-block" id="_desktop_top_menu" aria-label="Main navigation">
                        <ul class="top-menu nav flex-row gap-3 list-unstyled mb-0" id="top-menu-desktop">
                            <li class="nav-item">
                                <a class="nav-link text-body p-0" href="#" title="Link1">Link1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-body p-0" href="#" title="Link2">Link2</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-body p-0" href="#" title="Link3">Link3</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-body p-0" href="#" title="Link4">Link4</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="offcanvas offcanvas-start w-100 d-md-none" tabindex="-1" id="mobile_offcanvas_menu" aria-labelledby="mobile_offcanvas_menu_label">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="mobile_offcanvas_menu_label">
                        {{ __('front/general.menu') }}
                    </h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-3">
                    <div id="mobile_top_menu_wrapper" class="row gy-3">
                        <div class="js-top-menu mobile" id="_mobile_top_menu">
                            <ul id="top-menu" class="mobile-menu mb-3">
                                <li>
                                    <a href="#" title="Link1">Link1</a>
                                </li>
                                <li>
                                    <a href="#" title="Link2">Link2</a>
                                </li>
                                <li>
                                    <a href="#" title="Link3">Link3</a>
                                </li>
                                <li>
                                    <a href="#" title="Link4">Link4</a>
                                </li>
                            </ul>
                        </div>
                        <div class="js-top-menu-bottom mt-2">
                            <div id="_mobile_currency_selector" class="currency-selector-wrapper mobile-dropdown-wrapper">
                                <div class="currency-selector dropdown js-dropdown">
                                    <span class="currency-selector-label">{{ __('front/general.currency') }}:</span>
                                    <button type="button" class="mobile-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $frontCurrency->code }} {{ trim($frontCurrency->symbol_left . $frontCurrency->symbol_right) }} <i class="fa-solid fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach ($frontCurrencies as $currency)
                                            <li><a class="dropdown-item {{ $currency->id === $frontCurrency->id ? 'active' : '' }}" href="#" rel="nofollow" data-currency-id="{{ $currency->id }}">{{ $currency->code }} {{ trim($currency->symbol_left . $currency->symbol_right) }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div id="_mobile_language_selector" class="language-selector-wrapper mobile-dropdown-wrapper">
                                <div class="language-selector dropdown js-dropdown">
                                    <span class="language-selector-label">{{ __('front/general.language') }}:</span>
                                    <button type="button" class="mobile-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $frontLanguage->name }} <i class="fa-solid fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach ($frontLanguages as $language)
                                            <li><a class="dropdown-item {{ $language->id === $frontLanguage->id ? 'active' : '' }}" href="{{ route(Route::currentRouteName() ?? 'front.home', ['lang' => $language->code]) }}" data-iso-code="{{ $language->code }}">{{ $language->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div id="_mobile_contact_link">
                                <div id="contact-link">
                                    <a href="#">{{ __('front/general.contact_us') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
