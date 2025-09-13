<?php

namespace App\QueryFilters\Admin;

use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TradeStatusFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $statuses = array_map(function($value) {
            return TradeStatusEnum::fromName($value)->value;
        }, (array)$value);

        if (!empty($statuses)) {
            $query->whereIn('status', $statuses);
        }
    }
}
