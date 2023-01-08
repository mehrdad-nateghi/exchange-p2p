<?php

namespace App\Models;

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

}

