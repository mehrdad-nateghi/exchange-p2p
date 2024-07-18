<?php

namespace App\Models;

use App\Enums\BidStatusEnum;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use HasFactory, Ulid, SoftDeletes;

    protected $fillable = [
        'request_id', 'payment_method_id', 'price', 'status'
    ];

    protected $casts = [
        'status' => BidStatusEnum::class
    ];

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
