<?php

namespace App\Traits\Global;

use Illuminate\Support\Str;

trait Ulid
{
    /**
     * Boot function from laravel.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ulid = Str::ulid();
        });
    }
}
