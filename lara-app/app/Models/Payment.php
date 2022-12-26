<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'applicant_id',
        'trade_id',
        'trade_net_value',
        'target_account_id',
        'created_at'
    ];
}
