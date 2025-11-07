@props([
    'name',
    'label',
    'currentPath' => null,
    'note' => null,
])

@php
    $inputId = 'input-' . $name;
    $imageUrl = null;

    if ($currentPath) {
        if (filter_var($currentPath, FILTER_VALIDATE_URL)) {
            $imageUrl = $currentPath;
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($currentPath)) {
            $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($currentPath);
        } else {
            $imageUrl = asset($currentPath);
        }
    }

    $emptyText = __('admin.image_placeholder');
@endphp

<div class="row mb-4">
    <label class="col-sm-2 col-form-label" for="{{ $inputId }}">
        {{ $label }}
    </label>
    <div class="col-sm-10">
        <div class="image-upload-field border rounded p-3" data-image-field="{{ $name }}">
            <div class="d-flex flex-column flex-md-row gap-3 align-items-start">
                <div class="image-upload-preview bg-light border rounded d-flex align-items-center justify-content-center" data-image-preview data-image-empty-text="{{ $emptyText }}">
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $label }}" class="img-fluid">
                    @else
                        <span class="text-muted small">{{ $emptyText }}</span>
                    @endif
                </div>
                <div class="flex-grow-1 w-100">
                    @if ($imageUrl)
                        <button type="button" class="btn btn-outline-danger btn-sm mb-2" data-image-remove-trigger>
                            <i class="fa-solid fa-trash"></i>
                            {{ __('admin.delete') }}
                        </button>
                    @endif
                    <input
                        type="file"
                        name="{{ $name }}"
                        id="{{ $inputId }}"
                        accept="image/*"
                        class="form-control @error($name) is-invalid @enderror"
                    >
                    @error($name)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if ($note)
                        <small class="text-muted d-block mt-2">{!! $note !!}</small>
                    @endif
                    <input type="hidden" name="{{ $name }}_remove" value="0" data-image-remove-input>
                </div>
            </div>
        </div>
    </div>
</div>

