<?php

namespace App\Poster;

use App\Poster\Stores\PosterStore;
use Illuminate\Support\ServiceProvider;

class PosterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('poster.store', function ()  {
            return new PosterStore();
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
