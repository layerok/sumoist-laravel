<?php

namespace App\SalesboxIntegration\Handlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterDishModification;
use App\Poster\Models\PosterDishModificationGroup;
use App\Poster\Models\PosterProduct;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxStore;
use App\Salesbox\Models\SalesboxOfferV4;
use App\SalesboxIntegration\Transformers\PosterCategoryAsSalesboxCategory;
use App\SalesboxIntegration\Transformers\PosterDishModificationAsSalesboxOffer;
use RuntimeException;
use function collect;

class DishMultipleHandler extends AbstractHandler
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
        $poster_categories_as_salesbox_ones = array_map(function(PosterCategory $posterCategory) {
            $transformer = new PosterCategoryAsSalesboxCategory($posterCategory);
            return $transformer->transform();
        }, PosterStore::findCategory($ids));

        SalesboxStore::createManyCategories($poster_categories_as_salesbox_ones);
    }

    public function createOffers($ids = [])
    {
        $group_modificatons_as_salesbox_offers = collect(
            PosterStore::findProductsWithModificationGroups($ids)
        )
            ->map(function (PosterProduct $posterProduct) {
                return collect($posterProduct->getDishModificationGroups())
                    ->filter(function (PosterDishModificationGroup $modification) {
                        // skip 'multiple' type modifications
                        // because I don't know how to store them in salesbox
                        // I doubt it is even possible
                        return $modification->isSingleType();
                    })
                    ->map(function (PosterDishModificationGroup $modification) {
                        return collect($modification->getModifications())
                            ->map(function (PosterDishModification $modification) {
                                $transformer = new PosterDishModificationAsSalesboxOffer($modification);
                                return $transformer->transform();
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
                return collect($posterProduct->getDishModificationGroups())
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
                $poster_product = PosterStore::findProduct($offer->getExternalId());

                if(!$poster_product->hasModification($offer->getModifierId())) {
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
                $transformer = new PosterDishModificationAsSalesboxOffer($modification);
                $create_salesbox_offers[] = $transformer->transform();
            } else {
                $transformer = new PosterDishModificationAsSalesboxOffer($modification);
                $update_salesbox_offers[] = $transformer->updateFrom($offer);
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
                    'categories' => $offer->getCategories(),
                    'available' => $offer->getAvailable(),
                    'price' => $offer->getPrice(),
                ];
            }, $update_salesbox_offers);

            SalesboxApi::updateManyOffers([
                'offers' => array_values($offersAsArray) // reindex array, it's important, otherwise salesbox api will fail
            ]);

        }
    }

}
