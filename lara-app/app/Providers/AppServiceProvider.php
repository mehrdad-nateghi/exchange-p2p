<?php

namespace App\Providers;

use App\Interfaces\UserRepositoryInterface;
use App\Models\VerificationCode;
use App\Observers\VerificationCodeObserver;
use App\Repositories\UserRepository;
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

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force https
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register Observers
        VerificationCode::observe(VerificationCodeObserver::class);
    }
}
