@props(['type' => 'text', 'name', 'label', 'placeholder' => '', 'value' => '', 'required' => false])

<div class="row mb-3 {{ $required ? 'required' : '' }}">
    <label for="input-{{ $name }}" class="col-sm-2 col-form-label">
        {{ $label }}
    </label>
    <div class="col-sm-10">
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}" 
            placeholder="{{ $placeholder }}" 
            id="input-{{ $name }}" 
            class="form-control @error($name) is-invalid @enderror"
        />
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


