<?php

namespace App\Notifications;

use App\Enums\FileStatusEnum;
use App\Enums\NotificationIconsEnum;
use App\Enums\NotificationKeyNameEnum;
use App\Enums\NotificationModelNamesEnum;
use App\Enums\NotificationRecipientTypeEnum;
use App\Models\Invoice;
use App\Models\Trade;
use App\Services\Notifications\NotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UpdateReceiptByBuyerNotification extends Notification
{
    use Queueable;

    private Trade $trade;

    private string $sendTo;
    private int $fileStatus;

    public function __construct(Trade $trade, int $fileStatus)
    {
        $this->trade = $trade;
        $this->fileStatus = $fileStatus;
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
        return match ($this->fileStatus) {
            FileStatusEnum::ACCEPT_BY_BUYER->value => $this->sendToSellerForAccept(),
            FileStatusEnum::REJECT_BY_BUYER->value => $this->sendToSellerForReject(),
            default => throw new \InvalidArgumentException("Invalid notification recipient type."),
        };
    }

    private function sendToSellerForAccept()
    {
        return app()->make(NotificationMessage::class)->store(
            NotificationKeyNameEnum::ACCEPT_RECEIPT_BY_BUYER->value,
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

    private function sendToSellerForReject()
    {
        return app()->make(NotificationMessage::class)->store(
            NotificationKeyNameEnum::REJECT_RECEIPT_BY_BUYER->value,
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
