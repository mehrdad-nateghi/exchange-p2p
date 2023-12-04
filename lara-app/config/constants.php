<?php


return [
    'Euro_Daily_Rate' => '54000', // Keep the Euro Daily Rate Value in Toman to Serve in Create Request Process

    'SupportId_Prefixes' => [ // Keep the Constant Prefixes to Generate Support Id for Requests, Bids, Trades, Transactions, and Invoices
        'Bid_Pr' => 'BI-',
        'Request_Pr' => 'RE-',
        'Trade_Pr' => 'TR-',
        'Transaction_Pr' => 'TS-',
        'Invoice_Pr' => 'IN-'
    ],

    'Verification_Code_Expiration_Per_Minutes' => '60'
];
