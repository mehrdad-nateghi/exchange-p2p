<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum NotificationRecipientTypeEnum: string{
    use EnumTrait;

    case REQUESTER = 'requester';
    case BIDDER_WINNER = 'bidder_winner';
    case OTHER_BIDDERS = 'other_bidders';
}
