@props([
    'name',
    'label',
    'placeholder' => '',
    'rows' => '',
    'value' => '',
    'required' => false,
    'autoloadRte' => false,
    'rteHeight' => null,
])

<div class="row mb-3 {{ $required ? 'required' : '' }}">
    <label for="input-{{ $name }}" class="col-sm-2 col-form-label">
        {{ $label }}
    </label>
    <div class="col-sm-10">
        <textarea
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            id="input-{{ $name }}"
            class="form-control{{ $autoloadRte ? ' js-rte' : '' }} @error($name) is-invalid @enderror"
            @if($rows) rows="{{ $rows }}" @endif
            @if($autoloadRte) data-autoload-rte="true" @endif
            @if($rteHeight) data-rte-height="{{ $rteHeight }}" @endif
        >{{ old($name, $value) }}</textarea>
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>


