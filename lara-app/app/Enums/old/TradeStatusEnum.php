<?php

namespace App\Enums\old;

enum TradeStatusEnum: int{
    case RialPending = 0;
    case RialConfirmation = 1;
    case TargetPending = 2;
    case TargetConfirmation = 3;
    case SystemPending = 4;
    case Successful = 5;
    case Unsuccessful = 6;
};

