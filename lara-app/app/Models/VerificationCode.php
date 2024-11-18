<?php

namespace App\Models;

use App\Traits\Global\Ulid;
use App\Traits\VerificationCode\Scopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

// ObservedBy VerificationCodeObserver::class
class VerificationCode extends Model
{
    use HasFactory, SoftDeletes, Ulid, Notifiable, Scopes;

    protected $fillable = [
        'code','to', 'via', 'type', 'expired_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];
}
