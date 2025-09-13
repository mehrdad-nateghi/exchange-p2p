<?php

namespace App\Listeners;

use App\Enums\FileStatusEnum;
use App\Enums\NotificationRecipientTypeEnum;
use App\Events\UpdateReceiptByBuyerEvent;
use App\Events\UploadReceiptEvent;
use App\Models\Trade;
use App\Models\User;
use App\Notifications\UpdateReceiptByBuyerNotification;
use App\Notifications\UploadReceiptNotification;
use Illuminate\Support\Facades\Notification;

class UpdateReceiptByBuyerNotificationsListener
{
    private Trade $trade;
    private User $sellerUser;
    private int $fileStatus;

    public function handle(UpdateReceiptByBuyerEvent $event): void
    {
        $this->trade = $event->trade;
        $this->sellerUser = $this->trade->SellerUser;

        $this->fileStatus = $event->fileStatus;

        $this->sendNotificationToSeller();
    }

    private function sendNotificationToSeller(): void
    {
        Notification::send(
            $this->sellerUser,
            new UpdateReceiptByBuyerNotification($this->trade, $this->fileStatus)
        );
    }
}
