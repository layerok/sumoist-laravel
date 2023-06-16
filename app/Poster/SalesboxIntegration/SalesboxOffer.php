<?php

namespace App\Poster\SalesboxIntegration;

use App\Poster\Entities\Product;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use poster\src\PosterApi;

class SalesboxOffer
{

    static protected function createCategoryIfNotExists($posterId) {
        $salesboxCategories = collect(SalesboxApi::getCategories()['data']);
        $salesboxCategory = $salesboxCategories->firstWhere('externalId', $posterId);
        if(!$salesboxCategory) {
            $posterCategoriesRes = PosterApi::menu()->getCategories();
            Utils::assertResponse($posterCategoriesRes, 'getCategories');
            $posterCategories = collect($posterCategoriesRes->response);

            $salesboxCategory = SalesboxCategory::create($posterId, $salesboxCategories, $posterCategories);
        }
        return $salesboxCategory;
    }

    static public function create($posterId): array {
        $posterProduct = PosterApi::menu()->getProduct([
            'product_id' => $posterId
        ]);

        Utils::assertResponse($posterProduct, 'getProduct');

        $posterProductEntity = new Product($posterProduct);

        $common = [
            'externalId' => $posterProductEntity->getProductId(),
            'units' => 'pc',
            'stockType' => 'endless',
            'descriptions' => [],
            'photos' => [],
            'categories' => [],
            'names' => []
        ];

        if ($posterProductEntity->getPhoto()) {
            $common['photos'][] = [
                'url' => config('poster.url') . $posterProductEntity->getPhotoOrigin(),
                'previewURL' => config('poster.url') . $posterProductEntity->getPhoto(),
                'order' => 0,
                'type' => 'image',
                'resourceType' => 'image'
            ];
        }

        if(!!$posterProductEntity->getMenuCategoryId()) {
            $salesboxCategory = self::createCategoryIfNotExists($posterProductEntity->getMenuCategoryId());
            $common['categories'] = [$salesboxCategory['id']];
        }

        if(isset($posterProduct->response->modifications)) {

            // create offer with modifiers
            $offers = [];

            foreach($posterProductEntity->getModifications() as $modification) {
                $spot = $modification->spots[0];
                $offer = [
                    'price' =>  intval($spot->price) / 100,
                    'available' => $spot->visible === "1",
                    'modifierId' => $modification->modificator_id,
                ];
                $offer['names'] = [
                    [
                        'name' => $posterProductEntity->getProductName() . ' ' .$modification->modificator_name,
                        'lang' => 'uk'
                    ]
                ];

                $offers[] = array_merge($offer, $common);
            }
            $res = SalesboxApi::createManyOffers([
                'offers' => $offers
            ]);
            return $res['data']['ids'];
        }

        // create offer without modifiers
        $spot = $posterProductEntity->getSpots()[0];

        $offer = [
            'available' => !$posterProductEntity->isHidden($spot->spot_id),
            'price' =>  intval($posterProductEntity->getPrice($spot->spot_id)) / 100,
        ];

        $offer['names'] = [
            [
                'name' => $posterProductEntity->getProductName(),
                'lang' => 'uk' // todo: move this value to config, or fetch it from salesbox api
            ]
        ];

        $createResp = SalesboxApi::createManyOffers([
            'offers' => [array_merge($offer, $common)]
        ]);

        return $createResp['data']['ids'];

    }

    static public function update($posterId, $offer) {
        $posterProduct = PosterApi::menu()->getProduct([
            'product_id' => $posterId
        ]);

        Utils::assertResponse($posterProduct, 'getProduct');

        $posterProductEntity = new Product($posterProduct->response);
        $spot = $posterProductEntity->getSpots()[0];

        $updatedOffer = [
            'id' => $offer['id'],
            'price' => intval($posterProductEntity->getPrice($spot->spot_id)) / 100,
            'available' => !$posterProductEntity->isHidden($spot->spot_id)
        ];

        if(!!$posterProductEntity->getMenuCategoryId()) {
            $salesboxCategory = self::createCategoryIfNotExists($posterProductEntity->getMenuCategoryId());
            $updatedOffer['categories'][] = $salesboxCategory['id'];
        }

        // update photo only it isn't already present
        if(!$offer['originalURL'] && $posterProductEntity->getPhoto()) {
            $updatedOffer['photos'] = [
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
                $updatedOffer
            ]
        ]);

        return $updateRes['data'];
    }


}
