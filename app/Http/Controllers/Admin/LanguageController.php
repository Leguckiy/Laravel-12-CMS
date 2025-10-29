<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\AdminLanguageRequest;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LanguageController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'Home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'Languages',
            'route' => 'admin.language.index',
        ],
    ];

    protected string $title = 'Languages';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $languages = Language::paginate(15);

        return view('admin.language.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.language.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminLanguageRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        Language::create($validated);

        return redirect()->route('admin.language.index')->with('success', 'Language created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language): View
    {
        return view('admin.language.show', compact('language'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language): View
    {
        return view('admin.language.form', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminLanguageRequest $request, Language $language): RedirectResponse
    {
        $validated = $request->validated();
        
        $language->update($validated);

        return redirect()->route('admin.language.index')->with('success', 'Language updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language): RedirectResponse
    {
        $language->delete();

        return redirect()->route('admin.language.index')->with('success', 'Language deleted successfully.');
    }
}
