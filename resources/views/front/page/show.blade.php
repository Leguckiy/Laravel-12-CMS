@extends('layouts.front')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">{{ $title }}</h1>
            @if ($content)
                <div class="page-content">
                    {!! $content !!}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
