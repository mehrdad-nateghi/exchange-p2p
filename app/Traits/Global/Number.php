<?php

namespace App\Traits\Global;

use Illuminate\Support\Str;

trait Number
{
    /**
     * Boot function from laravel.
     */
    protected static function bootNumber(): void
    {
        // todo: is it enough? it's better add timestamp.
        static::creating(function ($model) {
            do {
                $model->number = static::$prefixNumber . str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            } while (static::where('number', $model->number)->exists());
        });
    }
}
