<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum FinnoTechResponseStatusEnum: string{

    use EnumTrait;

    case DONE = 'DONE';
}
