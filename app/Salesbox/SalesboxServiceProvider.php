<?php

namespace App\Salesbox;

use App\Salesbox\Stores\SalesboxStore;
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
        $this->app->singleton('salesbox.store', function ()  {
            return new SalesboxStore();
        });
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
