@extends('layouts.app')

@section('title', 'Admin panel')

@push('styles')
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div id="container">
        @include('admin.common.header')
        @include('admin.common.column_left')
        <div id="content">
            @yield('admin-content')
        </div>
        @include('admin.common.footer')
    </div>
@endsection
