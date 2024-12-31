<?php

namespace App\Models;

use App\Enums\TradeStepOwnerEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeStep extends Model
{
    use HasFactory, Ulid, Paginatable, SoftDeletes;

    protected $fillable = ['name', 'description', 'priority', 'owner', 'status', 'duration_minutes', 'expire_at', 'completed_at'];

    protected $casts = [
        'status' => TradeStepsStatusEnum::class,
        'owner' => TradeStepOwnerEnum::class,
        'expire_at' => 'datetime'
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    public function scopeByOwner($query, $owner)
    {
        // Is seller
        /*if($owner === TradeStepOwnerEnum::SELLER->key()){
            return $query->whereIn('owner', [TradeStepOwnerEnum::SELLER->value, TradeStepOwnerEnum::SYSTEM->value]);
        };*/

        // Is Buyer
        if ($owner === TradeStepOwnerEnum::BUYER->key()) {
            return $query->whereNotIn('priority', [4]);
        };


        return $query;
    }

    /*public function getStatusAttribute()
    {
        dd($this->status);
    }*/

    /*public function getActionsAttribute()
    {
        if($this->name === 'Pay Toman to System'){
            return [
                'deposit_reason' => !empty($this->trade->request->deposit_reason) ? 'done' : 'todo',
            ];
        }
        return null;
    }*/

    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }

    // error:     "message": "App\\Models\\TradeStep::request must return a relationship instance.",
    public function request(): HasOneThrough
    {
        return $this->hasOneThrough(
            Request::class,
            Trade::class,
            'id', // Foreign key on Trade table
            'id', // Foreign key on Request table
            'trade_id', // Local key on TradeStep table
            'request_id' // Local key on Trade table
        );
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function isExpired(): bool
    {
        return $this->expire_at !== null && $this->expire_at->isPast();
    }

}
