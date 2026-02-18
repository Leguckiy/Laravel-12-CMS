<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\ShippingMethodRequest;
use App\Models\Country;
use App\Models\ShippingMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShippingController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'shipping_methods',
            'route' => 'admin.shipping.index',
        ],
    ];

    protected string $title = 'shipping_methods';

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();
        $route = request()->route();
        if ($route && $route->getName() === 'admin.shipping.edit') {
            $code = $route->parameter('code');
            $breadcrumbs[] = [
                'title' => __('admin.shipping_method_' . $code),
                'url' => route('admin.shipping.edit', $code),
                'translate' => false,
            ];
        }

        return $breadcrumbs;
    }

    public function getTitle(): string
    {
        $route = request()->route();
        if ($route && $route->getName() === 'admin.shipping.edit') {
            $code = $route->parameter('code');

            return __('admin.shipping_method_' . $code);
        }

        return parent::getTitle();
    }

    public function index(): View
    {
        $methodCodes = array_keys(config('shipping.drivers', []));
        $shippingMethods = ShippingMethod::query()
            ->whereIn('code', $methodCodes)
            ->get()
            ->keyBy('code');
        $methods = collect($methodCodes)
            ->map(function (string $code) use ($shippingMethods) {
                $row = $shippingMethods->get($code);

                return [
                    'code' => $code,
                    'name' => __('admin.shipping_method_' . $code),
                    'sort_order' => $row ? $row->sort_order : 0,
                    'status' => $row ? $row->status : true,
                ];
            })
            ->sortBy('sort_order')
            ->values();

        return view('admin.shipping.index', compact('methods'));
    }

    public function edit(string $code): View
    {
        $drivers = config('shipping.drivers', []);
        if (! array_key_exists($code, $drivers)) {
            abort(404);
        }

        $shippingMethod = ShippingMethod::query()->where('code', $code)->first();

        if ($shippingMethod === null) {
            $shippingMethod = new ShippingMethod([
                'code' => $code,
                'config' => [],
                'countries' => [],
                'sort_order' => 0,
                'status' => true,
            ]);
        }

        $currentLanguageId = $this->context->language->id;
        $countries = Country::with(['translations' => fn ($q) => $q->where('language_id', $currentLanguageId)])
            ->where('status', true)
            ->orderBy('id')
            ->get();
        $countries->each(function (Country $country) use ($currentLanguageId): void {
            $country->name = $country->getName($currentLanguageId);
        });
        $countryOptions = $countries->map(fn (Country $c) => ['id' => $c->id, 'name' => $c->name])->values()->all();
        $selectedCountryIds = $shippingMethod->countries ?? [];

        $viewName = 'admin.shipping.' . $code;

        return view($viewName, [
            'shippingMethod' => $shippingMethod,
            'method' => [
                'code' => $code,
                'name' => __('admin.shipping_method_' . $code),
            ],
            'countryOptions' => $countryOptions,
            'selectedCountryIds' => $selectedCountryIds,
        ]);
    }

    public function update(ShippingMethodRequest $request, string $code): RedirectResponse
    {
        $drivers = config('shipping.drivers', []);
        if (! array_key_exists($code, $drivers)) {
            abort(404);
        }

        $shippingMethod = ShippingMethod::query()->firstOrCreate(
            ['code' => $code],
            ['config' => [], 'countries' => [], 'sort_order' => 0, 'status' => true]
        );

        $config = $shippingMethod->config ?? [];
        if ($code === 'flat_rate') {
            $config['cost'] = (float) $request->input('cost');
        }
        if ($code === 'free') {
            $config['sub_total'] = (float) $request->input('sub_total');
        }
        $shippingMethod->config = $config;
        $shippingMethod->countries = array_map('intval', (array) $request->input('countries', []));
        $shippingMethod->sort_order = (int) $request->input('sort_order', 0);
        $shippingMethod->status = (bool) $request->input('status', false);
        $shippingMethod->save();

        return redirect()
            ->route('admin.shipping.index')
            ->with('success', __('admin.updated_successfully'));
    }
}
