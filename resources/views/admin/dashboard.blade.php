@extends('layouts.admin')

@section('page-title', __('admin.dashboard'))

@section('admin-content')
    <div class="container-fluid">
        <h5 class="card-title">{{ __('admin.welcome_to_dashboard') }}</h5>
    </div>
@endsection
