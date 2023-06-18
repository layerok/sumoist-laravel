<?php

namespace App\Poster\ActionHandlers;


use App\Poster\meta\PosterProduct_meta;
use App\Poster\Queries\PosterProductsQuery;
use App\Poster\Queries\SalesboxCategoriesQuery;
use App\Poster\Queries\SalesboxOffersQuery;
use App\Poster\SalesboxIntegration\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;

class ProductActionHandler extends AbstractActionHandler
{
    public $productIdsToCreate = [];
    public $productIdsToUpdate = [];
    public $categoryIdsToCreate = [];

    public function checkCategory($posterId, $recursively = true)
    {
        $salesbox_category = collect(salesbox_fetchCategories())
            ->filter(salesbox_filterCategoriesByExternalId($posterId))
            ->first();
        $poster_category = collect(poster_fetchCategories())
            ->filter(poster_filterCategoriesById($posterId))
            ->first();

        if (!$salesbox_category) {
            $this->categoryIdsToCreate[] = $posterId;
        }

        if (!!$poster_category->parent_category && $recursively) {
            $this->checkCategory($poster_category->parent_category, $recursively);
        }
    }

    public function handle(): bool
    {
        $handler = SalesboxApi::getGuzzleHandler();
        // store in per-request cache
        $handler->push(Middleware::mapResponse(function (ResponseInterface $response) {
            return $response;
        }));

        if ($this->isAdded() || $this->isRestored() || $this->isChanged()) {
            $posterId = $this->getObjectId();

            $poster_product = collect(poster_fetchProducts())
                ->filter($posterId)
                ->first();

            $salesbox_offer = collect(salesbox_fetchOffers())
                ->filter(salesbox_filterOffersByExternalId($posterId))
                ->first();

            if (!!$poster_product->menu_category_id) {
                // recursively check parent categories for existence
                $this->checkCategory($poster_product->menu_category_id);
            }

            if (!$salesbox_offer) {
                $this->productIdsToCreate[] = $posterId;
            } else {
                $this->productIdsToUpdate[] = $posterId;
            }

            if (count($this->categoryIdsToCreate)) {

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


}
