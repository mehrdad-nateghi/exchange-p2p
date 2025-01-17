<?php

namespace App\Notifications;

use App\Enums\NotificationIconsEnum;
use App\Enums\NotificationKeyNameEnum;
use App\Enums\NotificationModelNamesEnum;
use App\Enums\NotificationRecipientTypeEnum;
use App\Models\Invoice;
use App\Models\Trade;
use App\Services\Notifications\NotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PayTomanToSystemNotification extends Notification
{
    use Queueable;

    private Trade $trade;

    private string $sendTo;
    private Invoice $invoice;

    public function __construct(Trade $trade, Invoice $invoice ,string $sendTo)
    {
        $this->trade = $trade;
        $this->invoice = $invoice;
        $this->sendTo = $sendTo;
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
        return match ($this->sendTo) {
            NotificationRecipientTypeEnum::BUYER->value => $this->sendToBuyer(),
            NotificationRecipientTypeEnum::SELLER->value => $this->sendToSeller(),
            default => throw new \InvalidArgumentException("Invalid notification recipient type."),
        };
    }

    private function sendToBuyer()
    {
        return app()->make(NotificationMessage::class)->store(
            NotificationKeyNameEnum::PAY_TOMAN_TO_SYSTEM_TO_BUYER->value,
            [
                'trade_number' => $this->trade->number,
                'invoice_amount' => $this->invoice->PayableAmountByBuyer,
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

    private function sendToSeller()
    {
        return app()->make(NotificationMessage::class)->store(
            NotificationKeyNameEnum::PAY_TOMAN_TO_SYSTEM_TO_SELLER->value,
            [
                'trade_number' => $this->trade->number,
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
