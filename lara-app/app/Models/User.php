<?php

namespace App\Models;

use App\Enums\Legacy\LinkedMethodStatusEnum;
use App\Enums\Legacy\UserRoleEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\TradeStatusEnum;
use App\Enums\UserStatusEnum;
use App\Models\Legacy\Invoice;
use App\Services\Notifications\NotificationMessage;
use App\Traits\Global\Ulid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

//use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Ulid, HasRoles;

    protected $table = 'users';

    public function getRouteKeyName(): string
    {
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

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getStatusAttribute($value): ?string
    {
        return strtolower(UserStatusEnum::tryFrom($value)?->name);
    }

    protected $hidden = [
        'password'
    ];

    public $timestamps = true;


    /*
    * Get the AuthenticationLogs for the User
    */
    public function authenticationLogs()
    {
        return $this->hasMany(AuthenticationLog::class, 'applicant_id');
    }

    /*
    * Get the LinkedMethods for the User
    */
    public function linkedMethods()
    {
        return $this->hasMany(LinkedMethod::class, 'applicant_id');
    }

    /*
    * Get the Requests for the User
    */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    /*
    * Get the Bids for the User
    */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
        //return $this->hasManyThrough(Bid::class, Request::class);
    }

    /*
    * Get the Invoices for the User
    */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'applicant_id');
    }


    /*
    * Get the Emails for the User (1 to n user-email general relation)
    */
    public function emails()
    {
        return $this->hasMany(Email::class, 'user_id');
    }

    /*
    * Get the Emails related the User (1 to n user-email polymorphic relation)
    */
    public function relatedEmails()
    {
        return $this->morphMany(Email::class, 'emailable');
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
            ->where('status', LinkedMethodStatusEnum::Active)
            ->get();

        return $linked_methods;
    }

    /**
     * Get applicant unlinked payment methods
     */
    public function getUnlinkedPaymentMethods()
    {
        $linked_methods = $this->linkedMethods()
            ->where('status', LinkedMethodStatusEnum::Removed)
            ->get();

        return $linked_methods;
    }

    /*
     * Get a specific linked method by linked_method_id if it exists and active
     */
    public function getLinkedMethodIfIsActive($linked_method_id)
    {
        $linked_method = $this->linkedMethods()
            ->where('id', $linked_method_id)
            ->where('status', LinkedMethodStatusEnum::Active)
            ->first();

        return $linked_method;
    }

    /*
     * Get a specific linked method by payment_method_id if it exists and active
     */
    public function getLinkedMethodByPaymentMethodIdIfIsActive($payment_method_id)
    {
        $linked_method = $this->linkedMethods()
            ->where('method_type_id', $payment_method_id)
            ->where('status', LinkedMethodStatusEnum::Active)
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

    public function tradesAsRequester(): HasManyThrough
    {
        return $this->hasManyThrough(Trade::class, Request::class);
    }

    public function tradesAsBidder(): HasMany
    {
        return $this->hasMany(Trade::class, 'bid_id')
            ->whereHas('bid', function ($query) {
                $query->where('user_id', $this->id);
            });
    }

    public function trades()
    {
        return Trade::where(function ($query) {
            $query->whereHas('request', function ($subQuery) {
                $subQuery->where('user_id', $this->id);
            })->orWhereHas('bid', function ($subQuery) {
                $subQuery->where('user_id', $this->id);
            });
        });
    }


    /*
    * Enum casting for the status and type fields
    */
    protected $casts = [
        'status' => UserStatusEnum::class,
        'role' => UserRoleEnum::class
    ];

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function getActiveRequestsAttribute()
    {
        return $this->requests()->whereIn('status', [
                RequestStatusEnum::PENDING->value,
                RequestStatusEnum::PROCESSING->value,
                RequestStatusEnum::TRADING->value,
            ]
        )->count();
    }

    public function getCompletedTradesAttribute()
    {
        return $this->trades()->whereIn('status', [
                TradeStatusEnum::COMPLETED->value,
            ]
        )->count();
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.User.' . $this->ulid;
    }

    /**
     * Get formatted notification message
     */
    public function getNotificationMessage($notification): string
    {
        $notificationMessage = App::make(NotificationMessage::class);
        return $notificationMessage->retrieve(
            $notification->data['key'],
            $notification->data['attributes']
        )['message'][app()->getLocale()] ?? '';
    }

    /**
     * Get all notifications with formatted messages
     */
    public function getFormattedNotifications()
    {
        return $this->notifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'message' => $this->getNotificationMessage($notification),
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
                'model' => [
                    'ulid' => $notification->data['model']['ulid'] ?? Str::ulid(),
                    'name' => $notification->data['model']['name'] ??  Arr::random(['trade','request','bid'])
                ]
                //'data' => $notification->data
            ];
        });
    }

    /**
     * Get unread notifications with formatted messages
     */
    public function getUnreadFormattedNotifications()
    {
        return $this->unreadNotifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'message' => $this->getNotificationMessage($notification),
                'created_at' => $notification->created_at,
                //'data' => $notification->data
            ];
        });
    }

}
