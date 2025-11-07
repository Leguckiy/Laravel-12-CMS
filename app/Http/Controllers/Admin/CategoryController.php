<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Models\CategoryLang;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'categories',
            'route' => 'admin.category.index',
        ],
    ];

    protected string $title = 'categories';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currentLanguageId = $this->getCurrentLanguageId();

        $categories = Category::with(['translations' => function ($query) use ($currentLanguageId) {
            $query->where('language_id', $currentLanguageId);
        }])->paginate(15);

        $categories->getCollection()->transform(function ($category) {
            $category->name = $category->translations->first()?->name ?? '';
            return $category;
        });

        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();
        
        // Initialize empty arrays for all multilingual fields
        $translations = [];
        $translations['name'] = [];
        $translations['slug'] = [];
        $translations['description'] = [];
        $translations['meta_title'] = [];
        $translations['meta_description'] = [];
        
        return view('admin.category.form', compact(
            'languages', 
            'currentLanguageId',
            'translations'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $nameData = $request->input('name', []);
        $slugData = $request->input('slug', []);
        $descriptionData = $request->input('description', []);
        $metaTitleData = $request->input('meta_title', []);
        $metaDescriptionData = $request->input('meta_description', []);

        // Create main category record
        $category = Category::create(
            $request->only([
                'image',
                'sort_order',
                'status',
            ])
        );

        // Create translations for all languages
        foreach ($nameData as $languageId => $name) {
            $category->translations()->create([
                'language_id' => $languageId,
                'name' => $name,
                'slug' => $slugData[$languageId] ?? '',
                'description' => $descriptionData[$languageId] ?? null,
                'meta_title' => $metaTitleData[$languageId] ?? null,
                'meta_description' => $metaDescriptionData[$languageId] ?? null,
            ]);
        }

        return redirect()->route('admin.category.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        return view('admin.category.show', $this->prepareCategoryViewData($category));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('admin.category.form', $this->prepareCategoryViewData($category));
    }

    private function prepareCategoryViewData(Category $category): array
    {
        $category->load('translations');

        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();
        $translations = [];

        $fields = [
            'name',
            'slug',
            'description',
            'meta_title',
            'meta_description'
        ];

        foreach ($fields as $field) {
            $translations[$field] = $category->translations->pluck($field, 'language_id')->toArray();
        }

        return [
            'category' => $category,
            'languages' => $languages,
            'currentLanguageId' => $currentLanguageId,
            'translations' => $translations,
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        // Update base category fields
        $category->update($request->only([
            'image',
            'sort_order',
            'status',
        ]));
        // Delete all existing translations and create new ones
        $category->translations()->delete();
        
        $nameData = $request->input('name', []);
        $slugData = $request->input('slug', []);
        $descriptionData = $request->input('description', []);
        $metaTitleData = $request->input('meta_title', []);
        $metaDescriptionData = $request->input('meta_description', []);
        
        // Create translations for all languages
        foreach ($nameData as $languageId => $name) {
            CategoryLang::create([
                'category_id' => (int) $category->id,
                'language_id' => (int) $languageId,
                'name' => $name,
                'slug' => $slugData[$languageId] ?? '',
                'description' => $descriptionData[$languageId] ?? null,
                'meta_title' => $metaTitleData[$languageId] ?? null,
                'meta_description' => $metaDescriptionData[$languageId] ?? null,
            ]);
        }

        return redirect()->route('admin.category.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        
        return redirect()->route('admin.category.index')->with('success', __('admin.deleted_successfully'));
    }
}
