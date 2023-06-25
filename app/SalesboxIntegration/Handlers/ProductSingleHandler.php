<?php

namespace App\SalesboxIntegration\Handlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterProduct;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxStore;
use App\Salesbox\Models\SalesboxOfferV4;
use App\SalesboxIntegration\Transformers\PosterCategoryAsSalesboxCategory;
use App\SalesboxIntegration\Transformers\PosterProductAsSalesboxOffer;
use RuntimeException;

class ProductSingleHandler extends AbstractHandler
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
        // handle products without modifications
        $poster_products_as_salesbox_offers = array_map(
            function(PosterProduct $posterProduct) {
                $transformer = new PosterProductAsSalesboxOffer($posterProduct);
                return $transformer->transform();
            },
            PosterStore::findProductsWithoutModifications($ids)
        );

        if (count($poster_products_as_salesbox_offers) > 0) {
            SalesboxStore::createManyOffers($poster_products_as_salesbox_offers);
        }
    }

    public function updateOffers(array $ids) {
        // handle products without modifications

        $poster_products_as_salesbox_offers = array_map(
            function(PosterProduct $posterProduct) {
                $offer = SalesboxStore::findOfferByExternalId($posterProduct->getProductId());
                $transformer = new PosterProductAsSalesboxOffer($posterProduct);
                return $transformer->updateFrom($offer);
            },
            PosterStore::findProductsWithoutModifications($ids)
        );

        if (count($poster_products_as_salesbox_offers) > 0) {
            $offersAsArray = array_map(function (SalesboxOfferV4 $offer) {
                return [
                    'id' => $offer->getId(),
                    'categories' => $offer->getCategories(),
                    'available' => $offer->getAvailable(),
                    'price' => $offer->getPrice(),
                ];
            }, $poster_products_as_salesbox_offers);

            SalesboxApi::updateManyOffers([
                'offers' => array_values($offersAsArray) // reindex array, it's important, otherwise salesbox api will fail
            ]);
        }
    }

}
