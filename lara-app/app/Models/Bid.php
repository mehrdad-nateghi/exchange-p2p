<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $table = 'bids';

    protected $fillable = [
        'request_id',
        'applicant_id',
        'bid_rate',
        'created_at'
    ];

    public $timestamps = false;


    /**
     * Get the User that owns the Bid.
     */
    public function user(){
        return $this->belongsTo(User::class, 'applicant_id');
    }

}
