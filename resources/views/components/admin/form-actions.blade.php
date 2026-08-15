@props([
    'isEdit' => false,
    'backRoute',
    'submitLabel' => null,
    'formId' => null,
    'showSubmit' => true
])

<div class="d-flex gap-2">
    @if ($showSubmit)
        <button type="submit" class="btn btn-primary" @if ($formId) form="{{ $formId }}" @endif>
            <i class="fa-solid fa-save"></i>
            {{ $submitLabel ?? ($isEdit ? __('admin.update') : __('admin.save')) }}
        </button>
    @endif

    <a href="{{ $backRoute }}" class="btn btn-secondary">
        <i class="fa-solid fa-times"></i>
        {{ __('admin.cancel') }}
    </a>
</div>
