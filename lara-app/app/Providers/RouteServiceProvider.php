<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    protected array $api_middlewares = [
        'api',
    ];

    protected $routes = [
        'V1' => [
            'swagger',
            'auth',
            //'user',
        ],
    ];

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // api
            foreach ($this->routes as $version => $fileNames) {
                foreach ($fileNames as $fileName) {
                    Route::middleware($this->api_middlewares)
                        ->prefix('api/' . strtolower($version))
                        ->as($version . ".")
                        ->group(
                            base_path('routes/api/' . strtolower($version) . '/' . $fileName . '.php')
                        );
                }
            }

            // web
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}