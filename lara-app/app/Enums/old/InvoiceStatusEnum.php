<?php

namespace App\Enums\old;

enum InvoiceStatusEnum: int{
    case Open = 0;
    case Paid = 1;
    case Unpaid = 2;
};

