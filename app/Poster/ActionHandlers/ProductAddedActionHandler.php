<?php

namespace App\Poster\ActionHandlers;

use App\Poster\PosterApiException;
use App\Salesbox\Facades\SalesboxApi;
use poster\src\PosterApi;

class ProductAddedActionHandler extends AbstractActionHandler {

    public function handle(): bool
    {
        SalesboxApi::authenticate();

        $offersRes = SalesboxApi::getOffers();

        $collection = collect($offersRes['data']);

        $salesboxProduct = $collection->firstWhere('externalId', $this->getObjectId());

        if($salesboxProduct) {
            return false;
        }

        $posterProducts = PosterApi::menu()->getProduct([
            'product_id' => $this->getObjectId()
        ]);

        if(!isset($posterProducts->response) || !$posterProducts->response) {
            throw new PosterApiException('getProduct', $posterProducts);
        }

        return true;
    }
}
