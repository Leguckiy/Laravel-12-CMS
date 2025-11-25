<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\CustomerRequest;
use App\Models\Customer;
use App\Models\CustomerGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'customers',
            'route' => 'admin.customer.index',
        ],
    ];

    protected string $title = 'customers';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $currentLanguageId = $this->context->language->id;

        $customers = Customer::with('customerGroup.translations')->paginate(15);

        $customers->getCollection()->transform(function (Customer $customer) use ($currentLanguageId) {
            $this->setCustomerGroupTranslation($customer, $currentLanguageId);

            return $customer;
        });

        return view('admin.customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $customerGroupsOptions = $this->getCustomerGroupOptions($this->context->language->id);

        return view('admin.customer.form', compact('customerGroupsOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Remove confirm field as it's not needed in database
        unset($validated['confirm']);

        Customer::create($validated);

        return redirect()->route('admin.customer.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): View
    {
        $currentLanguageId = $this->context->language->id;

        $customer->load('customerGroup.translations');
        $this->setCustomerGroupTranslation($customer, $currentLanguageId);

        return view('admin.customer.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): View
    {
        $customerGroupsOptions = $this->getCustomerGroupOptions($this->context->language->id);

        return view('admin.customer.form', compact('customer', 'customerGroupsOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validated();

        // Remove password from validation if empty
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // Remove confirm field as it's not needed in database
        unset($validated['confirm']);

        $customer->update($validated);

        return redirect()->route('admin.customer.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('admin.customer.index')->with('success', __('admin.deleted_successfully'));
    }

    /**
     * Get customer groups options for select fields in current language.
     */
    private function getCustomerGroupOptions(?int $languageId): array
    {
        return CustomerGroup::with('translations')
            ->get()
            ->map(function (CustomerGroup $group) use ($languageId) {
                return [
                    'id' => $group->id,
                    'name' => $this->translation($group->translations, $languageId)?->name ?? '',
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Attach translated customer group name to the customer instance.
     */
    private function setCustomerGroupTranslation(Customer $customer, ?int $languageId): void
    {
        if (! $customer->customerGroup) {
            return;
        }

        $customer->customerGroup->name = $this->translation($customer->customerGroup->translations, $languageId)?->name ?? '';
    }
}
