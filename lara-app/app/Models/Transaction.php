<?php

namespace App\Models;

use App\Enums\TransactionStatusEnum;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory,Ulid, Paginatable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'track_id',
        'ref_id',
        'amount',
        'currency',
        'status',
    ];

    protected $casts = [
      'status' => TransactionStatusEnum::class
    ];

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

}
