<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request_PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'request_paymentmethod';

    protected $fillable = [
        'payment_method_id',
        'request_id'
    ];
}
