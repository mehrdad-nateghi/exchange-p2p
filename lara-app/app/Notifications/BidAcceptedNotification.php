<?php

namespace App\Notifications;

use App\Enums\NotificationIconsEnum;
use App\Enums\NotificationKeyNameEnum;
use App\Enums\NotificationModelNamesEnum;
use App\Enums\NotificationRecipientTypeEnum;
use App\Models\Bid;
use App\Models\Request;
use App\Models\Trade;
use App\Services\Notifications\NotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BidAcceptedNotification extends Notification
{
    use Queueable;

    private Bid $bid;

    private Trade $trade;
    private Request $request;

    private string $sendTo;

    public function __construct(Bid $bid, string $sendTo)
    {
        $this->bid = $bid;
        $this->request = $bid->request;
        $this->trade = $bid->trade;
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
        if ($this->sendTo === NotificationRecipientTypeEnum::OTHER_BIDDERS->value){
            $this->otherBiddersNotification();
        }elseif ($this->sendTo === NotificationRecipientTypeEnum::BIDDER_WINNER->value){
            $this->BidderWinnerNotification();
        }
    }

    private function BidderWinnerNotification()
    {
        return app()->make(NotificationMessage::class)->store(
            NotificationKeyNameEnum::BID_ACCEPTED_BY_REQUESTER_TO_BIDDER->value,
            [
                'request_number' => $this->request->number,
            ],
            [
                'icon' => NotificationIconsEnum::BID->value,
                'model' => [
                    'name' => NotificationModelNamesEnum::TRADE->value,
                    'ulid' => $this->trade->ulid,
                ],
            ]
        );
    }

    private function otherBiddersNotification()
    {
        return app()->make(NotificationMessage::class)->store(
            NotificationKeyNameEnum::BID_ACCEPTED_BY_REQUESTER_TO_OTHER_BIDDERS->value,
            [
                'request_number' => $this->request->number,
            ],
            [
                'icon' => NotificationIconsEnum::BID->value,
                'model' => [
                    'name' => NotificationModelNamesEnum::REQUEST->value,
                    'ulid' => $this->trade->ulid,
                ],
            ]
        );
    }
}
