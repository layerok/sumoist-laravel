<?php

namespace App\Poster\ActionHandlers;

use App\Poster\Facades\SalesboxStore;

class ProductRemovedActionHandler extends AbstractActionHandler
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
