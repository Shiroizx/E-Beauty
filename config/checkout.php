<?php

return [

    'free_shipping_subtotal' => env('CHECKOUT_FREE_SHIPPING_SUBTOTAL', 500_000),

    'flat_shipping_cost' => env('CHECKOUT_FLAT_SHIPPING', 15_000),

    'bank' => [
        'name' => env('CHECKOUT_BANK_NAME', 'BCA'),
        'account_number' => env('CHECKOUT_BANK_ACCOUNT', '1234567890'),
        'account_holder' => env('CHECKOUT_BANK_HOLDER', 'PT Skinbae.ID Indonesia'),
    ],

];
