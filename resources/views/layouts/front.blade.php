@extends('layouts.app')

@section('meta-title', $title ?? config('app.name'))

@push('styles')
    <link href="{{ asset('css/front/main.css') }}" rel="stylesheet">
@endpush

@section('body')
    <main>
        @include('front.common.header')
    
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

        @yield('content')
        
        @include('front.common.footer')
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/front/main.js') }}"></script>
@endpush
