<?php

namespace App\Notifications;

use App\Enums\NotificationIconsEnum;
use App\Enums\NotificationKeyNameEnum;
use App\Mail\SignUpEmail;
use App\Services\Notifications\NotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

class SignUpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private NotificationMessage $notificationMessage;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return App::make(NotificationMessage::class)->store(NotificationKeyNameEnum::SIGNUP_SUCCESSFUL->value,
            [
                'app_name' => config('app.name'),
            ], [
                'icon' => NotificationIconsEnum::INFO->value,
            ]);
    }


    public function toMail($notifiable)
    {
        return (new SignUpEmail())->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            //
        ];
    }
}
