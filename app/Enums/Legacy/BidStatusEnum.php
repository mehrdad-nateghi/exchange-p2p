<?php

namespace App\Enums\Legacy;

enum BidStatusEnum: int{
    case Registered = 0;
    case Top = 1;
    case Confirmed = 2;
    case Rejected = 3;
    case Invalid = 4;
};

