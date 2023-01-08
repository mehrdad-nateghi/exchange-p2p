<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $fillable = [
        'name',
        'country_id'
    ];

    public $timestamps = false;

    /**
     * Get the country that owns the payment-metod.
     */
    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }

    /*
    * Get the attributes for the payment-method.
    */
    public function attributes(){
        return $this->hasMany(MethodAttribute::class);
    }
}
