<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MethodAttribute extends Model
{
    use HasFactory;

    protected $table = 'method_attributes';

    protected $fillable = [
        'payment_method_id',
        'name'
    ];

    public $timestamps = false;

    /*
    * Get the payment-method that owns the attribute
    */
    public function paymentMethod(){
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

}
