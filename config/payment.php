<?php

return [
    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'notchpay'),
    
    'notchpay' => [
        'base_url' => env('NOTCHPAY_BASE_URL', 'https://api.notchpay.co/v1'),
        'public_key' => env('NOTCHPAY_PUBLIC_KEY'),
        'private_key' => env('NOTCHPAY_PRIVATE_KEY'),
        'webhook_secret' => env('NOTCHPAY_WEBHOOK_SECRET'),
        'environment' => env('NOTCHPAY_ENVIRONMENT', 'sandbox'), // sandbox ou live
    ],
    
    'supported_countries' => [
        'CM' => [
            'name' => 'Cameroun',
            'currency' => 'XAF',
            'methods' => [
                'mobile_money' => [
                    'MTN' => 'MTN Mobile Money',
                    'ORANGE' => 'Orange Money',
                    'EXPRESS_UNION' => 'Express Union Mobile',
                ],
                'bank_transfer' => [
                    'bank_account' => 'Compte bancaire',
                ]
            ]
        ],
        // Ajouter d'autres pays supportés par NotchPay
    ],
    
    'fees' => [
        'transfer_fee_percentage' => 0, // Gratuit pour l'instant
        'transfer_fee_fixed' => 0,
        'currency_conversion_fee' => 0.02, // 2% pour conversion
    ],
];
