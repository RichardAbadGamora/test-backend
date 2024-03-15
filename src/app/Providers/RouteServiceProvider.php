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
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * List of all route files under routes/api folder.
     */
    private $apiRoutes = [
        'auth',
        'folders',
        'files',
        'invitations',
        'phases',
        'paths',
        'users',
        'system',
        'tasks',
        'signnow',
        'pages',
        'versions',
        'trello',
        'gmail',
        'iframes',
        'waveapps',
        'channels',
        'gdrive',
        'chat-messages'
    ];

    /**
     * Mapper for Custom API Routes.
     */
    public function mapApiRoutes()
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        foreach ($this->apiRoutes as $route) {
            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path("routes/api/$route.php"));
        }
    }

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            $this->mapApiRoutes();

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
