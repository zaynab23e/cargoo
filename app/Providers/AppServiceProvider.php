<?php

namespace App\Providers;
use Illuminate\Support\Facades\App;


use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
  $locale = request()->header('Accept-Language');

    if ($locale && in_array($locale, config('app.available_locales'))) {
        App::setLocale($locale);
    }
    }
}
