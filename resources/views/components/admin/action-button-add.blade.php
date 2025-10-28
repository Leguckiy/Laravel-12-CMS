@props([
    'permission',
    'route' => null,
    'text' => 'Add'
])

@if($permission)
    @canEdit($permission)
        <a class="btn btn-primary" href="{{ $route ?? route($permission) }}">
            <i class="fa-solid fa-plus"></i>
            {{ $text }}
        </a>
    @endcanEdit
@endif
