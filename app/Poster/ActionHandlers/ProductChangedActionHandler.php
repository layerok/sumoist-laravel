<?php

namespace App\Poster\ActionHandlers;

use App\Poster\SalesboxIntegration\SalesboxOffer;
use App\Salesbox\Facades\SalesboxApi;
use App\Salesbox\Facades\SalesboxApiV4;


class ProductChangedActionHandler extends AbstractActionHandler
{

    public function handle(): bool
    {
        $token = SalesboxApi::authenticate();
        SalesboxApiV4::authenticate($token);
        $offers = collect(SalesboxApiV4::getOffers()['data']);
        $offer = $offers->firstWhere('externalId', $this->getObjectId());

        if (!$offer) {
            SalesboxOffer::create($this->getObjectId());
            return true;
        }

        SalesboxOffer::update($this->getObjectId(), $offer);
        return true;
    }
}
