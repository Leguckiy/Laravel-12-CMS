@extends('layouts.app')

@section('meta-title', $title)

@push('styles')
    <link href="{{ asset('css/admin/main.css') }}" rel="stylesheet">
@endpush

@section('body')
<div id="container">
    @include('admin.common.header')
    @include('admin.common.column_left')
    <div id="content">
        <div class="page-header">
            <div class="container-fluid">
                <x-admin.breadcrumb :items="$breadcrumbs ?? []" />
            </div>
            <div class="container-fluid">
                <h1>
                    @yield('title', $title)
                </h1>
            </div>
        </div>
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    @include('admin.common.footer')
</div>
@endsection

@push('scripts')
    <script>
        window.TinyMceBaseUrl = "{{ asset('js/library/tinymce') }}";
    </script>
    <script src="{{ asset('js/library/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('js/admin/main.js') }}"></script>
@endpush
