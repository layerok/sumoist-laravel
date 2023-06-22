<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Facades\SalesboxStore;
use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterDishModificationGroup;
use App\Poster\Models\PosterDishModification;
use App\Poster\Models\PosterProduct;
use App\Poster\Models\PosterProductModification;
use App\Poster\Models\SalesboxOfferV4;
use App\Salesbox\Facades\SalesboxApi;
use RuntimeException;

class DishMultipleActionHandler extends AbstractActionHandler
{
    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            SalesboxStore::authenticate();
            SalesboxStore::loadCategories();
            SalesboxStore::loadOffers();
            PosterStore::loadCategories();
            if (!PosterStore::isProductsLoaded()) {
                PosterStore::loadProducts();
            }

            if (!PosterStore::productExists($this->getObjectId())) {
                throw new RuntimeException(sprintf('product#%s is not found in poster', $this->getObjectId()));
            }

            $poster_product = PosterStore::findProduct($this->getObjectId());

            if (!PosterStore::categoryExists($poster_product->getMenuCategoryId())) {
                throw new RuntimeException('category#%s is not found in poster', $poster_product->getMenuCategoryId());
            }

            $product_create_ids = [];
            $product_update_ids = [];
            $category_create_ids = [];

            if (!SalesboxStore::offerExistsWithExternalId($this->getObjectId())) {
                $product_create_ids[] = $this->getObjectId();
            } else {
                $product_update_ids[] = $this->getObjectId();
            }

            if (!SalesboxStore::categoryExistsWithExternalId($poster_product->getMenuCategoryId())) {
                $category_create_ids[] = $poster_product->getMenuCategoryId();
            }

            $poster_category = PosterStore::findCategory($poster_product->getMenuCategoryId());

            if ($poster_category->hasParentCategory()) {
                $parent_poster_categories = $poster_category->getParents();

                foreach ($parent_poster_categories as $parent_poster_category) {
                    if (!SalesboxStore::categoryExistsWithExternalId($parent_poster_category->getCategoryId())) {
                        $category_create_ids[] = $parent_poster_category->getCategoryId();
                    }
                }
            }

            if (count($category_create_ids) > 0) {
                $this->createCategories($category_create_ids);
                SalesboxStore::loadCategories();
            }

            if (count($product_create_ids) > 0) {
                $this->createOffers($product_create_ids);

            }

            if (count($product_update_ids) > 0) {
                $this->updateOffers($product_update_ids);
            }

        }


        return true;
    }

    public function createCategories($ids = [])
    {
        $salesbox_categories = PosterStore::asSalesboxCategories(PosterStore::findCategory($ids));
        SalesboxStore::createManyCategories($salesbox_categories);
    }

    public function createOffers($ids = [])
    {
        $group_modificatons_as_salesbox_offers = collect(
            PosterStore::findProductsWithModificationGroups($ids)
        )
            ->map(function (PosterProduct $posterProduct) {
                return collect($posterProduct->getModificationGroups())
                    ->filter(function (PosterDishModificationGroup $modification) {
                        // skip 'multiple' type modifications
                        // because I don't know how to store them in salesbox
                        // I doubt it is even possible
                        return $modification->isSingleType();
                    })
                    ->map(function (PosterDishModificationGroup $modification) {
                        return collect($modification->getModifications())
                            ->map(function (PosterDishModification $modification) {
                                return $modification->asSalesboxOffer();
                            });
                    });
            })
            ->flatten()
            ->toArray();

        if (count($group_modificatons_as_salesbox_offers) > 0) {
            SalesboxStore::createManyOffers($group_modificatons_as_salesbox_offers);
        }
    }

    public function updateOffers($ids = [])
    {
        /**
         * @var PosterDishModification[] $dish_modifications
         */
        $dish_modifications = collect(PosterStore::findProductsWithModificationGroups($ids))
            ->map(function (PosterProduct $posterProduct) {
                return collect($posterProduct->getModificationGroups())
                    ->filter(function (PosterDishModificationGroup $group) {
                        // skip 'multiple' type modifications
                        // because I don't know how to store them in salesbox
                        // I doubt it is even possible
                        return $group->isSingleType();
                    })
                    ->map(function (PosterDishModificationGroup $group) {
                        return $group->getModifications();
                    });
            })
            ->flatten()
            ->toArray();

        $salesbox_offers = SalesboxStore::findOfferByExternalId($ids);

        /**
         * @var SalesboxOfferV4[] $delete_salesbox_offers
         */
        $delete_salesbox_offers = [];
        /**
         * @var SalesboxOfferV4[] $delete_salesbox_offers
         */
        $create_salesbox_offers = [];
        /**
         * @var SalesboxOfferV4[] $delete_salesbox_offers
         */
        $update_salesbox_offers = [];

        foreach ($salesbox_offers as $offer) {
            if ($offer->hasModifierId()) {
                $dish_modification_exists = PosterStore::dishModificationExists($offer->getExternalId(), $offer->getModifierId());
                if (!$dish_modification_exists) {
                    $delete_salesbox_offers[] = $offer;
                }
            }
        }

        foreach ($dish_modifications as $modification) {
            $offer = SalesboxStore::findOfferByExternalId(
                $modification->getGroup()->getProduct()->getProductId(),
                $modification->getDishModificationId()
            );
            if (!$offer) {
                $create_salesbox_offers[] = $modification->asSalesboxOffer();
            } else {
                $update_salesbox_offers[] = $offer->updateFromDishModification($modification);
            }
        }

        if (count($create_salesbox_offers)) {
            SalesboxStore::createManyOffers($create_salesbox_offers);
        }

        if (count($delete_salesbox_offers) > 0) {
            SalesboxStore::deleteManyOffers($delete_salesbox_offers);
        }

        if (count($update_salesbox_offers) > 0) {
            $offersAsArray = array_map(function (SalesboxOfferV4 $offer) {
                return [
                    'id' => $offer->getId(),
                    'modifierId' => $offer->getModifierId(),
                    'descriptions' => $offer->getOriginalAttributes('descriptions'),// don't update descriptions
                    'categories' => $offer->getCategories(),
                    'available' => $offer->getAvailable(),
                    'price' => $offer->getPrice(),
                    //'names' => $offer->getNames(),
                    //'photos' => $offer->getPhotos(),
                    // 'units' => $offer->getUnits(),
                    // 'stockType' => $offer->getStockType(),
                ];
            }, $update_salesbox_offers);

            SalesboxApi::updateManyOffers([
                'offers' => array_values($offersAsArray) // reindex array, it's important, otherwise salesbox api will fail
            ]);

        }
    }


}
