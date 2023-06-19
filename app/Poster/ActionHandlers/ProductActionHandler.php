<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Facades\SalesboxStore;
use App\Poster\PosterCategory;
use App\Poster\PosterProduct;
use App\Poster\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\meta\CreatedSalesboxCategory_meta;
use App\Salesbox\meta\SalesboxOfferV4_meta;

class ProductActionHandler extends AbstractActionHandler
{
    /** @var CreatedSalesboxCategory_meta $created_categories */
    public $created_categories;

    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            SalesboxStore::authenticate();
            SalesboxStore::loadCategories();
            SalesboxStore::loadOffers();
            PosterStore::loadCategories();
            PosterStore::loadProducts();

            $poster_product = PosterStore::findProduct($this->getObjectId());
            $poster_category = PosterStore::findCategory($poster_product->getMenuCategoryId());

            $product_create_ids = [];
            $product_update_ids = [];
            $category_create_ids = [];

            if (!SalesboxStore::offerExists($this->getObjectId())) {
                $product_create_ids[] = $this->getObjectId();
            } else {
                $product_update_ids[] = $this->getObjectId();
            }

            if (!SalesboxStore::categoryExists($poster_product->getMenuCategoryId())) {
                $category_create_ids[] = $poster_product->getMenuCategoryId();
            }

            if ($poster_category->hasParentCategory()) {
                $parent_poster_categories = $poster_category->getParents();

                foreach ($parent_poster_categories as $parent_poster_category) {
                    if (!SalesboxStore::categoryExists($parent_poster_category->getCategoryId())) {
                        $category_create_ids[] = $parent_poster_category->getCategoryId();
                    }
                }
            }

            if (count($category_create_ids) > 0) {
                $poster_ategories_to_create = array_filter(PosterStore::getCategories(), function ($poster_category) use ($categories_ids) {
                    return in_array($poster_category->getCategoryId(), $categories_ids);
                });

                $poster_categories_as_salesbox = array_map(function(PosterCategory $poster_category) {
                    return $poster_category->asSalesboxCategory();
                }, $poster_ategories_to_create);

                SalesboxStore::createManyCategories($poster_categories_as_salesbox);
            }

            if (count($product_create_ids) > 0) {

                $simpleOffers = collect(PosterStore::getProducts())
                    ->whereIn('product_id', $products_ids)
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
                            'externalId' => $offer->getExternalId(),
                            'units' => $offer->getUnits(),
                            'stockType' => $offer->getStockType(),
                            'descriptions' => $offer->getDescriptions(),
                            'photos' => $offer->getPhotos(),
                            'categories' => $offer->getCategories(),
                            'names' => $offer->getNames(),
                            'available' => $offer->getAvailable(),
                            'price' => $offer->getPrice(),
                        ];
                    })
                    ->values()
                    ->toArray();


                SalesboxApi::createManyOffers([
                    'offers' => $simpleOffers
                ]);

            }

            if (count($product_update_ids) > 0) {


                $simpleOffers = collect(PosterStore::getProducts())
                    ->whereIn('product_id', PosterStore::getProducts())
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
                            'externalId' => $offer->getExternalId(),
                            'units' => $offer->getUnits(),
                            'stockType' => $offer->getStockType(),
                            'descriptions' => $offer->getDescriptions(),
                            'photos' => $offer->getPhotos(),
                            'categories' => $offer->getCategories(),
                            'names' => $offer->getNames(),
                            'available' => $offer->getAvailable(),
                            'price' => $offer->getPrice(),
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
            SalesboxStore::authenticate();

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



}
