@props(['name', 'label', 'value' => false])

<div class="row mb-3">
    <label for="input-{{ $name }}" class="col-sm-2 col-form-label">{{ $label }}</label>
    <div class="col-sm-10">
        <div class="form-check form-switch form-switch-lg">
            <input type="hidden" name="{{ $name }}" value="0"/>
            <input 
                type="checkbox" 
                name="{{ $name }}" 
                value="1" 
                id="input-{{ $name }}" 
                class="form-check-input @error($name) is-invalid @enderror" 
                {{ old($name, $value) ? 'checked' : '' }}
            />
        </div>
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
