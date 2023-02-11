<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'created_at'
    ];

    public $timestamps = false;

    /*
    * Get the Notifications for the User.
    */
    public function notifications(){
        return $this->hasMany(Notification::class);
    }

    /*
    * Get the UserVerify for the User.
    */
    public function userVerify(){
        return $this->hasOne(UserVerify::class);
    }

    /*
    * Get the AuthenticationLogs for the User.
    */
    public function authenticationLogs(){
        return $this->hasMany(AuthenticationLog::class, 'applicant_id');
    }

    /*
    * Get the LinkedMethods for the User.
    */
    public function linkedMethods(){
        return $this->hasMany(LinkedMethod::class, 'applicant_id');
    }

    /*
    * Get the Requests for the User.
    */
    public function requests(){
        return $this->hasMany(Request::class, 'applicant_id');
    }

    /*
    * Get the Bids for the User.
    */
    public function bids(){
        return $this->hasMany(Bid::class, 'applicant_id');
    }
}
