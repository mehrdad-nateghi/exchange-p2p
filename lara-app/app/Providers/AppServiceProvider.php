<?php

namespace App\Providers;

use App\Interfaces\UserRepositoryInterface;
use App\Models\VerificationCode;
use App\Observers\VerificationCodeObserver;
use App\Repositories\UserRepository;
use App\Services\SMS\Interface\SMSProviderInterface;
use App\Services\SMS\Services\Farapayamak\FarapayamakProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SMSProviderInterface::class, function ($app) {
            //$provider = config('services.sms.default', 'kavenegar');

            return match('farapayamk') {
                'farapayamk' => new FarapayamakProvider(),
                //'ghasedak' => new GhasedakProvider(),
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

        // Register Observers
        VerificationCode::observe(VerificationCodeObserver::class);

        //Passport::loadKeysFrom(storage_path('oauth'));
    }
}
