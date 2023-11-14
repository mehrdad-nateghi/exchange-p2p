<?php

namespace App\Models;

use App\Enums\BidStatusEnum;
use App\Enums\LinkedMethodStatusEnum;
use App\Enums\RequestStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
     * Update the linked method's attributes
     */
    public function updateAttributes($attributes){

        foreach ($attributes as $input_attr_name => $input_attr_value) {
            $linked_method_attr = $this->attributes()->where('name', $input_attr_name)->first();

            if ($linked_method_attr instanceof MethodAttribute) {
                $this->attributes()->updateExistingPivot($linked_method_attr->id, ['value' => $input_attr_value]);
            } else {
                $payment_method_attr = $this->paymentMethod->attributes()->where('name', $input_attr_name)->first();

                if ($payment_method_attr) {
                    $this->attributes()->attach([$payment_method_attr->id => ['value' => $input_attr_value]]);
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /*
     * Check whether the linked method is engaged with an active request
     */
    public function isEngagedWithAnyActiveRequest(){

        $linked_method_owner = $this->user;

        $requests_linked_methods_id = $linked_method_owner->requests()
        ->where('status', '!=', RequestStatusEnum::Removed)
        ->with('linkedMethods:id')
        ->get()
        ->pluck('linkedMethods.*.id')
        ->flatten()
        ->all();

        if (in_array($this->id, $requests_linked_methods_id)) {
            return true;
        }

        return false;
    }

    /*
     * Check whether the linked method is engaged with an active request
     */
    public function isEngagedWithAnyActiveBid(){

        $associatedBids = $this->bids()
                ->whereNotIn('status', [BidStatusEnum::Rejected, BidStatusEnum::Invalid])
                ->get();

        if (!$associatedBids->isEmpty()) {
            return true;
        }

        return false;
    }


    /*
    * Enum casting for the status and type fields
    */
    protected $casts = [
        'status' => LinkedMethodStatusEnum::class
    ];
}
