<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
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
    * Get the Notifications for the User
    */
    public function notifications(){
        return $this->hasMany(Notification::class);
    }

    /*
    * Get the UserVerify for the User
    */
    public function userVerify(){
        return $this->hasOne(UserVerify::class);
    }

    /*
    * Get the AuthenticationLogs for the User
    */
    public function authenticationLogs(){
        return $this->hasMany(AuthenticationLog::class, 'applicant_id');
    }

    /*
    * Get the LinkedMethods for the User
    */
    public function linkedMethods(){
        return $this->hasMany(LinkedMethod::class, 'applicant_id');
    }

    /*
    * Get the Requests for the User
    */
    public function requests(){
        return $this->hasMany(Request::class, 'applicant_id');
    }

    /*
    * Get the Bids for the User
    */
    public function bids(){
        return $this->hasMany(Bid::class, 'applicant_id');
    }

    /*
    * Get the Invoices for the User
    */
    public function invoices(){
        return $this->hasMany(Invoice::class, 'applicant_id');
    }


    /*
    * Get the Emails for the User (1 to n user-email general relation)
    */
    public function emails(){
        return $this->hasMany(Email::class, 'user_id');
    }

    /*
    * Get the Emails related the User (1 to n user-email polymorphic relation)
    */
     public function relatedEmails(){
         return $this->morphMany(Email::class, 'emailable');
     }

    /*
    * Enum casting for the status and type fields
    */
    protected $casts = [
        'status' => UserStatusEnum::class,
        'type' => UserTypeEnum::class
    ];

}
