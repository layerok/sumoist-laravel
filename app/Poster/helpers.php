<?php

use App\Poster\meta\PosterApiResponse_meta;
use App\Poster\meta\PosterCategory_meta;
use App\Poster\meta\PosterProduct_meta;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use App\Salesbox\meta\SalesboxCategory_meta;
use App\Salesbox\meta\SalesboxOfferV4_meta;
use poster\src\PosterApi;

if (!function_exists('perRequestCache')) {
    function perRequestCache()
    {
        return cache()->store('array');
    }
}

if (!function_exists('salesbox_fetchCategories')) {
    function salesbox_fetchCategories()
    {
        $cacheKey = 'salesbox.categories';
        return perRequestCache()->rememberForever($cacheKey, function() {
            return SalesboxApi::getCategories()->data;
        });
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
        $cacheKey = 'salesbox.accessToken';
        return perRequestCache()->rememberForever($cacheKey, function() {
            return SalesboxApi::getAccessToken()->data->token;
        });
    }
}

if (!function_exists('salesboxV4_fetchOffers')) {
    function salesboxV4_fetchOffers()
    {
        $cacheKey = 'salesboxv4.offers';
        return perRequestCache()->rememberForever($cacheKey, function() {
            return SalesboxApiV4::getOffers()->data;
        });
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
        $cacheKey = 'poster.categories';

        return perRequestCache()->rememberForever($cacheKey, function() {
            /** @var PosterApiResponse_meta $response */
            $response = PosterApi::menu()->getCategories();
            Utils::assertResponse($response, 'getCategories');
            return $response->response;
        });
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
        $cacheKey = 'poster.products';

        return perRequestCache()->rememberForever($cacheKey, function() {
            $response = PosterApi::menu()->getProducts();
            /** @var PosterApiResponse_meta $response */
            Utils::assertResponse($response, 'getProducts');
            return $response->response;
        });
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
