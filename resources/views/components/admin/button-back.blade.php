@props(['route', 'text' => null])

@if($route)
    <a href="{{ $route }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i>
        <span>{{ $text ?? __('admin.back_to_list') }}</span>
    </a>
@endif

