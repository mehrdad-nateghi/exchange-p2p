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
     * Get the country that owns the paymentMetod.
     */
    public function country(){
        return $this->belongsTo(Country::class);
    }

    /*
    * Get the attributes for the paymentMethod.
    */
    public function attributes(){
        return $this->hasMany(MethodAttribute::class);
    }

    /*
    * Get the linkedMethods for the paymentMethod.
    */
    public function linkedMethods(){
        return $this->hasMany(LinkedMethod::class, 'method_type_id');
    }

}
