<?php

namespace App\Models;

use App\Enums\TradeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Trade extends Model
{
    use HasFactory;

    protected $table = 'trades';

    protected $fillable = [
        'support_id',
        'request_id',
        'bid_id',
        'trade_fee',
        'status',
        'created_at'
    ];

    public $timestamps = true;

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
     * Set system fee to trade
     */
    public function setSystemFee(){

        $trade_volume = $this->request->trade_volume;

        $system_fee = 0;

        $financial = Financial::first();

        if (!$financial) {
            return false;
        }

        if ($trade_volume > 0 && $trade_volume <= 99) {
            $system_fee = $financial->system_fee_a;
        }
        elseif ($trade_volume >= 100 && $trade_volume <= 999) {
            $system_fee = $financial->system_fee_b;
        }
        elseif ($trade_volume >= 1000 && $trade_volume <= 2999) {
            $system_fee = $financial->system_fee_c;
        }
        elseif ($trade_volume >= 3000) {
            $system_fee = $financial->system_fee_d;
        }
        else{
            return false;
        }

        $this->update([
            'trade_fee' => $system_fee
        ]);

        return true;
    }

    /*
    * Enum casting for the status field
    */
    protected $casts = [
        'status' => TradeStatusEnum::class
    ];
}

