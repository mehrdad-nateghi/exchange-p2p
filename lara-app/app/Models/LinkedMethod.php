<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkedMethod extends Model
{
    use HasFactory;

    protected $table = 'linked_methods';

    protected $fillable = [
        'method_type_id',
        'applicant_id',
        'created_at'
    ];

    public $timestamps = false;

    /**
    * Get the PaymentMetod that owns the LinkedMethod.
    */
    public function paymentMethod(){
        return $this->belongsTo(PaymentMethod::class,'method_type_id');
    }

    /*
    * Get the MethodAttributes for the LinkedMethod.
    */
    public function attributes(){
        return $this->belongsToMany(MethodAttribute::class, 'LinkedMethod_MethodAttribute', 'linked_method_id', 'method_attribute_id')
            ->withPivot('value');
    }

    /**
    * Get the User that owns the linkedMethod.
    */
    public function user(){
        return $this->belongsTo(User::class,'applicant_id');
    }
}
