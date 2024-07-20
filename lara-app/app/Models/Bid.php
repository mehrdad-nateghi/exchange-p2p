<?php

namespace App\Models;

use App\Enums\BidStatusEnum;
use App\Traits\Global\Number;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use HasFactory, Ulid, SoftDeletes, Paginatable, Number;

    protected static $prefixNumber = 'BI-';

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

        return $this->price == $highestBid;
    }
}
