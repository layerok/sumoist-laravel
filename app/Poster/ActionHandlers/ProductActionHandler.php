<?php

namespace App\Poster\ActionHandlers;

use App\Poster\PosterCategory;
use App\Poster\PosterProduct;
use App\Poster\SalesboxCategory;
use App\Poster\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use App\Salesbox\meta\CreatedSalesboxCategory_meta;
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

            $accessToken = salesbox_fetchAccessToken();
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
                    ->whereIn('category_id', $this->categoryIdsToCreate)
                    ->map(function ($attributes) {
                        return new PosterCategory($attributes);
                    })
                    ->map(function (PosterCategory $posterCategory) {
                        return $posterCategory->asSalesboxCategory();
                    })
                    ->map(function (SalesboxCategory $category) {
                        return [
                            'externalId'        => $category->getExternalId(),
                            'parentId'          => $category->getParentId(),
                            'names'             => $category->getNames(),
                            'descriptions'      => $category->getDescriptions(),
                            'photos'            => $category->getPhotos(),
                            'internalId'        => $category->getInternalId(),
                            'previewURL'        => $category->getPreviewUrl(),
                            'originalURL'       => $category->getOriginalUrl(),
                            'available'         => $category->getAvailable(),
                        ];
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
                    ->whereIn('product_id', $this->productIdsToCreate)
                    ->map(function ($attributes) {
                        return new PosterProduct($attributes);
                    })
                    ->filter(function (PosterProduct $poster_product) {
                        return !$poster_product->hasModifications();
                    })
                    ->map(function (PosterProduct $poster_product) {
                        $salesbox_offer = $poster_product->asSalesboxOffer();

                        $salesbox_category = salesbox_fetchCategory($poster_product->getMenuCategoryId());

                        if ($salesbox_category) {
                            $salesbox_offer->setCategories([$salesbox_category->id]);
                        } else {
                            /** @var CreatedSalesboxCategory_meta[]|null $created_categories */
                            $created_categories = perRequestCache()
                                ->get('salesbox.categories.created');

                            /** @var CreatedSalesboxCategory_meta|null $created_category */
                            $created_category = collect($created_categories)
                                ->firstWhere('internalId', $poster_product->getMenuCategoryId());

                            if ($created_category) {
                                $salesbox_offer->setCategories([$created_category->id]);
                            }
                        }

                        return $salesbox_offer;
                    })
                    ->map(function (SalesboxOffer $offer) {
                        return [
                            'externalId'            => $offer->getExternalId(),
                            'units'                 => $offer->getUnits(),
                            'stockType'             => $offer->getStockType(),
                            'descriptions'          => $offer->getDescriptions(),
                            'photos'                => $offer->getPhotos(),
                            'categories'            => $offer->getCategories(),
                            'names'                 => $offer->getNames(),
                            'available'             => $offer->getAvailable(),
                            'price'                 => $offer->getPrice(),
                        ];
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
                    ->whereIn('product_id', $this->productIdsToCreate)
                    ->filter('poster_productWithoutModificators')
                    ->map(function (PosterProduct $poster_product) {
                        $salesbox_offer = $poster_product->asSalesboxOffer();

                        $salesbox_category = salesbox_fetchCategory($poster_product->getMenuCategoryId());

                        if ($salesbox_category) {
                            $salesbox_offer->setCategories([$salesbox_category->id]);
                        } else {
                            /** @var CreatedSalesboxCategory_meta[]|null $created_categories */
                            $created_categories = perRequestCache()
                                ->get('salesbox.categories.created');

                            /** @var CreatedSalesboxCategory_meta|null $created_category */
                            $created_category = collect($created_categories)
                                ->firstWhere('internalId', $poster_product->getMenuCategoryId());

                            if ($created_category) {
                                $salesbox_offer->setCategories([$created_category->id]);
                            }
                        }

                        return $salesbox_offer;
                    })
                    ->map(function (SalesboxOffer $offer) {
                        return [
                            'externalId'            => $offer->getExternalId(),
                            'units'                 => $offer->getUnits(),
                            'stockType'             => $offer->getStockType(),
                            'descriptions'          => $offer->getDescriptions(),
                            'photos'                => $offer->getPhotos(),
                            'categories'            => $offer->getCategories(),
                            'names'                 => $offer->getNames(),
                            'available'             => $offer->getAvailable(),
                            'price'                 => $offer->getPrice(),
                        ];
                    })
                    ->values()
                    ->toArray();

                SalesboxApi::createManyOffers([
                    'offers' => $simpleOffers
                ]);
            }

        }

        if ($this->isRemoved()) {
            $token = salesbox_fetchAccessToken();
            SalesboxApi::authenticate($token);
            SalesboxApiV4::authenticate($token);


            $salesbox_offers_ids = collect(salesboxV4_fetchOffers())
                ->where('externalId', $this->getObjectId())
                /** @param SalesboxOfferV4_meta $offer */
                ->map(function ($offer) {
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
        $salesbox_category = salesbox_fetchCategory($posterId);
        $poster_category = poster_fetchCategory($posterId);

        if (!$salesbox_category) {
            $this->categoryIdsToCreate[] = $posterId;
        }

        if (!!$poster_category->parent_category && $recursively) {
            $this->checkCategory($poster_category->parent_category, $recursively);
        }
    }


}
