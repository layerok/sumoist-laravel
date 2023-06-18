<?php

namespace App\Poster\SalesboxIntegration;

use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use App\Salesbox\meta\SalesboxApiResponse_meta;

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
        $token = salesbox_fetchAccessToken()->token;
        SalesboxApi::authenticate($token);
        SalesboxApiV4::authenticate($token);

        $product = collect(poster_fetchProducts())
            ->filter($posterId)
            ->first();


        /** @var SalesboxApiResponse_meta $response */
        $salesbox_relatedOffers = collect(salesboxV4_fetchOffers())
            ->filter(salesbox_filterOffersByExternalId($product->product_id));


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

}
