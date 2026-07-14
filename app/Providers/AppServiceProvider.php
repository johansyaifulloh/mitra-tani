<?php

namespace App\Providers;

use App\View\Composers\MobileStoreComposer;
use Illuminate\Support\Facades\View;
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
        View::composer([
            'layouts.mobile.*',
            'components.mobile.hero-header',
            'components.mobile.bottom-nav',
            'mobile.partials.cart-store',
        ], MobileStoreComposer::class);
    }
}
