@props(['name', 'label', 'options', 'value' => '', 'required' => false])

<div class="row mb-3 {{ $required ? 'required' : '' }}">
    <label for="input-{{ $name }}" class="col-sm-2 col-form-label">{{ $label }}</label>
    <div class="col-sm-10">
        <select name="{{ $name }}" id="input-{{ $name }}" class="form-select @error($name) is-invalid @enderror">
            @foreach ($options as $option)
                <option 
                    value="{{ $option['id'] }}" 
                    {{ old($name, $value) == $option['id'] ? 'selected' : '' }}
                >
                    {{ $option['name'] }}
                </option>
            @endforeach
        </select>
        @error($name) 
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
