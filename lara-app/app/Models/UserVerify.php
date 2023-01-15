<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerify extends Model
{
    use HasFactory;

    protected $table = 'user_verifies';

    protected $fillable = [
        'user_id'
    ];

    protected $hidden = [
        'token'
    ];

    public $timestamps = false;

    /*
    * Get the User that owns the UserVerify
    */
    public function user(){
        return $this->belongsTo(User::class);
    }


}
