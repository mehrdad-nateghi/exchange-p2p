<?php

namespace App\Listeners;

use App\Enums\NotificationRecipientTypeEnum;
use App\Events\PayTomanToSystemEvent;
use App\Models\Invoice;
use App\Models\Trade;
use App\Models\User;
use App\Notifications\PayTomanToSystemNotification;
use Illuminate\Support\Facades\Notification;

class PayTomanToSystemNotificationsListener
{
    private Trade $trade;
    private User $buyerUser;
    private User $sellerUser;
    private Invoice $invoice;

    public function handle(PayTomanToSystemEvent $event): void
    {

        $this->trade = $event->trade;
        $this->invoice = $event->invoice;

        $this->buyerUser = $this->trade->BuyerUser;
        $this->sellerUser = $this->trade->SellerUser;

        $this->sendNotificationToBuyer();
        $this->sendNotificationToSeller();
    }

    private function sendNotificationToBuyer(): void
    {
        Notification::send(
            $this->buyerUser,
            new PayTomanToSystemNotification($this->trade, $this->invoice, NotificationRecipientTypeEnum::BUYER->value)
        );
    }

    private function sendNotificationToSeller(): void
    {
        Notification::send(
            $this->sellerUser,
            new PayTomanToSystemNotification($this->trade, $this->invoice, NotificationRecipientTypeEnum::SELLER->value)
        );
    }
}
