<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financial extends Model
{
    use HasFactory;

    protected $table = 'financials';

    protected $fillable = [
        'system_fee_a',
        'system_fee_b',
        'system_fee_c',
        'system_fee_d',
        'total_system_income',
        'feasibility_band_percentage',
        'updated_at'
    ];

    public $timestamps = false;

}
