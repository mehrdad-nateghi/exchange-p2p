<?php

namespace App\Models;

use App\Enums\Legacy\LinkedMethodStatusEnum;
use App\Enums\Legacy\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{

    use HasApiTokens,HasFactory,Notifiable,SoftDeletes, Ulid, HasRoles;

    protected $table = 'users';

    public function getRouteKeyName() {
        return 'ulid';
    }

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'status',
        'email_verified_at',
        'created_at'
    ];

    public function getStatusAttribute($value): ?string
    {
        return strtolower(UserStatusEnum::tryFrom($value)?->name);
    }

    protected $hidden = [
        'password'
    ];

    public $timestamps = true;

    /*
    * Get the Notifications for the User
    */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /*
    * Get the AuthenticationLogs for the User
    */
    public function authenticationLogs()
    {
        return $this->hasMany(AuthenticationLog::class,'applicant_id');
    }

    /*
    * Get the LinkedMethods for the User
    */
    public function linkedMethods()
    {
        return $this->hasMany(LinkedMethod::class,'applicant_id');
    }

    /*
    * Get the Requests for the User
    */
    public function requests()
    {
        return $this->hasMany(Request::class,'applicant_id');
    }

    /*
    * Get the Bids for the User
    */
    public function bids()
    {
        return $this->hasMany(Bid::class,'applicant_id');
    }

    /*
    * Get the Invoices for the User
    */
    public function invoices()
    {
        return $this->hasMany(Invoice::class,'applicant_id');
    }


    /*
    * Get the Emails for the User (1 to n user-email general relation)
    */
    public function emails()
    {
        return $this->hasMany(Email::class,'user_id');
    }

    /*
    * Get the Emails related the User (1 to n user-email polymorphic relation)
    */
    public function relatedEmails()
    {
        return $this->morphMany(Email::class,'emailable');
    }

    /*
    * Get the Notifications related the User (1 to n user-notification polymorphic relation)
    */
    public function relatedNotifications()
    {
        return $this->morphMany(Notification::class,'notifiable');
    }

    /**
     * Check whther the user is an applicant
     */
    public function checkIsActiveApplicant()
    {
        return $this->status == UserStatusEnum::Active && $this->role == UserRoleEnum::Applicant;
    }

    /**
     * Get applicant linked payment methods
     */
    public function getLinkedPaymentMethods()
    {
        $linked_methods = $this->linkedMethods()
            ->where('status',LinkedMethodStatusEnum::Active)
            ->get();

        return $linked_methods;
    }

    /**
     * Get applicant unlinked payment methods
     */
    public function getUnlinkedPaymentMethods()
    {
        $linked_methods = $this->linkedMethods()
            ->where('status',LinkedMethodStatusEnum::Removed)
            ->get();

        return $linked_methods;
    }

    /*
     * Get a specific linked method by linked_method_id if it exists and active
     */
    public function getLinkedMethodIfIsActive($linked_method_id)
    {
        $linked_method = $this->linkedMethods()
            ->where('id',$linked_method_id)
            ->where('status',LinkedMethodStatusEnum::Active)
            ->first();

        return $linked_method;
    }

    /*
     * Get a specific linked method by payment_method_id if it exists and active
     */
    public function getLinkedMethodByPaymentMethodIdIfIsActive($payment_method_id)
    {
        $linked_method = $this->linkedMethods()
            ->where('method_type_id',$payment_method_id)
            ->where('status',LinkedMethodStatusEnum::Active)
            ->first();

        return $linked_method;
    }

    /*
     * Update the password of user
     */
    public function updatePassword($password)
    {

        $hashed_password = Hash::make($password);

        $this->password = $hashed_password;

        $this->save();

        return true;
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    /*
    * Enum casting for the status and type fields
    */
    protected $casts = [
        'status' => UserStatusEnum::class,
        'role' => UserRoleEnum::class
    ];

}
