<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends FrontController
{
    /**
     * Display the page by slug for the current language.
     */
    public function show(): View
    {
        $slug = request()->route('slug');
        $languageId = $this->context->language->id;

        $page = Page::query()
            ->where('status', true)
            ->whereHas('translations', function ($query) use ($languageId, $slug) {
                $query->where('language_id', $languageId)
                    ->where('slug', $slug);
            })
            ->with(['translations' => function ($query) use ($languageId) {
                $query->where('language_id', $languageId);
            }])
            ->firstOrFail();

        $translation = $page->translations->first();

        return view('front.page.show', [
            'page' => $page,
            'title' => $translation->title,
            'content' => $translation->content,
            'metaTitle' => $translation->meta_title ?? $translation->title,
            'metaDescription' => $translation->meta_description,
        ]);
    }
}
