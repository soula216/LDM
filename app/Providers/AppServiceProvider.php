<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        Paginator::useTailwind();

        // En production, ignorer public/hot (Vite dev) pour forcer le manifest build/
        if ($this->app->environment('production') && is_file(public_path('hot'))) {
            @unlink(public_path('hot'));
        }
    }
}
