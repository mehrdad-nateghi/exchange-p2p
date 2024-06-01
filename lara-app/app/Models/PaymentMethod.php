<?php

namespace App\Models;

use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory, Ulid;

    protected $fillable = [
        'type',
        'status',
    ];
}
