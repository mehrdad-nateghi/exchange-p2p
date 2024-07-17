<?php

namespace App\Models;

use App\Enums\PaymentMethodTypeEnum;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RialBankAccount extends Model
{
    use HasFactory,Ulid, SoftDeletes;

    protected $fillable = [
        'holder_name',
        'bank_name',
        'card_number',
        'sheba',
        'account_no',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function paymentMethod(): MorphOne
    {
        return $this->morphOne(PaymentMethod::class,'payment_method');
    }

    public function getIconAttribute(): string
    {
        return config('app.url') . '/images/' . PaymentMethodTypeEnum::RIAL_BANK->getKeyLowercase();
    }
}
