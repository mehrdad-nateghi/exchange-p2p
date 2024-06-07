<?php

namespace App\Models;

use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentMethod extends Model
{
    use HasFactory, Ulid;

    protected $fillable = [
        'user_id',
        'type',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): MorphTo
    {
        return $this->morphTo();
    }
}
