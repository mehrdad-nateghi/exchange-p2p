<?php

namespace App\Models;

use App\Enums\BidStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStepOwnerEnum;
use App\Traits\Global\Number;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Request extends Model
{
    use HasFactory, Ulid, Number, Paginatable, SoftDeletes;

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
        'canceled_at',
        /* 'deposit_reason',
         'deposit_reason_accepted'*/
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

    public function latestTradeWithTrashed(): HasOne
    {
        return $this->hasOne(Trade::class)->withTrashed()->latest();
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function getIsUserSellerAttribute(): bool
    {
        return $this->isUserRole(RequestTypeEnum::SELL);
    }

    public function getIsUserBuyerAttribute(): bool
    {
        return $this->isUserRole(RequestTypeEnum::BUY);
    }

    private function isUserRole(RequestTypeEnum $requestType): bool
    {
        $isAdmin = Auth::user()->hasRole('admin');
        $userId = $isAdmin ? $this->user->id : Auth::id();

        if ($this->type->value !== $requestType->value) {
            $bid = $this->bids()->where('status', BidStatusEnum::ACCEPTED)->first();
            return $bid && $bid->user_id == $userId;
        }

        return $this->user_id == $userId;
    }

    public function getUserRoleOnRequestAttribute(): ?string
    {
        if ($this->is_user_seller) {
            return TradeStepOwnerEnum::SELLER->key();
        }
        if ($this->is_user_buyer) {
            return TradeStepOwnerEnum::BUYER->key();
        }
        return null;
    }

    /*public function getSellerUserAttribute()
    {
        return $this->is_user_seller ? $this->user : $this->bid->user;
    }

    public function getBuyerUserAttribute()
    {
        return $this->is_user_buyer ? $this->user : $this->bid->user;
    }*/
}
