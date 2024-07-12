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
        $type = RequestStatusEnum::fromName($value);

        if ($type) {
            $query->where('status', $type->value);
        }
    }
}
