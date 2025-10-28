@props([
    'route', 
    'permission', 
    'title', 
    'confirmText',
    'variant' => 'icon' // 'icon' or 'text'
])

@if($route && $permission)
    @canEdit($permission)
        <button 
            type="button"
            class="btn {{ $variant === 'icon' ? 'btn-outline-danger' : 'btn-danger' }}"
            data-delete-url="{{ $route }}"
            data-confirm="{{ $confirmText }}"
            title="{{ $title }}"
        >
            <i class="fa-solid fa-trash"></i>
            @if($variant === 'text')
                <span>{{ $title }}</span>
            @endif
        </button>
    @endcanEdit
@endif

