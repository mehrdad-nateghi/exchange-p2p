<?php

namespace App\Models;

use App\Observers\VerificationCodeObserver;
use App\Traits\UUID;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

// ObservedBy VerificationCodeObserver::class
class VerificationCode extends Model
{
    use HasFactory, SoftDeletes, UUID, Notifiable;

    protected $fillable = [
        'code','to', 'via', 'type', 'expired_at',
    ];

    protected $casts = [
        'expire_at' => 'datetime',
    ];
}
