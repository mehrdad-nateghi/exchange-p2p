<?php

namespace App\Models;

use App\Enums\BidStatusEnum;
use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\RequestTypeEnum;
use App\Traits\Global\Number;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;

class Invoice extends Model
{
    use HasFactory, Ulid, Paginatable, Number, SoftDeletes;

    protected $fillable = ['user_id', 'amount', 'fee', 'status', 'type', 'fee_foreign' ,'fee_foreign_currency_code'];

    protected static $prefixNumber = 'IN-';

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    protected $appends = ['total_payable_amount'];

    protected function totalPayableAmount(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->amount + $this->fee;
            },
        );
    }

    protected $casts = [
        'status' => InvoiceStatusEnum::class,
        'type' => InvoiceTypeEnum::class,
    ];

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function scopeFilterByOwner($query)
    {
        return $query->where('user_id', Auth::id());
    }

    /*public function setFeeAttribute($value)
    {
        if ($value === null) {
            $this->attributes['fee'] = $this->calculateFee();
        } else {
            $this->attributes['fee'] = $value;
        }
    }

    protected function calculateFee()
    {
        return round($this->amount * 0.10, 2); // 10% fee
    }*/

    /*public function getNetAmountAttribute()
    {
        return $this->amount - $this->fee;
    }*/
}
