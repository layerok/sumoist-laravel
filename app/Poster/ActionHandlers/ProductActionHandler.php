<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Facades\PosterStore;
use App\Poster\Facades\SalesboxStore;
use App\Poster\Models\PosterCategory;
use App\Poster\Models\PosterProduct;
use App\Poster\Models\SalesboxOffer;
use RuntimeException;

class ProductActionHandler extends AbstractActionHandler
{
    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            SalesboxStore::authenticate();
            SalesboxStore::loadCategories();
            SalesboxStore::loadOffers();
            PosterStore::loadCategories();
            PosterStore::loadProducts();

            if(!PosterStore::productExists($this->getObjectId())) {
                throw new RuntimeException(sprintf('product#%s is not found in poster', $this->getObjectId()));
            }

            $poster_product = PosterStore::findProduct($this->getObjectId());

            if(!PosterStore::categoryExists($poster_product->getMenuCategoryId())) {
                throw new RuntimeException('category#%s is not found in poster', $poster_product->getMenuCategoryId());
            }

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

            $poster_category = PosterStore::findCategory($poster_product->getMenuCategoryId());


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

                $poster_products_without_modificatons = array_filter(
                    $found_poster_products,
                    function (PosterProduct $posterProduct) {
                        return !$posterProduct->hasModifications();
                    }
                );

                $poster_products_as_salesbox_offers = PosterStore::asSalesboxOffers(
                    $poster_products_without_modificatons
                );

                SalesboxStore::createManyOffers($poster_products_as_salesbox_offers);
            }

            if (count($product_update_ids) > 0) {

                $found_poster_products = PosterStore::findProduct($product_update_ids);

                $poster_products_without_modificatons = array_filter(
                    $found_poster_products,
                    function (PosterProduct $posterProduct) {
                        return !$posterProduct->hasModifications();
                    }
                );

                $poster_products_as_salesbox_offers = SalesboxStore::updateFromPosterProducts(
                    $poster_products_without_modificatons
                );

                array_map(function(SalesboxOffer $offer) {
                    // don't update photo if it was already there
                    if($offer->getOriginalAttributes('previewURL')) {
                        $offer->resetAttributeToOriginalOne('previewURL');
                        $offer->resetAttributeToOriginalOne('originalURL');
                        $offer->setPhotos([]);
                    }

                    // don't update names
                    $offer->setNames([
                        [
                            'name' => $offer->getOriginalAttributes('name'),
                            'lang' => 'uk'
                        ]
                    ]);
                    // don't update descriptions
                     $offer->setDescriptions([]);
                }, $poster_products_as_salesbox_offers);

                SalesboxStore::updateManyOffers($poster_products_as_salesbox_offers);
            }

        }


        return true;
    }


}
