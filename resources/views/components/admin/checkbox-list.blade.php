<div class="row mb-3">
    @if($label)
        <label class="col-sm-2 col-form-label">{{ $label }}</label>
    @endif
    <div class="{{ $columnClass }}">
        <div class="form-control" style="height: {{ $height }}px; overflow: auto; padding: 0;">
            <table class="table table-borderless table-striped mb-0">
                <tbody>
                    @forelse($processedItems as $item)
                        <tr>
                            <td>
                                <input
                                    type="checkbox" 
                                    name="{{ $name }}" 
                                    value="{{ $item['id'] }}"
                                    id="{{ $item['inputId'] }}" 
                                    class="form-check-input"
                                    @checked($item['isSelected'])
                                />
                                <label for="{{ $item['inputId'] }}" class="ms-2">
                                    {{ $item['name'] }}
                                </label>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center text-muted">{{ $emptyText ?: __('admin.no_items') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
