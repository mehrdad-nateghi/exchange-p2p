<?php

namespace App\QueryFilters;

use App\Enums\PaymentMethodTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class RequestPaymentMethodFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        // todo: refactor
        $type = PaymentMethodTypeEnum::FOREIGN_BANK->getKeyLowercase() === $value ? PaymentMethodTypeEnum::FOREIGN_BANK : PaymentMethodTypeEnum::PAYPAL;

        if ($type) {
            $query->whereHas('paymentMethods', function ($query) use ($type) {
                $query->where('type', $type);
            });
        }
    }
}
