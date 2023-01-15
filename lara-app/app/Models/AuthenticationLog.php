<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthenticationLog extends Model
{
    use HasFactory;

    protected $table = 'authentication_logs';

    protected $fillable = [
        'applicant_id',
        'created_at'
    ];

    public $timestamps = false;

    /**
    * Get the User that owns the AuthenticationLog.
    */
    public function user(){
        return $this->belongsTo(User::class,'applicant_id');
    }

}
