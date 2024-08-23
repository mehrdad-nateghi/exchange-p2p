<?php

namespace App\Models;

use App\Enums\FileStatusEnum;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory,Ulid, Paginatable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'path',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'status' => FileStatusEnum::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
