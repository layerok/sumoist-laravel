<?php

use App\Poster\meta\PosterCategory_meta;
use App\Poster\meta\PosterProduct_meta;
use App\Poster\Query;
use App\Poster\QueryClient;
use App\Salesbox\meta\SalesboxCategory_meta;
use App\Salesbox\meta\SalesboxOfferV4_meta;

if (!function_exists('perRequestCache')) {
    function perRequestCache()
    {
        return cache()->store('array');
    }
}

if (!function_exists('fetch_query')) {
    function fetch_query(Query $query)
    {
        $queryClient = app(QueryClient::class);
        return $queryClient->fetch($query);
    }
}

if (!function_exists('salesbox_fetchCategories')) {
    function salesbox_fetchCategories()
    {
        $query = app(\App\Poster\Queries\SalesboxCategoriesQuery::class);
        return fetch_query($query);
    }
}

if (!function_exists('salesbox_fetchCategory')) {
    /**
     * @param string|int $externalId
     * @return SalesboxCategory_meta|null
     */
    function salesbox_fetchCategory($externalId)
    {
        return collect(salesbox_fetchCategories())
            ->firstWhere('externalId', $externalId);
    }
}

if (!function_exists('salesbox_fetchAccessToken')) {
    function salesbox_fetchAccessToken()
    {
        $query = app(\App\Poster\Queries\SalesboxAccessTokenQuery::class);
        return fetch_query($query);
    }
}

if (!function_exists('salesboxV4_fetchOffers')) {
    function salesboxV4_fetchOffers()
    {
        $query = app(\App\Poster\Queries\SalesboxV4OffersQuery::class);
        return fetch_query($query);
    }
}

if(!function_exists('salesboxV4_fetchOffer')) {
    /**
     * @param string|int $externalId
     * @return SalesboxOfferV4_meta|null
     */
    function salesboxV4_fetchOffer($externalId) {
        return collect(salesboxV4_fetchOffers())
            ->firstWhere('externalId', $externalId);
    }
}

if (!function_exists('poster_fetchCategories')) {
    /**
     * @return SalesboxCategory_meta[]
     */
    function poster_fetchCategories()
    {
        $query = app(\App\Poster\Queries\PosterCategoriesQuery::class);
        return fetch_query($query);
    }
}

if (!function_exists('poster_fetchCategory')) {
    /**
     * @return PosterCategory_meta|null
     */
    function poster_fetchCategory($posterId)
    {
        return collect(poster_fetchCategories())
            ->firstWhere('category_id', $posterId);
    }
}

if (!function_exists('poster_fetchProducts')) {
    /**
     * @return PosterProduct_meta[]
     */
    function poster_fetchProducts()
    {
        $query = app(\App\Poster\Queries\PosterProductsQuery::class);
        return fetch_query($query);
    }
}

if(!function_exists('poster_fetchProduct')) {
    /**
     * @param string|int $posterId
     * @return PosterProduct_meta | null
     */
    function poster_fetchProduct($posterId) {
        return collect(poster_fetchProducts())
            ->where('product_id', $posterId)
            ->first();
    }
}
