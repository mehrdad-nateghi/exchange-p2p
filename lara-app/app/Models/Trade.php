<?php

namespace App\Models;

use App\Enums\TradeStatusEnum;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trade extends Model
{
    use HasFactory,Ulid, Paginatable, SoftDeletes;

    protected $fillable = ['request_id', 'bid_id', 'status', 'complete_at', 'canceled_at'];

    protected $casts = [
        'status' => TradeStatusEnum::class,
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
    }

    public function tradeSteps(): HasMany
    {
        return $this->hasMany(TradeStep::class);
    }
}
