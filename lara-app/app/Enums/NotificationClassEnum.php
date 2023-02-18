<?php

namespace App\Enums;

enum NotificationClassEnum: int{
    case Information = 0;
    case Bid = 1;
    case Request = 2;
    case Trade = 3;
}
