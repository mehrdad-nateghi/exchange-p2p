<?php

namespace App\Enums;

enum SendCodeViaEnum: int{
    case EMAIL = 1;
    case MOBILE = 2;
}
