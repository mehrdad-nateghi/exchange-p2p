<?php

namespace App\Models;

use App\Enums\FileStatusEnum;
use App\Traits\Global\Paginatable;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory,Ulid, Paginatable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'path',
        'mime_type',
        'size',
        'status'
    ];

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }

    protected $casts = [
        'status' => FileStatusEnum::class
    ];

    public function getUrlAttribute(): string
    {
        return Storage::temporaryUrl($this->path, now()->addMinutes(5));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
