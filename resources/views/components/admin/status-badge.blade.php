@props(['status'])

@if($status)
    <span class="badge bg-success">{{ __('admin.active') }}</span>
@else
    <span class="badge bg-danger">{{ __('admin.inactive') }}</span>
@endif
