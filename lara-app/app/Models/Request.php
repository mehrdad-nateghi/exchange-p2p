<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'type',
        'applicant_id',
        'trade_volume',
        'lower_bound_feasibility_treshold',
        'upper_bound_feasibility_threshold',
        'acceptance_threshold',
        'created_at'
    ];

    public $timestamps = false;

}
