<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum NotificationModelNamesEnum: string
{
    use EnumTrait;

    case REQUEST = 'request';
    case BID = 'bid';
    case TRADE = 'trade';
    case REQUESTS_GUEST = 'requests_guest';
    case TRADES_GUEST = 'trades_guest';

    case REQUESTS_USER = 'requests_user';
    case TRADES_USER = 'trades_user';

}
