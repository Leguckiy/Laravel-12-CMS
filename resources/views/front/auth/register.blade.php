@extends('layouts.front')

@push('styles')
    <link href="{{ asset('css/front/auth.css') }}" rel="stylesheet">
@endpush

@section('content')
<h1 class="mb-4">{{ __('front/auth.register_title') }}</h1>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="{{ route('front.auth.register', ['lang' => request()->route('lang')]) }}" class="auth-form">
                        @csrf
                        <div class="row mb-3 required">
                            <label for="register-firstname" class="col-sm-3 col-form-label">{{ __('front/auth.firstname') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="firstname" id="register-firstname" class="form-control" value="{{ old('firstname') }}" placeholder="{{ __('front/auth.firstname') }}" required>
                            </div>
                        </div>
                        <div class="row mb-3 required">
                            <label for="register-lastname" class="col-sm-3 col-form-label">{{ __('front/auth.lastname') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="lastname" id="register-lastname" class="form-control" value="{{ old('lastname') }}" placeholder="{{ __('front/auth.lastname') }}" required>
                            </div>
                        </div>
                        <div class="row mb-3 required">
                            <label for="register-email" class="col-sm-3 col-form-label">{{ __('front/auth.email') }}</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" id="register-email" class="form-control" value="{{ old('email') }}" placeholder="{{ __('front/auth.email') }}" required autocomplete="email">
                            </div>
                        </div>
                        <div class="row mb-3 required">
                            <label for="register-password" class="col-sm-3 col-form-label">{{ __('front/auth.password') }}</label>
                            <div class="col-sm-9">
                                <input type="password" name="password" id="register-password" class="form-control" placeholder="{{ __('front/auth.password') }}" required autocomplete="new-password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-primary">{{ __('front/auth.register') }}</button>
                            </div>
                        </div>
                    </form>
                    <p class="mt-4 mb-0 text-center">
                        <a href="{{ route('front.auth.login.show', ['lang' => request()->route('lang')]) }}">{{ __('front/auth.have_account') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
