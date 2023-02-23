<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    protected $table = 'emails';

    protected $fillable = [
        'template_id',
        'values',
        'reference_type',
        'reference_id',
        'created_at'
    ];

    public $timestamps = false;

    /*
    * Get the EmailTemplate owns the Email
    */
    public function emailTemplate(){
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    /*
    * Get the User for the Email
    */
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    * Set up polymorphic relationship
    */
    public function emailable(){
        return $this->morphTo();
    }

}
