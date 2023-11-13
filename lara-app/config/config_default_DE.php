<?php

/*
* Keep the country-specific configuration for Germany
*/
return [
    'country' => 'DE',
    'payment_methods' => [
        [
            'name' => 'Bank Transfer',
            'attributes' => [
                ['name' => 'bank_name'],
                ['name' => 'holder_name'],
                ['name' => 'iban'],
                ['name' => 'bic']
            ]
        ],
        [
            'name' => 'Paypal',
            'attributes' => [
                ['name' => 'holder_name'],
                ['name' => 'email'],
            ]
        ]
    ]
];
