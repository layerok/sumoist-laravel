<?php

use App\Poster\meta\PosterCategory_meta;
use App\Poster\meta\PosterProduct_meta;
use App\Poster\Query;
use App\Poster\QueryClient;
use App\Salesbox\meta\SalesboxCategory_meta;
use App\Salesbox\meta\SalesboxOfferV4_meta;
use Illuminate\Support\Arr;

if (!function_exists('perRequestCache')) {
    function perRequestCache()
    {
        return cache()->store('array');
    }
}

if (!function_exists('poster_filterProductsById')) {
    /**
     * @param int|string|array $poster_ids
     * @return Closure
     */
    function poster_filterProductsById($poster_ids): Closure
    {
        $poster_ids = Arr::wrap($poster_ids);
        /**
         * @param PosterProduct_meta $product
         */
        return function ($product) use ($poster_ids) {
            return in_array($product->product_id, $poster_ids);
        };
    }
}

if (!function_exists('poster_filterCategoriesByCategoryId')) {
    /**
     * @param int|string|array $poster_ids
     * @return Closure
     */
    function poster_filterCategoriesByCategoryId($poster_ids): Closure
    {
        $poster_ids = Arr::wrap($poster_ids);
        /**
         * @param PosterCategory_meta $category
         */
        return function ($category) use ($poster_ids) {
            return in_array($category->category_id, $poster_ids);
        };
    }

}

if (!function_exists('salesbox_filterOffersByExternalId')) {
    /**
     * @param int|string|array $external_ids
     * @return Closure
     */
    function salesbox_filterOffersByExternalId($external_ids): Closure
    {
        $external_ids = Arr::wrap($external_ids);
        /* @param SalesboxOfferV4_meta $offer */
        return function ($offer) use ($external_ids) {
            return in_array($offer->externalId, $external_ids);
        };
    }
}

if (!function_exists('salesbox_filterCategoriesByExternalId')) {
    /**
     * @param int|string|array $external_ids
     * @return Closure
     */
    function salesbox_filterCategoriesByExternalId($external_ids): Closure
    {
        $external_ids = Arr::wrap($external_ids);
        /* @param SalesboxCategory_meta $category */
        return function ($category) use ($external_ids) {
            return in_array($category->externalId, $external_ids);
        };
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

if (!function_exists('poster_fetchCategories')) {
    function poster_fetchCategories()
    {
        $query = app(\App\Poster\Queries\PosterCategoriesQuery::class);
        return fetch_query($query);
    }
}

if (!function_exists('poster_fetchProducts')) {
    function poster_fetchProducts()
    {
        $query = app(\App\Poster\Queries\PosterProductsQuery::class);
        return fetch_query($query);
    }
}

if (!function_exists('poster_mapCategoryToJson')) {
    function poster_mapCategoryToJson() {
        /** @param PosterCategory_meta $poster_category */
        return function ($poster_category): array
        {
            /** @var SalesboxCategory_meta $salesbox_category */
            $salesbox_category = collect(salesbox_fetchCategories())
                ->filter(salesbox_filterCategoriesByExternalId($poster_category->category_id))
                ->first();

            /** @var SalesboxCategory_meta $salesbox_parentCategory */
            $salesbox_parentCategory = collect(salesbox_fetchCategories())
                ->filter(salesbox_filterCategoriesByExternalId($poster_category->parent_category))
                ->first();


            $json = [
                'available' => !!$poster_category->visible[0]->visible,
                'externalId' => $poster_category->category_id,
                'names' => [
                    [
                        'name' => $poster_category->category_name,
                        'lang' => 'uk' // todo: should this language be configurable?
                    ]
                ],
                'descriptions' => [],
                'photos' => [],
            ];

            if($salesbox_category) {
                // update
                $json['id'] = $salesbox_category->id;
                $json['internalId'] = $salesbox_category->internalId; // salesbox uses internalId to reference parent category
                // update photo only if it isn't already present

            } else {
                $json['internalId'] = $poster_category->category_id;
            }

            if ($poster_category->category_photo) {
                $json['previewURL'] = config('poster.url') . $poster_category->category_photo;
            }

            if ($poster_category->category_photo_origin) {
                $json['originalURL'] = config('poster.url') . $poster_category->category_photo_origin;
            }

            if($salesbox_parentCategory) {
                $json['parentId'] = $salesbox_parentCategory->internalId;

            } else if(!!$poster_category->parent_category) {
                $json['parentId'] = $poster_category->parent_category;
            }

            return $json;
        };
    }
}


if(!function_exists('salesbox_categoryHasPhoto')) {
    function salesbox_categoryHasPhoto($externalId) {
        /** @var SalesboxCategory_meta $salesbox_category */
        $salesbox_category = collect(salesbox_fetchCategories())
            ->filter(salesbox_filterCategoriesByExternalId($externalId))
            ->first();
        return isset($salesbox_category->previewURL);
    }
}
