@props(['type' => 'text', 'name', 'label', 'placeholder' => '', 'value' => [], 'required' => false, 'languages', 'currentLanguageId'])

<div class="row mb-3 {{ $required ? 'required' : '' }}">
    <label class="col-sm-2 col-form-label">
        {{ $label }}
    </label>
    <div class="col-sm-10">
        <div class="row">
            <div class="col-sm-10">
                @foreach($languages as $index => $language)
                    <div class="input-group multilang-group {{ $language->id === $currentLanguageId ? '' : 'd-none' }}" data-lang-id="{{ $language->id }}" data-field-name="{{ $name }}">
                        <input 
                            type="{{ $type }}" 
                            name="{{ $name }}[{{ $language->id }}]" 
                            value="{{ old($name . '.' . $language->id, $value[$language->id] ?? '') }}" 
                            placeholder="{{ $placeholder }}" 
                            id="input-{{ str_replace(['[', ']'], ['-', ''], $name) }}-{{ $language->id }}"
                            class="form-control @error($name . '.' . $language->id) is-invalid @enderror"
                        />
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
                            <option value="{{ $language->id }}" {{ $language->id === $currentLanguageId ? 'selected' : '' }}>
                                {{ strtoupper($language->code) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
