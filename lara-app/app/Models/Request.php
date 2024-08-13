<?php

namespace App\Models;

use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Traits\Global\Number;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use HasFactory, Ulid, Number,Paginatable, SoftDeletes;

    protected $casts = [
        'status' => RequestStatusEnum::class,
        'type' => RequestTypeEnum::class
    ];

    protected static $prefixNumber = 'RE-';

    protected $fillable = [
        'volume',
        'type',
        'status',
        'price',
        // 'description', todo-mn: need to add it?
        'min_allowed_price',
        'max_allowed_price',
        'deposit_reason'
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class, 'payment_method_request')->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }
}
