<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\StockStatusRequest;
use App\Models\StockStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StockStatusController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'stock_statuses',
            'route' => 'admin.stock_statuses.index',
        ],
    ];

    protected string $title = 'stock_statuses';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currentLanguageId = $this->getCurrentLanguageId();

        $stockStatuses = StockStatus::with(['translations' => function ($query) use ($currentLanguageId) {
            $query->where('language_id', $currentLanguageId);
        }])->paginate(15);

        $stockStatuses->getCollection()->transform(function ($status) {
            $status->name = $status->translations->first()?->name ?? '';
            return $status;
        });

        return view('admin.stock_status.index', compact('stockStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();
        
        return view('admin.stock_status.form', compact('languages', 'currentLanguageId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockStatusRequest $request): RedirectResponse
    {
        $nameData = $request->input('name', []);

        // Create main stock status record
        $stockStatus = StockStatus::create([]);

        // Create translations for all languages
        foreach ($nameData as $languageId => $name) {
            $stockStatus->translations()->create([
                'language_id' => $languageId,
                'name' => $name,
            ]);
        }

        return redirect()->route('admin.stock_status.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(StockStatus $stockStatus): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();

        // Get all translations for this stock status
        $translations = $stockStatus->getNames();

        return view('admin.stock_status.show', compact('stockStatus', 'languages', 'translations', 'currentLanguageId'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockStatus $stockStatus): View
    {
        $languages = $this->getLanguages();
        $currentLanguageId = $this->getCurrentLanguageId();

        // Get all translations for this stock status
        $translations = $stockStatus->getNames();

        return view('admin.stock_status.form', compact('stockStatus', 'languages', 'translations', 'currentLanguageId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StockStatusRequest $request, StockStatus $stockStatus): RedirectResponse
    {
        $nameData = $request->input('name', []);

        foreach ($nameData as $languageId => $name) {
            $stockStatus->translations()
                ->where('language_id', $languageId)
                ->update(['name' => $name]);
        }

        return redirect()->route('admin.stock_status.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockStatus $stockStatus): RedirectResponse
    {
        $stockStatus->delete();
        
        return redirect()->route('admin.stock_status.index')->with('success', __('admin.deleted_successfully'));
    }
}
