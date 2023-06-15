<?php

namespace App\Poster\SalesboxIntegration;

use App\Poster\Entities\Product;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use poster\src\PosterApi;

class SalesboxOffer
{
    static public function createIfNotExists($posterId): array {
        $salesboxOffer = SalesboxApiV4::getOfferByExternalId($posterId);

        if ($salesboxOffer) {
            return $salesboxOffer;
        }

        $posterProduct = PosterApi::menu()->getProduct([
            'product_id' => $posterId
        ]);

        Utils::assertResponse($posterProduct, 'getProduct');

        $posterProductEntity = new Product($posterProduct->response);
        $spot = $posterProductEntity->getSpots()[0];

        $offer = [
            'units' => 'pc',
            'stockType' => 'endless',
            'available' => !$posterProductEntity->isHidden($spot->spot_id),
            'price' =>  intval($posterProductEntity->getPrice($spot->spot_id)) / 100,
            'externalId' => $posterProductEntity->getProductId(),
        ];

        $offer['names'] = [
            [
                'name' => $posterProductEntity->getProductName(),
                'lang' => 'uk' // todo: move this value to config, or fetch it from salesbox api
            ]
        ];

        $offer['descriptions'] = [];
        $offer['categories'] = [];

        if(!!$posterProductEntity->getMenuCategoryId()) {
            $salesboxCategory = SalesboxCategory::createIfNotExists($posterProductEntity->getMenuCategoryId());
            $offer['categories'][] = $salesboxCategory['id'];
        }

        $offer['photos'] = [];
        if ($posterProductEntity->getPhoto()) {
            $offer['photos'][] = [
                'url' => config('poster.url') . $posterProductEntity->getPhotoOrigin(),
                'previewURL' => config('poster.url') . $posterProductEntity->getPhoto(),
                'order' => 0,
                'type' => 'image',
                'resourceType' => 'image'
            ];
        }

        $createResp = SalesboxApi::createManyOffers([
            'offers' => [$offer]
        ]);

        return $createResp['data']['ids'][0];

    }

    static public function updateOrCreateIfNotExists($posterId) {
        $salesboxOffer = SalesboxApiV4::getOfferByExternalId($posterId);

        if (!$salesboxOffer) {
            return self::createIfNotExists($posterId);
        }

        $posterProduct = PosterApi::menu()->getProduct([
            'product_id' => $posterId
        ]);

        Utils::assertResponse($posterProduct, 'getProduct');

        $posterProductEntity = new Product($posterProduct->response);
        $spot = $posterProductEntity->getSpots()[0];

        $offer = [
            'id' => $salesboxOffer['id'],
            'price' => intval($posterProductEntity->getPrice($spot->spot_id)) / 100,
            'available' => !$posterProductEntity->isHidden($spot->spot_id)
        ];

        // not sure if we need to update name
//        $offer['names'] = [
//            [
//                'name' => $posterProductEntity->getProductName(),
//                'lang' => 'uk'
//            ]
//        ];

        if(!!$posterProductEntity->getMenuCategoryId()) {
            $salesboxCategory = SalesboxCategory::createIfNotExists($posterProductEntity->getMenuCategoryId());
            $offer['categories'][] = $salesboxCategory['id'];
        }

        // update photo only it isn't already present
        if(!$salesboxOffer['originalURL'] && $posterProductEntity->getPhoto()) {
            $offer['photos'] = [
                [
                    'url' => config('poster.url') . $posterProductEntity->getPhotoOrigin(),
                    'previewURL' => config('poster.url') . $posterProductEntity->getPhoto(),
                    'order' => 0,
                    'type' => 'image',
                    'resourceType' => 'image'
                ]
            ];
        }

        $updateRes = SalesboxApi::updateManyOffers([
            'offers' => [
                $offer
            ]
        ]);

        return $updateRes['data'][0];

    }
}
