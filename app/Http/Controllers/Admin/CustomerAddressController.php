<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\CustomerAddressRequest;
use App\Models\Address;
use App\Models\Country;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerAddressController extends AdminController
{
    protected array $breadcrumbs = [
        ['title' => 'home', 'route' => 'admin.dashboard'],
        ['title' => 'customers', 'route' => 'admin.customer.index'],
    ];

    protected string $title = 'addresses';

    public function create(Customer $customer): View
    {
        $this->pushCustomerBreadcrumb($customer);
        $this->breadcrumbs[] = ['title' => 'add_address', 'route' => null, 'translate' => true];
        $countryOptions = Country::getOptions($this->context->language->id);

        return view('admin.customer.address.form', [
            'customer' => $customer,
            'address' => null,
            'countryOptions' => $countryOptions,
        ]);
    }

    public function store(CustomerAddressRequest $request, Customer $customer): RedirectResponse
    {
        $customer->addresses()->create($request->validated());

        return redirect()
            ->route('admin.customer.edit', $customer->id)
            ->with('success', __('admin.created_successfully'));
    }

    public function show(Customer $customer, Address $address): View
    {
        $this->ensureAddressBelongsToCustomer($customer, $address);
        $address->load('country');
        $this->pushCustomerBreadcrumb($customer);
        $this->breadcrumbs[] = ['title' => 'address', 'route' => null, 'translate' => true];

        return view('admin.customer.address.show', [
            'customer' => $customer,
            'address' => $address,
        ]);
    }

    public function edit(Customer $customer, Address $address): View
    {
        $this->ensureAddressBelongsToCustomer($customer, $address);
        $this->pushCustomerBreadcrumb($customer);
        $this->breadcrumbs[] = ['title' => 'edit_address', 'route' => null, 'translate' => true];
        $countryOptions = Country::getOptions($this->context->language->id);

        return view('admin.customer.address.form', [
            'customer' => $customer,
            'address' => $address,
            'countryOptions' => $countryOptions,
        ]);
    }

    public function update(CustomerAddressRequest $request, Customer $customer, Address $address): RedirectResponse
    {
        $this->ensureAddressBelongsToCustomer($customer, $address);
        $address->update($request->validated());

        return redirect()
            ->route('admin.customer.edit', $customer->id)
            ->with('success', __('admin.updated_successfully'));
    }

    public function destroy(Customer $customer, Address $address): RedirectResponse
    {
        $this->ensureAddressBelongsToCustomer($customer, $address);
        $address->delete();

        return redirect()
            ->route('admin.customer.edit', $customer->id)
            ->with('success', __('admin.deleted_successfully'));
    }

    protected function ensureAddressBelongsToCustomer(Customer $customer, Address $address): void
    {
        if ((int) $address->customer_id !== (int) $customer->id) {
            abort(404);
        }
    }

    protected function pushCustomerBreadcrumb(Customer $customer): void
    {
        $this->breadcrumbs[] = [
            'title' => $customer->fullname,
            'url' => route('admin.customer.edit', $customer->id),
            'translate' => false,
        ];
    }
}
