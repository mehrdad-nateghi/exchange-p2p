<?php

namespace App\QueryFilters;

use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class RequestStatusFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $statuses = array_map(function($value) {
            return RequestStatusEnum::fromName($value)->value;
        }, (array)$value);

        if (!empty($statuses)) {
            $query->whereIn('status', $statuses);
        }
    }
}
