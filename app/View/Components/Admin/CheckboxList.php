<?php

namespace App\View\Components\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CheckboxList extends Component
{
    public array $processedItems;
    public string $emptyText;
    public string $columnClass;

    public function __construct(
        public $items,
        public array $selectedItems = [],
        public string $name = 'items[]',
        public string $label = '',
        public string $idField = 'id',
        public string $nameField = 'name',
        public ?string $emptyMessage = null,
        public ?string $itemPrefix = null
    ) {
        $this->itemPrefix = $this->itemPrefix ?? $this->extractPrefixFromName();
        $this->processedItems = $this->processItems();
        $this->emptyText = $this->emptyMessage ?? '';
        $this->columnClass = $this->label ? 'col-sm-10' : 'col-12';
    }

    protected function extractPrefixFromName(): string
    {
        if (preg_match('/^([a-z]+)\[\]$/', $this->name, $matches)) {
            return $matches[1];
        }

        return 'item';
    }

    protected function processItems(): array
    {
        $processed = [];

        foreach ($this->items as $item) {
            $itemId = $this->getItemId($item);
            $processed[] = [
                'id' => $itemId,
                'name' => $this->getItemName($item),
                'isSelected' => $this->isItemSelected($item),
                'inputId' => $this->itemPrefix . '-item-' . $itemId,
            ];
        }

        return $processed;
    }

    protected function getItemId($item): ?int
    {
        if (is_array($item)) {
            return $item[$this->idField] ?? null;
        }

        return $item->{$this->idField} ?? null;
    }

    protected function getItemName($item): string
    {
        if (is_array($item)) {
            return $item[$this->nameField] ?? '';
        }

        if (method_exists($item, 'translations') && $item->translations->isNotEmpty()) {
            return $item->translations->first()?->{$this->nameField} ?? '';
        }

        return $item->{$this->nameField} ?? '';
    }

    protected function isItemSelected($item): bool
    {
        $itemId = $this->getItemId($item);
        return in_array($itemId, $this->selectedItems);
    }

    public function render(): View
    {
        return view('components.admin.checkbox-list');
    }
}
