<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Support\AdminContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    /**
     * Get translation model for the current or provided language.
     * Works as a language filter - returns the entire translation model with all fields.
     *
     * @param  Collection  $translations  Collection of translation models
     * @param  int|null  $languageId  Target language ID (uses current language if not provided)
     * @return Model|null Translation model with all fields or null
     */
    protected function translation(Collection $translations, ?int $languageId = null): ?Model
    {
        if ($translations->isEmpty()) {
            return null;
        }

        $languageId ??= $this->context->language->id ?? null;

        if (! $languageId) {
            return $translations->first();
        }

        return $translations->firstWhere('language_id', $languageId)
            ?? $translations->first();
    }
}
