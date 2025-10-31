@props([
    'route', 
    'permission', 
    'title',
    'variant' => 'icon' // 'icon' or 'text'
])

@if($route && $permission)
    @canView($permission)
        <a href="{{ $route }}" 
           class="btn {{ $variant === 'icon' ? 'btn-outline-info' : 'btn-info' }}" 
           title="{{ $title }}">
            <i class="fa-solid fa-eye"></i>
            @if($variant === 'text')
                <span>{{ $title }}</span>
            @endif
        </a>
    @endcanView
@endif
