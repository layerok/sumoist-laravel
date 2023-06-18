<?php

namespace App\Poster;

use App\Poster\Queries\PosterCategoriesQuery;
use App\Poster\Queries\PosterProductsQuery;
use App\Poster\Queries\SalesboxAccessTokenQuery;
use App\Poster\Queries\SalesboxCategoriesQuery;
use App\Poster\Queries\SalesboxOffersQuery;
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
        $this->app->instance(QueryClient::class, function () {
            return new QueryClient();
        });

        $this->app->instance(SalesboxCategoriesQuery::class, function () {
            return new SalesboxCategoriesQuery();
        });

        $this->app->instance(SalesboxAccessTokenQuery::class, function () {
            return new SalesboxAccessTokenQuery();
        });

        $this->app->instance(SalesboxOffersQuery::class, function () {
            return new SalesboxOffersQuery();
        });

        $this->app->instance(PosterProductsQuery::class, function () {
            return new PosterProductsQuery();
        });

        $this->app->instance(PosterCategoriesQuery::class, function () {
            return new PosterCategoriesQuery();
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
