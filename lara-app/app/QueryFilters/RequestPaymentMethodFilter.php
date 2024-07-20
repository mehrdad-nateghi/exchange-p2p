<?php

namespace App\QueryFilters;

use App\Enums\PaymentMethodTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class RequestPaymentMethodFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $types = array_map(function($value) {
            return PaymentMethodTypeEnum::fromName($value)->value;
        }, (array)$value);

        if (!empty($types)) {
            $query->whereHas('paymentMethods', function ($query) use ($types) {
                $query->whereIn('type', $types);
            });
        }
    }
}
