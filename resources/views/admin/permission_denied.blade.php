@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fa-solid fa-triangle-exclamation text-danger" style="font-size: 72px;"></i>
                    </div>
                    <h1 class="text-danger mb-3">{{ __('admin.permission_denied') }}</h1>
                    <p class="text-muted mb-4">
                        {{ __('admin.permission_denied_message') }}
                    </p>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                        <i class="fa-solid fa-home"></i> {{ __('admin.go_to_dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
