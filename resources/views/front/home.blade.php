@extends('layouts.front')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1>Welcome to {{ config('app.name') }}</h1>
            <p>Hello! This is the frontend homepage.</p>
            <p>Current Language: {{ $frontLanguage->name ?? 'N/A' }}</p>
            <p>Current Currency: {{ $frontCurrency->title ?? 'N/A' }}</p>
        </div>
    </div>
</div>
@endsection
