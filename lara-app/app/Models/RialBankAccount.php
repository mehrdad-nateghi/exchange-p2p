<?php

namespace App\Models;

use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class RialBankAccount extends Model
{
    use HasFactory,Ulid;

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
}
