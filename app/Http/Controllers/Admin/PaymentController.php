<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\PaymentMethodRequest;
use App\Models\Country;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'payment_methods',
            'route' => 'admin.payment.index',
        ],
    ];

    protected string $title = 'payment_methods';

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();
        $route = request()->route();
        if ($route && $route->getName() === 'admin.payment.edit') {
            $code = $route->parameter('code');
            $breadcrumbs[] = [
                'title' => __('admin.payment_method_' . $code),
                'url' => route('admin.payment.edit', $code),
                'translate' => false,
            ];
        }

        return $breadcrumbs;
    }

    public function getTitle(): string
    {
        $route = request()->route();
        if ($route && $route->getName() === 'admin.payment.edit') {
            $code = $route->parameter('code');

            return __('admin.payment_method_' . $code);
        }

        return parent::getTitle();
    }

    public function index(): View
    {
        $methodCodes = array_keys(config('payment.drivers', []));
        $paymentMethods = PaymentMethod::query()
            ->whereIn('code', $methodCodes)
            ->get()
            ->keyBy('code');
        $methods = collect($methodCodes)
            ->map(function (string $code) use ($paymentMethods) {
                $row = $paymentMethods->get($code);

                return [
                    'code' => $code,
                    'name' => __('admin.payment_method_'.$code),
                    'sort_order' => $row ? $row->sort_order : 0,
                    'status' => $row ? $row->status : true,
                ];
            })
            ->sortBy('sort_order')
            ->values();

        return view('admin.payment.index', compact('methods'));
    }

    public function edit(string $code): View
    {
        $drivers = config('payment.drivers', []);
        if (! array_key_exists($code, $drivers)) {
            abort(404);
        }

        $viewName = 'admin.payment.' . $code;
        if (! view()->exists($viewName)) {
            abort(404);
        }

        $paymentMethod = PaymentMethod::query()->where('code', $code)->first();
        if ($paymentMethod === null) {
            $paymentMethod = new PaymentMethod([
                'code' => $code,
                'config' => [],
                'countries' => [],
                'sort_order' => 0,
                'status' => true,
            ]);
        }

        $orderStatusOptions = $this->getOrderStatusOptions();
        $viewData = [
            'paymentMethod' => $paymentMethod,
            'method' => [
                'code' => $code,
                'name' => __('admin.payment_method_' . $code),
            ],
            'orderStatusOptions' => $orderStatusOptions,
        ];

        if (in_array($code, ['cod', 'cheque', 'bank_transfer'], true)) {
            $currentLanguageId = $this->context->language->id;
            $countries = Country::with(['translations' => fn ($q) => $q->where('language_id', $currentLanguageId)])
                ->where('status', true)
                ->orderBy('id')
                ->get();
            $countries->each(function (Country $country) use ($currentLanguageId): void {
                $country->name = $country->getName($currentLanguageId);
            });
            $viewData['countryOptions'] = $countries->map(fn (Country $c) => ['id' => $c->id, 'name' => $c->name])->values()->all();
            $viewData['selectedCountryIds'] = $paymentMethod->countries ?? [];
        }

        if ($code === 'bank_transfer') {
            $viewData['languages'] = $this->getLanguages();
            $viewData['adminLanguage'] = $this->context->language;
            $viewData['instructionsTranslations'] = $paymentMethod->config['instructions'] ?? [];
        }

        return view($viewName, $viewData);
    }

    public function update(PaymentMethodRequest $request, string $code): RedirectResponse
    {
        $drivers = config('payment.drivers', []);
        if (! array_key_exists($code, $drivers)) {
            abort(404);
        }

        $paymentMethod = PaymentMethod::query()->firstOrCreate(
            ['code' => $code],
            ['config' => [], 'countries' => [], 'sort_order' => 0, 'status' => true]
        );

        $config = $paymentMethod->config ?? [];
        if (in_array($code, ['free_checkout', 'cod', 'cheque', 'bank_transfer'], true)) {
            $config['order_status_id'] = (int) $request->input('order_status_id');
        }
        if ($code === 'cheque') {
            $config['payable_to'] = (string) $request->input('payable_to', '');
        }
        if ($code === 'bank_transfer') {
            $instructions = $request->input('instructions', []);
            $config['instructions'] = is_array($instructions)
                ? array_map(fn ($v) => (string) $v, $instructions)
                : [];
        }
        $paymentMethod->config = $config;
        if (in_array($code, ['cod', 'cheque', 'bank_transfer'], true)) {
            $paymentMethod->countries = array_map('intval', (array) $request->input('countries', []));
        }
        $paymentMethod->sort_order = (int) $request->input('sort_order', 0);
        $paymentMethod->status = (bool) $request->input('status', false);
        $paymentMethod->save();

        return redirect()
            ->route('admin.payment.index')
            ->with('success', __('admin.updated_successfully'));
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function getOrderStatusOptions(): array
    {
        $currentLanguageId = $this->context->language->id;
        $statuses = OrderStatus::with(['translations' => fn ($q) => $q->where('language_id', $currentLanguageId)])
            ->orderBy('id')
            ->get();

        return $statuses->map(function ($status) {
            $name = $this->translation($status->translations)?->name ?? '';

            return ['id' => $status->id, 'name' => $name];
        })->values()->all();
    }
}
