<?php

namespace App\Poster;

use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use App\Salesbox\meta\SalesboxApiResponse_meta;
use App\Salesbox\meta\SalesboxCategory_meta;
use App\Salesbox\meta\SalesboxOfferV4_meta;
use Illuminate\Support\Collection;

class SalesboxApiWrapper {
    /**
     * @param string|int $externalId
     * @return SalesboxCategory_meta | null
     */
    static public function getCategory($externalId)
    {
        return self::getCategories()
            ->filter(salesbox_filterCategoriesByExternalId($externalId))
            ->first();
    }
    static public function categoryExists($posterId)
    {
        return !!self::getCategory($posterId);
    }

    static public function getCategories(): Collection
    {
        self::authenticate();
        /** @var SalesboxApiResponse_meta $salesbox_categoriesResponse */
        $salesbox_categoriesResponse = perRequestCache()
            ->rememberForever(CacheKeys::SALESBOX_CATEGORIES, function () {
                return SalesboxApi::getCategories();
            });

        return collect($salesbox_categoriesResponse->data);
    }

    static public function authenticate()
    {
        /** @var SalesboxApiResponse_meta $salesbox_accessTokenResponse */
        $salesbox_accessTokenResponse = perRequestCache()
            ->rememberForever(CacheKeys::SALESBOX_ACCESS_TOKEN, function () {
                return SalesboxApi::getAccessToken();
            });

        SalesboxApi::authenticate($salesbox_accessTokenResponse->data->token);
    }

    public static function authenticateV4() {
        /** @var SalesboxApiResponse_meta $response */
        $response = perRequestCache()->rememberForever(CacheKeys::SALESBOX_ACCESS_TOKEN, function() {
            return SalesboxApi::getAccessToken();
        });
        $token = $response->data->token;
        SalesboxApi::authenticate($token);
        SalesboxApiV4::authenticate($token);
    }

    public static function getOffers($posterId = null)
    {
        /** @var SalesboxApiResponse_meta $response */
        $response = perRequestCache()->rememberForever(CacheKeys::SALESBOX_OFFERS, function () {
            return SalesboxApiV4::getOffers();
        });
        $collection = collect($response->data);
        if($posterId) {
            $collection = $collection
                ->filter(salesbox_filterOffersByExternalId($posterId));
        }
        return $collection;
    }

    /**
     * @param string|int $posterId
     * @return SalesboxOfferV4_meta
     */
    public static function getOffer($posterId)
    {
        // probably it can be cached too
        return self::getOffers($posterId)->first();
    }
}
