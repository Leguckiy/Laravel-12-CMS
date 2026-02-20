<?php

namespace App\Services;

use App\DTO\CartResult;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function add(array $validated, ?int $customerId): CartResult
    {
        $product = Product::query()
            ->where('id', $validated['product_id'])
            ->where('status', true)
            ->first();

        if (! $product) {
            return new CartResult(
                success: false,
                message: 'Product not available.',
                status: 422
            );
        }

        $cart = $this->getOrCreateCart($validated['cart_token'], $customerId);
        $cartProduct = $this->findCartProduct($cart, $product->id);

        $quantity = (int) $validated['quantity'];
        $currentInCart = $cartProduct ? $cartProduct->quantity : 0;
        $totalWanted = $currentInCart + $quantity;

        if ($totalWanted > $product->quantity) {
            $availableToAdd = $product->quantity - $currentInCart;
            $message = __('front/general.cart_insufficient_quantity', ['available' => $availableToAdd]);

            return new CartResult(success: false, message: $message, status: 422);
        }

        if ($cartProduct) {
            $cartProduct->quantity += $quantity;
            $cartProduct->save();
        } else {
            CartProduct::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
                'created_at' => now(),
            ]);
        }

        $cart->touch();

        return new CartResult(
            success: true,
            message: __('front/general.cart_added_success'),
            data: ['cart_count' => $this->getCartCount($cart)]
        );
    }

    public function update(array $validated): CartResult
    {
        $cart = $this->getCartByToken($validated['cart_token']);
        if (! $cart) {
            return new CartResult(
                success: false,
                message: __('front/general.product_not_available'),
                status: 404
            );
        }

        $cartProduct = $this->findCartProduct($cart, (int) $validated['product_id']);
        if (! $cartProduct) {
            return new CartResult(
                success: false,
                message: __('front/general.product_not_available'),
                status: 404
            );
        }

        $cartProduct->load('product');
        $product = $cartProduct->product;
        if (! $product) {
            return new CartResult(
                success: false,
                message: __('front/general.product_not_available'),
                status: 422
            );
        }

        $quantity = (int) $validated['quantity'];
        if ($quantity > $product->quantity) {
            return new CartResult(
                success: false,
                message: __('front/general.cart_insufficient_quantity', ['available' => $product->quantity]),
                status: 422
            );
        }

        $cartProduct->quantity = $quantity;
        $cartProduct->save();
        $cart->touch();

        $rowTotal = (float) $cartProduct->price * $cartProduct->quantity;
        $subtotal = $this->getSubtotal($cart);

        return new CartResult(
            success: true,
            message: __('front/general.cart_updated'),
            data: [
                'cart_count' => $this->getCartCount($cart),
                'quantity' => $cartProduct->quantity,
                'row_total' => $rowTotal,
                'subtotal' => $subtotal,
            ]
        );
    }

    public function destroy(array $validated): CartResult
    {
        $cart = $this->getCartByToken($validated['cart_token']);
        if (! $cart) {
            return new CartResult(
                success: false,
                message: __('front/general.product_not_available'),
                status: 404
            );
        }

        $cartProduct = $this->findCartProduct($cart, (int) $validated['product_id']);
        if (! $cartProduct) {
            return new CartResult(
                success: false,
                message: __('front/general.product_not_available'),
                status: 404
            );
        }

        $cartProduct->delete();
        $cart->touch();

        $subtotal = $this->getSubtotal($cart);
        $cartCount = $this->getCartCount($cart);

        return new CartResult(
            success: true,
            message: __('front/general.cart_item_removed'),
            data: [
                'cart_count' => $cartCount,
                'subtotal' => $subtotal,
                'empty' => $cartCount === 0,
            ]
        );
    }

    /**
     * @return array{cartRows: array<int, array{item: CartProduct, product: Product, slug: ?string, name: string, rowTotal: float}>, subtotal: float}
     */
    public function getCartRowsForDisplay(?Cart $cart, int $languageId): array
    {
        $cartRows = [];
        $subtotal = 0.0;

        if ($cart === null) {
            return ['cartRows' => $cartRows, 'subtotal' => $subtotal];
        }

        $cart->load(['items.product.translations']);

        foreach ($cart->items as $item) {
            $product = $item->product;
            if (! $product) {
                continue;
            }
            $translation = $product->translations->firstWhere('language_id', $languageId);
            $slug = $translation?->slug;
            $name = $translation?->name;
            $rowTotal = (float) $item->price * $item->quantity;
            $subtotal += $rowTotal;
            $cartRows[] = [
                'item' => $item,
                'product' => $product,
                'slug' => $slug,
                'name' => $name,
                'rowTotal' => $rowTotal,
            ];
        }

        return ['cartRows' => $cartRows, 'subtotal' => $subtotal];
    }

    private function getOrCreateCart(string $cartToken, ?int $customerId): Cart
    {
        $cart = Cart::query()->where('cart_token', $cartToken)->first();

        if ($cart !== null) {
            if ($customerId !== null) {
                $cart->customer_id = $customerId;
                $cart->save();
            }

            return $cart;
        }

        return Cart::create([
            'cart_token' => $cartToken,
            'customer_id' => $customerId,
        ]);
    }

    private function getCartByToken(string $cartToken): ?Cart
    {
        return Cart::query()->where('cart_token', $cartToken)->first();
    }

    private function findCartProduct(Cart $cart, int $productId): ?CartProduct
    {
        return $cart->items()->where('product_id', $productId)->first();
    }

    private function getCartCount(Cart $cart): int
    {
        return (int) $cart->items()->sum('quantity');
    }

    public function getSubtotal(Cart $cart): float
    {
        return (float) $cart->items()->sum(DB::raw('price * quantity'));
    }
}
