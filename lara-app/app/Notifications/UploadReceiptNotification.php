<?php

namespace App\Notifications;

use App\Enums\NotificationIconsEnum;
use App\Enums\NotificationKeyNameEnum;
use App\Enums\NotificationModelNamesEnum;
use App\Enums\NotificationRecipientTypeEnum;
use App\Models\Invoice;
use App\Models\Request;
use App\Models\Trade;
use App\Services\Notifications\NotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UploadReceiptNotification extends Notification
{
    use Queueable;

    private Trade $trade;

    private string $sendTo;
    private Request $request;

    public function __construct(Trade $trade, string $sendTo)
    {
        $this->trade = $trade;
        $this->request = $this->trade->request;
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
            default => throw new \InvalidArgumentException("Invalid notification recipient type."),
        };
    }

    private function sendToBuyer()
    {
        return app()->make(NotificationMessage::class)->store(
            NotificationKeyNameEnum::UPLOAD_RECEIPT_TO_BUYER->value,
            [
                'trade_number' => $this->trade->number,
                'request_volume' => $this->request->volume,
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
