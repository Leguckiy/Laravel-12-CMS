<?php

namespace App\Http\Controllers\Front;

use App\DTO\CartResult;
use App\Http\Controllers\FrontController;
use App\Http\Requests\Front\CartRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends FrontController
{
    public function show(CartService $cartService): View
    {
        $cart = $this->context->cart;
        $languageId = $this->context->language->id;
        $currency = $this->context->currency;

        $display = $cartService->getCartRowsForDisplay($cart, $languageId);
        $cartRows = $display['cartRows'];
        $subtotal = $display['subtotal'];

        $this->setBreadcrumbs([
            ['label' => __('front/general.breadcrumb_home'), 'url' => route('front.home', ['lang' => $this->context->getLanguage()?->code])],
            ['label' => __('front/general.cart_title'), 'url' => null],
        ]);

        $this->languageUrls = $this->context->getLanguages()
            ->keyBy('code')
            ->map(fn ($l) => route('front.cart.show', ['lang' => $l->code]))
            ->toArray();

        return view('front.cart.show', [
            'cartRows' => $cartRows,
            'subtotal' => $subtotal,
            'currency' => $currency,
        ]);
    }

    public function add(CartRequest $request, CartService $cartService): JsonResponse
    {
        $sessionId = $request->session()->getId();
        if (! $sessionId) {
            return response()->json(['success' => false, 'message' => 'Session required.'], 422);
        }

        $result = $cartService->add(
            $request->validated(),
            $sessionId,
            $request->user('web')?->id
        );

        return response()->json(
            ['success' => $result->success, 'message' => $result->message, ...$result->data],
            $result->status
        );
    }

    public function update(CartRequest $request, CartService $cartService): JsonResponse
    {
        $result = $cartService->update(
            $request->validated(),
            $request->session()->getId()
        );

        return $this->cartResultToJsonResponse($result, ['row_total', 'subtotal']);
    }

    public function destroy(CartRequest $request, CartService $cartService): JsonResponse
    {
        $result = $cartService->destroy(
            $request->validated(),
            $request->session()->getId()
        );

        return $this->cartResultToJsonResponse($result, ['subtotal']);
    }

    /**
     * @param  array<int, string>  $formatPriceKeys  Keys from result->data to format as price and expose as key_formatted
     */
    private function cartResultToJsonResponse(CartResult $result, array $formatPriceKeys = []): JsonResponse
    {
        $payload = ['success' => $result->success, 'message' => $result->message, ...$result->data];

        if ($result->success && $formatPriceKeys !== []) {
            $currency = $this->context->getCurrency();
            foreach ($formatPriceKeys as $key) {
                if (isset($payload[$key])) {
                    $payload[$key.'_formatted'] = $currency->formatPriceFromBase((string) $payload[$key]);
                    unset($payload[$key]);
                }
            }
        }

        return response()->json($payload, $result->status);
    }
}
