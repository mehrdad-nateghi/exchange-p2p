<?php

namespace App\Models;

use App\Enums\BidStatusEnum;
use App\Enums\BidTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $table = 'bids';

    protected $fillable = [
        'request_id',
        'applicant_id',
        'target_account_id',
        'bid_rate',
        'created_at'
    ];

    public $timestamps = false;


    /**
     * Get the User that owns the Bid
     */
    public function user(){
        return $this->belongsTo(User::class, 'applicant_id');
    }


    /**
     * Get the Request that owns the Bid
     */
    public function request(){
        return $this->belongsTo(Request::class, 'request_id');
    }

    /*
    * Get the Trade for the Bid
    */
    public function trade(){
        return $this->hasOne(Trade::class);
    }

    /*
    * Get the Emails related to the Bid
    */
    public function emails(){
        return $this->morphMany(Email::class, 'emailable');
    }

    /*
    * Get the Notifications related to the Bid
    */
    public function notifications(){
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /*
    * Get the LinkedMethod belongs to the Bid
    */
    public function linkedMethod(){
        return $this->belongsTo(LinkedMethod::class, 'target_account_id');
    }

    /*
    * Enum casting for the status and type fields
    */
    protected $casts = [
        'status' => BidStatusEnum::class,
        'type' => BidTypeEnum::class
    ];


}
