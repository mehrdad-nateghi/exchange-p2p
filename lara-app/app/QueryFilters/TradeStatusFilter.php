<?php

namespace App\QueryFilters;

use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TradeStatusFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $types = $value === 'active' ? [TradeStatusEnum::PROCESSING->value] : [TradeStatusEnum::COMPLETED->value, TradeStatusEnum::CANCELED->value] ;

        if ($types) {
            $query->whereIn('trades.status', $types);
        }
    }
}
