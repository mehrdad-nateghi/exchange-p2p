<?php

namespace App\Listeners;

use App\Enums\NotificationRecipientTypeEnum;
use App\Events\BidStoredEvent;
use App\Notifications\BidAcceptedNotification;
use Illuminate\Support\Facades\Notification;

class BidAcceptedByRequesterNotificationsListener
{
    public function handle(BidStoredEvent $event): void
    {
        $bid = $event->bid;
        $requester = $bid->request;
        $otherBidders = $bid->otherBidders;
        $bidder = $bid->user;

        $this->sendAcceptanceNotifications($requester, $bidder, $otherBidders, $bid);
    }

    private function sendAcceptanceNotifications($requester, $bidder, $otherBidders, $bid): void
    {
        Notification::send(
            $bidder,
            new BidAcceptedNotification($bid, NotificationRecipientTypeEnum::BIDDER_WINNER->value)
        );

        if (!empty($otherBidders)) {
            Notification::send(
                $otherBidders,
                new BidAcceptedNotification($bid, NotificationRecipientTypeEnum::OTHER_BIDDERS->value)
            );
        }
    }
}
