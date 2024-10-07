<?php


return [
    'verification_code_expiration_time_per_minutes' => env('VERIFICATION_CODE_EXPIRATION_TIME_PER_MINUTES', 60),
    'bid_price_plus_latest_bid_price_rial' => env('BID_PRICE_PLUS_LATEST_BID_PRICE_RIAL', 500),
    'invoice_fee_percentage' => env('INVOICE_FEE_PERCENTAGE', 10),

    'Euro_Daily_Rate' => '54000', // Keep the Euro Daily Rate Value in Toman to Serve in Create Request Process

    'SupportId_Prefixes' => [ // Keep the Constant Prefixes to Generate Support Id for Requests, Bids, Trades, Transactions, and Invoices
        'Bid_Pr' => 'BI-',
        'Request_Pr' => 'RE-',
        'Trade_Pr' => 'TR-',
        'Transaction_Pr' => 'TS-',
        'Invoice_Pr' => 'IN-'
    ],

    //'Verification_Code_Expiration_Per_Minutes' => '60',

    'PASSPORT_ACCESS_TOKEN_EXPIRES_IN_PER_HOUR' => '24',

    'COOKIE_EXPIRES_IN_PER_MIN' => '100',

    'access_token_expiration_time_per_minutes' => env('ACCESS_TOKEN_EXPIRATION_TIME_PER_MINUTES', 14400),
    'refresh_token_expiration_time_per_minutes' => env('REFRESH_TOKEN_EXPIRATION_TIME_PER_MINUTES', 14400),


    'frontend_url_after_payment' => env('FRONTEND_URL_AFTER_PAYMENT', 'http://localhost:8000/dashboard/trades'),

    'default_hours_increase_expire_at_trade_step' => env('DEFAULT_HOURS_INCREASE_EXPIRE_AT_TRADE_STEP', '2'),
];
