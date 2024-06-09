<?php

namespace App\Models;

use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, Ulid, SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    protected $fillable = [
        'user_id',
        'type',
        'status',
    ];

    protected $with = ['paymentMethod'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): MorphTo
    {
        return $this->morphTo();
    }
}
