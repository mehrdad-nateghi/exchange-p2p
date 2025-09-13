<?php

namespace App\Enums\Legacy;

enum RequestStatusEnum: int{
    case Pending = 0;
    case InProcess = 1;
    case InTrade = 2;
    case Removed = 3;
}
