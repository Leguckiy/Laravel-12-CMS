@props([
    'name',
    'label',
    'placeholder' => '',
    'rows' => '',
    'value' => [],
    'required' => false,
    'languages',
    'currentLanguageId' => null,
    'autoloadRte' => false,
    'rteHeight' => null,
])

<div class="row mb-3 {{ $required ? 'required' : '' }}">
    <label class="col-sm-2 col-form-label">
        {{ $label }}
    </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-sm-10">
                @foreach($languages as $index => $language)
                    <div class="input-group multilang-group {{ $language->id === ($currentLanguageId ?? $languages->first()->id) ? '' : 'd-none' }}" data-lang-id="{{ $language->id }}" data-field-name="{{ $name }}">
                        <textarea
                            name="{{ $name }}[{{ $language->id }}]" 
                            placeholder="{{ $placeholder }}" 
                            id="input-{{ str_replace(['[', ']'], ['-', ''], $name) }}-{{ $language->id }}"
                            class="form-control{{ $autoloadRte ? ' js-rte' : '' }} @error($name . '.' . $language->id) is-invalid @enderror"
                            @if($rows) rows="{{ $rows }}" @endif
                            @if($autoloadRte) data-autoload-rte="true" @endif
                            @if($rteHeight) data-rte-height="{{ $rteHeight }}" @endif
                        >{{ old($name . '.' . $language->id, $value[$language->id] ?? '') }}</textarea>
                        <div id="error-{{ $name }}-{{ $language->id }}" class="invalid-feedback">
                            @error($name . '.' . $language->id)
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-sm-2">
                <div class="input-group mb-2">
                    <select class="form-select multilang-selector" data-field-name="{{ $name }}">
                        @foreach($languages as $language)
                            <option value="{{ $language->id }}" {{ $language->id === ($currentLanguageId ?? $languages->first()->id) ? 'selected' : '' }}>
                                {{ strtoupper($language->code) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

