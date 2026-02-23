@extends('layouts.front')

@section('content')
    <h1 class="mb-4">{{ __('front/checkout.success_heading') }}</h1>

    <div class="mb-4">
        <p class="mb-2">
            {!! __('front/checkout.success_contact_hint', [
                'store_owner' => '<a href="'.($contactUrl ?? '#').'" class="alert-link">'.__('front/checkout.success_store_owner').'</a>',
            ]) !!}
        </p>
        <p class="mb-0">{{ __('front/checkout.success_thanks') }}</p>
    </div>

    <div class="text-end">
        <a href="{{ route('front.home', ['lang' => request()->route('lang')]) }}" class="btn btn-primary">
            {{ __('front/checkout.success_continue') }}
        </a>
    </div>
@endsection
