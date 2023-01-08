<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';

    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    /**
     * Get the payment-methods for the country.
     */
    public function paymentMethods(){
        return $this->hasMany(PaymentMethod::class);
    }
}
