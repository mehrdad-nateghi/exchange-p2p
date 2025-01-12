<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum NotificationIconsEnum: string{
    use EnumTrait;

    case INFO = 'info';
    case BID = 'bid';
    case TRADE = 'trade';
}
