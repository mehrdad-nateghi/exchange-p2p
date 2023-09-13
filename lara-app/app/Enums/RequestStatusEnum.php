<?php

namespace App\Enums;

enum RequestStatusEnum: int{
    case Pending = 0;
    case Inprocess = 1;
    case Removed = 2;
}
