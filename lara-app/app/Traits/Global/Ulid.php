<?php

namespace App\Traits\Global;

use Illuminate\Support\Str;

trait Ulid
{
    /**
     * Boot function from laravel.
     */
    protected static function bootUlid(): void
    {
        static::creating(function ($model) {
            $model->ulid = Str::ulid();
        });
    }
}
