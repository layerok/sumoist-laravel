<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Facades\SalesboxStore;
use App\Poster\PosterCategory;
use App\Poster\PosterProduct;
use App\Poster\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\meta\CreatedSalesboxCategory_meta;

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
                $found_poster_categories = PosterStore::findCategory($category_create_ids);

                $poster_categories_as_salesbox_ones = array_map(
                    function (PosterCategory $poster_category) {
                        return $poster_category->asSalesboxCategory();
                    },
                    $found_poster_categories
                );

                SalesboxStore::createManyCategories($poster_categories_as_salesbox_ones);
                SalesboxStore::loadCategories();
            }

            if (count($product_create_ids) > 0) {

                $found_poster_products = PosterStore::findProduct($product_create_ids);

                $poster_products_withotu_modificatons = array_filter($found_poster_products, function(PosterProduct $posterProduct) {
                    return !$posterProduct->hasModifications();
                });

                $poster_products_as_salesbox_offers = array_map(function (PosterProduct $poster_product) {
                    if(SalesboxStore::offerExists($poster_product->getProductId())) {
                        $offer = SalesboxStore::findOffer($poster_product->getProductId());
                        return $offer->updateFromPosterProduct($poster_product);
                    }
                    return $poster_product->asSalesboxOffer();
                }, $poster_products_withotu_modificatons);


                $offersAsJson = array_map(function(SalesboxOffer $offer) {
                    return $offer->asJson();
                }, $poster_products_as_salesbox_offers);


                SalesboxApi::createManyOffers([
                    'offers' => array_values($offersAsJson)// reindex array, it's important, otherwise salesbox api will fail
                ]);

            }

            if (count($product_update_ids) > 0) {

                $filtered_products = array_filter(PosterStore::getProducts(),
                    function (PosterProduct $posterProduct) use ($product_update_ids) {
                        return in_array($posterProduct->getProductId(), $product_update_ids) &&
                            !$posterProduct->hasModifications();
                    });

                $offers = array_map(function (PosterProduct $poster_product) {
                    $salesbox_offer = $poster_product->asSalesboxOffer();

                    $salesbox_category = SalesboxStore::findCategory($poster_product->getMenuCategoryId());

                    if ($salesbox_category) {
                        $salesbox_offer->setCategories([$salesbox_category->getId()]);
                    }


                    return [
                        'externalId' => $salesbox_offer->getExternalId(),
                        'units' => $salesbox_offer->getUnits(),
                        'stockType' => $salesbox_offer->getStockType(),
                        'descriptions' => $salesbox_offer->getDescriptions(),
                        'photos' => $salesbox_offer->getPhotos(),
                        'categories' => $salesbox_offer->getCategories(),
                        'names' => $salesbox_offer->getNames(),
                        'available' => $salesbox_offer->getAvailable(),
                        'price' => $salesbox_offer->getPrice(),
                    ];
                }, $filtered_products);


                SalesboxApi::createManyOffers([
                    'offers' => array_values($offers)
                ]);
            }

        }

        if ($this->isRemoved()) {
            SalesboxStore::authenticate();
            SalesboxStore::loadOffers();

            $ids_to_delete = array_filter(SalesboxStore::getOffers(), function (SalesboxOffer $offer) {
                return $offer->getExternalId() === $this->getObjectId();
            });

            // delete products
            SalesboxApi::deleteManyOffers([
                'ids' => array_values($ids_to_delete)
            ]);

        }


        return true;
    }



}
