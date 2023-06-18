<?php

namespace App\Poster\ActionHandlers;
use App\Poster\meta\PosterProduct_meta;
use App\Poster\SalesboxIntegration\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;
use App\Salesbox\meta\SalesboxOfferV4_meta;

class ProductActionHandler extends AbstractActionHandler
{
    public $productIdsToCreate = [];
    public $productIdsToUpdate = [];
    public $categoryIdsToCreate = [];

    public function handle(): bool
    {
        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            $posterId = $this->getObjectId();

            $accessToken = salesbox_fetchAccessToken()->token;
            SalesboxApi::authenticate($accessToken);
            SalesboxApiV4::authenticate($accessToken);

            /** @var PosterProduct_meta $poster_product */
            $poster_product = collect(poster_fetchProducts())
                ->filter(poster_filterProductsById($posterId))
                ->first();

            /** @var SalesboxOfferV4_meta $salesbox_offer */
            $salesbox_offer = collect(salesboxV4_fetchOffers())
                ->filter(salesbox_filterOffersByExternalId($posterId))
                ->first();

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
                    ->map(poster_mapCategoryToJsonCreate())
                    ->values()
                    ->toArray();

                $createCategoriesResponse = SalesboxApi::createManyCategories([
                    'categories' => $categories
                ]);
            }

            if (count($this->productIdsToCreate) > 0) {
                $poster_productsToCreate = collect(poster_fetchProducts())
                    ->filter($this->productIdsToCreate);

                function mapToJson(): \Closure
                {
                    /** @param PosterProduct_meta $product */
                    return function ($product) {
                        if (isset($product->modifications)) {
                            return SalesboxOffer::getJsonForProductWithModificationsCreation($product->product_id);
                        }
                        return SalesboxOffer::getJsonForSimpleProductCreation(
                            $product->product_id,
                            null
                        );
                    };
                }

                $offers = $poster_productsToCreate->map(mapToJson());
            }

            if (count($this->productIdsToUpdate) > 0) {

            }

        }

        if ($this->isRemoved()) {
            SalesboxOffer::delete($this->getObjectId());
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
