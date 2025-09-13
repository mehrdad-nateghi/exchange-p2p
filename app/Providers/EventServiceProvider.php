<?php

namespace App\Providers;

use App\Events\BidStoredEvent;
use App\Events\BidAcceptedEvent;
use App\Events\PayTomanToSystemEvent;
use App\Events\SignUpEvent;
use App\Events\TransferToSellerEvent;
use App\Events\UpdateReceiptByBuyerEvent;
use App\Events\UploadReceiptEvent;
use App\Listeners\AssignDefaultPaymentMethodToUserListener;
use App\Listeners\BidAcceptedByRequesterNotificationsListener;
use App\Listeners\BidRegisterNotificationsListener;
use App\Listeners\PayTomanToSystemNotificationsListener;
use App\Listeners\TransferToSellerNotificationsListener;
use App\Listeners\UpdateReceiptByBuyerNotificationsListener;
use App\Listeners\UploadReceiptNotificationsListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        SignUpEvent::class => [
            AssignDefaultPaymentMethodToUserListener::class,
        ],

        BidStoredEvent::class => [
            BidRegisterNotificationsListener::class,
        ],

        BidAcceptedEvent::class => [
            BidAcceptedByRequesterNotificationsListener::class,
        ],

        PayTomanToSystemEvent::class => [
            PayTomanToSystemNotificationsListener::class,
        ],

        UploadReceiptEvent::class => [
            UploadReceiptNotificationsListener::class,
        ],

        UpdateReceiptByBuyerEvent::class => [
            UpdateReceiptByBuyerNotificationsListener::class,
        ],

        TransferToSellerEvent::class => [
            TransferToSellerNotificationsListener::class,
        ],






    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
