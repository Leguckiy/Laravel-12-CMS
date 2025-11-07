<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\CountryRequest;
use App\Models\Country;
use App\Models\CountryLang;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CountryController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'countries',
            'route' => 'admin.country.index',
        ],
    ];

    protected string $title = 'countries';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currentLanguageId = $this->getCurrentLanguageId();

        $countries = Country::with(['translations' => function ($query) use ($currentLanguageId) {
            $query->where('language_id', $currentLanguageId);
        }])->paginate(15);

        $countries->getCollection()->transform(function ($country) {
            $country->name = $country->translations->first()?->name ?? '';
            return $country;
        });

        return view('admin.country.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();
        
        return view('admin.country.form', compact('languages', 'currentLanguageId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryRequest $request): RedirectResponse
    {
        $nameData = $request->input('name', []);

        // Create main country record
        $country = Country::create(
            $request->only([
                'iso_code_2',
                'iso_code_3',
                'postcode_required',
                'status',
            ])
        );

        // Create translations for all languages
        foreach ($nameData as $languageId => $name) {
            $country->translations()->create([
                'language_id' => $languageId,
                'name' => $name,
            ]);
        }

        return redirect()->route('admin.country.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();

        // Get all translations for this stock status
        $translations = $country->getNames();

        return view('admin.country.show', compact('country', 'languages', 'translations', 'currentLanguageId'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();

        // Get all translations for this stock status
        $translations = $country->getNames();

        return view('admin.country.form', compact('country', 'languages', 'translations', 'currentLanguageId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryRequest $request, Country $country): RedirectResponse
    {
        // Update base country fields (do not pass translations array)
        $country->update($request->only([
            'iso_code_2',
            'iso_code_3',
            'postcode_required',
            'status',
        ]));

        $nameData = $request->input('name', []);

        // Replace translations per language
        $country->translations()->delete();

        foreach ($nameData as $languageId => $name) {
            CountryLang::create([
                'country_id' => (int) $country->id,
                'language_id' => (int) $languageId,
                'name' => $name,
            ]);
        }

        return redirect()->route('admin.country.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country): RedirectResponse
    {
        $country->delete();
        
        return redirect()->route('admin.country.index')->with('success', __('admin.deleted_successfully'));
    }
}
