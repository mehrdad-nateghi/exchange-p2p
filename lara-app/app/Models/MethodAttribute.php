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
    * Get the PaymentMethod that owns the Attribute
    */
    public function paymentMethod(){
        return $this->belongsTo(PaymentMethod::class);
    }

    /*
    * Get the linkedMethods for the Attribute.
    */
    public function linkedMethods(){
        return $this->belongsToMany(LinkedMethod::class, 'LinkedMethod_MethodAttribute', 'method_attribute_id', 'linked_method_id');
    }

}
