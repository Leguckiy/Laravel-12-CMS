@extends('layouts.front')

@push('styles')
    <link href="{{ asset('css/front/auth.css') }}" rel="stylesheet">
@endpush

@section('content')
<h1 class="mb-4">{{ __('front/auth.login_title') }}</h1>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="{{ route('front.auth.login', ['lang' => request()->route('lang')]) }}" class="auth-form">
                        @csrf
                        @if(request('back'))
                            <input type="hidden" name="back" value="{{ request('back') }}">
                        @endif
                        <div class="row mb-3 required">
                            <label for="login-email" class="col-sm-3 col-form-label">{{ __('front/auth.email') }}</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" id="login-email" class="form-control" value="{{ old('email') }}" placeholder="{{ __('front/auth.email') }}" required autocomplete="email">
                            </div>
                        </div>
                        <div class="row mb-3 required">
                            <label for="login-password" class="col-sm-3 col-form-label">{{ __('front/auth.password') }}</label>
                            <div class="col-sm-9">
                                <input type="password" name="password" id="login-password" class="form-control" placeholder="{{ __('front/auth.password') }}" required autocomplete="current-password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-primary">{{ __('front/auth.login') }}</button>
                            </div>
                        </div>
                    </form>
                    <p class="mt-4 mb-0 text-center">
                        <a href="{{ route('front.auth.register.show', ['lang' => request()->route('lang')]) }}">{{ __('front/auth.no_account_create') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
