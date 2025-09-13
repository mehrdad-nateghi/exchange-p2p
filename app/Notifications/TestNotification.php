<?php

namespace App\Notifications;

use App\Notifications\Channels\SMSChannel;
use App\Services\Notifications\NotificationMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class TestNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast', SMSChannel::class];
    }

    public function toDatabase($notifiable)
    {
        return App::make(NotificationMessage::class)->store('signup_successful', [
            'app_name' => config('app.name'),
        ]);
    }

    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Email Test Notification')
            ->greeting('Hello!')
            ->line('This is a test notification sent via email.')
            ->action('Visit our website', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toSMS($notifiable)
    {
        return [
            'message' => 'Hi, This is a sample SMS.',
            'to' => 'test@test.com',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'data' => 'This is a test notification',
            'created_at' => now()->toISOString(),
        ]);
    }

    // Override the default private channel with a public one
    public function broadcastOn()
    {
        return new Channel('notifications');  // Public channel
    }

    public function toArray($notifiable)
    {
        return [
            'data' => 'This is a test notification',
        ];
    }
}
