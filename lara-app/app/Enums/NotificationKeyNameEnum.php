<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum NotificationKeyNameEnum: string{
    use EnumTrait;

    case BID_ACCEPTED_AUTOMATIC_TO_REQUESTER = 'bid_accepted_automatic_to_requester';
    case BID_ACCEPTED_AUTOMATIC_TO_BIDDER = 'bid_accepted_automatic_to_bidder';
    case BID_ACCEPTED_AUTOMATIC_TO_OTHER_BIDDERS = 'bid_accepted_automatic_to_other_bidders';
    case BID_ACCEPTED_BY_REQUESTER_TO_BIDDER = 'bid_accepted_by_requester_to_bidder';
    case BID_ACCEPTED_BY_REQUESTER_TO_OTHER_BIDDERS = 'bid_accepted_by_requester_to_other_bidders';
    case BID_REGISTERED_TO_REQUESTER = 'bid_registered_to_requester';
    case BID_REGISTERED_TO_OTHER_BIDDERS = 'bid_registered_to_other_bidders';
    case SIGNUP_SUCCESSFUL = 'signup_successful';
    case PAY_TOMAN_TO_SYSTEM_TO_SELLER = 'pay_toman_to_system_to_seller';
    case PAY_TOMAN_TO_SYSTEM_TO_BUYER = 'pay_toman_to_system_to_buyer';
    case UPLOAD_RECEIPT_TO_BUYER = 'upload_receipt_to_buyer';

    case ACCEPT_RECEIPT_BY_BUYER = 'accept_receipt_by_buyer';
    case REJECT_RECEIPT_BY_BUYER = 'reject_receipt_by_buyer';

    case TRANSFER_TO_SELLER_TO_SELLER = 'transfer_to_seller_to_seller';
}
