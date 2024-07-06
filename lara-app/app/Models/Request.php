<?php

namespace App\Models;

use App\Enums\Legacy\BidStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Traits\Global\Number;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Request extends Model
{
    use HasFactory, Ulid, Number;

    protected $table = 'requests';
    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    protected $fillable = [
        'volume',
        'type',
        'status',
        'price',
        // 'description', todo-mn: need to add it?
        'min_allowed_price',
        'max_allowed_price',
    ];

    public $timestamps = true;

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class, 'payment_method_request')->withTimestamps();
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
