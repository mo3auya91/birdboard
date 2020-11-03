<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Inertia::share([
//            'app' => [
//                'name' => config('app.name'),
//                'locale' => $this->app->getLocale(),
//
//                // You can add a `locales => ['fr', 'en']` in your config.app
//                // to represent you app supported locales.
//                'locales' => config('app.locales'),
//
//                // Here we properly return the translation to Vue.
//                // Note that it is lazy loaded, so Inertia will not load the translations in every request.
//                // Inertia will load only on demand. Using, VueJs, will call this method only once, when the app is open.
//                'translations' => fn() => translations($this->app->getLocale())
//            ],
            // ...
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object)[];
            },
        ]);
    }
}
