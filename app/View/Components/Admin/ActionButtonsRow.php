<?php

namespace App\View\Components\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ActionButtonsRow extends Component
{
    public function __construct(
        public $id,
        public string $baseName,
        public ?string $showRoute = null,
        public ?string $editRoute = null,
        public ?string $destroyRoute = null,
        public ?string $showPermission = null,
        public ?string $editPermission = null,
        public ?string $destroyPermission = null,
        public ?string $confirmText = null,
        public ?string $itemName = null
    ) {
        if ($this->baseName && $this->id) {
            $baseRoute = 'admin.' . $this->baseName;

            $this->showRoute = $this->showRoute ?? route($baseRoute . '.show', $this->id);
            $this->editRoute = $this->editRoute ?? route($baseRoute . '.edit', $this->id);
            $this->destroyRoute = $this->destroyRoute ?? route($baseRoute . '.destroy', $this->id);

            $this->showPermission = $this->showPermission ?? $baseRoute . '.show';
            $this->editPermission = $this->editPermission ?? $baseRoute . '.edit';
            $this->destroyPermission = $this->destroyPermission ?? $baseRoute . '.destroy';

            $this->itemName = $this->itemName ?? $this->baseName;
        }

        $this->itemName = $this->itemName ?? 'item';
        $this->confirmText = $this->confirmText ?? 'Are you sure you want to delete this ' . $this->itemName . '?';
    }

    public function render(): View
    {
        return view('components.admin.action-buttons-row');
    }
}


