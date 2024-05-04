<?php

namespace App\Traits\Global;

use Illuminate\Support\Str;

trait UUID
{
    /**
     * Boot function from laravel.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }
}
