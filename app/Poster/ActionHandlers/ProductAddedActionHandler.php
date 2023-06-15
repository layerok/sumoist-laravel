<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Entities\Category;
use App\Poster\Entities\Product;
use App\Poster\PosterApiException;
use App\Poster\Utils;
use App\Salesbox\Facades\SalesboxApi;
use poster\src\PosterApi;

class ProductAddedActionHandler extends AbstractActionHandler
{

    public function handle(): bool
    {
        SalesboxApi::authenticate();

        $salesboxOffer = SalesboxApi::getOfferByExternalId($this->getObjectId());

        if ($salesboxOffer) {
            return false;
        }

        $posterProduct = PosterApi::menu()->getProduct([
            'product_id' => $this->getObjectId()
        ]);

        Utils::assertResponse($posterProduct, 'getProduct');

        $posterProductEntity = new Product($posterProduct->response);
        $spot = $posterProductEntity->getSpots()[0];

        $offer = [
            'units' => 'pc',
            'stockType' => 'endless',
            'available' => false, // todo: don't forget to show product by default
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
            $posterCategory = PosterApi::menu()->getCategory([
                'category_id' => $posterProductEntity->getMenuCategoryId()
            ]);
            Utils::assertResponse($posterCategory, 'getCategory');

            $posterCategoryEntity = new Category($posterCategory->response);

            $salesboxCategory = SalesboxApi::getCategoryByExternalId($posterCategoryEntity->getId());

            if($salesboxCategory) {
                $offer['categories'][] = $salesboxCategory['id'];
            } else {
                // create category in salesbox
            }
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

        SalesboxApi::createManyOffers([
            'offers' => [$offer]
        ]);

        return true;
    }
}
