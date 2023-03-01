<?php

namespace App\Models;

use App\Enums\TradeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $table = 'trades';

    protected $fillable = [
        'request_id',
        'bid_id',
        'trade_fee',
        'created_at'
    ];

    public $timestamps = false;

    /*
    * Get the Request owns the Trade
    */
    public function request(){
        return $this->belongsTo(Request::class, 'request_id');
    }

    /*
    * Get the Bid for the Trade
    */
    public function bid(){
        return $this->belongsTo(Bid::class);
    }

    /*
    * Get the Invoices for the Trade
    */
    public function invoices(){
        return $this->hasMany(Invoice::class, 'trade_id');
    }

    /*
    * Get the Emails related to the Trade
    */
    public function emails(){
        return $this->morphMany(Email::class, 'emailable');
    }

    /*
    * Get the Notifications related to the Trade
    */
    public function notifications(){
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /*
    * Enum casting for the status field
    */
    protected $casts = [
        'status' => TradeStatusEnum::class
    ];
}

