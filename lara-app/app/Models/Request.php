<?php

namespace App\Models;

use App\Enums\old\BidStatusEnum;
use App\Enums\old\RequestStatusEnum;
use App\Enums\old\RequestTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'support_id',
        'type',
        'status',
        'description',
        'trade_volume',
        'lower_bound_feasibility_threshold',
        'upper_bound_feasibility_threshold',
        'acceptance_threshold',
        'request_rate',
        'payment_reason',
        'applicant_id',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    /*
    * Get the PaymentMethods for the Request
    */
    public function linkedMethods(){
        return $this->belongsToMany(LinkedMethod::class, 'request_linkedmethod', 'request_id', 'linked_method_id');
    }

    /*
    * Get the User owns the Request
    */
    public function user(){
        return $this->belongsTo(User::class, 'applicant_id');
    }

    /*
    * Get the Bids for the Request
    */
    public function bids(){
        return $this->hasMany(Bid::class, 'request_id');
    }

    /*
    * Get the Trades for the Request
    */
    public function trades(){
        return $this->hasMany(Trade::class, 'request_id');
    }

    /*
    * Get the Emails related to the Request
    */
   public function emails(){
       return $this->morphMany(Email::class, 'emailable');
   }

    /*
    * Get the Notifications related to the Request
    */
    public function notifications(){
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /*
     * Set a bid as the top bid of the request
     */
    public function setTopBid($bid_id){
        $current_top_bid = $this->bids()->where("status", BidStatusEnum::Top)->first();
        $processing_bid = $this->bids()->where("id", $bid_id)->first();

        if($processing_bid) {
            $processing_bid->status = BidStatusEnum::Top;

            if($current_top_bid) {
                $current_top_bid->status = BidStatusEnum::Registered;
                $current_top_bid->save();
            }

            $processing_bid->save();

            return true;
        }

        return false;
    }

    /*
     * Get the top bid of the request
     */
    public function getTopBid(){
        return $this->bids()->where('status', BidStatusEnum::Top)->first();
    }

    /*
     * Get the request payment methods
     */
    public function getRequestPaymentMethods() {
        return $this->linkedMethods()->with('paymentMethod')->get()->pluck('paymentMethod')->unique();
    }

    /*
    * Enum casting for the status and type fields
    */
    protected $casts = [
        'status' => RequestStatusEnum::class,
        'type' => RequestTypeEnum::class
    ];

}
