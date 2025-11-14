@props([
    'label',
    'value' => '',
    'languages',
    'currentLanguageId',
    'fieldName' => 'text-field',
    'allowHtml' => false,
])

<div class="col-sm-3 fw-bold">{{ $label }}:</div>
<div class="col-sm-9">
    <div class="row">
        <div class="col-sm-8">
                @foreach($languages as $index => $language)
                @php
                    $content = is_array($value) ? ($value[$language->id] ?? '') : '';
                @endphp
                <div class="multilang-text-group {{ $language->id === $currentLanguageId ? '' : 'd-none' }}" data-lang-id="{{ $language->id }}" data-field-name="{{ $fieldName }}">
                    @if($allowHtml)
                        {!! $content !!}
                    @else
                        {{ $content }}
                    @endif
                </div>
            @endforeach
        </div>
        <div class="col-sm-4">
            <select class="form-select form-select-sm multilang-selector" data-field-name="{{ $fieldName }}" style="width: auto;">
                @foreach($languages as $language)
                    <option value="{{ $language->id }}" {{ $language->id === $currentLanguageId ? 'selected' : '' }}>
                        {{ strtoupper($language->code) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
