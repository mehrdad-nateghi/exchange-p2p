<?php

namespace App\Listeners;

use App\Enums\FileStatusEnum;
use App\Enums\NotificationRecipientTypeEnum;
use App\Events\TransferToSellerEvent;
use App\Events\UpdateReceiptByBuyerEvent;
use App\Events\UploadReceiptEvent;
use App\Models\Invoice;
use App\Models\Trade;
use App\Models\User;
use App\Notifications\TransferToSellerNotification;
use App\Notifications\UpdateReceiptByBuyerNotification;
use App\Notifications\UploadReceiptNotification;
use Illuminate\Support\Facades\Notification;

class TransferToSellerNotificationsListener
{
    private User $sellerUser;
    private int $fileStatus;
    private Invoice $invoice;

    public function handle(TransferToSellerEvent $event): void
    {
        $this->invoice = $event->invoice;
        $this->trade = $this->invoice->invoiceable;
        $this->sellerUser = $this->trade->SellerUser;

        $this->sendNotificationToSeller();
    }

    private function sendNotificationToSeller(): void
    {
        Notification::send(
            $this->sellerUser,
            new TransferToSellerNotification($this->invoice)
        );
    }
}
