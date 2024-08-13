<?php

namespace App\Models;

use App\Enums\TradeStepOwnerEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeStep extends Model
{
    use HasFactory,Ulid, Paginatable, SoftDeletes;

    protected $fillable = ['name', 'description', 'priority', 'owner', 'status', 'duration_minutes', 'expire_at'];

    protected $casts = [
        'status' => TradeStepsStatusEnum::class,
        'owner' => TradeStepOwnerEnum::class,
    ];

    /*public function getActionsAttribute()
    {
        if($this->name === 'Pay Toman to System'){
            return [
                'deposit_reason' => !empty($this->trade->request->deposit_reason) ? 'done' : 'todo',
            ];
        }
        return null;
    }*/

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }
}
