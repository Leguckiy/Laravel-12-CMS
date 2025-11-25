<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\CustomerGroupRequest;
use App\Models\CustomerGroup;
use App\Models\CustomerGroupLang;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerGroupController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'customer_groups',
            'route' => 'admin.customer_group.index',
        ],
    ];

    protected string $title = 'customer_groups';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currentLanguageId = $this->context->language->id;

        $customerGroups = CustomerGroup::with(['translations' => function ($query) use ($currentLanguageId) {
            $query->where('language_id', $currentLanguageId);
        }])->paginate(15);

        $customerGroups->getCollection()->transform(function ($group) {
            $group->name = $this->translation($group->translations)?->name ?? '';

            return $group;
        });

        return view('admin.customer_group.index', compact('customerGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = $this->getLanguages();

        // Initialize empty arrays for all multilingual fields
        $translations = [
            'name' => [],
            'description' => [],
        ];

        return view('admin.customer_group.form', compact('languages', 'translations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerGroupRequest $request): RedirectResponse
    {
        $nameData = $request->input('name', []);
        $descriptionData = $request->input('description', []);

        $customerGroup = CustomerGroup::create([
            'approval' => (bool) $request->input('approval', false),
            'sort_order' => (int) $request->input('sort_order', 0),
        ]);

        foreach ($nameData as $languageId => $name) {
            $customerGroup->translations()->create([
                'language_id' => (int) $languageId,
                'name' => $name,
                'description' => $descriptionData[$languageId] ?? null,
            ]);
        }

        return redirect()->route('admin.customer_group.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerGroup $customerGroup): View
    {
        $languages = $this->getLanguages();

        $translations = [
            'name' => $customerGroup->translations()->pluck('name', 'language_id')->toArray(),
            'description' => $customerGroup->translations()->pluck('description', 'language_id')->toArray(),
        ];

        return view('admin.customer_group.show', compact('customerGroup', 'languages', 'translations'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerGroup $customerGroup): View
    {
        $languages = $this->getLanguages();

        $translations = [
            'name' => $customerGroup->translations()->pluck('name', 'language_id')->toArray(),
            'description' => $customerGroup->translations()->pluck('description', 'language_id')->toArray(),
        ];

        return view('admin.customer_group.form', compact('customerGroup', 'languages', 'translations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerGroupRequest $request, CustomerGroup $customerGroup): RedirectResponse
    {
        $customerGroup->update([
            'approval' => (bool) $request->input('approval', false),
            'sort_order' => (int) $request->input('sort_order', 0),
        ]);

        $nameData = $request->input('name', []);
        $descriptionData = $request->input('description', []);

        $customerGroup->translations()->delete();

        foreach ($nameData as $languageId => $name) {
            CustomerGroupLang::create([
                'customer_group_id' => (int) $customerGroup->id,
                'language_id' => (int) $languageId,
                'name' => $name,
                'description' => $descriptionData[$languageId] ?? null,
            ]);
        }

        return redirect()->route('admin.customer_group.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerGroup $customerGroup): RedirectResponse
    {
        $customersCount = $customerGroup->customers()->count();

        if ($customersCount > 0) {
            return redirect()
                ->route('admin.customer_group.index')
                ->with('error', __('admin.customer_group_cannot_delete', ['count' => $customersCount]));
        }

        $customerGroup->delete();

        return redirect()->route('admin.customer_group.index')->with('success', __('admin.deleted_successfully'));
    }
}
