<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
     /**
     * Register services.
     */
    

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Broadcasting channels load karna
        require base_path('routes/channels.php');
    }
}
