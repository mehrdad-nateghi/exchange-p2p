<?php

namespace App\QueryFilters;

use App\Enums\PaymentMethodTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class RequestVolumeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $operator = $property === 'volume_from' ? '>=' : '<=';
        $query->where('volume', $operator, $value);
    }
}
