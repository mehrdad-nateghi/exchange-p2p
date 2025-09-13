<?php

namespace App\QueryFilters;

use App\Enums\BidStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class BidStatusFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        if($value === 'active'){
            $query->whereIn('bids.status', [BidStatusEnum::REGISTERED->value, BidStatusEnum::ACCEPTED->value]);
        }else{
            $query->whereIn('bids.status', [BidStatusEnum::REJECTED->value]);
        }
    }
}
