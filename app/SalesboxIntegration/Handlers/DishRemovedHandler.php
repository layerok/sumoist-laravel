<?php

namespace App\SalesboxIntegration\Handlers;

use App\Salesbox\Facades\SalesboxStore;

class DishRemovedHandler extends AbstractHandler
{
    public function handle(): bool
    {
        SalesboxStore::authenticate();
        SalesboxStore::loadOffers();

        $offers_to_delete = SalesboxStore::findOfferByExternalId([$this->getObjectId()]);

        if(count($offers_to_delete) > 0) {
            // delete products
            SalesboxStore::deleteManyOffers($offers_to_delete);
        }

        return true;
    }


}
