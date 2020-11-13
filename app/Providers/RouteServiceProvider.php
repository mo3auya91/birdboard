<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix(LaravelLocalization::setLocale())
                ->middleware([
                    'web',
                    //start Mcamara LaravelLocalization middlewares
                    LocaleSessionRedirect::class,
                    LaravelLocalizationRedirectFilter::class,
                    LaravelLocalizationViewPath::class,
                    //could remove these two middlewares
                    LaravelLocalizationRoutes::class,
                    LocaleCookieRedirect::class,
                    //end Mcamara LaravelLocalization middlewares
                ])
                ->group(base_path('routes/web.php'));

            Route::prefix(LaravelLocalization::setLocale())
                ->middleware([
                    'web',
                    //start Mcamara LaravelLocalization middlewares
                    LocaleSessionRedirect::class,
                    LaravelLocalizationRedirectFilter::class,
                    LaravelLocalizationViewPath::class,
                    //could remove these two middlewares
                    LaravelLocalizationRoutes::class,
                    LocaleCookieRedirect::class,
                    //end Mcamara LaravelLocalization middlewares
                ])
                ->group(base_path('routes/auth.php'));

            Route::prefix(LaravelLocalization::setLocale())
                ->middleware([
                    'web',
                    //start Mcamara LaravelLocalization middlewares
                    LocaleSessionRedirect::class,
                    LaravelLocalizationRedirectFilter::class,
                    LaravelLocalizationViewPath::class,
                    //could remove these two middlewares
                    LaravelLocalizationRoutes::class,
                    LocaleCookieRedirect::class,
                    //end Mcamara LaravelLocalization middlewares
                ])
                ->group(base_path('routes/jetstream.php'));

            Route::prefix(LaravelLocalization::setLocale())
                ->middleware([
                    'api',
                    //'auth:sanctum'
                ])
                ->group(base_path('routes/channels.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60);
        });
    }
}
