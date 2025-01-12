<?php

namespace App\Notifications;

use App\Enums\NotificationIconsEnum;
use App\Enums\NotificationModelNamesEnum;
use App\Enums\NotificationRecipientTypeEnum;
use App\Models\Bid;
use App\Models\Request;
use App\Services\Notifications\NotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BidRegisteredNotification extends Notification
{
    use Queueable;

    private Bid $bid;

    private Request $request;

    private string $sendTo;

    public function __construct(Bid $bid, string $sendTo)
    {
        $this->bid = $bid;
        $this->request = $bid->request;
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
            NotificationRecipientTypeEnum::OTHER_BIDDERS->value => $this->otherBiddersNotification(),
            NotificationRecipientTypeEnum::REQUESTER->value => $this->requesterNotification(),
            default => throw new \InvalidArgumentException("Invalid notification recipient type"),
        };
    }

    private function requesterNotification()
    {
        return app()->make(NotificationMessage::class)->store(
            'bid_registered_to_requester',
            [
                'request_number' => $this->request->number,
                'bid_price' => $this->bid->price,
            ],
            [
                'icon' => NotificationIconsEnum::BID->value,
                'model' => [
                    'name' => NotificationModelNamesEnum::REQUEST->value,
                    'ulid' => $this->request->ulid,
                ],
            ]
        );
    }

    private function otherBiddersNotification()
    {
        return app()->make(NotificationMessage::class)->store(
            'bid_registered_to_other_bidders',
            [
                'request_number' => $this->request->number,
                'bid_price' => $this->bid->price,
            ],
            [
                'icon' => NotificationIconsEnum::BID->value,
                'model' => [
                    'name' => NotificationModelNamesEnum::REQUEST->value,
                    'ulid' => $this->request->ulid,
                ],
            ]
        );
    }
}
