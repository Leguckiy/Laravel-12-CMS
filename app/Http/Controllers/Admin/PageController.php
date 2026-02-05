<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use App\Models\PageLang;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'page_list',
            'route' => 'admin.page.index',
        ],
    ];

    protected string $title = 'pages';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currentLanguageId = $this->context->language->id;

        $pages = Page::with(['translations' => function ($query) use ($currentLanguageId) {
            $query->where('language_id', $currentLanguageId);
        }])->paginate(15);

        $pages->getCollection()->transform(function ($page) {
            $trans = $this->translation($page->translations);
            $page->title = $trans?->title ?? '';
            $page->slug = $trans?->slug ?? '';

            return $page;
        });

        return view('admin.page.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = $this->getLanguages();
        $translations = [
            'title' => [],
            'slug' => [],
            'content' => [],
            'meta_title' => [],
            'meta_description' => [],
        ];

        return view('admin.page.form', compact('languages', 'translations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PageRequest $request): RedirectResponse
    {
        $titleData = $request->input('title', []);
        $slugData = $request->input('slug', []);
        $contentData = $request->input('content', []);
        $metaTitleData = $request->input('meta_title', []);
        $metaDescriptionData = $request->input('meta_description', []);

        $page = Page::create([
            'sort_order' => (int) $request->input('sort_order', 0),
            'status' => (bool) $request->input('status', false),
        ]);

        foreach ($titleData as $languageId => $title) {
            $page->translations()->create([
                'language_id' => $languageId,
                'title' => $title,
                'slug' => $slugData[$languageId] ?? '',
                'content' => $contentData[$languageId] ?? null,
                'meta_title' => $metaTitleData[$languageId] ?? null,
                'meta_description' => $metaDescriptionData[$languageId] ?? null,
            ]);
        }

        return redirect()->route('admin.page.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page): View
    {
        return view('admin.page.show', $this->preparePageViewData($page));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page): View
    {
        return view('admin.page.form', $this->preparePageViewData($page));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $page->update([
            'sort_order' => (int) $request->input('sort_order', 0),
            'status' => (bool) $request->input('status', false),
        ]);

        $page->translations()->delete();

        $titleData = $request->input('title', []);
        $slugData = $request->input('slug', []);
        $contentData = $request->input('content', []);
        $metaTitleData = $request->input('meta_title', []);
        $metaDescriptionData = $request->input('meta_description', []);

        foreach ($titleData as $languageId => $title) {
            PageLang::create([
                'page_id' => $page->id,
                'language_id' => $languageId,
                'title' => $title,
                'slug' => $slugData[$languageId] ?? '',
                'content' => $contentData[$languageId] ?? null,
                'meta_title' => $metaTitleData[$languageId] ?? null,
                'meta_description' => $metaDescriptionData[$languageId] ?? null,
            ]);
        }

        return redirect()->route('admin.page.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.page.index')->with('success', __('admin.deleted_successfully'));
    }

    /**
     * @return array{page: Page, languages: \Illuminate\Database\Eloquent\Collection, translations: array<string, array<int, mixed>>}
     */
    private function preparePageViewData(Page $page): array
    {
        $page->load('translations');
        $languages = $this->getLanguages();
        $fields = ['title', 'slug', 'content', 'meta_title', 'meta_description'];
        $translations = [];
        foreach ($fields as $field) {
            $translations[$field] = $page->translations->pluck($field, 'language_id')->toArray();
        }

        return [
            'page' => $page,
            'languages' => $languages,
            'translations' => $translations,
        ];
    }
}
