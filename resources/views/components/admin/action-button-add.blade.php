@props([
    'permission',
    'route' => null,
    'text' => null
])

@if($permission)
    @canEdit($permission)
        <a class="btn btn-primary" href="{{ $route ?? route($permission) }}">
            <i class="fa-solid fa-plus"></i>
            {{ $text ?? __('admin.add') }}
        </a>
    @endcanEdit
@endif
