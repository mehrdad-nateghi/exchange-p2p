<?php

namespace App\Listeners;

use App\Enums\NotificationRecipientTypeEnum;
use App\Events\UploadReceiptEvent;
use App\Models\Trade;
use App\Models\User;
use App\Notifications\UploadReceiptNotification;
use Illuminate\Support\Facades\Notification;

class UploadReceiptNotificationsListener
{
    private Trade $trade;
    private User $buyerUser;

    public function handle(UploadReceiptEvent $event): void
    {
        $this->trade = $event->trade;

        $this->buyerUser = $this->trade->BuyerUser;

        $this->sendNotificationToBuyer();
    }

    private function sendNotificationToBuyer(): void
    {
        Notification::send(
            $this->buyerUser,
            new UploadReceiptNotification($this->trade, NotificationRecipientTypeEnum::BUYER->value)
        );
    }
}
