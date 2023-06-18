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
        $this->app->instance(QueryClient::class, new QueryClient());

        $this->app->instance(SalesboxCategoriesQuery::class, new SalesboxCategoriesQuery());
        $this->app->instance(SalesboxAccessTokenQuery::class, new SalesboxAccessTokenQuery());
        $this->app->instance(SalesboxOffersQuery::class, new SalesboxOffersQuery());
        $this->app->instance(PosterProductsQuery::class, new PosterProductsQuery());
        $this->app->instance(PosterCategoriesQuery::class, new PosterCategoriesQuery());
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
