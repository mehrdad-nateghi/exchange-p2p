<?php

namespace App\Models;

use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepositReason extends Model
{
    use HasFactory,Ulid, Paginatable, SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    protected $fillable = ['title'];

}
