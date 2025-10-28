<?php

namespace App\View\Components\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DetailActions extends Component
{
    public function __construct(
        public $id,
        public string $baseName,
        public ?string $editRoute = null,
        public ?string $destroyRoute = null,
        public ?string $backRoute = null,
        public ?string $editPermission = null,
        public ?string $destroyPermission = null,
        public ?string $confirmText = null,
        public ?string $itemName = null
    ) {
        if ($this->baseName && $this->id) {
            $baseRoute = 'admin.' . str_replace('_', '.', $this->baseName);

            $this->editRoute = $this->editRoute ?? route($baseRoute . '.edit', $this->id);
            $this->destroyRoute = $this->destroyRoute ?? route($baseRoute . '.destroy', $this->id);
            $this->backRoute = $this->backRoute ?? route($baseRoute . '.index');

            $this->editPermission = $this->editPermission ?? $baseRoute . '.edit';
            $this->destroyPermission = $this->destroyPermission ?? $baseRoute . '.destroy';

            $this->itemName = $this->itemName ?? $this->baseName;
        }

        $this->itemName = $this->itemName ?? 'item';
        $this->confirmText = $this->confirmText ?? 'Are you sure you want to delete this ' . $this->itemName . '?';
    }

    public function render(): View
    {
        return view('components.admin.detail-actions');
    }
}


