<?php

namespace App\Models;

use App\Enums\BidStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Traits\Global\Number;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use HasFactory, Ulid, SoftDeletes, Paginatable, Number;

    protected static $prefixNumber = 'BI-';

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    protected $fillable = [
        'user_id', 'request_id', 'payment_method_id', 'price', 'status','rejected_at'
    ];

    protected $casts = [
        'status' => BidStatusEnum::class,
        'rejected_at' => 'datetime'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function trade(): HasOne
    {
        return $this->hasOne(Trade::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*public function getIconAttribute()
    {
        if ($this->status === BidStatusEnum::ACCEPTED->value) {
            return 'accepted';
        }

        if ($this->status === BidStatusEnum::REJECTED->value) {
            return 'rejected';
        }

        $highestBid = Bid::query()
            ->where('request_id', $this->request_id)
            ->where('status', BidStatusEnum::REGISTERED->value)
            ->max('price');

        if ($this->price == $highestBid) {
            return 'highest_price';
        }else{
            return 'not_highest_price';
        }
    }*/

    public function getIsHighestPriceAttribute()
    {
        $highestBid = Bid::query()
            ->where('request_id', $this->request_id)
            //->where('status', BidStatusEnum::REGISTERED->value)
            ->max('price');

        return $this->price == $highestBid;
    }

    public function getIsBestPriceAttribute()
    {
        if($this->request->type->value === RequestTypeEnum::BUY->value){
            $bestPrice = Bid::query()
                ->where('request_id', $this->request_id)
                ->min('price');
        }

        if($this->request->type->value === RequestTypeEnum::SELL->value){
            $bestPrice = Bid::query()
                ->where('request_id', $this->request_id)
                ->max('price');
        }

        return $this->price == $bestPrice;
    }

    // deprecated
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function getIsFirstBidAttribute()
    {
        $firstBid = static::query()
            ->where('request_id', $this->request_id)
            ->orderBy('created_at')
            ->first();

        return $firstBid && $firstBid->id === $this->id;
    }

    public function getIsNotFirstBidAttribute()
    {
        return !$this->is_first_bid;
    }

    public function getOtherBiddersAttribute()
    {
        return static::query()
            ->where('request_id', $this->request_id)
            ->where('user_id', '!=', $this->user_id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique();
    }
}
