<?php

namespace App\Models;

use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
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
        'created_at'
    ];

    public $timestamps = false;

    /*
    * Get the PaymentMethods for the Request
    */
    public function paymentMethods(){
        return $this->belongsToMany(PaymentMethod::class, 'request_paymentmethod', 'request_id', 'payment_method_id');
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
    * Enum casting for the status and type fields
    */
    protected $casts = [
        'status' => RequestStatusEnum::class,
        'type' => RequestTypeEnum::class
    ];

}
