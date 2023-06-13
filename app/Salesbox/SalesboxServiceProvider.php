<?php

namespace App\Salesbox;

use Illuminate\Support\ServiceProvider;

class SalesboxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('salesboxapi', SalesboxApi::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
