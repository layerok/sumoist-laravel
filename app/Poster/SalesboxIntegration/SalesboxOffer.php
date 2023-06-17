<?php

namespace App\Poster\SalesboxIntegration;

use App\Poster\PosterApiWrapper;
use App\Poster\SalesboxApiWrapper;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;

class SalesboxOffer
{
    /**
     * @param $posterId
     * @return mixed
     */
    public static function syncProductWithModifications($posterId, $menuCategoryId = null)
    {
        // logic for syncing product with modifications

        // handle 3 scenarios
        // 1. modifications could have been added to product
        // 2. modifications could have been deleted from product
        // 3. modifications could have been updated on product

        // authenticate in salesbox
        SalesboxApiWrapper::authenticateV4();
        $product = PosterApiWrapper::getProduct($posterId);

        $salesbox_relatedOffers = SalesboxApiWrapper::getOffers($product->product_id);

        $poster_productModifications = collect($product->modifications);

        $updateModifications = [];
        $addModifications = [];


        $poster_productModifications->map(function ($modification) use (
            &$updateModifications,
            &$addModifications,
            $salesbox_relatedOffers
        ) {
            $modificatorId = $modification->modificator_id;
            $offer = $salesbox_relatedOffers->firstWhere('modifierId', $modificatorId);
            if ($offer) {
                // offer for modification exists, update it
                $updateModifications[] = $modificatorId;
            } else {
                // offer for modification doesn't exist, create it
                $addModifications[] = $modificatorId;
            }
        });


        // delete this offers, because they don't any more exist in poster
        $deleteOffersIds = $salesbox_relatedOffers
            ->filter(function ($offer) use ($poster_productModifications) {
                return !!$poster_productModifications
                    ->firstWhere('modificator_id', $offer['modifierId']);
            })->pluck('id')->all();

        if (count($deleteOffersIds) > 0) {
            SalesboxApi::deleteManyOffers([
                'ids' => $deleteOffersIds
            ]);
        }


        $common = [
            'externalId' => $product->product_id,
            'units' => 'pc',
            'stockType' => 'endless',
            'descriptions' => [],
            'photos' => [],
            'categories' => [],
            'names' => []
        ];

        if ($product->photo) {
            $common['photos'][] = [
                'url' => config('poster.url') . $product->photo_origin,
                'previewURL' => config('poster.url') . $product->photo,
                'order' => 0,
                'type' => 'image',
                'resourceType' => 'image'
            ];
        }

        if (!!$menuCategoryId) {
            $common['categories'] = [$menuCategoryId];
        }

        // create offer with modifiers
        $offers = [];

        $poster_productModifications->each(function ($modification) use ($salesbox_relatedOffers, $product, $common) {
            $existingOffer = $salesbox_relatedOffers
                ->firstWhere('modifierId', data_get($modification, 'modificator_id'));

            if ($existingOffer) {
                // modification exists in salesbox
                // update it
            } else {
                // modification doesn't exists in salesbox
            }

            $spot = $modification->spots[0];
            $offer = [
                'price' => intval($spot->price) / 100,
                'available' => $spot->visible === "1",
                'modifierId' => $modification->modificator_id,
            ];
            $offer['names'] = [
                [
                    'name' => $product->product_name . ' ' . $modification->modificator_name,
                    'lang' => 'uk'
                ]
            ];

            $offers[] = array_merge($offer, $common);
        });


        $res = SalesboxApi::createManyOffers([
            'offers' => $offers
        ]);
        return $res->data->ids;
    }

    public static function getJsonForProductWithModifications($posterId) {

    }

    /**
     * @param string|int $posterId
     * @return array|mixed
     */
    public static function syncSimpleProduct($posterId)
    {
        $salesbox_offer = SalesboxApiWrapper::getOffer($posterId);
        $poster_product = PosterApiWrapper::getProduct($posterId);

        if($salesbox_offer) {
            // todo: fix this
            $categoryId = $poster_product->menu_category_id;
            $offersJson = self::getJsonForSimpleProductUpdate($posterId, $categoryId);
            SalesboxApi::updateManyOffers([
                'offers' => $offersJson
            ]);
        } else {
            // todo: fix this
            $categoryId = $poster_product->menu_category_id;
            $offersJson = self::getJsonForSimpleProductCreation($posterId, $categoryId);
            SalesboxApi::createManyOffers([
                'offers' => $offersJson
            ]);
        }

    }

    public static function getJsonForSimpleProductCreation($posterId, $menuCategoryId) {
        SalesboxApiWrapper::authenticateV4();
        $poster_product = PosterApiWrapper::getProduct($posterId);

        $json = [
            'externalId' => $poster_product->product_id,
            'units' => 'pc',
            'stockType' => 'endless',
            'descriptions' => [],
            'photos' => [],
            'categories' => [],
            'names' => []
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
            $json['categories'] = [$menuCategoryId];
        }


        // create offer without modifiers
        $spot = $poster_product->spots[0];

        $json = [
            'available' => !Utils::productIsHidden($poster_product, $spot->spot_id),
            'price' => intval('100') / 100,
        ];

        $json['names'] = [
            [
                'name' => $poster_product->product_name,
                'lang' => 'uk' // todo: move this value to config, or fetch it from salesbox api
            ]
        ];

        return [$json];
    }


    public static function getJsonForSimpleProductUpdate($posterId, $menuCategoryId) {
        SalesboxApiWrapper::authenticateV4();
        $product = PosterApiWrapper::getProduct($posterId);

        $offer = SalesboxApiWrapper::getOffer($product->product_id);

        $spot = $product->spots[0];

        $json = [
            'id' => $offer['id'],
            'price' => intval('100') / 100,
            'available' => !Utils::productIsHidden($product, $spot->spot_id)
        ];

        if (!!$product->menu_category_id) {
            // todo: find salesbox category id by external id here
            $json['categories'][] = $menuCategoryId;
        }

        // update photo only it isn't already present
        if (!$offer['originalURL'] && $product->photo) {
            $json['photos'] = [
                [
                    'url' => config('poster.url') . $product->photo_origin,
                    'previewURL' => config('poster.url') . $product->photo,
                    'order' => 0,
                    'type' => 'image',
                    'resourceType' => 'image'
                ]
            ];
        }
        return [$json];
    }

    static public function delete($posterId)
    {
        SalesboxApi::authenticate();
        // delete product
    }
}
