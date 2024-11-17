<?php

namespace App\Models;

use App\Enums\TradeStatusEnum;
use App\Enums\TradeStepOwnerEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Traits\Global\Number;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trade extends Model
{
    use HasFactory,Ulid, Paginatable, Number, SoftDeletes;

    protected $fillable = ['request_id', 'bid_id', 'status', 'completed_at', 'canceled_at', 'deposit_reason','deposit_reason_accepted'];

    protected static $prefixNumber = 'TR-';

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    protected $casts = [
        'status' => TradeStatusEnum::class,
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function paymentMethods()
    {
        return $this->belongsToMany(PaymentMethod::class, 'payment_method_request', 'request_id', 'payment_method_id')
            ->withTimestamps()
            ->wherePivot('request_id', $this->request_id);
    }


    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
    }

    public function tradeSteps(): HasMany
    {
        return $this->hasMany(TradeStep::class);
    }

    public function invoices(): MorphMany
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

    public function setNumberAttribute($value)
    {
        $this->attributes['number'] = $value;
        $this->attributes['deposit_reason'] = $value;
    }

    public function getStatusByOwner($owner)
    {
        // Is Buyer
        if($owner === TradeStepOwnerEnum::BUYER->key()){
            $stepThreeHasDone = $this->tradeSteps()->where('priority', 3)->where('status', TradeStepsStatusEnum::DONE)->exists();
            if($stepThreeHasDone){
               return TradeStatusEnum::COMPLETED->key();
            }
        };

        return $this->status->key();
    }
}
