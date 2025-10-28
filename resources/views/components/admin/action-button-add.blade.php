@props([
    'route' => null,
    'text' => null,
    'permission' => null,
    'baseName' => null
])

@php
    if ($baseName) {
        $baseRoute = 'admin.' . str_replace('_', '.', $baseName);
        $route = $route ?? route($baseRoute . '.create');
        $permission = $permission ?? $baseRoute . '.create';
        $text = $text ?? 'Add ' . str_replace('_', ' ', $baseName);
    }
@endphp

@if($route && $permission)
    @canEdit($permission)
        <a class="btn btn-primary" href="{{ $route }}">
            <i class="fa-solid fa-plus"></i>
            {{ $text }}
        </a>
    @endcanEdit
@endif

