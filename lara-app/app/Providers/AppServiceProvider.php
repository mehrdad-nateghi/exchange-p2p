<?php

namespace App\Providers;

use App\Models\VerificationCode;
use App\Observers\VerificationCodeObserver;
use App\Services\Notifications\NotificationMessage;
use App\Services\SMS\Interface\SMSProviderInterface;
use App\Services\SMS\Services\Farapayamak\FarapayamakProvider;
use App\Services\SMS\SmsMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(NotificationMessage::class);
        $this->app->singleton(SmsMessage::class);

        $this->app->bind(SMSProviderInterface::class, function ($app) {
            $provider = config('services.sms.default', 'farapayamak');

            return match($provider) {
                'farapayamak' => new FarapayamakProvider(),
                default => throw new \Exception('Invalid SMS provider'),
            };
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Log a warning if we spend more than 1000ms on a single query.
        /*DB::listen(function ($query) {
            Log::info("Query data", [
                'time' => $query->time,
                'sql' => $query->sql
            ]);

            if ($query->time > 200) {
                Log::warning("An individual database query exceeded 200 ms.", [
                    'sql' => $query->sql
                ]);
            }
        });*/

        // Force https
        if($this->app->environment('production') || $this->app->environment('nightly')) {
            URL::forceScheme('https');
        }

        /*Notification::extend('sms', function ($app) {
            Log::info('SMS channel extended and registered');
            return $app->make(SMSChannel::class);
        });*/

        // Register Observers
        VerificationCode::observe(VerificationCodeObserver::class);

        // REPLACE the existing channel registration with this
        /*Notification::extend('sms', function ($app) {
            return $app->make(SMSChannel::class);
        });*/

        //Passport::loadKeysFrom(storage_path('oauth'));
    }
}
