<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeConstraint extends Model
{
    use HasFactory;

    protected $table = 'trade_constraints';

    protected $fillable = [
        'payment_rial_time_constraint',
        'payment_currency_time_constraint',
        'confirmation_receipt_time_constraint',
        'system_payment_time_constraint',
        'updated_at'
    ];

    public $timestamps = false;

}

