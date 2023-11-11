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
                ['name' => 'account_number'],
                ['name' => 'card_number'],
                ['name' => 'shaba_number']
            ]
        ]
    ]
];
