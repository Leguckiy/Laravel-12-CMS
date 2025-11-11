<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\FeatureRequest;
use App\Models\Feature;
use App\Models\FeatureLang;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeatureController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'features',
            'route' => 'admin.feature.index',
        ],
    ];

    protected string $title = 'features';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currentLanguageId = $this->getCurrentLanguageId();

        $features = Feature::with(['translations' => function ($query) use ($currentLanguageId) {
            $query->where('language_id', $currentLanguageId);
        }])
        ->withCount('values')
        ->paginate(15);

        $features->getCollection()->transform(function ($feature) {
            $feature->name = $feature->translations->first()?->name ?? '';
            return $feature;
        });

        return view('admin.feature.index', compact('features'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();
        
        return view('admin.feature.form', compact('languages', 'currentLanguageId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FeatureRequest $request): RedirectResponse
    {
        $nameData = $request->input('name', []);

        // Create main feature record
        $feature = Feature::create([
            'sort_order' => $request->input('sort_order')
        ]);

        // Create translations for all languages
        foreach ($nameData as $languageId => $name) {
            $feature->translations()->create([
                'language_id' => $languageId,
                'name' => $name,
            ]);
        }

        return redirect()->route('admin.feature.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Feature $feature): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();

        // Get all translations for this feature
        $translations = $feature->getNames();

        return view('admin.feature.show', compact('feature', 'languages', 'translations', 'currentLanguageId'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feature $feature): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();

        // Get all translations for this feature
        $translations = $feature->getNames();

        return view('admin.feature.form', compact('feature', 'languages', 'translations', 'currentLanguageId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeatureRequest $request, Feature $feature): RedirectResponse
    {
        $nameData = $request->input('name', []);

        $feature->update([
            'sort_order' => $request->input('sort_order')
        ]);

        $feature->translations()->delete();

        foreach ($nameData as $languageId => $name) {
            FeatureLang::create([
                'feature_id' => (int) $feature->id,
                'language_id' => (int) $languageId,
                'name' => $name,
            ]);
        }

        return redirect()->route('admin.feature.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feature $feature): RedirectResponse
    {
        $feature->delete();
        
        return redirect()->route('admin.feature.index')->with('success', __('admin.deleted_successfully'));
    }
}
