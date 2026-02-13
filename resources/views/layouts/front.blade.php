@extends('layouts.app')

@section('meta-title', $metaTitle ?? '')
@section('meta-description', $metaDescription ?? '')

@push('styles')
    <link href="{{ asset('css/front/main.css') }}" rel="stylesheet">
@endpush

@section('body')
    <main>
        @include('front.common.header')

        @if(!empty($breadcrumbs))
            <nav aria-label="{{ __('front/general.breadcrumb') }}" class="breadcrumb-wrapper">
                <div class="container">
                    <ol class="breadcrumb mb-0 py-2">
                        @foreach ($breadcrumbs as $item)
                            <li class="breadcrumb-item {{ $loop->last && $item['url'] === null ? 'active' : '' }}">
                                @if($item['url'] !== null)
                                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                                @else
                                    <span>{{ $item['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </nav>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div id="front-ajax-alerts" class="container"></div>

        @yield('content')
        
        @include('front.common.footer')
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/front/main.js') }}"></script>
@endpush
