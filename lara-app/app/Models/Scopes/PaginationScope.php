<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PaginationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        //
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('paginateWithDefault', function (Builder $builder, $perPage = null) {
            return $builder->paginate(
                $perPage ?? request('per_page', config('pagination.default_per_page'))
            )->appends(request()->query());
        });
    }
}
