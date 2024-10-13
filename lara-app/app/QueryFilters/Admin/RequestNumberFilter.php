<?php

namespace App\QueryFilters\Admin;

use App\Enums\PaymentMethodTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class RequestNumberFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->where('number', 'like', "%{$value}%");
    }
}
