<?php

namespace App\Models;

use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use HasFactory, Ulid, SoftDeletes;

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Request::class);
    }
}
