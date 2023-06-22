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

class DishSingleActionHandler extends AbstractActionHandler
{
    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            SalesboxStore::authenticate();
            SalesboxStore::loadCategories();
            SalesboxStore::loadOffers();
            PosterStore::loadCategories();
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
        $salesbox_categories = PosterStore::asSalesboxCategories(
            PosterStore::findCategory($ids)
        );
        SalesboxStore::createManyCategories($salesbox_categories);
    }

    public function createOffers($ids = [])
    {
        // handle products without modifications
        $poster_products_as_salesbox_offers = PosterStore::asSalesboxOffers(
            PosterStore::findProductsWithoutModificationGroups($ids)
        );

        if (count($poster_products_as_salesbox_offers) > 0) {
            SalesboxStore::createManyOffers($poster_products_as_salesbox_offers);
        }

    }

    public function updateOffers($ids = [])
    {
        // handle products without modifications
        $poster_products_as_salesbox_offers = SalesboxStore::updateFromPosterProducts(
            PosterStore::findProductsWithoutModifications($ids)
        );

        if (count($poster_products_as_salesbox_offers) > 0) {
            $offersAsArray = array_map(function (SalesboxOfferV4 $offer) {
                return [
                    'id' => $offer->getId(),
                    'modifierId' => $offer->getModifierId(),
                    'descriptions' => $offer->getOriginalAttributes('descriptions'), // don't update descriptions, use original ones
                    'categories' => $offer->getCategories(),
                    'available' => $offer->getAvailable(),
                    'price' => $offer->getPrice(),
                    //'names' => $offer->getNames(),
                    //'photos' => $offer->getPhotos(),
                    // 'units' => $offer->getUnits(),
                    // 'stockType' => $offer->getStockType(),
                ];
            }, $poster_products_as_salesbox_offers);

            SalesboxApi::updateManyOffers([
                'offers' => array_values($offersAsArray) // reindex array, it's important, otherwise salesbox api will fail
            ]);
        }
    }
}
