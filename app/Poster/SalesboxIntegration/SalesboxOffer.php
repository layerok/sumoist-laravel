<?php

namespace App\Poster\SalesboxIntegration;

use App\Poster\meta\PosterApiResponse_meta;
use App\Poster\meta\PosterProduct_meta;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use poster\src\PosterApi;

class SalesboxOffer
{
    /**
     * @param PosterProduct_meta $product
     * @return mixed
     */
    protected static function syncProductWithModifications($product)
    {
        // logic for syncing product with modifications

        // handle 3 scenarios
        // 1. modifications could have been added to product
        // 2. modifications could have been deleted from product
        // 3. modifications could have been updated on product

        // authenticate in salesbox
        $accessToken = SalesboxApi::authenticate();
        SalesboxApiV4::authenticate($accessToken);


        $salesbox_offers = collect(SalesboxApiV4::getOffers()['data']);
        $salesbox_relatedOffers = $salesbox_offers->where('externalId', $product->product_id);

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

        if (!!$product->menu_category_id) {
            $salesboxCategory = SalesboxCategory::sync($product->menu_category_id);
            $common['categories'] = [$salesboxCategory['id']];
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
        return $res['data']['ids'];
    }

    /**
     * @param PosterProduct_meta $product
     * @return array|mixed
     */
    protected static function syncSingleSimpleProduct($product)
    {
        $token = SalesboxApi::authenticate();
        SalesboxApiV4::authenticate($token);
        $allOffers = collect(SalesboxApiV4::getOffers()['data']);
        // poster product either has modifications, either doesn't
        // and it can't be changed
        // so here we update just single offer
        $offer = $allOffers->firstWhere('externalId', $product->product_id);
        if (!$offer) {
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

            if (!!$product->menu_category_id) {
                $salesboxCategory = SalesboxCategory::sync($product->menu_category_id);
                $common['categories'] = [$salesboxCategory['id']];
            }


            // create offer without modifiers
            $spot = $product->spots[0];

            $offer = [
                'available' => !Utils::productIsHidden($product, $spot->spot_id),
                'price' => intval('100') / 100,
            ];

            $offer['names'] = [
                [
                    'name' => $product->product_name,
                    'lang' => 'uk' // todo: move this value to config, or fetch it from salesbox api
                ]
            ];

            $createResp = SalesboxApi::createManyOffers([
                'offers' => [array_merge($offer, $common)]
            ]);

            return $createResp['data']['ids'];
        }


        $spot = $product->spots[0];

        $updatedOffer = [
            'id' => $offer['id'],
            'price' => intval('100') / 100,
            'available' => !Utils::productIsHidden($product, $spot->spot_id)
        ];

        if (!!$product->menu_category_id) {
            $salesboxCategory = SalesboxCategory::sync($product->menu_category_id);
            $updatedOffer['categories'][] = $salesboxCategory['id'];
        }

        // update photo only it isn't already present
        if (!$offer['originalURL'] && $product->photo) {
            $updatedOffer['photos'] = [
                [
                    'url' => config('poster.url') . $product->photo_origin,
                    'previewURL' => config('poster.url') . $product->photo,
                    'order' => 0,
                    'type' => 'image',
                    'resourceType' => 'image'
                ]
            ];
        }

        return SalesboxApi::updateManyOffers([
            'offers' => [
                $updatedOffer
            ]
        ]);
    }

    public static function sync($posterId): bool
    {
        /** @var PosterApiResponse_meta $posterProductsResponse */
        $posterProductsResponse = perRequestCache()
            ->rememberForever('poster.products', function () {
                return PosterApi::menu()->getProducts();
            });

        Utils::assertResponse($posterProductsResponse, 'getProducts');

        $posterProductsCollection = collect($posterProductsResponse->response);


        $productKey = $posterProductsCollection->search(
            /** @param $product PosterProduct_meta */
            function ($product) use ($posterId) {
                return $product->product_id === $posterId;
            }
        );
        /** @var PosterProduct_meta $product */
        $product = $posterProductsCollection->get($productKey);

        // branch logic depending on presence of modifications
        if (property_exists($product, 'modifications')) {
            self::syncProductWithModifications($product);
        } else {
            self::syncSingleSimpleProduct($product);
        }

        return true;
    }

    static public function delete($posterId)
    {
        SalesboxApi::authenticate();
        // delete product
    }
}
