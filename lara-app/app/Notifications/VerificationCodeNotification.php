<?php

namespace App\Notifications;

use App\Enums\VerificationCodeViaEnum;
use App\Mail\VerificationCodeEmail;
use App\Notifications\Channels\SMSChannel;
use App\Services\Localization\LocalizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;


class VerificationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private string $to;
    private string $verificationCode;
    private int $via;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $to, int $via, string $verificationCode)
    {
        $this->to = $to;
        $this->verificationCode = $verificationCode;
        $this->via = $via;
    }

    // In VerificationCodeNotification
    public function via($notifiable)
    {
        $channels = [
            VerificationCodeViaEnum::EMAIL->value => ['mail'],
            VerificationCodeViaEnum::MOBILE->value => [SMSChannel::class],
        ];

        return $channels[$this->via] ?? [];
    }


    public function toMail($notifiable)
    {
        return (new VerificationCodeEmail($this->verificationCode))->to($this->to);
    }

    public function toSMS($notifiable)
    {
        return [
            'message' => trans('sms.send_verification_code', [
                'verification_code' => $this->verificationCode,
            ]),
            'to' => $this->to,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
