<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\OrderHistoryRequest;
use App\Http\Requests\Admin\OrderQuantityRequest;
use App\Http\Requests\Admin\OrderRequest;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\Customer;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Address;
use App\Models\Product;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Setting;
use App\Services\OrderService;
use App\Services\Shipping\ShippingService;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class OrderController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'orders',
            'route' => 'admin.order.index',
        ],
    ];

    protected string $title = 'orders';

    /**
     * Display a listing of the orders.
     */
    public function index(): View
    {
        $currentLanguageId = $this->context->language->id;

        $orders = Order::with([
            'customer',
            'orderStatus.translations' => function ($query) use ($currentLanguageId) {
                $query->where('language_id', $currentLanguageId);
            },
            'currency',
        ])
            ->orderByDesc('id')
            ->paginate(10);

        $orders->getCollection()->transform(function (Order $order) use ($currentLanguageId) {
            $statusTranslations = $order->orderStatus->translations;
            $order->status_name = $this->translation($statusTranslations, $currentLanguageId)?->name;

            return $order;
        });

        return view('admin.order.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(): View
    {
        $languagesOptions = Language::getActiveOptions();
        $currenciesOptions = Currency::getOptions();
        $defaultCurrencyId = (int) Setting::get('config_currency_id', '0');

        $currentCurrency = Currency::find($defaultCurrencyId);

        $order = null;

        return view('admin.order.form', compact(
            'order',
            'languagesOptions',
            'currenciesOptions',
            'defaultCurrencyId',
            'currentCurrency'
        ));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(OrderRequest $request, OrderService $orderService): RedirectResponse
    {
        $data = $request->validated();

        $customer = Customer::findOrFail($data['customer_id']);
        $address = Address::findOrFail($data['shipping_address_id']);

        $orderStatusId = $orderService->getOrderStatusIdForPaymentCode($data['payment_method']['code']);

        $order = Order::create([
            'customer_id'         => $data['customer_id'],
            'firstname'           => $customer->firstname,
            'lastname'            => $customer->lastname,
            'email'               => $customer->email,
            'shipping_address_id' => $data['shipping_address_id'],
            'shipping_firstname'  => $address->firstname,
            'shipping_lastname'   => $address->lastname,
            'shipping_company'    => $address->company,
            'shipping_address_1'  => $address->address_1,
            'shipping_address_2'  => $address->address_2,
            'shipping_city'       => $address->city,
            'shipping_postcode'   => $address->postcode,
            'shipping_country_id' => $address->country_id,
            'shipping_method'     => $data['shipping_method'],
            'shipping_cost'       => $data['shipping_method']['cost'],
            'payment_method'      => $data['payment_method'],
            'subtotal'            => $data['subtotal'],
            'total'               => $data['total'],
            'language_id'         => $data['language_id'],
            'currency_id'         => $data['currency_id'],
            'comment'             => $data['comment'] ?? null,
            'order_status_id'     => $orderStatusId,
            'ip'                  => $request->ip(),
            'user_agent'          => $request->userAgent(),
        ]);

        
        foreach ($data['items'] as $item) {
        $product = Product::with('translations')->findOrFail($item['product_id']);
        $name = $product->translations->firstWhere('language_id', $data['language_id'])?->name;

            OrderProduct::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'name'       => $name,
                'reference'  => $product->reference,
                'quantity'   => $item['quantity'],
                'price'      => $product->price,
                'total'      => $item['quantity'] * $product->price,
            ]);
        }

        return redirect()->route('admin.order.edit', $order)->with('success', __('admin.order_created'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $currentLanguageId = $this->context->language->id;

        $order->load([
            'customer',
            'shippingCountry.translations' => function ($query) use ($currentLanguageId) {
                $query->where('language_id', $currentLanguageId);
            },
            'products.product',
            'language',
            'currency',
            'orderStatus.translations' => function ($query) use ($currentLanguageId) {
                $query->where('language_id', $currentLanguageId);
            },
            'histories' => function ($query) {
                $query->orderBy('created_at');
            },
            'histories.orderStatus.translations' => function ($query) use ($currentLanguageId) {
                $query->where('language_id', $currentLanguageId);
            },
        ]);

        $this->breadcrumbs[] = [
            'title' => 'order',
            'route' => null,
            'translate' => true,
        ];

        return view('admin.order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order): View
    {
        $order->load([
            'customer',
            'shippingCountry',
            'language',
            'currency',
            'products.product',
        ]);

        $languagesOptions = Language::getActiveOptions();
        $currenciesOptions = Currency::getOptions();
        $defaultCurrencyId = (int) Setting::get('config_currency_id');

        $currentCurrency = $order->currency ?: Currency::find($defaultCurrencyId);

        $order->load(['histories.orderStatus']);

        $orderStatusesOptions = OrderStatus::getOptions($order->language_id);

        return view('admin.order.form', compact(
            'order',
            'languagesOptions',
            'currenciesOptions',
            'defaultCurrencyId',
            'currentCurrency',
            'orderStatusesOptions'
        ));
    }

    /**
     * Update the specified order.
     */
    public function update(OrderRequest $request, Order $order): RedirectResponse
    {
        $data = $request->validated();

        $customer = Customer::findOrFail($data['customer_id']);
        $address = Address::findOrFail($data['shipping_address_id']);

        $order->update([
            'customer_id'          => $data['customer_id'],
            'firstname'            => $customer->firstname,
            'lastname'             => $customer->lastname,
            'email'                => $customer->email,
            'shipping_address_id'  => $data['shipping_address_id'],
            'shipping_firstname'   => $address->firstname,
            'shipping_lastname'    => $address->lastname,
            'shipping_company'     => $address->company,
            'shipping_address_1'   => $address->address_1,
            'shipping_address_2'   => $address->address_2,
            'shipping_city'        => $address->city,
            'shipping_postcode'    => $address->postcode,
            'shipping_country_id'  => $address->country_id,
            'shipping_method'      => $data['shipping_method'],
            'shipping_cost'        => $data['shipping_method']['cost'],
            'payment_method'       => $data['payment_method'],
            'subtotal'             => $data['subtotal'],
            'total'                => $data['total'],
            'language_id'          => $data['language_id'],
            'currency_id'          => $data['currency_id'],
            'comment'              => $data['comment'] ?? null,
            'ip'                   => $request->ip(),
            'user_agent'           => $request->userAgent(),
        ]);

        $order->products()->delete();

        foreach ($data['items'] as $item) {
            $product = Product::with('translations')->findOrFail($item['product_id']);
            $name = $product->translations->firstWhere('language_id', $data['language_id'])?->name;

            OrderProduct::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'name'       => $name,
                'reference'  => $product->reference,
                'quantity'   => $item['quantity'],
                'price'      => $product['price'],
                'total'      => $item['quantity'] * $product->price,
            ]);
        }

        return redirect()->route('admin.order.edit', $order)->with('success', __('admin.order_updated'));
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $order->products()->delete();
        $order->histories()->delete();

        $order->delete();

        return redirect()->route('admin.order.index')->with('success', __('admin.order_deleted'));
    }

    public function changeCurrency(Request $request): JsonResponse
    {
        $fromId = (int) $request->input('from_currency_id');
        $toId = (int) $request->input('to_currency_id');

        $from = Currency::findOrFail($fromId);
        $to = Currency::findOrFail($toId);

        $fromRate = (float) $from->value;
        $toRate = (float) $to->value;
        $rate = $toRate / $fromRate;

        return response()->json([
            'success' => true,
            'rate' => $rate,
            'decimals' => (int) $to->decimal_place,
            'symbol_left' => $to->symbol_left ?? '',
            'symbol_right' => $to->symbol_right ?? '',
        ]);
    }

    public function checkQuantity(OrderQuantityRequest $request): JsonResponse
    {
        $productId = (int) $request->input('product_id');
        $quantity = (int) $request->input('quantity');
        $currencyId = (int) $request->input('currency_id');
        $orderId = (int) $request->input('order_id', 0);

        $product = Product::query()
            ->whereKey($productId)
            ->where('status', true)
            ->first();

        if (! $product || $product->quantity <= 0) {
            return response()->json([
                'success' => false,
                'message' => __('admin.order_product_not_found'),
            ]);
        }

        $existingQty = 0;
        if ($orderId > 0) {
            $existingQty = (int) OrderProduct::query()
                ->where('order_id', $orderId)
                ->where('product_id', $productId)
                ->sum('quantity');
        }

        $effectiveFree = $product->quantity + $existingQty;

        if ($effectiveFree <= 0) {
            return response()->json([
                'success' => false,
                'message' => __('admin.order_product_not_found'),
            ]);
        }

        if ($quantity > $effectiveFree) {
            return response()->json([
                'success' => false,
                'message' => __('admin.order_product_quantity_not_available'),
            ]);
        }

        $currency = Currency::find($currencyId);

        $priceInCurrent = $currency->convertFromBase((float) $product->price);
        $totalInCurrent = $priceInCurrent * $quantity;

        $availableRemaining = max($effectiveFree - $quantity, 0);

        return response()->json([
            'success' => true,
            'quantity' => $quantity,
            'available' => $availableRemaining,
            'price' => [
                'raw' => $priceInCurrent,
                'formatted' => $currency->formatPrice($priceInCurrent),
            ],
            'total' => [
                'raw' => $totalInCurrent,
                'formatted' => $currency->formatPrice($totalInCurrent),
            ],
        ]);
    }

    public function getShippingMethodsForOrder(Request $request, ShippingService $shippingService): JsonResponse
    {
        $addressId  = (int) $request->input('shipping_address_id');
        $currencyId = (int) $request->input('currency_id');

        $address   = Address::findOrFail($addressId);
        $countryId = (int) $address->country_id;
        $currency  = Currency::findOrFail($currencyId);

        $items = $request->input('items', []);

        $methods = $shippingService->getAvailableMethodsForItems($items, $countryId, $currency);

        return response()->json([
            'success'     => true,
            'methods'     => $methods,
            'selected_id' => $request->input('selected_id'),
        ]);
    }

    public function setShippingMethodForOrder(Request $request, ShippingService $shippingService): JsonResponse
    {
        $addressId  = (int) $request->input('shipping_address_id');
        $currencyId = (int) $request->input('currency_id');
        $methodId   = $request->input('method_id');

        $address   = Address::findOrFail($addressId);
        $countryId = (int) $address->country_id;
        $currency  = Currency::findOrFail($currencyId);

        $items = $request->input('items', []);

        $methods = $shippingService->getAvailableMethodsForItems($items, $countryId, $currency);

        if ($methods === []) {
            return response()->json([
                'success' => false,
                'message' => __('front/checkout.shipping_methods_none_available'),
            ], 422);
        }

        $selected = collect($methods)->firstWhere('id', $methodId);
        if (! $selected) {
            return response()->json([
                'success' => false,
                'message' => __('front/checkout.shipping_method_no_longer_available'),
            ], 422);
        }

        $subtotalBase        = (float) $request->input('subtotal_base', 0);
        $shippingCostBase    = (float) $selected['cost'];
        $orderTotalBase      = $subtotalBase + $shippingCostBase;
        $orderTotalFormatted = $currency->formatPriceFromBase((string) $orderTotalBase);

        return response()->json([
            'success' => true,
            'method'  => $selected,
            'order_total_formatted' => $orderTotalFormatted,
        ]);
    }

    public function getPaymentMethodsForOrder(Request $request, PaymentService $paymentService): JsonResponse {
        $addressId = (int) $request->input('shipping_address_id');

        $address = Address::findOrFail($addressId);
        $countryId = (int) $address->country_id;

        $items = $request->input('items', []);

        $rawMethods = $paymentService->getAvailableMethodsForItems($items, $countryId);

        $methods = array_map(fn (array $method) => [
            'id' => $method['code'],
            'name' => $method['title'],
        ], $rawMethods);

        return response()->json([
            'success' => true,
            'methods' => $methods,
            'selected_id' => $request->input('selected_id'),
        ]);
    }

    public function setPaymentMethodForOrder(Request $request, PaymentService $paymentService): JsonResponse {
        $addressId = (int) $request->input('shipping_address_id');
        $methodId = $request->input('method_id');

        $address = Address::findOrFail($addressId);
        $countryId = (int) $address->country_id;

        $rawMethods = $paymentService->getAvailableMethodsForItems(null, $countryId);

        if ($rawMethods === []) {
            return response()->json([
                'success' => false,
                'message' => __('front/checkout.payment_methods_none_available'),
            ], 422);
        }

        $selected = collect($rawMethods)->firstWhere('code', $methodId);

        if (! $selected) {
            return response()->json([
                'success' => false,
                'message' => __('front/checkout.error_generic'),
            ], 422);
        }

        $instructions = $paymentService->getInstructionsForMethod(
            $selected['code'],
            $this->context->language->id
        );

        return response()->json([
            'success' => true,
            'method' => [
                'id' => $selected['code'],
                'name' => $selected['title'],
            ],
            'instructions' => $instructions,
        ]);
    }

    /**
     * Store a new history entry for the specified order.
     */
    public function storeHistory(OrderHistoryRequest $request, Order $order): JsonResponse {
        $data = $request->validated();

        if ((int) $data['order_status_id'] === (int) $order->order_status_id) {
            return response()->json([
                'success' => false,
                'message' => __('admin.order_status_not_changed'),
            ]);
        }

        $history = OrderHistory::create([
            'order_id' => $order->id,
            'order_status_id' => $data['order_status_id'],
            'comment' => $data['comment'] ?? null,
            'notify' => (bool) ($data['notify'] ?? false),
            'created_at' => now(),
        ]);

        $order->update([
            'order_status_id' => $data['order_status_id'],
        ]);

        $history->load('orderStatus');

        return response()->json([
            'success' => true,
            'message' => __('admin.history_added'),
            'history' => [
                'id' => $history->id,
                'date_added' => $history->created_at?->format('d/m/Y'),
                'comment' => $history->comment,
                'status' => $history->orderStatus?->getName($order->language_id),
                'notify' => $history->notify,
            ],
        ]);
    }
}
