<?php

return [
    'drivers' => [
        'free_checkout' => \App\Services\Payment\FreeCheckoutPaymentMethod::class,
        'cod' => \App\Services\Payment\CodPaymentMethod::class,
        'cheque' => \App\Services\Payment\ChequePaymentMethod::class,
        'bank_transfer' => \App\Services\Payment\BankTransferPaymentMethod::class,
    ],
];
