<?php

namespace App\Models;

use App\Enums\NotificationClassEnum;
use App\Enums\NotificationStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'status',
        'created_at'
    ];

    public $timestamps = false;

    /*
    * Get user owns the notification
    */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /*
    * Set up polymorphic relationship
    */
    public function notifiable(){
        return $this->morphTo();
    }

    /*
    * Enum casting for the status field
    */
    protected $casts = [
        'status' => NotificationStatusEnum::class,
        'class' => NotificationClassEnum::class
    ];

}
