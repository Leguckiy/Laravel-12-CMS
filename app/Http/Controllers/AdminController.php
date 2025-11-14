<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Support\AdminContext;

class AdminController extends Controller
{
    protected array $breadcrumbs;

    protected string $title;

    protected AdminContext $context;

    public function __construct(AdminContext $context)
    {
        $this->context = $context;
    }

    public function getBreadcrumbs(): array
    {
        return array_map(function ($item) {
            $shouldTranslate = $item['translate'] ?? true;

            if ($shouldTranslate && isset($item['title'])) {
                $item['title'] = __("admin.{$item['title']}");
            }

            return $item;
        }, $this->breadcrumbs ?? []);
    }

    public function getTitle(): string
    {
        return __("admin.{$this->title}");
    }

    /**
     * Get list of all active languages.
     */
    protected function getLanguages()
    {
        return Language::where('status', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
