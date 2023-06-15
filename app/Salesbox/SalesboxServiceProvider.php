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
        $this->app->singleton('salesboxapi', function () {
            return new SalesboxApi(config('salesbox'));
        });
        $this->app->singleton('salesboxapi.v4', function () {
            return new SalesboxApiV4(config('salesbox'));
        });
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
