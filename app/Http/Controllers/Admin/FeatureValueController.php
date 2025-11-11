<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\FeatureValueRequest;
use App\Models\Feature;
use App\Models\FeatureValue;
use App\Models\FeatureValueLang;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeatureValueController extends AdminController
{
    protected array $breadcrumbs = [];

    protected array $baseBreadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'features',
            'route' => 'admin.feature.index',
        ],
    ];

    public function callAction($method, $parameters)
    {
        $this->breadcrumbs = $this->baseBreadcrumbs;

        if (isset($parameters['feature']) && $parameters['feature'] instanceof Feature) {
            $this->setFeatureBreadcrumbs($parameters['feature']);
        }

        return $this->{$method}(...array_values($parameters));
    }

    protected string $title = 'feature_values';

    public function index(Feature $feature): View
    {
        $currentLanguageId = $this->getCurrentLanguageId() ?? $this->getDefaultLanguageId();

        $values = $feature->values()
            ->with(['translations' => function ($query) use ($currentLanguageId) {
                if ($currentLanguageId !== null) {
                    $query->where('language_id', $currentLanguageId);
                }
            }])
            ->orderBy('id')
            ->paginate(15);

        $values->getCollection()->transform(function (FeatureValue $value) {
            $value->setAttribute('value', $value->translations->first()?->value ?? '');

            return $value;
        });

        return view('admin.feature_value.index', [
            'feature' => $feature,
            'values' => $values,
        ]);
    }

    public function create(Feature $feature): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId() ?? $this->getDefaultLanguageId();

        return view('admin.feature_value.form', [
            'feature' => $feature,
            'languages' => $languages,
            'currentLanguageId' => $currentLanguageId,
        ]);
    }

    public function store(FeatureValueRequest $request, Feature $feature): RedirectResponse
    {
        $featureValue = $feature->values()->create([
            'sort_order' => $request->input('sort_order', 0),
        ]);

        $valueData = $request->input('value', []);

        foreach ($valueData as $languageId => $value) {
            $featureValue->translations()->create([
                'language_id' => (int) $languageId,
                'value' => $value,
            ]);
        }

        return redirect()
            ->route('admin.feature_value.index', ['feature' => $feature->id])
            ->with('success', __('admin.created_successfully'));
    }

    public function show(Feature $feature, FeatureValue $featureValue): View
    {
        $this->ensureFeatureMatches($feature, $featureValue);

        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId() ?? $this->getDefaultLanguageId();

        $translations = $featureValue->translations()
            ->pluck('value', 'language_id')
            ->toArray();

        return view('admin.feature_value.show', [
            'feature' => $feature,
            'featureValue' => $featureValue,
            'languages' => $languages,
            'translations' => $translations,
            'currentLanguageId' => $currentLanguageId,
        ]);
    }

    public function edit(Feature $feature, FeatureValue $featureValue): View
    {
        $this->ensureFeatureMatches($feature, $featureValue);

        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId() ?? $this->getDefaultLanguageId();

        $translations = $featureValue->translations()
            ->pluck('value', 'language_id')
            ->toArray();

        return view('admin.feature_value.form', [
            'feature' => $feature,
            'featureValue' => $featureValue,
            'languages' => $languages,
            'translations' => $translations,
            'currentLanguageId' => $currentLanguageId,
        ]);
    }

    public function update(FeatureValueRequest $request, Feature $feature, FeatureValue $featureValue): RedirectResponse
    {
        $this->ensureFeatureMatches($feature, $featureValue);

        $featureValue->update([
            'sort_order' => $request->input('sort_order', 0),
        ]);

        $featureValue->translations()->delete();

        $valueData = $request->input('value', []);

        foreach ($valueData as $languageId => $value) {
            FeatureValueLang::create([
                'feature_value_id' => (int) $featureValue->id,
                'language_id' => (int) $languageId,
                'value' => $value,
            ]);
        }

        return redirect()
            ->route('admin.feature_value.index', ['feature' => $feature->id])
            ->with('success', __('admin.updated_successfully'));
    }

    public function destroy(Feature $feature, FeatureValue $featureValue): RedirectResponse
    {
        $this->ensureFeatureMatches($feature, $featureValue);

        $featureValue->delete();

        return redirect()
            ->route('admin.feature_value.index', ['feature' => $feature->id])
            ->with('success', __('admin.deleted_successfully'));
    }

    protected function ensureFeatureMatches(Feature $feature, FeatureValue $featureValue): void
    {
        if ((int) $featureValue->feature_id !== (int) $feature->id) {
            abort(response()->view('admin.not_found', [], 404));
        }
    }

    protected function setFeatureBreadcrumbs(Feature $feature): void
    {
        $this->breadcrumbs = array_merge($this->baseBreadcrumbs, [
            [
                'title' => $this->getFeatureDisplayName($feature),
                'url' => route('admin.feature_value.index', ['feature' => $feature->id]),
                'translate' => false,
            ],
        ]);
    }

    protected function getFeatureDisplayName(Feature $feature): string
    {
        $feature->loadMissing('translations');

        $currentLanguageId = $this->getCurrentLanguageId() ?? $this->getDefaultLanguageId();

        $translation = $feature->translations
            ->firstWhere('language_id', $currentLanguageId)
            ?? $feature->translations->first();

        return $translation?->name ?? __('admin.feature') . ' #' . $feature->id;
    }
}
