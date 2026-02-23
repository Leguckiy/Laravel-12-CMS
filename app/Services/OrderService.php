<?php

namespace App\Services;

use App\DTO\OrderFromCheckoutData;
use App\Models\Cart;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderProduct;
use App\Models\PaymentMethod;
use App\Services\Payment\PaymentService;
use App\Services\Shipping\ShippingService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        protected CartService $cartService,
        protected ShippingService $shippingService,
        protected PaymentService $paymentService,
    ) {}

    /**
     * Create order from checkout. Request (CheckoutConfirmOrderRequest) already validated session data.
     * Validates cart stock, that selected shipping/payment methods are still available, then creates order.
     *
     * @throws InvalidArgumentException when cart invalid, method no longer available, or payment config invalid
     */
    public function createOrderFromCheckout(Cart $cart, OrderFromCheckoutData $data): Order
    {
        return DB::transaction(function () use ($cart, $data): Order {
            $this->cartService->validateCartStock($cart);

            $this->validateShippingMethodAvailable($cart, $data);
            $this->validatePaymentMethodAvailable($cart, $data);

            $orderStatusId = $this->getOrderStatusIdForPaymentCode($data->paymentMethodCode);
            $subtotal = $this->cartService->getSubtotal($cart);
            $total = $subtotal + $data->shippingCost;

            $shippingMethodSnapshot = [
                'code' => $data->shippingMethodCode,
                'name' => $this->shippingService->getMethodTitle($data->shippingMethodCode),
            ];
            $paymentMethodSnapshot = [
                'code' => $data->paymentMethodCode,
                'name' => $this->paymentService->getMethodTitle($data->paymentMethodCode),
            ];

            $order = Order::query()->create([
                'customer_id' => $data->customerId,
                'firstname' => $data->firstname,
                'lastname' => $data->lastname,
                'email' => $data->email,
                'shipping_address_id' => $data->shippingAddressId,
                'shipping_firstname' => $data->shippingFirstname,
                'shipping_lastname' => $data->shippingLastname,
                'shipping_company' => $data->shippingCompany,
                'shipping_address_1' => $data->shippingAddress1,
                'shipping_address_2' => $data->shippingAddress2,
                'shipping_city' => $data->shippingCity,
                'shipping_postcode' => $data->shippingPostcode,
                'shipping_country_id' => $data->shippingCountryId,
                'shipping_method' => $shippingMethodSnapshot,
                'shipping_cost' => $data->shippingCost,
                'payment_method' => $paymentMethodSnapshot,
                'subtotal' => $subtotal,
                'total' => $total,
                'order_status_id' => $orderStatusId,
                'language_id' => $data->languageId,
                'currency_id' => $data->currencyId,
                'comment' => $data->comment,
                'ip' => $data->ip,
                'user_agent' => $data->userAgent,
            ]);

            $this->createOrderProductsFromCart($order->id, $cart, $data->languageId);
            $this->createInitialOrderHistory($order->id, $orderStatusId);

            return $order->fresh();
        });
    }

    protected function validateShippingMethodAvailable(Cart $cart, OrderFromCheckoutData $data): void
    {
        $currency = Currency::query()->find($data->currencyId);
        if ($currency === null) {
            throw new InvalidArgumentException(__('front/checkout.error_generic'));
        }
        $methods = $this->shippingService->getAvailableMethods($cart, $data->shippingCountryId, $currency);
        $found = collect($methods)->firstWhere('id', $data->shippingMethodCode);
        if ($found === null) {
            throw new InvalidArgumentException(__('front/checkout.shipping_method_no_longer_available'));
        }
    }

    protected function validatePaymentMethodAvailable(Cart $cart, OrderFromCheckoutData $data): void
    {
        $methods = $this->paymentService->getAvailableMethods($cart, $data->shippingCountryId);
        $found = collect($methods)->firstWhere('code', $data->paymentMethodCode);
        if ($found === null) {
            throw new InvalidArgumentException(__('front/checkout.payment_method_no_longer_available'));
        }
    }

    protected function getOrderStatusIdForPaymentCode(string $code): int
    {
        $method = PaymentMethod::query()
            ->where('code', $code)
            ->where('status', true)
            ->first();

        if ($method === null || empty($method->config['order_status_id'])) {
            throw new InvalidArgumentException(__('front/checkout.error_generic'));
        }

        return (int) $method->config['order_status_id'];
    }

    protected function createOrderProductsFromCart(int $orderId, Cart $cart, int $languageId): void
    {
        $cart->load(['items.product.translations']);

        foreach ($cart->items as $item) {
            $product = $item->product;
            $name = $product->translations->firstWhere('language_id', $languageId)?->name;

            $reference = $product?->reference;
            $quantity = (int) $item->quantity;
            $price = (float) $item->price;
            $total = $price * $quantity;

            OrderProduct::query()->create([
                'order_id' => $orderId,
                'product_id' => $product->id,
                'name' => $name,
                'reference' => $reference,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $total,
                'created_at' => now(),
            ]);
        }
    }

    protected function createInitialOrderHistory(int $orderId, int $orderStatusId): void
    {
        OrderHistory::query()->create([
            'order_id' => $orderId,
            'order_status_id' => $orderStatusId,
            'comment' => null,
            'notify' => false,
            'created_at' => now(),
        ]);
    }
}
