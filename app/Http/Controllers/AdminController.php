<?php

namespace App\Http\Controllers;

use App\Models\Language;

class AdminController extends Controller
{
    protected array $breadcrumbs;

    protected string $title;

    public function getBreadcrumbs(): array
    {
        return array_map(function($item) {
            $item['title'] = __("admin.{$item['title']}");
            return $item;
        }, $this->breadcrumbs);
    }

    public function getTitle(): string
    {
        return __("admin.{$this->title}");
    }

    /**
     * Get current user's language_id from request attributes.
     */
    protected function getCurrentLanguageId(): ?int
    {
        return request()->attributes->get('language_id');
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

    /**
     * Get default language id based on app locale.
     */
    protected function getDefaultLanguageId(): ?int
    {
        return Language::where('status', true)
            ->where('code', config('app.locale'))
            ->value('id');
    }
}
