<?php

namespace App\Traits\Global;

use App\Models\Scopes\PaginationScope;

trait Paginatable
{
    public static function bootPaginatable()
    {
        static::addGlobalScope(new PaginationScope);
    }
}
