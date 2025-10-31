<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\AdminCurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CurrencyController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'currencies',
            'route' => 'admin.currency.index',
        ],
    ];

    protected string $title = 'currencies';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currencies = Currency::paginate(15);

        return view('admin.currency.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.currency.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminCurrencyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Currency::create($validated);

        return redirect()->route('admin.currency.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency): View
    {
        return view('admin.currency.show', compact('currency'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency): View
    {
        return view('admin.currency.form', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminCurrencyRequest $request, Currency $currency): RedirectResponse
    {
        $validated = $request->validated();

        $currency->update($validated);

        return redirect()->route('admin.currency.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency): RedirectResponse
    {
        $currency->delete();

        return redirect()->route('admin.currency.index')->with('success', __('admin.deleted_successfully'));
    }
}
