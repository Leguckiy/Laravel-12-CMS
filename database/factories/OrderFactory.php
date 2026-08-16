<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Services\Payment\PaymentService;
use App\Services\Shipping\ShippingService;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    private const MIN_PRODUCTS_PER_ORDER = 1;
    private const MAX_PRODUCTS_PER_ORDER = 5;

    private const MIN_QUANTITY_PER_PRODUCT = 1;
    private const MAX_QUANTITY_PER_PRODUCT = 5;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /*
         * Find a random customer with an address.
         */
        $customer = Customer::query()
            ->with('addresses')
            ->whereHas('addresses')
            ->inRandomOrder()
            ->first();

        if ($customer === null) {
            throw new RuntimeException(
                'Cannot create order: no customers with addresses found.'
            );
        }

        $address = $customer->addresses->first();

        if ($address === null) {
            throw new RuntimeException(
                "Cannot create order: customer #{$customer->id} has no address."
            );
        }

        /*
         * Find an active language.
         */
        $language = Language::query()
            ->where('status', true)
            ->inRandomOrder()
            ->first();

        if ($language === null) {
            throw new RuntimeException(
                'Cannot create order: no active languages found.'
            );
        }

        /*
         * Find an active currency.
         */
        $currency = Currency::query()
            ->where('status', true)
            ->inRandomOrder()
            ->first();

        if ($currency === null) {
            throw new RuntimeException(
                'Cannot create order: no active currencies found.'
            );
        }

        /*
         * Make sure the customer's address belongs
         * to an active country.
         */
        $country = Country::query()
            ->where('id', $address->country_id)
            ->where('status', true)
            ->first();

        if ($country === null) {
            throw new RuntimeException(
                "Cannot create order: customer's address has no active country."
            );
        }

        /*
         * Select products before creating the order.
         *
         * Products are required here because shipping and payment
         * methods depend on the order items.
         */
        $products = Product::query()
            ->where('status', true)
            ->where('quantity', '>', 0)
            ->inRandomOrder()
            ->limit(
                fake()->numberBetween(
                    self::MIN_PRODUCTS_PER_ORDER,
                    self::MAX_PRODUCTS_PER_ORDER
                )
            )
            ->get();

        if ($products->isEmpty()) {
            throw new RuntimeException(
                'Cannot create order: no active products with stock found.'
            );
        }

        $items = [];
        $orderProducts = [];
        $subtotal = 0.0;

        foreach ($products as $product) {
            $quantity = fake()->numberBetween(
                self::MIN_QUANTITY_PER_PRODUCT,
                min(
                    self::MAX_QUANTITY_PER_PRODUCT,
                    (int) $product->quantity
                )
            );

            $price = (float) $product->price;
            $total = $price * $quantity;

            $productName = $this->getProductName(
                $product,
                (int) $language->id
            );

            /*
             * Data required later for order_products.
             */
            $orderProducts[] = [
                'product_id' => $product->id,
                'name' => $productName,
                'reference' => $product->reference,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $total,
            ];

            /*
             * Data required by shipping/payment services.
             */
            $items[] = [
                'price' => $price,
                'quantity' => $quantity,
            ];

            $subtotal += $total;
        }

        /*
         * Resolve available shipping methods.
         */
        $shippingService = app(ShippingService::class);

        $shippingMethods = $shippingService->getAvailableMethodsForItems(
            $items,
            (int) $country->id,
            $currency
        );

        if ($shippingMethods === []) {
            throw new RuntimeException(
                'Cannot create order: no available shipping methods.'
            );
        }

        $shippingMethod = fake()->randomElement($shippingMethods);

        /*
         * Resolve available payment methods.
         */
        $paymentService = app(PaymentService::class);

        $paymentMethods = $paymentService->getAvailableMethodsForItems(
            $items,
            (int) $country->id
        );

        if ($paymentMethods === []) {
            throw new RuntimeException(
                'Cannot create order: no available payment methods.'
            );
        }

        $paymentMethod = fake()->randomElement($paymentMethods);

        /*
         * The order status is determined by the selected
         * payment method.
         */
        $orderStatusId = $paymentMethod['order_status_id'] ?? null;

        if ($orderStatusId === null) {
            throw new RuntimeException(
                "Cannot create order: payment method '{$paymentMethod['code']}' has no order status."
            );
        }

        $shippingCost = (float) $shippingMethod['cost'];
        $total = $subtotal + $shippingCost;

        /*
         * _factory_order_products is a temporary Eloquent attribute.
         *
         * It will be moved to Order::$factoryOrderProducts
         * in afterMaking() and removed before the Order is saved.
         */
        return [
            'customer_id' => $customer->id,

            'firstname' => $customer->firstname,
            'lastname' => $customer->lastname,
            'email' => $customer->email,

            'shipping_address_id' => $address->id,
            'shipping_firstname' => $address->firstname,
            'shipping_lastname' => $address->lastname,
            'shipping_company' => $address->company,
            'shipping_address_1' => $address->address_1,
            'shipping_address_2' => $address->address_2,
            'shipping_city' => $address->city,
            'shipping_postcode' => $address->postcode,
            'shipping_country_id' => $country->id,

            'shipping_method' => [
                'code' => $shippingMethod['id'],
                'name' => $shippingMethod['name'],
            ],
            'shipping_cost' => $shippingCost,

            'payment_method' => [
                'code' => $paymentMethod['code'],
                'name' => $paymentMethod['title'],
            ],

            'subtotal' => $subtotal,
            'total' => $total,

            'order_status_id' => $orderStatusId,

            'language_id' => $language->id,
            'currency_id' => $currency->id,

            'comment' => fake()->optional()->sentence(),
            'ip' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),

            /*
             * Temporary data. This is NOT a database column.
             */
            '_factory_order_products' => $orderProducts,
        ];
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this
            ->afterMaking(function (Order $order): void {
                /*
                 * Move temporary product data from Eloquent attributes
                 * to the real PHP property of the Order model.
                 */
                $order->factoryOrderProducts = $order->getAttribute(
                    '_factory_order_products'
                );

                /*
                 * Remove the temporary attribute.
                 *
                 * Otherwise Laravel would try to insert
                 * _factory_order_products into the orders table.
                 */
                $order->offsetUnset('_factory_order_products');
            })
            ->afterCreating(function (Order $order): void {
                /*
                 * At this point Order has already been inserted
                 * and therefore has its ID.
                 */
                foreach ($order->factoryOrderProducts as $orderProduct) {
                    OrderProduct::create([
                        'order_id' => $order->id,
                        ...$orderProduct,
                    ]);
                }
            });
    }

    /**
     * Get product name for the order product.
     */
    private function getProductName(Product $product, int $languageId): string {
        $translation = $product->translation($languageId);

        return $translation?->name ?? $product->reference;
    }
}
