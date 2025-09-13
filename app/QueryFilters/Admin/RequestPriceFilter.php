<?php

namespace App\QueryFilters\Admin;

use App\Enums\PaymentMethodTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class RequestPriceFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $operator = $property === 'price_from' ? '>=' : '<=';
        $query->where('price', $operator, $value);
    }
}
