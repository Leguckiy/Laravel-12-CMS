<div class="checkout-payment-instructions checkout-payment-instructions--cheque mt-2">
    <p class="mb-1 fw-bold">{{ __('front/checkout.payment_cheque_payable_to') }}:</p>
    <p class="mb-3">{{ $payableTo }}</p>
    @if($sendTo)
        <p class="mb-1 fw-bold">{{ __('front/checkout.payment_cheque_send_to') }}:</p>
        <p class="mb-3">{{ $sendTo }}</p>
    @endif
    <p class="small text-muted mb-0">{{ __('front/checkout.payment_order_notice') }}</p>
</div>
