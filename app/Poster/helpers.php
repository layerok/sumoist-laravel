<?php

use App\Poster\meta\PosterCategory_meta;
use App\Poster\meta\PosterProduct_meta;
use App\Salesbox\meta\SalesboxCategory_meta;
use App\Salesbox\meta\SalesboxOfferV4_meta;
use Illuminate\Support\Arr;

if(!function_exists('perRequestCache')) {
    function perRequestCache() {
        return cache()->store('array');
    }
}

if(!function_exists('poster_filterProductsById')) {
    /**
     * @param int|string|array $poster_ids
     * @return Closure
     */
    function poster_filterProductsById($poster_ids): Closure {
        $poster_ids = Arr::wrap($poster_ids);
        /**
         * @param PosterProduct_meta $product
         */
        return function ($product) use ($poster_ids) {
            return in_array($product->product_id, $poster_ids);
        };
    }
}

if(!function_exists('poster_filterCategoriesById')) {
    /**
     * @param int|string|array $poster_ids
     * @return Closure
     */
    function poster_filterCategoriesById($poster_ids): Closure {
        $poster_ids = Arr::wrap($poster_ids);
        /**
         * @param PosterCategory_meta $category
         */
        return function ($category) use ($poster_ids) {
            return in_array($category->category_id, $poster_ids);
        };
    }

}

if(!function_exists('salesbox_filterOffersByExternalId')) {
    /**
     * @param int|string|array $external_ids
     * @return Closure
     */
    function salesbox_filterOffersByExternalId($external_ids): Closure {
        $external_ids = Arr::wrap($external_ids);
        /* @param SalesboxOfferV4_meta $offer */
        return function ($offer) use ($external_ids) {
            return in_array($offer->externalId, $external_ids);
        };
    }
}

if(!function_exists('salesbox_filterCategoriesByExternalId')) {
    /**
     * @param int|string|array $external_ids
     * @return Closure
     */
    function salesbox_filterCategoriesByExternalId($external_ids): Closure {
        $external_ids = Arr::wrap($external_ids);
        /* @param SalesboxCategory_meta $category */
        return function ($category) use ($external_ids) {
            return in_array($category->externalId, $external_ids);
        };
    }
}
