<?php

return [
    'client_id' => env('FINNOTECH_CLIENT_ID'),
    'client_secret' => env('FINNOTECH_CLIENT_SECRET'),
    'national_id' => env('FINNOTECH_NATIONAL_ID'),
    'base_url' => env('FINNOTECH_BASE_URL'),
    'authorization_code' => env('FINNOTECH_AUTHORIZATION_CODE'),
    'authorization_token' => env('FINNOTECH_AUTHORIZATION_TOKEN'),
    //'credentials_token_scopes' => env('FINNOTECH_CREDENTIALS_TOKEN_SCOPES'),
    'credentials_token_scopes' => "facility:card-to-iban:get",
];
