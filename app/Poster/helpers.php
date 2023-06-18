<?php

use App\Poster\meta\PosterCategory_meta;
use App\Poster\meta\PosterProduct_meta;
use App\Poster\Query;
use App\Poster\QueryClient;
use App\Poster\Utils;
use App\Salesbox\meta\CreatedSalesboxCategory_meta;
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

if (!function_exists('salesbox_filterCategoriesByInternalId')) {
    /**
     * @param int|string|array $external_ids
     * @return Closure
     */
    function salesbox_filterCategoriesByInternalId($internalIds): Closure
    {
        $internalIds = Arr::wrap($internalIds);
        /* @param SalesboxCategory_meta $category */
        return function ($category) use ($internalIds) {
            return in_array($category->internalId, $internalIds);
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

if (!function_exists('salesbox_fetchCategory')) {
    /**
     * @param string|int $externalId
     * @return SalesboxCategory_meta|null
     */
    function salesbox_fetchCategory($externalId)
    {
        return collect(salesbox_fetchCategories())
            ->filter(salesbox_filterCategoriesByExternalId($externalId))
            ->first();
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
            ->filter(salesbox_filterOffersByExternalId($externalId))
            ->first();
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
            ->filter(poster_filterCategoriesByCategoryId($posterId))
            ->first();
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
            ->filter(poster_filterProductsById($posterId))
            ->first();
    }
}

if (!function_exists('poster_mapCategoryToJson')) {

    /** @param PosterCategory_meta $poster_category */
    function poster_mapCategoryToJson($poster_category): array
    {
        $salesbox_category = salesbox_fetchCategory($poster_category->category_id);

        $salesbox_parentCategory = salesbox_fetchCategory($poster_category->parent_category);

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

        if ($salesbox_category) {
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

        if ($salesbox_parentCategory) {
            $json['parentId'] = $salesbox_parentCategory->internalId;

        } else if (!!$poster_category->parent_category) {
            $json['parentId'] = $poster_category->parent_category;
        }

        return $json;
    }

}

if (!function_exists('salesbox_categoryHasPhoto')) {
    function salesbox_categoryHasPhoto($externalId)
    {
        $salesbox_category = salesbox_fetchCategory($externalId);
        return isset($salesbox_category->previewURL);
    }
}

if (!function_exists('poster_mapProductToJson')) {
    /** @param PosterProduct_meta $poster_product */
    function poster_mapProductToJson($poster_product)
    {
        $spot = $poster_product->spots[0];

        $json = [
            'externalId' => $poster_product->product_id,
            'units' => 'pc',
            'stockType' => 'endless',
            'descriptions' => [],
            'photos' => [],
            'categories' => [],
            'names' => [
                [
                    'name' => $poster_product->product_name,
                    'lang' => 'uk' // todo: move this value to config, or fetch it from salesbox api
                ]
            ],
            'available' => !Utils::productIsHidden($poster_product, $spot->spot_id),
            'price' => intval($poster_product->price->{$spot->spot_id}) / 100,
        ];

        if ($poster_product->photo) {
            $json['photos'][] = [
                'url' => config('poster.url') . $poster_product->photo_origin,
                'previewURL' => config('poster.url') . $poster_product->photo,
                'order' => 0,
                'type' => 'image',
                'resourceType' => 'image'
            ];
        }

        if (!!$poster_product->menu_category_id) {
            // category maybe not created yet

            $salesbox_category = salesbox_fetchCategory($poster_product->menu_category_id);

            if ($salesbox_category) {
                $json['categories'][] = $salesbox_category->id;
            } else {
                /** @var CreatedSalesboxCategory_meta[]|null $created_categories */
                $created_categories = perRequestCache()
                    ->get('salesbox.categories.created');

                /** @var CreatedSalesboxCategory_meta|null $created_category */
                $created_category = collect($created_categories)
                    ->filter(salesbox_filterCategoriesByInternalId($poster_product->menu_category_id))
                    ->first();

                if ($created_category) {
                    $json['categories'][] = $created_category->id;
                } else {
                    // todo: should I throw here?
                    // category doesn't exist in salesbox and wasn't created
                    // which is unexpected situation
                }
            }

        }

        return $json;
    }
}

if (!function_exists('poster_productHasModifications')) {
    /** @param PosterProduct_meta $product */
    function poster_productHasModifications($product)
    {
        return isset($product->modifications);
    }
}

if (!function_exists('poster_productWithoutModifications')) {
    /** @param PosterProduct_meta $product */
    function poster_productWithoutModifications($product)
    {
        return !isset($product->modifications);
    }
}
