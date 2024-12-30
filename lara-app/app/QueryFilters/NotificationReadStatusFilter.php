<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class NotificationReadStatusFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        match ($value) {
            'read' => $query->whereNotNull('read_at'),
            'unread' => $query->whereNull('read_at'),
            //default => null
        };
    }
}
