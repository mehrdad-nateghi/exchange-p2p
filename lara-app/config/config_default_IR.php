<?php

/*
* Keep the country-specific configuration for Iran
*/
return [
    'country' => 'IR',
    'payment_methods' => [
        [
            'name' => 'Bank Transfer',
            'attributes' => [
                ['name' => 'bank_name'],
                ['name' => 'holder_name'],
                ['name' => 'iban'],
                ['name' => 'bic']
            ]
        ]
    ]
];
