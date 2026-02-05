@extends('layouts.front')

@section('content')
@php
    $lang = $frontLanguage?->code ?? config('app.locale');
@endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 text-center">
            <div class="not-found-block py-5 px-3">
                <div class="not-found-code text-muted mb-3">404</div>
                <div class="mb-4">
                    <i class="fa-solid fa-circle-exclamation text-muted" style="font-size: 64px;"></i>
                </div>
                <h1 class="h2 mb-3">{{ __('front/general.not_found') }}</h1>
                <p class="text-muted mb-4">
                    {{ __('front/general.not_found_message') }}
                </p>
                <a href="{{ route('front.home', ['lang' => $lang]) }}" class="btn btn-primary">
                    <i class="fa-solid fa-house me-2"></i>{{ __('front/general.back_to_home') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
