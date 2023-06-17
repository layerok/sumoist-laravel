<?php

namespace App\Poster\ActionHandlers;


use App\Poster\meta\PosterProduct_meta;
use App\Poster\PosterApiWrapper;
use App\Poster\SalesboxApiWrapper;
use App\Poster\SalesboxIntegration\SalesboxOffer;
use \App\Salesbox\Facades\SalesboxApi;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;

class ProductActionHandler extends AbstractActionHandler
{
    public $productIdsToCreate = [];
    public $productIdsToUpdate = [];
    public $categoryIdsToCreate = [];

    public function checkCategory($posterId, $recursively = true)
    {
        $salesbox_category = SalesboxApiWrapper::getCategory($posterId);
        $poster_category = PosterApiWrapper::getCategory($posterId);

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

            $poster_product = PosterApiWrapper::getProduct($posterId);
            $salesbox_offer = SalesboxApiWrapper::getOffer($posterId);

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
                $poster_productsToCreate = PosterApiWrapper::getProducts($this->productIdsToCreate);
                $offers = $poster_productsToCreate->map(
                /** @param PosterProduct_meta $product */
                    function ($product) {
                        if(isset($product->modifications)) {
                            return SalesboxOffer::getJsonForProductWithModificationsCreation($product->product_id);
                        }
                        return SalesboxOffer::getJsonForSimpleProductCreation(
                            $product->product_id,
                            null
                        );
                    }
                );
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
