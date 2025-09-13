<?php

namespace App\QueryFilters;

use App\Enums\PaymentMethodTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class PaymentMethodTypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $types = $value === 'rial' ? [PaymentMethodTypeEnum::RIAL_BANK->value] : [PaymentMethodTypeEnum::FOREIGN_BANK->value,PaymentMethodTypeEnum::PAYPAL->value] ;

        if ($types) {
            $query->whereIn('type', $types);
        }
    }
}
