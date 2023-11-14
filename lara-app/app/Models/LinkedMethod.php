<?php

namespace App\Models;

use App\Enums\LinkedMethodStatusEnum;
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

    public $timestamps = true;

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
        return $this->belongsToMany(MethodAttribute::class, 'linkedmethod_methodattribute', 'linked_method_id', 'method_attribute_id')
            ->withPivot('value');
    }

    /**
    * Get the User that owns the linkedMethod.
    */
    public function user(){
        return $this->belongsTo(User::class,'applicant_id');
    }

    /*
    * Get the Bids belongs to the LinkedMethod
    */
    public function bids(){
        return $this->hasMany(Bid::class, 'target_account_id');
    }

    /*
    * Get the Requests for the LinkedMethod.
    */
    public function requests(){
        return $this->belongsToMany(Request::class, 'request_linkedmethod', 'linked_method_id', 'request_id');
    }

    /*
     * Initialize the linked method's attributes
     */
    public function initializeAttributes($attibutes){

        foreach($attibutes as $input_attr_name => $input_attr_value) {
            $payment_method_attr = $this->attributes()->where('name',$input_attr_name)->first();
            $this->attributes()->attach($payment_method_attr, ['value' => $input_attr_value]);
        }

        return true;
    }

    /*
    * Enum casting for the status and type fields
    */
    protected $casts = [
        'status' => LinkedMethodStatusEnum::class
    ];
}
