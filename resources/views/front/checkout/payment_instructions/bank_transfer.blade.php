<div class="checkout-payment-instructions checkout-payment-instructions--bank-transfer">
    <p class="text-muted mb-2">{{ __('front/checkout.payment_bank_transfer_intro') }}</p>
    <div class="bg-light border rounded p-3 mb-2">
        @if($instructionsText)
            {!! nl2br(e($instructionsText)) !!}
        @endif
    </div>
    <p class="small text-muted mb-0">{{ __('front/checkout.payment_order_notice') }}</p>
</div>
