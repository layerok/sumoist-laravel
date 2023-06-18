<?php

namespace App\Poster\ActionHandlers;

use App\Poster\meta\PosterProduct_meta;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use App\Salesbox\meta\SalesboxOfferV4_meta;

class ProductActionHandler extends AbstractActionHandler
{
    public $productIdsToCreate = [];
    public $productIdsToUpdate = [];
    public $categoryIdsToCreate = [];

    public $createdCategories = [];

    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            $posterId = $this->getObjectId();

            $accessToken = salesbox_fetchAccessToken()->token;
            SalesboxApi::authenticate($accessToken);
            SalesboxApiV4::authenticate($accessToken);

            $poster_product = poster_fetchProduct($posterId);

            $salesbox_offer = salesboxV4_fetchOffer($posterId);

            if (!$salesbox_offer) {
                $this->productIdsToCreate[] = $posterId;
            } else {
                $this->productIdsToUpdate[] = $posterId;
            }

            if (!!$poster_product->menu_category_id) {
                // recursively check parent categories for existence
                $this->checkCategory($poster_product->menu_category_id);
            }

            if (count($this->categoryIdsToCreate)) {
                $categories = collect(poster_fetchCategories())
                    ->filter(poster_filterCategoriesByCategoryId($this->categoryIdsToCreate))
                    ->map('poster_mapCategoryToJson')
                    ->map(function ($json) {
                        return collect($json)->only([
                            'internalId',
                            'externalId',
                            'parentId',
                            'previewURL',
                            'originalURL',
                            'names',
                            'available',
                            'photos',
                            'descriptions'
                        ]);
                    })
                    ->values()
                    ->toArray();

                perRequestCache()->rememberForever('salesbox.categories.created', function () use ($categories) {
                    return SalesboxApi::createManyCategories([
                        'categories' => $categories
                    ])->data->ids;
                });

            }

            if (count($this->productIdsToCreate) > 0) {

                $simpleOffers = collect(poster_fetchProducts())
                    ->filter(poster_filterProductsById($this->productIdsToCreate))
                    ->filter('poster_productWithoutModifications')
                    ->map('poster_mapProductToJson')
                    ->map(function($json) {
                        return collect($json)
                            ->only([
                                'externalId',
                                'units',
                                'stockType',
                                'descriptions',
                                'photos',
                                'categories',
                                'names',
                                'available',
                                'price'
                            ]);
                    })
                    ->values()
                    ->toArray();

//                $offers2 = $poster_productsToCreate
//                    ->filter(function($product) {
//                        return !poster_productHasModifications($product);
//                    });
//
//                $foo = [];

                SalesboxApi::createManyOffers([
                    'offers' => $simpleOffers
                ]);

            }

            if (count($this->productIdsToUpdate) > 0) {


                $simpleOffers = collect(poster_fetchProducts())
                    ->filter(poster_filterProductsById($this->productIdsToCreate))
                    ->filter('poster_productWithoutModificators')
                    ->map('poster_mapProductToJson')
                    ->map(function($json) {
                        return collect($json)
                            ->only([
                                'externalId',
                                'units',
                                'stockType',
                                'descriptions',
                                'photos',
                                'categories',
                                'names',
                                'available',
                                'price'
                            ]);
                    })
                    ->values()
                    ->toArray();

                SalesboxApi::createManyOffers([
                    'offers' => $simpleOffers
                ]);
            }

        }

        if ($this->isRemoved()) {
            $token = salesbox_fetchAccessToken()->token;
            SalesboxApi::authenticate($token);
            SalesboxApiV4::authenticate($token);


            $salesbox_offers_ids = collect(salesboxV4_fetchOffers())
                ->filter(salesbox_filterOffersByExternalId($this->getObjectId()))
                /** @param SalesboxOfferV4_meta $offer */
                ->map(function($offer) {
                    return $offer->id;
                })
                ->values()
                ->toArray();


            // delete products
            SalesboxApi::deleteManyOffers([
                'ids' => $salesbox_offers_ids
            ]);

        }


        return true;
    }

    public function checkCategory($posterId, $recursively = true)
    {
        $salesbox_category = collect(salesbox_fetchCategories())
            ->filter(salesbox_filterCategoriesByExternalId($posterId))
            ->first();
        $poster_category = collect(poster_fetchCategories())
            ->filter(poster_filterCategoriesByCategoryId($posterId))
            ->first();

        if (!$salesbox_category) {
            $this->categoryIdsToCreate[] = $posterId;
        }

        if (!!$poster_category->parent_category && $recursively) {
            $this->checkCategory($poster_category->parent_category, $recursively);
        }
    }


}
