<?php

namespace App\Models;

use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory,Ulid, Paginatable, SoftDeletes;

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    /*public function getNetAmountAttribute()
    {
        return $this->amount - $this->fee;
    }*/
}
