<?php

namespace App\Listeners;

use App\Enums\NotificationRecipientTypeEnum;
use App\Events\BidStoredEvent;
use App\Notifications\BidAcceptedAutomaticNotification;
use App\Notifications\BidRegisteredNotification;
use Illuminate\Support\Facades\Notification;

class BidRegisterNotificationsListener
{
    public function handle(BidStoredEvent $event): void
    {
        $bid = $event->bid;
        $bidder = $bid->user;
        $requester = $bid->request->user;
        $otherBidders = $bid->otherBidders;

        if ($event->isAutomaticAcceptance) {
            $this->sendAutomaticAcceptanceNotifications($requester, $bidder, $otherBidders, $bid);
        } else {
            $this->sendBidRegisteredNotifications($requester, $otherBidders, $bid);
        }
    }

    private function sendAutomaticAcceptanceNotifications($requester, $bidder, $otherBidders, $bid): void
    {
        Notification::send(
            $requester,
            new BidAcceptedAutomaticNotification($bid, NotificationRecipientTypeEnum::REQUESTER->value)
        );

        Notification::send(
            $bidder,
            new BidAcceptedAutomaticNotification($bid, NotificationRecipientTypeEnum::BIDDER_WINNER->value)
        );

        if (!empty($otherBidders)) {
            Notification::send(
                $otherBidders,
                new BidAcceptedAutomaticNotification($bid, NotificationRecipientTypeEnum::OTHER_BIDDERS->value)
            );
        }
    }

    private function sendBidRegisteredNotifications($requester, $otherBidders, $bid): void
    {
        Notification::send(
            $requester,
            new BidRegisteredNotification($bid, NotificationRecipientTypeEnum::REQUESTER->value)
        );

        if (!empty($otherBidders)) {
            Notification::send(
                $otherBidders,
                new BidRegisteredNotification($bid, NotificationRecipientTypeEnum::OTHER_BIDDERS->value)
            );
        }
    }
}
