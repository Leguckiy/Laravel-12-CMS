@props(['route', 'text' => 'Back to List'])

@if($route)
    <a href="{{ $route }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        <span>{{ $text }}</span>
    </a>
@endif

