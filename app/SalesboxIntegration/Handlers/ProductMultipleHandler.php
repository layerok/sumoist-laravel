<?php

namespace App\SalesboxIntegration\Handlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterProduct;
use App\Poster\Models\PosterProductModification;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxStore;
use App\Salesbox\Models\SalesboxOfferV4;
use App\SalesboxIntegration\Transformers\PosterCategoryAsSalesboxCategory;
use App\SalesboxIntegration\Transformers\PosterProductModificationAsSalesboxOffer;
use RuntimeException;
use function collect;

class ProductMultipleHandler extends AbstractHandler
{
    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            SalesboxStore::authenticate();
            SalesboxStore::loadCategories();
            SalesboxStore::loadOffers();
            if(!PosterStore::isCategoriesLoaded()) {
                PosterStore::loadCategories();
            }
            if(!PosterStore::isProductsLoaded()) {
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
                // reload categories after creating them
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

    public function createCategories(array $ids) {
        $found_poster_categories = PosterStore::findCategory($ids);
        $poster_categories_as_salesbox_ones = array_map(function(PosterCategory $posterCategory) {
            $transformer = new PosterCategoryAsSalesboxCategory($posterCategory);
            return $transformer->transform();
        }, $found_poster_categories);

        SalesboxStore::createManyCategories($poster_categories_as_salesbox_ones);
    }

    public function createOffers(array $ids) {
        // handle products with modifications
        $modificatons_as_salesbox_offers = collect(
            PosterStore::findProductsWithModifications($ids)
        )
            ->map(function (PosterProduct $posterProduct) {
                return collect($posterProduct->getProductModifications())
                    ->map(function (PosterProductModification $modification) {
                        $transformer = new PosterProductModificationAsSalesboxOffer($modification);
                        return $transformer->transform();
                    });
            })
            ->flatten()
            ->toArray();

        if (count($modificatons_as_salesbox_offers) > 0) {
            SalesboxStore::createManyOffers($modificatons_as_salesbox_offers);
        }
    }

    public function updateOffers(array $ids) {
        // handle products with modifications
        /**
         * @var PosterProductModification[] $products_modifications
         */
        $products_modifications = array_merge(...array_map(function (PosterProduct $posterProduct) {
            return $posterProduct->getProductModifications();
        }, PosterStore::findProductsWithModifications($ids)));

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

                if (!$poster_product->hasModification($offer->getModifierId())) {
                    $delete_salesbox_offers[] = $offer;
                }
            }
        }

        foreach ($products_modifications as $modification) {
            $offer = SalesboxStore::findOfferByExternalId(
                $modification->getProduct()->getProductId(),
                $modification->getModificatorId()
            );
            if (!$offer) {
                $transformer = new PosterProductModificationAsSalesboxOffer($modification);
                $create_salesbox_offers[] = $transformer->transform();
            } else {
                $transformer = new PosterProductModificationAsSalesboxOffer($modification);
                $update_salesbox_offers[] = $transformer->updateFrom($offer);
            }
        }

        if(count($create_salesbox_offers)) {
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
