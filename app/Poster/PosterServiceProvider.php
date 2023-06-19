<?php

namespace App\Poster;

use App\Poster\Stores\PosterStore;
use App\Poster\Stores\RootStore;
use App\Poster\Stores\SalesboxStore;
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
        $rootStore = new RootStore();

        $this->app->singleton('root.store', function() use($rootStore) {
            return $rootStore;
        });

        $this->app->singleton('poster.store', function () use($rootStore) {
            return $rootStore->getPosterStore();
        });

        $this->app->singleton('salesbox.store', function () use($rootStore) {
            return $rootStore->getSalesboxStore();
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
