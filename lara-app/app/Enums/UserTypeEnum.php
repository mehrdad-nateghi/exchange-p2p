<?php

namespace App\Enums;

enum UserTypeEnum: int{
    case Guest = 0;
    case Applicant = 1;
    case Admin = 2;
}
