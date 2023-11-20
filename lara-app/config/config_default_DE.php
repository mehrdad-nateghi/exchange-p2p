<?php

/*
* Keep the country-specific configuration for Germany
*/
return [
    'country' => 'DE',
    'payment_methods' => [
        'bank_transfer' => [
            'name' => 'Bank Transfer',
            'attributes' => [
                ['name' => 'bank_name'],
                ['name' => 'holder_name'],
                ['name' => 'iban'],
                ['name' => 'bic']
            ]
        ],
        'paypal' => [
            'name' => 'Paypal',
            'attributes' => [
                ['name' => 'holder_name'],
                ['name' => 'email'],
            ]
        ]
    ]
];
