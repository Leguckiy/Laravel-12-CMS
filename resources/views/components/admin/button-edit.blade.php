@props([
    'route', 
    'permission', 
    'title',
    'variant' => 'icon' // 'icon' or 'text'
])

@if($route && $permission)
    @canEdit($permission)
        <a href="{{ $route }}" 
           class="btn {{ $variant === 'icon' ? 'btn-outline-primary' : 'btn-primary' }}" 
           title="{{ $title }}">
            <i class="fa-solid fa-edit"></i>
            @if($variant === 'text')
                <span>{{ $title }}</span>
            @endif
        </a>
    @endcanEdit
@endif

