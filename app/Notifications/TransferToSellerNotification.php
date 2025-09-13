<?php

namespace App\Notifications;

use App\Enums\NotificationKeyNameEnum;
use App\Enums\NotificationRecipientTypeEnum;
use App\Enums\NotificationIconsEnum;
use App\Enums\NotificationModelNamesEnum;
use App\Models\Bid;
use App\Models\Invoice;
use App\Models\Request;
use App\Models\Trade;
use App\Services\Notifications\NotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Notifications\Notification;

class TransferToSellerNotification extends Notification
{
    use Queueable;

    private Trade $trade;

    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->trade = $this->invoice->invoiceable;

        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return $this->sendNotificationToSeller();
    }

    /**
     * @throws BindingResolutionException
     */
    private function sendNotificationToSeller()
    {
        return app()->make(NotificationMessage::class)->store(
            NotificationKeyNameEnum::TRANSFER_TO_SELLER_TO_SELLER->value,
            [
                'app_name' => config('app.name'),
                'trade_number' => $this->trade->number,
                'invoice_amount' => $this->invoice->PayableAmountToSeller,
            ],
            [
                'icon' => NotificationIconsEnum::TRADE->value,
                'model' => [
                    'name' => NotificationModelNamesEnum::TRADE->value,
                    'ulid' => $this->trade->ulid,
                ],
            ]
        );
    }
}
