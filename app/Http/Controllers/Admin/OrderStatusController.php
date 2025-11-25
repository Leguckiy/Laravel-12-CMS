<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\OrderStatusRequest;
use App\Models\OrderStatus;
use App\Models\OrderStatusLang;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderStatusController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'order_statuses',
            'route' => 'admin.order_status.index',
        ],
    ];

    protected string $title = 'order_statuses';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currentLanguageId = $this->context->language->id;

        $orderStatuses = OrderStatus::with(['translations' => function ($query) use ($currentLanguageId) {
            $query->where('language_id', $currentLanguageId);
        }])->paginate(15);

        $orderStatuses->getCollection()->transform(function ($status) {
            $status->name = $this->translation($status->translations)?->name ?? '';

            return $status;
        });

        return view('admin.order_status.index', compact('orderStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = $this->getLanguages();

        return view('admin.order_status.form', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStatusRequest $request): RedirectResponse
    {
        $nameData = $request->input('name', []);

        // Create main stock status record
        $orderStatus = OrderStatus::create([]);

        // Create translations for all languages
        foreach ($nameData as $languageId => $name) {
            $orderStatus->translations()->create([
                'language_id' => $languageId,
                'name' => $name,
            ]);
        }

        return redirect()->route('admin.order_status.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderStatus $orderStatus): View
    {
        $languages = $this->getLanguages();

        // Get all translations for this stock status
        $translations = $orderStatus->getNames();

        return view('admin.order_status.show', compact('orderStatus', 'languages', 'translations'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderStatus $orderStatus): View
    {
        $languages = $this->getLanguages();

        // Get all translations for this stock status
        $translations = $orderStatus->getNames();

        return view('admin.order_status.form', compact('orderStatus', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderStatusRequest $request, OrderStatus $orderStatus): RedirectResponse
    {
        $nameData = $request->input('name', []);

        $orderStatus->translations()->delete();

        foreach ($nameData as $languageId => $name) {
            OrderStatusLang::create([
                'order_status_id' => (int) $orderStatus->id,
                'language_id' => (int) $languageId,
                'name' => $name,
            ]);
        }

        return redirect()->route('admin.order_status.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderStatus $orderStatus): RedirectResponse
    {
        $orderStatus->delete();

        return redirect()->route('admin.order_status.index')->with('success', __('admin.deleted_successfully'));
    }
}
