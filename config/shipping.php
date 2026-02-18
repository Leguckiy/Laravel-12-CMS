<?php

return [
    'drivers' => [
        'flat_rate' => \App\Services\Shipping\FlatRateShippingMethod::class,
        'free' => \App\Services\Shipping\FreeShippingMethod::class,
    ],
];
