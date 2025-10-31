@props([
    'isEdit' => false,
    'backRoute',
    'submitLabel' => null
])

<div class="row">
    <div class="col-sm-10 offset-sm-2">
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save"></i>
            {{ $submitLabel ?? ($isEdit ? __('admin.update') : __('admin.save')) }}
        </button>
        <a href="{{ $backRoute }}" class="btn btn-secondary">
            <i class="fa-solid fa-times"></i>
            {{ __('admin.cancel') }}
        </a>
    </div>
</div>
