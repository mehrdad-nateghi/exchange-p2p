<?php

namespace App\QueryFilters;

use App\Enums\RequestTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class RequestTypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $type = RequestTypeEnum::fromName($value)->value;

        if ($type) {
            $query->where('type', $type);
        }
    }
}
